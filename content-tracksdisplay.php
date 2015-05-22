<?php  /* Stream Display */ ?>
		
        
        <?php
			
			$page_size = (isset($_GET['limit'])) ? $_GET['limit'] : get_user_meta(get_current_user_id(),'nssclimit',true);
			
			if (!$page_size) $page_size = 20;
		
			$sct = SC_TOKEN;  
			
			if ($sct) { 
				$sc = sc_getuser();
				$scv2 = sc_getuser_v2();
			} 
		 	
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			
			if ($paged == 1) {
				unset($_SESSION['homepage']);
			} else {
				$next_href = (($paged != 1)&& $_SESSION['homepage'][$paged-1]['next_href']) ? $_SESSION['homepage'][$paged-1]['next_href'] : false;
			}
$me = json_decode($sc->get('me'));	
if ($next_href) {
	$next_href = parse_url($next_href); parse_str($next_href['query'], $next_href_q);
	$scresp = $scv2->get('stream', $next_href_q);
	if (is_array($scresp) && $scresp['error'] == 1) {
		$tracks = false;
		scErrorHandle($scresp);
	} else {
		$tracks = json_decode($scresp);
	}
}
else {
	$scresp = $scv2->get('stream', array(
		'limit' => $page_size
	));
	if (is_array($scresp) && $scresp['error'] == 1) {
		$tracks = false;
		scErrorHandle($scresp);
	} else {
		$tracks = json_decode($scresp);
	}
} ?>
       
			<?php include(locate_template('display-tracks.php')); ?>
            <?php if ($tracks) : ?>
            <?php $i = 0;
			
			//LENGth filter
			
			$lf[0] = get_user_meta(get_current_user_id(),'nssclength',true);
			$lf[0] = (!$lf[0] || $lf[0] == 0) ? false : $lf[0];
			$lf[1] = (!$lf) ? false : get_user_meta(get_current_user_id(),'nssclengthsplit',true);
			
			$d = 0; ?>
            <?php if ($lf[0]) : ?>
            	<div class="notice notice-info">
                	<?php if ($lf[0] == 1) :?>
                    <p><strong>Active Filter:</strong> You're currently showing only <strong>Tunes</strong>. <br /><small>Because of the way SoundCloud's API works you are seeing the tunes from this set of <?php echo $page_size; ?> results from your stream.</small></p>
                    <?php elseif  ($lf[0] == 2) : ?>
                    <p><strong>Active Filter:</strong> You're currently showing only <strong>Mixes</strong> <br /><small>Because of the way SoundCloud's API works you are seeing the tunes from this set of <?php echo $page_size; ?> results from your stream.</small></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div id="vm">
			
        	<?php foreach ($tracks->collection as $track) :
        	$trackmetadata = false;
			if ($track->type != 'track' && $track->type != 'playlist') continue;
			// Playlist check
			$trackmetadata['type'] = $track->type;
			
			if ($trackmetadata['type'] == 'playlist') {
					$pl = $sc->get('playlists/'.$track->playlist->id, array());
	
					if (!is_array($pl)) {
						$pl = json_decode($pl);
						$trackmetadata['tracks'] = $pl->tracks;
					} else {
						$trackmetadata['tracks'] = array();
					}
				}
			
			// Length Filter check
			if ($lf[0] && $trackmetadata['type'] == 'track') {
        		$leng = intval($track->track->duration)/1000;
        		switch ($lf[0]) :
        			case '1' :
        			$goflight = ($lf[1] >= $leng) ? true : false;
        			break;
        			
        			case '2' :
        			$goflight = ($lf[1] <= $leng) ? true : false;
        			break;
        			endswitch;
        	} elseif($lf[0] && $trackmetadata['type'] == 'playlist') {
				foreach ($trackmetadata['tracks'] as $tr) {
					$leng = intval($tr->duration)/1000;
					switch ($lf[0]) :
						case '1' :
						$goflight = ($lf[1] >= $leng) ? true : false;
						break;
						
						case '2' :
						$goflight = ($lf[1] <= $leng) ? true : false;
						break;
					endswitch;	
					
					if ($goflight) break;
					
					}
			}else {
        		$goflight = true;
        	}
        	
			
			
			if ($goflight) {
				$d++;
				$trackmetadata['title'] = $track->$trackmetadata['type']->title;
				$trackmetadata['trackdate'] = strtotime($track->created_at);
				$trackmetadata['streamable'] = $track->$trackmetadata['type']->streamable;
				$trackmetadata['id'] = $track->$trackmetadata['type']->id;
				$trackmetadata['permalink_url'] = $track->$trackmetadata['type']->permalink_url;
				$trackmetadata['artwork_url'] = $track->$trackmetadata['type']->artwork_url;
				$trackmetadata['avatar_url'] = $track->$trackmetadata['type']->user->avatar_url;
				$trackmetadata['artworkuse'] = ($trackmetadata['artwork_url']) ? $trackmetadata['artwork_url'] : false;
				$trackmetadata['artworkuse'] = (!$trackmetadata['artworkuse'] && $trackmetadata['avatar_url']) ? $trackmetadata['avatar_url'] : $trackmetadata['artworkuse'];
				$trackmetadata['artworkuse'] = ($trackmetadata['artworkuse']) ? $trackmetadata['artworkuse'] : get_bloginfo('stylesheet_directory').'/images/null-art-large.jpg';
				$trackmetadata['artist_permalink_url'] = $track->$trackmetadata['type']->user->permalink_url;
				$trackmetadata['artist_id'] = $track->$trackmetadata['type']->user->id;
				$trackmetadata['artist_username'] = $track->$trackmetadata['type']->user->username;
				$trackmetadata['waveform_url'] = str_replace('wis.sndcdn.com','w1.sndcdn.com', $track->$trackmetadata['type']->waveform_url);
				$trackmetadata['user_favorite'] = $track->$trackmetadata['type']->user_favorite;
				$trackmetadata['purchase_url'] = $track->$trackmetadata['type']->purchase_url;
				$trackmetadata['purchase_title'] = $track->$trackmetadata['type']->purchase_title;
				$trackmetadata['downloadable'] = $track->$trackmetadata['type']->downloadable;
				$trackmetadata['commentable'] = $track->$trackmetadata['type']->commentable;
				$trackmetadata['playback_count'] = $track->$trackmetadata['type']->playback_count;
				$trackmetadata['download_count'] = $track->$trackmetadata['type']->download_count;
				$trackmetadata['comment_count'] = $track->$trackmetadata['type']->comment_count;
				$trackmetadata['favoritings_count'] = $track->$trackmetadata['type']->favoritings_count;
				$trackmetadata['duration'] = $track->$trackmetadata['type']->duration;
				$trackmetadata['description'] = $track->$trackmetadata['type']->description;
				$trackmetadata['original_content_size'] = $track->$trackmetadata['type']->original_content_size;
				
				include(locate_template('display-tracks.php')); 
			}?>
			
         <?php endforeach; ?>  
        <?php if ($d == 0) : // We want the D hehehehe ?>
        	<h3>No tracks in this batch have matched your filter. Please try the next page.</h3>
        <?php else : ?>
			<?php if ($lf[0]) : ?>
            	<div class="notice notice-info">
                    <p>Showing <?php echo $d; ?> of <?php echo $page_size; ?> <?php echo ($lf[0] == 2) ? 'mixes' : 'tunes'; ?> from your stream.</p>
                </div>
            <?php endif; ?>
		<?php endif; ?>
        <?php $page = (!isset($_GET['page'])) ? 1 : intval($_GET['page']); 
			  $_SESSION['homepage'][$paged]['next_href'] = $tracks->next_href;
			  $_SESSION['homepage'][$paged]['future_href'] = $tracks->future_href; ?>
        
         <div id="sc-nav">
            <?php if ($paged != 1) : ?>
                <a href="<?php the_permalink(); ?><?php if ($paged-1 != 1) : ?>page/<?php echo $paged-1; endif;?>/" class="sc-prevpage awesome grey">&laquo; Previous Page</a>
             <?php endif; ?>   
         
                <a href="<?php the_permalink(); ?>page/<?php echo $paged+1; ?>/" class="sc-nextpage awesome grey">Next Page &raquo;</a>
        </div>
        
        
        <?php if (user_can(get_current_user_id(),'administrator')) : ?>
        <div class="metadisplay">
        	<?php SCDebugButton('API Response',$tracks); ?>
            <?php $me = $sc->get('me');
					if (is_array($me) && $me['error'] == 1) {
						$me = false;
						scErrorHandle($me);
					} else {
						$me = json_decode($me);
						
					}
					?>
            <?php SCDebugButton('User Data',$me ); ?>
            <?php $pag['Stored Future HREF'] = $future_href;
				  $pag['Stored Next HREF Query'] = $next_href_q;
				  $pag['API Returned Future HREF'] = $tracks->future_href ;
				  $pag['API returned Next HREF'] = $tracks->next_href; ?>
            <?php SCDebugButton('Pagination',$pag ); ?>  
        </div>
        <?php endif; ?>
        
        <script type="text/javascript"> var vm = new Vue({ el: '#vm' }) </script>
        </div>
		<?php endif;  //if $tracks?>