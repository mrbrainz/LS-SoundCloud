<?php /* Display Tracks Template */

global $trackmetadata, $tt, $track, $fs;
if ($trackmetadata) : 
$fing = $sc->get('me/followings/'.$trackmetadata['artist_id']);
$fing = ($fing['lastHttpResponseCode'] == 404) ? false : 1; ?>
<?php if ($trackmetadata['streamable']) : ?>
           
			<?php if ($trackmetadata['type'] == 'track') : ?>
			 <div class="track-container track-<?php echo $trackmetadata['id']; ?> track-plang type-track" v-component="plangular" v-src="'<?php echo $trackmetadata['permalink_url']; ?>'" v-class="player.playing == track ? 'track-playing' : 'track-stopped'">
            
            <?php SCdebugButton('API Response',($tt) ? $tt : $track); ?>
            
            <div class="player clearfix">
            	<div class="gayvatar">
					<img src="<?php echo $trackmetadata['artworkuse']; ?>" alt="<?php echo esc_html($trackmetadata['title']); ?>" title="<?php echo esc_html($trackmetadata['title']); ?>" class="onebatts" data-featherlight="<?php echo str_replace('large.jpg','t500x500.jpg',$trackmetadata['artworkuse']); ?>" width="105" height="105" />
					<ul class="toptrackmeta">
                        <li class="posttime">
                            <i class="fa fa-clock-o"></i> <time class="relativeTime posttiming" title="Posted on <?php echo date('j F Y H:i',$trackmetadata['trackdate']); ?>" datetime="<?php echo date('Y-m-d',$trackmetadata['trackdate'])?>T<?php echo date('H:i:s',$trackmetadata['trackdate']); ?>"><span class="sc-visuallyhidden"><?php echo scHumanTiming($trackmetadata['trackdate']); ?> ago</span><span aria-hidden="true"><?php echo scHumanTiming($trackmetadata['trackdate']); ?></span></time></li>
                        <?php if ($trackmetadata['playback_count']) :?><li class="playbackcount" data-count="<?php echo $trackmetadata['playback_count']; ?>"><i class="fa fa-play"></i> <?php echo $trackmetadata['playback_count']; ?></li><?php endif; ?>
                        <?php if ($trackmetadata['comment_count']) :?><li class="commentcount" data-count="<?php echo $trackmetadata['comment_count']; ?>"><i class="fa fa-comment"></i> <?php echo $trackmetadata['comment_count']; ?></li><?php endif; ?>
                        <?php if ($trackmetadata['favoritings_count']) :?><li class="likecount" data-count="<?php echo $trackmetadata['favoritings_count']; ?>"><i class="fa fa-heart"></i> <?php echo $trackmetadata['favoritings_count']; ?></li><?php endif; ?>
                        <?php if ($trackmetadata['download_count']) :?><li class="downloadcount" data-count="<?php echo $trackmetadata['download_count']; ?>"><i class="fa fa-cloud-download"></i> <?php echo $trackmetadata['download_count']; ?></li><?php endif; ?>
                    </ul>
				</div>
				<div class="playbutton">
					<button class="button-icon" v-on="click: playPause()">
						<svg class="vhs-pop-in" v-if="player.playing != track" v-plangular-icon="'play'"></svg>
						<svg class="vhs-pop-in" v-if="player.playing == track" v-plangular-icon="'pause'"></svg>
					</button>
				</div>
                <div class="trackstuff">
                    <div class="titlebox">
                        <h4 class="artist"><a href="<?php echo $trackmetadata['artist_permalink_url']; ?>" target="_blank"><?php echo esc_html($trackmetadata['artist_username']); ?> <?php if ($fing) : ?><span class="followbutton"><a href="javascript:void(0)" onclick="nsscFollow('<?php echo $trackmetadata['artist_id']; ?>',this);" class="followbutton followed"><i class="fa fa-eye" title="Unfollow"></i></span><?php else : ?><span class="followbutton"><a href="javascript:void(0)" onclick="nsscFollow('<?php echo $trackmetadata['artist_id']; ?>',this);" class="followbutton"><i class="fa fa-eye-slash" title="Follow"></i></span><?php endif; ?></a></h4>
                        <h3 class="tracktitle"><a href="<?php echo $trackmetadata['permalink_url']; ?>" target="_blank"><?php echo esc_html($trackmetadata['title']); ?></a></h3>
                    </div>
                    
                    <?php if (user_can(get_current_user_id(),'administrator')&&1==1) : ?>
                    	<div class="nssc-supermenu">
                    		<div class="nssc-activator">
                    			<i class="fa fa-reorder"></i>
                    		</div>
                    		<ul class="nssc-sm">
                    			<li class="sm-share"><a href="javascript:nsscShare(this)" title="Share <?php echo esc_html($trackmetadata['title']); ?>" class="sm-link"><i class="fa fa-share"></i> Share</a></li>
                    			<li class="sm-buy"><a href="javascript:void(0)" title="Buy Search for <?php echo esc_html($trackmetadata['title']); ?>" onclick="nsscBuy('<?php echo esc_html($trackmetadata['title']); ?>','<?php echo esc_html($trackmetadata['artist']); ?>');" class="sm-link"><i class="fa fa-shopping-cart"></i> Buy Search</a></li>
                    			<li class="sm-switch"><a href="javascript:void(0)" title="Switch Players" onclick="nsscSwitchPlayers(this,)<?php echo $trackmetadata['id']; ?>;" class="sm-link"><i class="fa fa-retweet"></i> Switch Players</a></li>
                    			<?php /* <li class="sm-botherme">Report A Problem</li>
                    			<li class="sm-wild">Unicorn</li> */ ?>
                    		</ul>
                    	</div>
                    <?php endif; ?>
            		
                    <?php $durhours = floor($trackmetadata['duration'] / 1000 / 3600);
						  $durhours = (!$durhours) ? false : $durhours.":";
						  $durmins = floor(($trackmetadata['duration'] / 1000 - ($durhours*3600)) / 60);
						  $durmins = ($durmins < 10 && $durmins != 0) ? '0'.$durmins : $durmins;
						  $durmins = (!$durmins) ? '00:' : $durmins.":";
					      $dursecs = floor($trackmetadata['duration'] / 1000 % 60);
						  $dursecs = ($dursecs < 10) ? '0'.$dursecs : $dursecs ;
						  
						  $duration = $durhours.$durmins.$dursecs; ?>
                    
                   <div class="pghold progresscontain" v-if="player.playing != track" v-on="click: play()">
                        <div class="progressbase"></div>
                        <div class="progressbar" v-style="width: (currentTime / duration * 100) + '%'"></div>
                        <div class="seeker"></div>
                   </div>
                   <div class="progresscontain" v-if="player.playing == track" v-on="click: seek($event)">            		
                        <div class="progressbase"></div>
                        <div class="progressbar" v-style="width: (currentTime / duration * 100) + '%'"></div>
                        <div class="seeker"></div>
                    </div>
                    <div class="waveform" style="background-image:url(<?php echo $trackmetadata['waveform_url']; ?>)"></div>
                    <div class="durationcontain"><span class="starttime" v-on="click:return false" v-class="true == true ? 'time-alive' : 'time-alive'">{{ currentTime | prettyTime }}</span>
                        <span class="duration" v-on="click:return false"><?php echo $duration; ?></span></div>
                </div>
                    
             <?php /******************************
			 				PLAYLISTS
					*****************************/ ?>
                    
                    
            <?php elseif ($trackmetadata['type'] == 'playlist'): ?>
           
            <div class="track-container track-<?php echo $trackmetadata['id']; ?> playlist-plang type-playlist" v-component="plangular" v-src="'<?php echo $trackmetadata['permalink_url']; ?>'" v-class="player.playing == track ? 'track-playing' : 'track-stopped'">
            
            <?php SCdebugButton('API Reponse',($tt) ? $tt : $track); ?>

                       
            <div class="player clearfix">
				<div class="gayvatar">
					<img src="<?php echo $trackmetadata['artworkuse']; ?>" alt="<?php echo $trackmetadata['title']; ?>" title="<?php echo $trackmetadata['title']; ?>" class="onebatts" data-featherlight="<?php echo str_replace('large.jpg','t500x500.jpg',$trackmetadata['artworkuse']); ?>" width="105" height="105" />
						<ul class="toptrackmeta">
							<li class="posttime">
                            	<i class="fa fa-clock-o"></i> <time class="relativeTime posttiming" title="Posted on <?php echo date('j F Y H:i',$trackmetadata['trackdate']); ?>" datetime="<?php echo date('Y-m-d',$trackmetadata['trackdate'])?>T<?php echo date('H:i:s',$trackmetadata['trackdate']); ?>"><span class="sc-visuallyhidden"><?php echo scHumanTiming($trackmetadata['trackdate']); ?> ago</span><span aria-hidden="true"><?php echo scHumanTiming($trackmetadata['trackdate']); ?></span></time></li>
                        	<?php if ($trackmetadata['playback_count']) :?><li class="playbackcount" data-count="<?php echo $trackmetadata['playback_count']; ?>"><i class="fa fa-play"></i> <?php echo $trackmetadata['playback_count']; ?></li><?php endif; ?>
                            <?php if ($trackmetadata['comment_count']) :?><li class="commentcount" data-count="<?php echo $trackmetadata['comment_count']; ?>"><i class="fa fa-comment"></i> <?php echo $trackmetadata['comment_count']; ?></li><?php endif; ?>
                            <?php if ($trackmetadata['favoritings_count']) :?><li class="likecount" data-count="<?php echo $trackmetadata['favoritings_count']; ?>"><i class="fa fa-heart"></i> <?php echo $trackmetadata['favoritings_count']; ?></li><?php endif; ?>
                            <?php if ($trackmetadata['download_count']) :?><li class="downloadcount" data-count="<?php echo $trackmetadata['download_count']; ?>"><i class="fa fa-cloud-download"></i> <?php echo $trackmetadata['download_count']; ?></li><?php endif; ?>
						</ul>
					</div>
                    <div class="playbutton">
                    <button class="button-icon" v-on="click: previous()">
                          <svg v-plangular-icon="'previous'"></svg>
                        </button>
                    <button class="h2 button-icon" v-on="click: playPause(player.playlistIndex)">
					  <svg v-if="player.tracks[player.i] != track || !player.playing" v-plangular-icon="'play'"></svg>
                      <svg v-if="player.tracks[player.i] == track && player.playing" v-plangular-icon="'pause'"></svg>
                    </button>
                    <button class="button-icon" v-on="click: next()">
                          <svg v-plangular-icon="'next'"></svg>
                        </button>
                    </div>
                    <div class="trackstuff">
                        <div class="titlebox">
                            <h4 class="artist"><a href="<?php echo $trackmetadata['artist_permalink_url']; ?>" target="_blank"><?php echo esc_html($trackmetadata['artist_username']); ?></a> <?php if ($fing) : ?><span class="followbutton"><a href="javascript:void(0)" onclick="nsscFollow('<?php echo $trackmetadata['artist_id']; ?>',this);" class="followbutton followed"><i class="fa fa-eye" title="Unfollow"></i></span><?php else : ?><span class="followbutton"><a href="javascript:void(0)" onclick="nsscFollow('<?php echo $trackmetadata['artist_id']; ?>',this);" class="followbutton"><i class="fa fa-eye-slash" title="Follow"></i></span><?php endif; ?></h4>
                            <h3 class="tracktitle"><a href="<?php echo $trackmetadata['permalink_url']; ?>" target="_blank"><?php echo esc_html($trackmetadata['title']); ?></a></h3>
                        </div>
                
                       <div class="pghold progresscontain" v-if="player.tracks[player.i] != track || !player.playing" v-on="click: playPause(0)">
                            <div class="progressbase"></div>
                            <div class="progressbar" v-style="width: (currentTime / duration * 100) + '%'"></div>
                            <div class="seeker"></div>
                       </div>
                       <div class="progresscontain" v-if="player.tracks[player.i] == track && player.playing" v-on="click: seek($event)">
                            <div class="progressbase"></div>
                            <div class="progressbar" v-style="width: (currentTime / duration * 100) + '%'"></div>
                            <div class="seeker"></div>  
                        </div>
                   		<div class="waveform" style="background-image:url({{ player.currentTrack.waveform_url }}); display:none;" v-if="player.tracks[player.i] == track && player.playing" v-class="'showme'"></div>
                   		<div class="waveform coverhold" style="background-image:url(<?php echo str_replace('large.jpg','t500x500.jpg',$trackmetadata['artworkuse']); ?>);" v-if="player.tracks[player.i] != track || !player.playing"></div>
                        <div class="waveform covercover" v-if="player.tracks[player.i] != track || !player.playing"></div>
                   		
                        <div class="durationcontain"><span class="starttime" v-on="click:return false" v-class="true == true ? 'time-alive' : 'time-alive'">{{ currentTime | prettyTime }}</span>
                        <span class="duration" v-on="click:return false" v-class="true == true ? 'time-alive' : 'time-alive'">{{ duration | prettyTime }}</span></div>
                    </div>
                       <ul class="playlist-tracklist">
                        <?php /* <li v-repeat="t : tracks" class="tracklist-item"><a href="javascript:void(0)" id="playlist-item-{{ $index+1 }}" class="playlist-item" v-on="click: playPause($index);" v-class="player.currentTrack == t ? 'now-playing' : ''">
                          <span class="trackno">{{ $index+1 }}.</span>
                          <span class="artist">{{ t.user.username }}</span> - <span class="tracktitle">{{ t.title }}</span></a>
                          
                          <?php 
						  
						  $likes = false;
						  foreach ($trackmetadata['tracks'] as $track) { $likes .= ($track->user_favourite) ? $track->id.',' : false; }
						  $likes = substr($likes, 0, -1);
						  ?>
                          <div class="submeta" data-likes="<?php echo $likes; ?>">
                          	<div class="metaitem"><a href="{{t.permalink_url}}" target="_blank" class="awesome nssc soundcloudlink" title="Listen to {{ t.title }} on Soundcloud"><i class="fa fa-soundcloud"></i></a></div>
                            <div class="metaitem" v-if="<!-- Can't find the track ID in data-likes -->"><a href="javascript:void(0)" onclick="nsscLike({{ t.id }},this,'like');" class="awesome nssc likebutton" title="Favourite {{ t.title }}"><i class="fa fa-heart-o"></i></a></div>
                            <div class="metaitem" v-if="<!-- Can find the track ID in data-likes -->"><a href="javascript:void(0)" onclick="nsscLike({{ t.id }},this,'unlike');" class="awesome nssc likebutton liked" title="Favourite {{ t.title }}"><i class="fa fa-heart"></i></a></div>
                            <div class="metaitem" v-if="t.commentable == '1'"><a href="javascript:void(0)" onclick="nsscComment({{ t.id }},this);" class="awesome nssc commentbutton" title="Comment on {{ t.title }}"><i class="fa fa-comment"></i></a></div>
                            <div class="metaitem" v-if="t.downloadable == '1'"><a href="https://api.soundcloud.com/tracks/{{ t.id }}/download?client_id=<?php echo SC_CLIENT_ID; ?>" onclick="" class="awesome nssc downloadbutton" title="Download {{ t.title }}"><i class="fa fa-cloud-download"></i></a></div>
                            <div class="metaitem" v-if="t.purchase_url"><a href="{{ t.purchase_url }}" target="_blank" class="awesome nssc buynowbutton" title="{{ (purchase_title) ? purchase_title : 'Buy Now' }}"><i class="fa fa-shopping-cart"></i></a></div>
                          </div>
                        </li> */ ?>
                        <?php $i = 0; foreach($trackmetadata['tracks'] as $playlistitem) : ?>
                         <li class="tracklist-item"><a href="javascript:void(0)" id="playlist-item-<?php echo $i+1; ?>" class="playlist-item" v-on="click: playPause(<?php echo $i; ?>);" v-class="player.currentTrack.id == '<?php echo $playlistitem->id; ?>' ? 'now-playing' : ''">
                          <span class="trackinfo">
                          <span class="trackno"><?php echo $i+1; ?>.</span>
                          <span class="artist"><?php echo esc_html($playlistitem->user->username);?></span> - <span class="tracktitle"><?php echo esc_html($playlistitem->title);?></span>
                          </span>
                        </a> 
                        <div class="submeta">
                          	<div class="metaitem"><a href="<?php echo $playlistitem->permalink_url; ?>" target="_blank" class="awesome nssc soundcloudlink" title="Listen to <?php echo esc_html($playlistitem->title); ?> on Soundcloud"><i class="fa fa-soundcloud"></i></a></div>
                            <?php if(!$playlistitem->user_favorite) : ?><div class="metaitem"><a href="javascript:void(0)" onclick="nsscLike(<?php echo $playlistitem->id; ?> ,this,'like');" class="awesome nssc likebutton" title="Favourite <?php echo esc_html($playlistitem->title); ?>"><i class="fa fa-heart-o"></i></a></div><?php endif; ?>
                            <?php if($playlistitem->user_favorite) : ?><div class="metaitem"><a href="javascript:void(0)" onclick="nsscLike(<?php echo $playlistitem->id; ?>,this,'unlike');" class="awesome nssc likebutton liked" title="Favourite <?php echo esc_html($playlistitem->title);?>"><i class="fa fa-heart"></i></a></div><?php endif; ?>
                            <?php if($playlistitem->commentable) : ?><div class="metaitem"><a href="javascript:void(0)" onclick="nsscComment(<?php echo $playlistitem->id; ?>,this);" class="awesome nssc commentbutton" title="Comment on <?php echo esc_html($playlistitem->title); ?>"><i class="fa fa-comment"></i></a></div><?php endif; ?>
                            <?php if($playlistitem->downloadable) : ?><div class="metaitem"><a href="https://api.soundcloud.com/tracks/<?php echo $playlistitem->id; ?>/download?client_id=<?php echo SC_CLIENT_ID; ?>" class="awesome nssc downloadbutton" title="Download <?php echo esc_html($playlistitem->title); ?>"><i class="fa fa-cloud-download"></i></a></div><?php endif; ?>
                            <?php if($playlistitem->purchase_url) : ?><div class="metaitem buylink"><a href="<?php echo $playlistitem->purchase_url; ?>" target="_blank" class="awesome nssc buynowbutton" title="<?php echo ($playlistitem->purchase_title) ? esc_html($playlistitem->purchase_title) : 'Buy Now'; ?>"><i class="fa fa-shopping-cart"></i></a></div><?php endif; ?>
                          </div></li>
                        <?php $i++; endforeach; ?>
                      </ul>
                   
            <?php endif; ?>
            
            <?php  else : ?>
            
				<?php if ($trackmetadata['type'] == 'track') : // TRY EMBEDS ? ?>
                <div class="track-container track-<?php echo $trackmetadata['id']; ?> track-embed type-track">
                <div class="sleeve">
                <?php SCdebugButton('Track Meta Data',$trackmetadata); ?>
                <iframe width="100%" height="165" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?visual=false&url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F<?php echo $trackmetadata['id']; ?>&show_artwork=true&maxheight=70&show_comments=true"></iframe>
             
                <?php elseif ($trackmetadata['type'] == 'playlist') : ?>
                <div class="track-container track-<?php echo $trackmetadata['id']; ?> playlist-embed type-playlist">
                <div class="sleeve">
                <?php SCdebugButton('Track Meta Data',$trackmetadata); ?>
                <iframe width="100%" height="370" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?visual=false&url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F<?php echo $trackmetadata['id']; ?>&show_artwork=true&maxheight=70&show_comments=true"></iframe>
                <?php endif;  ?>
            <?php endif; ?>
            <ul class="trackmeta">
                <?php if ($trackmetadata['type'] == 'track') : ?><li class="likebutton">
                	<a href="javascript:void(0)" title="Favourite <?php echo esc_html($trackmetadata['title']); ?>" onclick="nsscLike('<?php echo $trackmetadata['id']; ?>',this,'<?php echo $trackmetadata['type']; ?>');" class="awesome nssc likebutts<?php if ($trackmetadata['user_favorite']) { echo " liked\"><i class=\"fa fa-heart\"></i> Liked"; } else { ?>"><i class="fa fa-heart-o"></i> Like<?php } ?></a>
                </li><?php endif; ?>
                <?php if ($trackmetadata['commentable']) : ?><li class="commentlink"> 
                		<a href="javascript:void(0)" title="Comment on <?php echo esc_html($trackmetadata['title']); ?>" class="awesome nssc" onclick="nsscComment('<?php echo $trackmetadata['id']; ?>',this);"><i class="fa fa-comment"></i> Comment</a></li><?php endif; ?>
				<?php if ($trackmetadata['purchase_url']) : ?><li class="buylink"> 
                		<a href="<?php echo $trackmetadata['purchase_url']; ?>" target="_blank" class="awesome nssc" title="<?php echo ($trackmetadata['purchase_title']) ? esc_html($trackmetadata['purchase_title']) : 'Buy Now'; ?>"><i class="fa fa-shopping-cart"></i> <?php echo ($trackmetadata['purchase_title']) ? esc_html($trackmetadata['purchase_title']) : 'Buy Now'; ?></a>
                	</li>	<?php endif; ?>
               <?php if ($trackmetadata['downloadable'] && ($trackmetadata['type'] != 'playlist')) : ?><li class="download">
               			<a href="https://api.soundcloud.com/tracks/<?php echo $trackmetadata['id']; ?>/download?client_id=<?php echo SC_CLIENT_ID; ?>" title="Download <?php echo esc_html($trackmetadata['title']); ?>" target="_blank" class="awesome nssc"><i class="fa fa-cloud-download"></i>  Download</a>
               		</li>
			    <?php if (user_can(get_current_user_id(),'administrator') && DB_TOKEN) : ?>
			   	<li class="dropbox">
               			<a href="javascript:void(0)" title="Send <?php echo esc_html($trackmetadata['title']); ?> To Dropbox" onclick="nsscDBUpload('<?php echo $trackmetadata['id']; ?>',this,<?php echo esc_html($trackmetadata['original_content_size']); ?>);" class="awesome nssc"><i class="fa fa-dropbox"></i></a>
               		</li>
               <?php endif; ?>
			   <?php endif; ?>
            </ul>
            </div>
			<?php if ($trackmetadata['reposttime']) : ?><p class="reposttime"><time class="relativeTime reposttiming" title="Posted on <?php echo date('j F Y H:i',$trackmetadata['reposttime']); ?>" datetime="<?php echo date('Y-m-d',$trackmetadata['reposttime'])?>T<?php echo date('H:i:s',$trackmetadata['reposttime']); ?>"><span class="sc-visuallyhidden">Reposted <?php echo scHumanTiming($trackmetadata['reposttime']); ?> ago</span><span aria-hidden="true"><?php echo scHumanTiming($trackmetadata['reposttime']); ?></span></time> ago by <a href="http://soundcloud.com<?php echo $trackmetadata['reposterurl']; ?>" target="_blank"><?php echo esc_html($trackmetadata['reposter']); ?></a></p><?php endif; ?>           
            <?php if ($trackmetadata['description']) : ?>
            <div class="track-description-cont">            
                <div class="track-description">
                    <?php echo scDescription($trackmetadata['description']); ?>
                </div>
            </div>
            <?php endif; ?>
         </div> 
<?php endif; ?>