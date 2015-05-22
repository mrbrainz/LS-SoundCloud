<?php 
/* 
add_action('init','sc_rewrites');
function sc_rewrites() {
       global $wp;
       $wp->add_query_var('nssc-auth');
       add_rewrite_rule('nssc-auth/?(.*)$', get_bloginfo('url').'?nssc-auth&$matches[1]', 'top');
}
*/

function home_marketing_template($template) {
	return dirname(__FILE__).'/../home-marketing.php';
}

add_action('template_redirect','sc_redirects',1); 

function sc_redirects($content) {
	
	// Should make this a case statement.... 
	
	if (strpos( $_SERVER['QUERY_STRING'],'nssc-auth') === 0 ) {
			$scl = 'auth';
		} elseif ($_SERVER['QUERY_STRING'] == 'nssc-sclogin') {
			$scl = 'login';
		} elseif ($_SERVER['QUERY_STRING'] == 'nssc-dbauth') {
			$scl = 'dbauth';
		}
		elseif ($_SERVER['QUERY_STRING'] == 'dropboxsuccess') {
			$scl = 'dbsuccess';
		}
		elseif ($_SERVER['QUERY_STRING'] == 'dropboxfail') {
			$scl = 'dbfail';
		}
		elseif ($_SERVER['QUERY_STRING'] == 'reposter') {
			$scl = 'reposter';
		}
		elseif ($_SERVER['QUERY_STRING'] == 'reposts') {
			$scl = 'repostdisplay';
		}
		elseif ($_SERVER['QUERY_STRING'] == 'murkreposts') {
			$scl = 'repostmurk';
		}
		elseif ($_SERVER['QUERY_STRING'] == 'loggedout=true') {
			$scl = 'bth';
		}
		elseif ((is_home() && !is_user_logged_in()) || (is_home() && is_user_logged_in() && !SC_TOKEN)) {
			add_filter( 'template_include', 'home_marketing_template' );
		}
		elseif ((!is_page('login')&&!is_page('about')&&!is_page('faq')&&!is_home()&&!is_front_page()&&!is_user_logged_in()) || (is_user_logged_in() && !SC_TOKEN) || (is_page('logout')&&!is_user_logged_in())) {
			wp_redirect(get_bloginfo('url'));
			exit;
		}
		elseif (!user_can(get_current_user_id(),'administrator') && is_page('your-profile')) {
			wp_redirect(get_bloginfo('url')); 
			exit;
		}
		elseif(is_user_logged_in() && is_page('logout')) {
			wp_clear_auth_cookie();
			session_unset();
			wp_redirect(get_bloginfo('url')); 
			exit;
		}elseif(is_page('stream')) {
			session_start();
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$next_href = (($paged != 1)&& $_SESSION['homepage'][$paged-1]['next_href']) ? $_SESSION['homepage'][$paged-1]['next_href'] : false;
			$cr = $paged-2;
			
			if (!$next_href && ($paged != 1)) {
				while (!$next_page) {
					if ($cr == -1) {
						wp_redirect(get_bloginfo('url'));
						exit;
					}
					elseif (isset($_SESSION['homepage'][$cr]['next_href'])) {
						wp_redirect(get_permalink().'page/'.($cr+1).'/');
						exit;
						$next_page = true;
					}
					$cr--;
				}
			}
		}
		else {
			$scl = false;	
		}
		
	switch ($scl) :
	
		case('login') :
		
		require_once 'Services/Soundcloud.php';
		
		// create client object with app credentials
		$client = new Services_Soundcloud(SC_CLIENT_ID, SC_CLIENT_SECRET, get_bloginfo('url').'/?nssc-auth');
		
		// redirect user to authorize URL
		header("Location: " . $client->getAuthorizeUrl().'&scope=non-expiring&display=popup');	
		exit;	
		break;
		
		case('auth') :
		// Receive oAuth response
		$id = get_current_user_id();
		$code = $_GET['code'];
		if ($code) {

			require_once 'Services/Soundcloud.php';
			$client = new Services_Soundcloud(SC_CLIENT_ID, SC_CLIENT_SECRET, get_bloginfo('url').'/?nssc-auth');
			
			$access_token = $client->accessToken($code);
			
			$sc = sc_getuser($access_token['access_token']);
			
			$me = json_decode($sc->get('me'));
			$loser = get_user_by('login','sc-'.$me->permalink);
			
			 if (is_user_logged_in()) {
				delete_user_meta($id, 'sc_token');
				update_user_meta($id, 'sc_token', $access_token['access_token']);
				 session_start();
				 if (isset($_SESSION['repostdata'])) {
					wp_redirect(get_bloginfo('url').'?reposter'); 
				 }
				 else {			 
					wp_redirect(get_bloginfo('url'));
				 }
				 exit;
			 }
			 elseif($loser) {
				// login if exists prev SC login
				delete_user_meta($loser->ID, 'sc_token');
				update_user_meta($loser->ID, 'sc_token', $access_token['access_token']);
				
				$customdata = array(
					'user_url'    =>  $me->website,
					'first_name' => $me->first_name,
					'last_name' => $me->last_name,
					'display_name' => $me->username,
					'nickname' => $me->username,
					'city'			=> $me->city,
					'avatar_url' 	=> $me->avatar_url,
					'sc_id'			=> $me->id,
					'sc-username'	=> $me->permalink
					);
				
				wp_set_auth_cookie( $loser->ID, true );
				 session_start();
				 if (isset($_SESSION['repostdata'])) {
					wp_redirect(get_bloginfo('url').'?reposter'); 
				 }
				 else {			 
					wp_redirect(get_bloginfo('url'));
				 }
				 exit;
			 }
			 else {
				// create user 
				
				// if not exist, create user with that username and do the shit above kinda
				
				 
				$random_password = wp_generate_password( 12,false );
	
				$userdata = array(
					'user_login'  =>  'sc-'.$me->permalink,
					'user_nicename' => 'sc-'.$me->permalink,
					'user_url'    =>  $me->website,
					'user_pass'   =>  $random_password,
					'first_name' => $me->first_name,
					'last_name' => $me->last_name,
					'display_name' => $me->username,
					'nickname' => $me->username,
					'user_email' => 'sc-'.$me->permalink.'@lsscfakeemail.com'
				);
				$customdata = array(
					'city'			=> $me->city,
					'avatar_url' 	=> $me->avatar_url,
					'sc_id'			=> $me->id,
					'sc-username'	=> $me->permalink
					);
				
				$user_id = wp_insert_user( $userdata ) ;
				
				//On success
				if( !is_wp_error($user_id) ) {
				foreach ($customdata as $key=>$meta) {
					update_user_meta($user_id,$key,$meta);	
				}	
				 update_user_meta($user_id, 'sc_token', $access_token['access_token']);			
				 wp_set_auth_cookie( $user_id, true );
				 session_start();
				 if (isset($_SESSION['repostdata'])) {
					wp_redirect(get_bloginfo('url').'?reposter');
					exit;
				 }
				 else {			 
					wp_redirect(get_bloginfo('url'));
					exit;
				 }
				 exit;
				}
				
			 }
		} else {
			wp_redirect(get_bloginfo('url'));
			exit;	
		}
		break;
		
		case('reposter') :
			session_start();
			if (isset($_POST['tunestring']) && isset($_POST['nsscid'])) { 
				$nsscid = sc_decryptf($_POST['nsscid']);
				if (is_user_logged_in() && ($nsscid != get_current_user_id())) {
					// For when Bookmarklet don't match logged in user. Need to handle this better
					wp_redirect(get_bloginfo('url'));
					exit;
				}
			 }
			if (isset($_POST['tunestring'])) { $_SESSION['repostdata'] = $_POST['tunestring']; }
			if(!is_user_logged_in()) {
				session_start();
		 		$_SESSION['loginmessage'] = '<p class="deposit-reposts">To deposit your reposts, please link NSSC with your $oundCloud account</p><p class="nothanks"><a href="/?murkreposts">No Thanks</a></p>';
				wp_redirect(get_bloginfo('url'));
				exit;
			}	
			elseif (!$_SESSION['repostdata']) {	
				wp_redirect('/reposts/');
				exit;
			} elseif ($nsscid == get_current_user_id()) {
				wp_redirect(get_bloginfo('url').'/repost-process/');
				exit;
			}
			else {
				wp_redirect(get_bloginfo('url'));
				exit;	
			}
		break;
		
		case('repostdisplay') :
			if (!is_user_logged_in()) {
				wp_redirect(get_bloginfo('url'));
				exit;	
			}
			session_start();
			unset($_SESSION['loginmessage']);
			wp_redirect(get_bloginfo('url').'/reposts/');
			exit;
		break;
		
		case('repostmurk') :
			session_start();
			unset($_SESSION['loginmessage']);
			unset($_SESSION['repostdata']);
			wp_redirect(get_bloginfo('url'));
			exit;
		break;
		
		case('bth') :
			session_start();
			unset($_SESSION['loginmessage']);
			unset($_SESSION['repostdata']);
			wp_redirect(get_bloginfo('url'));
			exit;
		break;
		
		case ('dbauth') :
			wp_redirect(SSL_DOMAIN.'/nssc/dropboxauth/?authorize&nsscid='.urlencode(sc_encryptf(get_current_user_id())));
			exit;
		break;
		
		case ('dbsuccess') :
			session_start();
		 	$_SESSION['toast'] = '<p class="toast-msg toast-success">Successfully linked to DropBox</p>';
			wp_redirect(get_bloginfo('url'));
			exit;
		break;
		
		case ('dbfail') :
			session_start();
			if ($_GET['dropboxfail'] == 'nocode') {
				$msg = 'Dropbox link failed. No code found.';
			} elseif ($_GET['dropboxfail'] == 'statemismatch') {
				$msg = 'Dropbox link failed. State Mismatch';
			}
			if ($msg) {
			 	$_SESSION['toast'] = '<p class="toast-msg toast-fail">'.$msg.'</p>';
			}
			wp_redirect(get_bloginfo('url'));
			exit;
		break;
		
	endswitch;	
}