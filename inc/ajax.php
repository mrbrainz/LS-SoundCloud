<?php 
require_once "Dropbox/autoload.php";
use \Dropbox as dbx;
if ($_POST['task'] == 'doreposts') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something');
	
	$reposts = json_decode(base64_decode($_POST['repostdata']));

	$sct = SC_TOKEN;  
	if ($sct) {
			$sc = sc_getuser();
	} else {
		exit('No Token Found');	
	}
	$uid = get_current_user_id();
	$count = 0;	
	
	foreach ($reposts as $repost) {
		if ($repost->url && $repost->reposter && $repost->reposterurl) {
			$resolveurl = 'http://api.soundcloud.com/resolve.json?url=https://soundcloud.com'.$repost->url.'&client_id='.SC_CLIENT_ID;
			$track = json_decode(file_get_contents($resolveurl));
			if ($track->status == "302 - Found") {
				$track = json_decode(file_get_contents($track->location));
				$trackdetails['id'] = $track->id;
				$trackdetails['length'] = $track->duration;
				$trackdetails['url'] = 'http://soundcloud.com'.$repost->url;
				$trackdetails['reposter'] = $repost->reposter;
				$trackdetails['reposterurl'] = $repost->reposterurl;
				$trackdetails['reposttime'] = strtotime($repost->reposttime);
				$trackdetails['type'] = $repost->type;
				
				$trackquery = new WP_Query( 
					array(  'author' => $uid,
							'post_type' => 'repost',
							'meta_key' => 'scid', 
							'meta_value' => $trackdetails['id'],
							'meta_compare' => '==' ) 
				);
				
				$exists = false;
				
				if ($trackquery->have_posts()) {
					$trackquery->the_post();
					if (get_post_meta($trackquery->post->ID,'reposttime',true) < $trackdetails['reposttime']) {
						wp_delete_post( $trackquery->post->ID, true );		
					}
					else {
						$exists = true;	
					}
				}
				if (!$exists) {
					$post = array(
					  'post_name'      => $trackdetails['id'],
					  'post_title'     => $trackdetails['id'],
					  'post_status'    => 'publish',
					  'post_type'      => 'repost',
					  'post_author'    => $uid,
					  'post_date'      => date('Y-m-d H:i:s',$trackdetails['reposttime']),
					  'post_date_gmt'  => date('Y-m-d H:i:s',$trackdetails['reposttime'])
					);  
					
					$booya = wp_insert_post( $post );
					
					if (!is_wp_error($booya)) {
						update_post_meta($booya,'scid',$trackdetails['id']);
						update_post_meta($booya,'url',$trackdetails['url']);
						update_post_meta($booya,'reposter',$trackdetails['reposter']);
						update_post_meta($booya,'reposterurl',$trackdetails['reposterurl']);
						update_post_meta($booya,'reposttime',$trackdetails['reposttime']);
						update_post_meta($booya,'type',$trackdetails['type']);
						update_post_meta($booya,'length',$trackdetails['length']);
						$output['repostdata'][] = $booya;		
						$count++;
					}
				}
			}
			
		}
	}
	if ($count) {
		unset($_SESSION['repostdata']);
		unset($_SESSION['loginmessage']);
		$output['repostcount'] = $count;
	}
	else {
		$output['error'] = "No Reposts To Output. Shit be fucked.";	
	}
	echo json_encode($output);	
	
} elseif ($_POST['task'] == 'like' || $_POST['task'] == 'unlike') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) {
		$output['error'] = 'Bad nonce. Like Rolf Harris or something';
	} else {
	
		$sct = SC_TOKEN;  
		
		if ($sct) {
			$sc = sc_getuser();
			$sc->setAccessToken($sct);
			if (isset($_POST['scid'])) {
				if ($_POST['task'] == 'like') {
					$like = $sc->put('me/favorites/' . $_POST['scid'].'.'.$_POST['type'],array('format'=>$_POST['type'],'track_id'=>$_POST['scid']));
					if ($like = "<status>201 - Created</status>") {
						$output['success'] = 'liked';
					} else {
						$output['error'] = 'error';		
					}
					$output['return'] = $like;
				} else {
					$like = $sc->delete('me/favorites/' . $_POST['scid'].'.'.$_POST['type'],array('format'=>$_POST['type'],'track_id'=>$_POST['scid']));				
					if ($like = '<status>200 - OK</status>') {
						$output['success'] = 'unliked';
					}
					else {
						$output['error'] = 'error';	
					}
					$output['return'] = $like;
				}
			}
			else {
				$output['error'] = 'No correct task found';
			}
		} else {
			$output['error'] = 'No Token Found';	
		}
	}
	echo json_encode($output);
} elseif ($_POST['task'] == 'clearrepostcache') {

require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something');
	$uid = get_current_user_id();
	$rp = new WP_Query(array(
			'author' => $uid,
			'post_type' => 'repost',
			'showposts' => -1
		));
	
	while ( $rp->have_posts() ) : $rp->the_post();
		wp_delete_post( $rp->post->ID, true );
	endwhile;
	echo json_encode(array('success' => 'Process Finished'));
}
elseif ($_POST['task'] == 'deauthdropbox') {

require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something');
	$uid = get_current_user_id();
	delete_user_meta($uid,'dropboxtoken');
	delete_user_meta($uid,'dropboxuserid');
	echo json_encode(array('success' => 'Process Finished'));
}

elseif ($_POST['task'] == 'comment') {

require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) {
		$output['error'] = 'Bad nonce. Like Rolf Harris or something';
	} else {
	
		$sct = SC_TOKEN;  
		
		if ($sct) {
			$sc = sc_getuser();
			$sc->setAccessToken($sct);
			if (isset($_POST['scid']) && isset($_POST['commenttext'])) {
					
					$comment = json_decode($sc->post('tracks/' . $_POST['scid'] . '/comments', array(
						'comment[body]' => $_POST['commenttext']
					)));
					$output['success'] = 'Comment left';
					$output['return'] = $comment;

			}
			else {
				$output['error'] = 'Not all data received to leave a comment';
			}
		} else {
			$output['error'] = 'No Token Found';	
		}
	}
	echo json_encode($output);
} elseif ($_POST['task'] == 'follow' || $_POST['task'] == 'unfollow') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) {
		$output['error'] = 'Bad nonce. Like Rolf Harris or something';
	} else {
	
		$sct = SC_TOKEN;  
		
		if ($sct) {
			$sc = sc_getuser();
			$sc->setAccessToken($sct);
			if (isset($_POST['scid'])) {
				if ($_POST['task'] == 'follow') {
					$fw = $sc->put('me/followings/' . $_POST['scid'], array());
					$fwr = json_decode($fw);
					if ($fwr->id == $_POST['scid']) {
						$output['return'] = $fwr;
						$output['success'] = 'followed';
					} else {
						$output['error'] = 'error';		
					} 
					$output['return'] = $fw;
				} else {
					$fw = $sc->delete('me/followings/' . $_POST['scid'], array());				
					if ($fw = '<status>200 - OK</status>') {
						$output['success'] = 'unfollowed';
					}
					else {
						$output['error'] = 'error';	
					}
					$output['return'] = $fw;
				}
			}
			else {
				$output['error'] = 'No correct task found';
			}
		} else {
			$output['error'] = 'No Token Found';	
		}
	}
	echo json_encode($output);
}
elseif ($_POST['task'] == 'limitchange') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something');
	$uid = get_current_user_id();
	$lc = update_user_meta($uid,'nssclimit',intval($_POST['limitno']));
	if (!is_wp_error($lc)) {
		echo json_encode(array('success' => 'Process Finished'));
	} else {
		echo json_encode(array('error' => $lc->get_error_message()));	
	}
}
elseif ($_POST['task'] == 'lengthchange') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something');
	$uid = get_current_user_id();
	$lc = update_user_meta($uid,'nssclength',intval($_POST['length']));
	if (!is_wp_error($lc)) {
		echo json_encode(array('success' => 'Process Finished'));
	} else {
		echo json_encode(array('error' => $lc->get_error_message()));	
	}
}
elseif ($_POST['task'] == 'songlength') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something');
	$uid = get_current_user_id();
	$lc = update_user_meta($uid,'nssclengthsplit',intval($_POST['length']));
	if (!is_wp_error($lc)) {
		echo json_encode(array('success' => 'Process Finished'));
	} else {
		echo json_encode(array('error' => $lc->get_error_message()));	
	}
}
elseif ($_POST['task'] == 'urlresolve') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something');	
	require_once('URLResolver.php');
	$resolver = new URLResolver();
	$resolver->setUserAgent('Mozilla/5.0 (compatible; NSSC/1.0; +'.get_bloginfo('url').')');
	$resolver->setCookieJar('/tmp/url_resolver.cookies');

	$url_result = $resolver->resolveURL($_POST['url']);
	
	if ($url_result->didErrorOccur()) {
		echo json_encode(array('error' => $url_result->getErrorMessageString()));
	}
	
	else {
		echo json_encode(array('success' => 'Process Finished', 'status' => $url_result->getHTTPStatusCode(), 'url' => $url_result->getURL()));
	}
}
elseif ($_POST['task'] == 'rapexlr8r') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something');	
	require_once('simple_html_dom.php');
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $_POST['url']);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36');
	$html = curl_exec($ch);
	curl_close($ch);
	$html = str_get_html($html);
			
	if ($html) {
		$link = $html->find('.download ul.a a');
	}
	if (is_array($link)) {
		
		if ($link[0]->href) {
			echo json_encode(array('success' => 'Process Finished', 'url' => $link[0]->href));
		} else {
			echo json_encode(array('error' => 'Shit be fucked'));
		}
	} else {
		echo json_encode(array('error' => 'Didn\'t find element'));	
	}
}
elseif ($_POST['task'] == 'dbupload') {
	require('../../../../wp-blog-header.php');
	header("HTTP/1.1 200 OK");
	if (!check_ajax_referer( 'jaxysmash' )) exit('Bad nonce. Like Rolf Harris or something'); 
	if (DB_TOKEN) {
		if (SC_TOKEN) {
			$sc = sc_getuser();	
			// Get Tracks by ID
			$track = $sc->get('tracks/'.$_POST['scid']);
			if (is_array($track)) {
				$track = array();
				echo json_encode(array('error' => 'No track found.'));
				exit;
			} else {
				$track = json_decode($track);				
				//error_log(print_r($track,true).PHP_EOL,3,'log.log');
				global $dbxClient;	
				if (!$dbxClient) {
					$appInfo = dbx\AppInfo::loadFromJsonFile(get_stylesheet_directory()."/inc/nsscjsonfmb.json");	
					$dbxClient = new dbx\Client(DB_TOKEN, "NSSC/1.0");	
				}
				$accountInfo = $dbxClient->getAccountInfo();
				$free = $accountInfo['quota_info']['quota'] - ($accountInfo['quota_info']['shared'] + $accountInfo['quota_info']['normal']);
				if ($free > $track->original_content_size) {
					if ($track->download_url) {					
						$f = get_headers($track->download_url.'?client_id='.SC_CLIENT_ID, 1);		
						if ($f[0] == 'HTTP/1.1 302 Found') {
							$fh = get_headers($f['Location'], 1);			
							$fn = rtrim(str_replace('attachment;filename="','',$fh['Content-Disposition']), '"');
							$loc = $f['Location'];
							$f = fopen($loc, "rb");
							$result = $dbxClient->uploadFile("/".$fn, dbx\WriteMode::add(), $f);
							fclose($f);
							//error_log(print_r($result,true),3,'log.log');
							
							if ($result['path']) {
								echo json_encode(array('success' => 'Process Finished','path' => $result['path'],'size' => $result['size']));
							}
							else {
								echo json_encode(array('error' => 'Upload error','result' => $result));		
							}
						} else {
							echo json_encode(array('error' => 'No headers returned for track.'));
							exit;	
						}
					}
					else {
						echo json_encode(array('error' => 'No download URL? WTF? '));
						exit;
					}
				} else {
					echo json_encode(array('error' => 'Not enough free space on your Dropbox, baby.'));
					exit;
				}
			}
		}
	} else {
		echo json_encode(array('error' => 'No Dropbox Token Found. Have you connected it yet?'));		
	}
}
else {
	header("HTTP/1.1 401 Not Authorised");	
}