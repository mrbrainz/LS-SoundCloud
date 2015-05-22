<?php  /* Repost Display - Custom Player Version */ ?>

        <?php 
		
		$page_size = (isset($_GET['limit'])) ? intval($_GET['limit']) : get_user_meta(get_current_user_id(),'nssclimit',true);
			
			if (!$page_size) $page_size = 20;
		
		the_post(); $rp_content = get_the_content(); 
		
	 // Check my length (it's not that big)		
		$lf[0] = get_user_meta(get_current_user_id(),'nssclength',true);
		$lf[0] = (!$lf[0] || $lf[0] == 0) ? false : $lf[0];
		$lf[1] = (!$lf) ? false : intval(get_user_meta(get_current_user_id(),'nssclengthsplit',true))*1000;
		
		 // Check for Soundcloud User API Token
		
		$sct = SC_TOKEN;  if ($sct) : 
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		 if ($lf[0] == 1) {
			$rp = new WP_Query(array(
				'post_type' => 'repost',
				'author' => get_current_user_id(),
				'posts_per_page' => $page_size,
				'orderby' => 'date',
				'paged' => $paged,
				'meta_query' => array(
					array(
						'key'     => 'length',
						'value'   => $lf[1],
						'compare' => '<=',
						'type'    => 'numeric'
					),
				)				
			));
		} elseif ($lf[0] == 2) {
			$rp = new WP_Query(array(
				'post_type' => 'repost',
				'author' => get_current_user_id(),
				'posts_per_page' => $page_size,
				'orderby' => 'date',
				'paged' => $paged,
				'meta_query' => array(
					array(
						'key'     => 'length',
						'value'   => $lf[1],
						'compare' => '>=',
						'type'    => 'numeric'
					),
				)
			));
		} else {
			$rp = new WP_Query(array(
				'post_type' => 'repost',
				'author' => get_current_user_id(),
				'posts_per_page' => $page_size,
				'orderby' => 'date',
				'paged'=>$paged
			)); 
		}
 		 
		$sids = ''; 
		
		$rpc = nssc_count_user_posts_by_type( get_current_user_id());
		if ($rpc) :
		?>
		
        
        <div class="reposthelp">
			<p><a href="javascript:void(0)" class="awesome blue helper"><i class="fa fa-plus"></i> Reposts Help</a></p>
			<div class="intro-copy" style="height: 0px;">
                <div class="copy-sleeve">
                    <?php echo apply_filters('the_content',$rp_content); ?>
                </div>
            </div>
        </div>
		
        <?php if($rp->have_posts()) : ?>
        <?php include(locate_template('display-tracks.php')); ?>	
        <div id="vm">
		
		
		<?php
		while ( $rp->have_posts() ) : $rp->the_post();
			$date = get_the_date('d-m-Y');
			$sid = get_the_title(); 
			$custom = get_post_custom();
			$data[$sid]['id'] = get_the_title();
			$data[$sid]['url'] = $custom['url'][0];
			$data[$sid]['reposter'] = $custom['reposter'][0];
			$data[$sid]['reposterurl'] = $custom['reposterurl'][0];
			$data[$sid]['reposttime'] = $custom['reposttime'][0];
			$data[$sid]['type'] = $custom['type'][0];
			$data[$sid]['length'] = $custom['length'][0];
			$sids[$custom['type'][0]][] = $sid;
		endwhile; ?>
        <div id="repost-head">
            <p><span class="dateimported"><strong>Last import:</strong> <?php echo $date; ?></span> | 
            <span class="rpcount"><strong>Currently storing:</strong> <?php echo nssc_count_user_posts_by_type( get_current_user_id() ); ?> Reposts for <?php echo get_user_meta(get_current_user_id(),'nickname',true); ?></span></p>
		</div>
        
         <?php if ($lf[0]) : ?>
            	<div class="notice notice-info">
                	<?php if ($lf[0] == 1) :?>
                    <p><strong>Active Filter:</strong> You're currently showing only <strong>Tunes</strong>.</p>
                    <?php elseif  ($lf[0] == 2) : ?>
                    <p><strong>Active Filter:</strong> You're currently showing only <strong>Mixes</strong>.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
		
			<?php // Intialise API Call
		$sc = sc_getuser();	
		
		$sidst = ($sids['t']) ? implode(',',$sids['t']) : false;
		
		$haserrors = false;
		
		// Get Tracks by ID
		if ($sidst) {
			$tracks = $sc->get('tracks', array(
				'ids' => $sidst,
				/*'duration-from' => $from,
				'duration-to' => $to,
				'q' => '*',
				'filter' => 'all',
				'order' => 'default'*/
			));
			if (!is_array($tracks)) {
				$tracks = json_decode($tracks);
			} else {
				$tracks = array();
				$haserrors = true;
			}
		} else {
			$tracks = array();
			$haserrors = true;
		}
		
		// Get Playlists by ID
		if (is_array($sids['p'])) {
			foreach($sids['p'] as $pid) {
				$playlist = $sc->get('playlists/'.$pid, array(
				));
				if (!is_array($playlist)) {
					$playlists[] = json_decode($playlist);
				} 
				else {
					$playlists = array();
					$haserrors = true;
				}
			}
		}
		else {
			$playlists = array();
			$haserrors = true;
		}
		
		$trackdata = (object) array_merge((array) $tracks, (array) $playlists);
		
		wp_reset_query();		
		
		if ($trackdata) :
	
		$trackmetadata = false;
		
		if ($rp->have_posts()) :
		while ( $rp->have_posts() ) : $rp->the_post();
		
			$sid = get_the_title(); 
			
			$trackmetadata = false;
			$tt = false;
			foreach ($trackdata as $track) {		
				
				if ($track->id != $sid) continue;
				$tt = $track;
				break;
			}
			if ($tt) {
				$trackmetadata['id'] = $sid;
				$trackmetadata['permalink_url'] = $data[$sid]['url'];
				$trackmetadata['type'] = ($data[$sid]['type'] == 't') ? 'track' : false;
				$trackmetadata['type'] = ($data[$sid]['type'] == 'p') ? 'playlist' : $trackmetadata['type'];
				$trackmetadata['reposter'] = $data[$sid]['reposter'];
				$trackmetadata['reposterurl'] = $data[$sid]['reposterurl'];
				$trackmetadata['reposttime'] = $data[$sid]['reposttime'];		
				$trackmetadata['title'] = $tt->title;
				$trackmetadata['trackdate'] = strtotime($tt->created_at);
				$trackmetadata['streamable'] = $tt->streamable;
				$trackmetadata['artwork_url'] = $tt->artwork_url;
				$trackmetadata['avatar_url'] = $tt->user->avatar_url;
				$trackmetadata['artworkuse'] = ($trackmetadata['artwork_url']) ? $trackmetadata['artwork_url'] : false;
				$trackmetadata['artworkuse'] = (!$trackmetadata['artworkuse'] && $trackmetadata['avatar_url']) ? $trackmetadata['avatar_url'] : $trackmetadata['artworkuse'];
				$trackmetadata['artworkuse'] = ($trackmetadata['artworkuse']) ? $trackmetadata['artworkuse'] : get_bloginfo('stylesheet_directory').'/images/null-art-large.jpg';
				$trackmetadata['artist_permalink_url'] = $tt->user->permalink_url;
				$trackmetadata['artist_id'] = $tt->user->id;
				$trackmetadata['artist_username'] = $tt->user->username;
				$trackmetadata['waveform_url'] = $tt->waveform_url;
				$trackmetadata['user_favorite'] = $tt->user_favorite;
				$trackmetadata['purchase_url'] = $tt->purchase_url;
				$trackmetadata['purchase_title'] = $tt->purchase_title;
				$trackmetadata['downloadable'] = $tt->downloadable;
				$trackmetadata['commentable'] = $tt->commentable;
				$trackmetadata['playback_count'] = $tt->playback_count;
				$trackmetadata['comment_count'] = $tt->comment_count;
				$trackmetadata['favoritings_count'] = $tt->favoritings_count;
				$trackmetadata['duration'] = $tt->duration;
				$trackmetadata['description'] = $tt->description;
				$trackmetadata['original_content_size'] = $tt->original_content_size;
				
				if ($trackmetadata['type'] == 'playlist') {
					$trackmetadata['tracks'] = $tt->tracks;
				}
				
				include locate_template('/display-tracks.php'); 
			} elseif(!$haserrors) {
				// Track no longer exists, so delete
				wp_delete_post( $rp->post->ID, true );
			}
			
			endwhile; ?>
			
            <div id="sc-nav">
            <?php if (function_exists('wp_pagenavi')) {
					wp_pagenavi( array( 'query' => $rp) );
			} /* PAGINATION CODE HERE DON'T WORK AS IT'S A CUSTOM QUERY AND I CAN'T WORK IT OUT. SO MUST USE WP_PAGENAVI FOR NOW
			
			else { ?>
            		<?php posts_nav_link(); ?>
                    <!-- sc-prevpage awesome grey --><?php previous_posts_link('&laquo; Previous Page'); ?>
             
                    <?php previous_posts_link('Next Page &raquo;'); ?>
            
            <?php } */?>
            </div>
			<script type="text/javascript"> var vm = new Vue({ el: '#vm' }) </script>
		</div>
			<?php endif; ?>
            <?php else : ?>
            	<p>Soundcloud returned no tracks to us.</p>
            <?php endif; ?>
		<?php endif; ?>
		<?php else : // Count is 0;?>
            <div class="intro-copy">
            	<div class="copy-sleeve">
        			<?php echo apply_filters('the_content',$rp_content); ?>
                </div>
            </div>
		<?php endif; endif; ?>