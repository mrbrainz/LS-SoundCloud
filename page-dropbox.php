<?php 			require_once get_stylesheet_directory()."/inc/Dropbox/autoload.php";
				use \Dropbox as dbx;
				$dbt = get_user_meta(get_current_user_id(),'dropboxtoken',true);
				$appInfo = dbx\AppInfo::loadFromJsonFile(get_stylesheet_directory()."/inc/nsscjsonfmb.json");	
				$dbxClient = new dbx\Client($dbt, "NSSC/1.0");	
				$accountInfo = $dbxClient->getAccountInfo();
 ?>

<?php get_header(); ?>

<?php get_template_part('pagepart','top'); ?>
			
				<div class="row-fluid appo_blog_post<?php echo (!is_user_logged_in()) ? ' newbie' : false; ?>">
					<?php  the_post(); ?>
					<?php if(has_post_thumbnail()):?>
					<div class="blog_img">
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('', array('class' => "img-polaroid" )); ?></a>
					</div>
					<?php endif;?>
					<!-- <img src="images/large.jpg"> -->
					<div class="app-page-content">
					<?php the_content(); ?>
                   
                   <pre><?php print_r($accountInfo); 
										
					// 'small' file
					// https://api.soundcloud.com/tracks/24543452/download?client_id=a7f6658adbea8581e9e9f95b9a82e4ab
					
					// Big File
					// https://api.soundcloud.com/tracks/168301291/download?client_id=a7f6658adbea8581e9e9f95b9a82e4ab
					
					if ($dbx) :
					
					$file = "https://api.soundcloud.com/tracks/24543452/download?client_id=a7f6658adbea8581e9e9f95b9a82e4ab";
					
					if ($file) {					
						$f = get_headers($file, 1);		
						$fh = get_headers($f['Location'], 1);			
						$fn = rtrim(str_replace('attachment;filename="','',$fh['Content-Disposition']), '"');
						$loc = $f['Location'];
						$f = fopen($loc, "rb");
						// $result = $dbxClient->uploadFile("/".$fn, dbx\WriteMode::add(), $f);
						fclose($f);
						print_r($result);
					}
					endif;
					
					?>	
					</pre>
                    </div>
					<?php if(wp_link_pages(array('echo'=>0))):?>
                    <div class="pagination_blog">
					<ul class="page-numbers"><?php 
					 $args=array('before' => '<li>'.__('Pages:','appointment'),'after' => '</li>');
					 wp_link_pages($args); ?>
					</ul>
					</div>
					<?php endif; ?>				    
				</div>
				<div class="row-fluid comment_mn">
				<?php comments_template( '', true );?>
				</div>
			<?php get_template_part('pagepart','bottom'); ?>
<?php get_footer(); ?>