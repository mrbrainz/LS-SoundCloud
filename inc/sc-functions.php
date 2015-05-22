<?php 

define ('SC_CLIENT_ID', 'SOUNDCLOUD CLIENT ID HERE');
define ('SC_CLIENT_SECRET', 'SOUNDCLOUD SCRET KEY HERE');
define ('SSL_DOMAIN', 'https://domain-hosting-ssl-files.com');

require_once 'repost-object.php';
require_once 'Services/Soundcloud.php';
require_once 'redirect-handler.php';

/* If this is to go public, this needs to have admin options to define these */



define('SCEX_KEY', (!AUTH_KEY) ? 'SuP£r$ecr£TP455W0rD' : AUTH_KEY);

function sc_decryptf($gburns) {
	return openssl_decrypt($gburns, "bf-ecb", SCEX_KEY);
}

function sc_encryptf($gburns) {	
	return openssl_encrypt($gburns, "bf-ecb", SCEX_KEY);
}

if (is_user_logged_in()) {
	$id = get_current_user_id();
	$sct = get_user_meta($id,'sc_token',true);
	if ($sct) {
		define('SC_TOKEN',$sct);	
	}
	else { define('SC_TOKEN',false); }
}

add_action('wp_enqueue_scripts','lessshit_lssc_enqueue_script');
function lessshit_lssc_enqueue_script() {
	
	//VueJS & Plangular
	wp_enqueue_script('vuejs','http://cdnjs.cloudflare.com/ajax/libs/vue/0.10.6/vue.min.js'); 
	wp_enqueue_script('v-plangular',get_template_directory_uri().'/js/v-plangular.js'); 

	// Featherlight Lightbox
	wp_enqueue_script('featherlight',get_template_directory_uri().'/js/featherlight.min.js',array( 'jquery' )); 
	
	// noUi Slider for time range inputs
	wp_enqueue_script('nouislider',get_template_directory_uri().'/js/jquery.nouislider.min.js',array( 'jquery' )); 
	
	// Liblink for noUi Slider
	wp_enqueue_script('liblink',get_template_directory_uri().'/js/jquery.liblink.js',array( 'jquery' )); 
	
	// Feedback tab
	wp_enqueue_script('slidetab',get_template_directory_uri().'/js/jquery.tabSlideOut.v1.3.min.js',array( 'jquery' )); 
	
	wp_enqueue_script('nssc',get_template_directory_uri().'/js/nssc.js?v=5',array( 'jquery' )); 
	
	// wp_enqueue_style('font_awesome', get_template_directory_uri().'/css/font/font-awesome.min.css');
	wp_enqueue_style('font_awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');
	
	
}

function sc_auth() {
	return new Services_Soundcloud(SC_CLIENT_ID, SC_CLIENT_SECRET, get_bloginfo('url').'/?nssc-auth');	
}

function sc_getuser($token = false) {
	$token =  (!$token) ? SC_TOKEN : $token;
	if ($token) {
		$client = new Services_Soundcloud(SC_CLIENT_ID, SC_CLIENT_SECRET);
		$client->setAccessToken($token);
		return $client;	
	} else {
		return false;	
	}
}

function sc_getuser_v2($token = false) {
	$token =  (!$token) ? SC_TOKEN : $token;
	if ($token) {
		$client = new Services_Soundcloud_v2(SC_CLIENT_ID, SC_CLIENT_SECRET);
		$client->setAccessToken($token);
		return $client;	
	} else {
		return false;	
	}
}

function scErrorHandle($errordata) {
	echo '<p class="sc-api-error">I\'m sorry there was a problem. Please try again.';
			
			if (user_can(get_current_user_id(),'administrator')) {
				$scresp['lastHttpResponseBody'] = json_decode($scresp['lastHttpResponseBody']); ?>
				<div class="meta"><p><a href="javascript:void(0)" class="awesome blue"><i class="fa fa-plus"></i> API Response</a></p><pre style="display:none;"><?php print_r($errordata); ?></pre></div>
				<?php
			}
}


function sc_new_window($text) {
  return str_replace('<a', '<a target="_blank"', $text);
}


function parseSCUNs($text) {
	$regex = '/<a\b(?=\s)(?:[^>=]|=\'[^\']*\'|="[^"]*"|=[^\'"\s]*)*"\s?>.*?<\/a>|@([A-Za-z0-9\-\_\.]+)/';
	$output = preg_replace_callback(
        $regex,
        function ($matches) {
            if (array_key_exists (1, $matches)) {
				if (strpos($matches[1],'.') === false) {
					return '<a href="https://soundcloud.com/' . $matches[1] . '">@' . $matches[1] . '</a>';
				} else {
					return $matches[1];
				}
            }
            return $matches[0];
        },
        $text
    );
	return $output;
}

function scDescription($text) {
	$output = apply_filters('the_content',sc_new_window(parseSCUNs(make_clickable(strip_tags($text)))));
	return $output;
}

function scHumanTiming($time)
{

    $time = time() - $time; // to get the time since that moment

    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        // 604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'min',
        1 => 'sec'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}

function createSCBookmarklet() {
	$cu = get_current_user_id();
	$uid = sc_encryptf($cu);
	$bmurl = "javascript:(function(){var rnd = Math.floor(Math.random()*9999999999);scrape=document.createElement('SCRIPT');scrape.type='text/javascript';scrape.id='nssc-script';scrape.src='".SSL_DOMAIN."/nssc/nssc-rp.js?'+rnd;var nsscs=document.getElementById('nssc-script');if (nsscs == null){document.getElementsByTagName('head')[0].appendChild(scrape);window.nsscid='".$uid."';}})();";
	return '<p class="nssc-bookmurk-p"><a href="'.htmlentities($bmurl).'" class="nssc-bookmurk awesome nssc">NSSC Importer -> '.get_user_meta($cu,'nickname',true).'</a></p>';
}

add_shortcode( 'bookmurk', 'createSCBookmarklet' );


function SCdebugButton($label = false,$dbdata,$return = false) {
	if(user_can(get_current_user_id(),'administrator') && $dbdata) {
		$output = '
	<div class="meta">
		<p><a href="javascript:void(0)" class="awesome blue"><i class="fa fa-plus"></i> '.$label.'</a></p>
		<pre style="display:none;">'.print_r($dbdata,true).'</pre>
	</div>';
		if ($return) {
			return $output;
		} else {
			echo $output;	
		}  	
	}
	else {
		return false;	
	}
}

function nssc_count_user_posts_by_type( $userid, $post_type = 'repost' ) {
	global $wpdb;

	$where = get_posts_by_author_sql( $post_type, true, $userid );

	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

  	return apply_filters( 'get_usernumposts', $count, $userid );
}


function sc_pagenavi($posts_per_page,$total_pages,$paged,$args = array()) {
	if ( !is_array( $args ) ) {
		$argv = func_get_args();

		$args = array();
		foreach ( array( 'before', 'after', 'options' ) as $i => $key )
			$args[ $key ] = isset( $argv[ $i ]) ? $argv[ $i ] : "";
	}

	$args = wp_parse_args( $args, array(
		'before' => '',
		'after' => '',
		'options' => array(),
		'query' => $GLOBALS['wp_query'],
		'type' => 'posts',
		'echo' => true
	) );

	extract( $args, EXTR_SKIP );
	
	$options = wp_parse_args( $options, PageNavi_Core::$options->get() );
	
	$instance = new PageNavi_Call( $args );

	if ( 1 == $total_pages && !$options['always_show'] )
		return;

	$pages_to_show = absint( $options['num_pages'] );
	$larger_page_to_show = absint( $options['num_larger_page_numbers'] );
	$larger_page_multiple = absint( $options['larger_page_numbers_multiple'] );
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start = floor( $pages_to_show_minus_1/2 );
	$half_page_end = ceil( $pages_to_show_minus_1/2 );
	$start_page = $paged - $half_page_start;

	if ( $start_page <= 0 )
		$start_page = 1;
	$end_page = $paged + $half_page_end;

	if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 )
		$end_page = $start_page + $pages_to_show_minus_1;

	if ( $end_page > $total_pages ) {
		$start_page = $total_pages - $pages_to_show_minus_1;
		$end_page = $total_pages;
	}

	if ( $start_page < 1 )
		$start_page = 1;

	$out = '';
	
	// Text
			if ( !empty( $options['pages_text'] ) ) {
				$pages_text = str_replace(
					array( "%CURRENT_PAGE%", "%TOTAL_PAGES%" ),
					array( number_format_i18n( $paged ), number_format_i18n( $total_pages ) ),
				$options['pages_text'] );
				$out .= "<span class='pages'>$pages_text</span>";
			}

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				// First
				$first_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $total_pages ), $options['first_text'] );
				$out .= $instance->get_single( 1, $first_text, array(
					'class' => 'first'
				), '%TOTAL_PAGES%' );
			}

			// Previous
			if ( $paged > 1 && !empty( $options['prev_text'] ) ) {
				$out .= $instance->get_single( $paged - 1, $options['prev_text'], array(
					'class' => 'previouspostslink',
					'rel'	=> 'prev'
				) );
			}

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				if ( !empty( $options['dotleft_text'] ) )
					$out .= "<span class='extend'>{$options['dotleft_text']}</span>";
			}

			// Smaller pages
			$larger_pages_array = array();
			if ( $larger_page_multiple )
				for ( $i = $larger_page_multiple; $i <= $total_pages; $i+= $larger_page_multiple )
					$larger_pages_array[] = $i;

			$larger_page_start = 0;
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page < ($start_page - $half_page_start) && $larger_page_start < $larger_page_to_show ) {
					$out .= $instance->get_single( $larger_page, $options['page_text'], array(
						'class' => 'smaller page',
					) );
					$larger_page_start++;
				}
			}

			if ( $larger_page_start )
				$out .= "<span class='extend'>{$options['dotleft_text']}</span>";

			// Page numbers
			$timeline = 'smaller';
			
			foreach ( range( $start_page, $end_page ) as $i ) {
				if ( $i == $paged && !empty( $options['current_text'] ) ) {
					$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['current_text'] );
					$out .= "<span class='current'>$current_page_text</span>";
					$timeline = 'larger';
				} else {
					$out .= $instance->get_single( $i, $options['page_text'], array(
						'class' => "page $timeline",
					) );
				}
			}
			
			
			
			// Large pages
			$larger_page_end = 0;
			$larger_page_out = '';
			if (is_array($larger_pages_array)) {
				foreach ( $larger_pages_array as $larger_page ) {
					if ( $larger_page > ($end_page + $half_page_end) && $larger_page_end < $larger_page_to_show ) {
						$larger_page_out .= $instance->get_single( $larger_page, $options['page_text'], array(
							'class' => 'larger page',
						) );
						$larger_page_end++;
					}
				}
			}

			if ( $larger_page_out ) {
				$out .= "<span class='extend'>{$options['dotright_text']}</span>";
			}
			$out .= $larger_page_out;

			if ( $end_page < $total_pages ) {
				if ( !empty( $options['dotright_text'] ) )
					$out .= "<span class='extend'>{$options['dotright_text']}</span>";
			}

			// Next
			if ( $paged < $total_pages && !empty( $options['next_text'] ) ) {
				$out .= $instance->get_single( $paged + 1, $options['next_text'], array(
					'class' => 'nextpostslink',
					'rel'	=> 'next'
				) );
			}

			if ( $end_page < $total_pages ) {
				// Last
				$out .= $instance->get_single( $total_pages, $options['last_text'], array(
					'class' => 'last',
				), '%TOTAL_PAGES%' );
			}	
		$out = $before . "<div class='wp-pagenavi'>\n$out\n</div>" . $after;

	$out = apply_filters( 'wp_pagenavi', $out );

	if ( !$echo )
		return $out;

	echo $out;
}





function progressCallback($dltotal, $dlnow, $ultotal, $ulnow) {
		error_log('LOG'.PHP_EOL,3,'log.log');
		echo '<p>'.$ulnow.'</p>';
    }
	
/* function hykwyd() {	
	if (user_can(get_current_user_id(),'administrator')) {
	
		$trackquery = new WP_Query( 
		array(  'post_type' => 'repost',
				'posts_per_page' => -1
		));
		
		while ($trackquery->have_posts()) : $trackquery->the_post();
			$resolveurl = 'https://api.soundcloud.com/tracks/'.get_the_title().'.json?client_id='.SC_CLIENT_ID;
			$track = json_decode(file_get_contents($resolveurl));
			$u = update_post_meta($trackquery->post->ID,'length',$track->duration);
			if ($u) {
				echo 'Updated: post '.$trackquery->post->ID.' track '.get_the_title().' duration '.$track->duration;
			}else {
				echo 'Shit be Fucked with: post '.$trackquery->post->ID.' track '.get_the_title();
			}
		endwhile;
		
	}
}

add_action('wp_footer','hykwyd'); */

function nsscFeedbackTab() { ?>
<?php if (function_exists('gravity_form')) : ?>
<div class="slide-out-div">
    <a class="handle" href="javascript:void(0)">Feedback</a>
    <div class="feedbackform">
        gravity_form(1, false, false, false, '', true); ?>
    </div>
</div>
<?php endif; ?>
<?php }

if (is_user_logged_in()) {
	add_action('wp_footer','nsscFeedbackTab');
	gravity_form_enqueue_scripts(1, true);
}