<?php  /* Favourites Display - Customer Player Version */ ?>
	

<?php $page_size = (isset($_GET['limit'])) ? $_GET['limit'] : get_user_meta(get_current_user_id(),'nssclimit',true);
			
	if (!$page_size) $page_size = 20;

	$sct = SC_TOKEN;  
	
	if ($sct) { 
		$sc = sc_getuser();
	} 
	
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$offset = ($paged !== 1) ? (($paged * $page_size) - $page_size) : 0;		

// Check my length (it's not that big)		
		$lf[0] = get_user_meta(get_current_user_id(),'nssclength',true);
		$lf[0] = (!$lf[0] || $lf[0] == 0) ? false : $lf[0];
		$lf[1] = (!$lf) ? false : get_user_meta(get_current_user_id(),'nssclengthsplit',true);
		
		switch ($lf[0]) :
			case '1' :
				$to = $lf[1] * 1000;
				$from = 0;
			break;
			
			case '2' :
				$to = 0;
				$from = $lf[1] * 1000;
			break;
			case false :
				$to = false;
				$from = false;
			break;
        endswitch; 

$me = json_decode($sc->get('me'));		

$scresp = $sc->get('users/'.$me->id.'/favorites', array(
	'order' => 'created_at',
	'limit' => $page_size,
	'offset' => $offset
));
// Experimental API Apparently
/* $scresp = $sc->get('e1/users/'.$me->id.'/sounds', array(
	'order' => 'created_at',
	'limit' => $page_size,
	'offset' => $offset)
); */

if (is_array($scresp) && $scresp['error'] == 1) {
	$tracks = false;
	scErrorHandle($scresp);
} else {
	$tracks = json_decode($scresp);
}
?> 


        <?php if ($tracks) : ?>
        <?php include(locate_template('display-tracks.php')); ?>
        	
         <?php //LENGth filter
			
			$lf[0] = get_user_meta(get_current_user_id(),'nssclength',true);
			$lf[0] = (!$lf[0] || $lf[0] == 0) ? false : $lf[0];
			$lf[1] = (!$lf) ? false : get_user_meta(get_current_user_id(),'nssclengthsplit',true); ?>
          <?php if ($lf[0]) : ?>
            	<div class="notice notice-info">
                	<?php if ($lf[0] == 1) :?>
                    <p><strong>Active Filter:</strong> You're currently showing only <strong>Tunes</strong>. <br /><small>Because of the way SoundCloud's API works you are seeing the tunes from this set of <?php echo $page_size; ?> results from your stream.</small></p>
                    <?php elseif  ($lf[0] == 2) : ?>
                    <p><strong>Active Filter:</strong> You're currently showing only <strong>Mixes</strong>. <br /><small>Because of the way SoundCloud's API works you are seeing the tunes from this set of <?php echo $page_size; ?> results from your stream.</small></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <div id="vm">
		<?php $i = 0; $d = 0;
        	foreach ($tracks as $track) :
				$trackmetadata = false;
				
				// Playlist check
				$trackmetadata['type'] = $track->kind;
				
				if ($trackmetadata['type'] == 'playlist') {
					$pl = $sc->get('playlists/'.$track->origin->id, array());
	
					if (!is_array($pl)) {
						$pl = json_decode($pl);
						$trackmetadata['tracks'] = $pl->tracks;
					} else {
						$trackmetadata['tracks'] = array();
					}
				}
				
				// Length Filter check
				if ($lf[0] && $trackmetadata['type'] == 'track') {
					$leng = intval($track->duration)/1000;
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
					$trackmetadata['title'] = $track->title;
					$trackmetadata['trackdate'] = strtotime($track->created_at);
					$trackmetadata['streamable'] = $track->streamable;
					$trackmetadata['id'] = $track->id;
					$trackmetadata['permalink_url'] = $track->permalink_url;
					$trackmetadata['artwork_url'] = $track->artwork_url;
					$trackmetadata['avatar_url'] = $track->user->avatar_url;
					$trackmetadata['artworkuse'] = ($trackmetadata['artwork_url']) ? $trackmetadata['artwork_url'] : false;
					$trackmetadata['artworkuse'] = (!$trackmetadata['artworkuse'] && $trackmetadata['avatar_url']) ? $trackmetadata['avatar_url'] : $trackmetadata['artworkuse'];
					$trackmetadata['artworkuse'] = ($trackmetadata['artworkuse']) ? $trackmetadata['artworkuse'] : get_bloginfo('stylesheet_directory').'/images/null-art-large.jpg';
					$trackmetadata['artist_permalink_url'] = $track->user->permalink_url;
					$trackmetadata['artist_id'] = $track->user->id;
					$trackmetadata['artist_username'] = $track->user->username;
					$trackmetadata['waveform_url'] = $track->waveform_url;
					$trackmetadata['user_favorite'] = $track->user_favorite;
					$trackmetadata['purchase_url'] = $track->purchase_url;
					$trackmetadata['purchase_title'] = $track->purchase_title;
					$trackmetadata['downloadable'] = $track->downloadable;
					$trackmetadata['commentable'] = $track->commentable;
					$trackmetadata['playback_count'] = $track->playback_count;
					$trackmetadata['comment_count'] = $track->comment_count;
					$trackmetadata['favoritings_count'] = $track->favoritings_count;
					$trackmetadata['duration'] = $track->duration;
					$trackmetadata['description'] = $track->description;
					$trackmetadata['original_content_size'] = $track->original_content_size;

					include(locate_template('display-tracks.php')); 
				} ?>			

         <?php endforeach; ?> 
        <?php if ($d == 0) : // We want the D hehehehe ?>
        	<h3>No tracks in this batch have matched your filter. Please try the next page.</h3>
        <?php else : ?>
			<?php if ($lf[0]) : ?>
            	<div class="notice notice-info">
                    <p>Showing <?php echo $d; ?> of <?php echo $page_size; ?> results from your stream.</p>
                </div>
            <?php endif; ?>
		<?php endif; ?>
              
         <div id="sc-nav">
            <?php if (function_exists('wp_pagenavi')) {
				$tp = ceil($me->public_favorites_count/$page_size);
				sc_pagenavi($page_size,$tp,$paged);
			} else { ?>
			<?php if ($page != 1) : ?>
                <a href="<?php the_permalink(); ?><?php if ($page-1 != 1) : ?>page/<?php echo $page-1; ?>/<?php endif;?>" class="sc-prevpage awesome grey">&laquo; Previous Page</a>
             <?php endif; ?>   
         
                <a href="<?php the_permalink(); ?>page/<?php echo $page+1; ?>/" class="sc-nextpage awesome grey">Next Page &raquo;</a>
           <?php } ?>
        </div>
        
        
        <?php if (user_can(get_current_user_id(),'administrator')) : ?>
        <div class="metadisplay">
        	<?php SCDebugButton('API Response',$tracks); ?>
            <?php $me = json_decode($sc->get('me')); ?>
            <?php SCDebugButton('User Data',$me ); ?>
        </div>
        <?php endif; ?>
        <script type="text/javascript"> var vm = new Vue({ el: '#vm' }) </script>
		</div>
		<?php endif;  //if $tracks?>