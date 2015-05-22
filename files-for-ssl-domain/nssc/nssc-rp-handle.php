<?php include LOCATION_OF_WP_INSTALL_ON_SERVER.'/wp-blog-header.php';
header("HTTP/1.1 200 OK");
header('Content-Type: application/json; charset=utf8');

$uid = intval(sc_decryptf($_GET['nsscid']));

	$rp = new WP_Query(array(
			'post_type' => 'repost',
			'author' => $uid,
			'posts_per_page' => 1,
			'orderby' => 'date'
		));
		
		if ($rp->have_posts()) :
		
		while ( $rp->have_posts() ) : $rp->the_post();
			$return['lastimport'] = get_the_date('U');
			$return['lastreposttime'] = get_post_meta($rp->post->ID,'reposttime',true);
		endwhile; 
		
		else :
		
			$return = array('error'=>'NFR','errormsg'=>'No Previous Import Found');
		
		endif;	
			if (user_can($uid,'administrator')) {
				$return['admin'] = 'admin';	
			}
echo 'jsonCallback( 
'.json_encode($return).'
);';
exit;