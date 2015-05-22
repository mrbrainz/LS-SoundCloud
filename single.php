<?php wp_redirect(get_bloginfo('url')); exit; 

get_header(); ?>

<?php get_template_part('pagepart','top'); ?>
			
				<div class="row-fluid appo_blog_post">
					<?php  the_post(); ?>
					<h3 class="main_title"><a class="blog_title-anchor" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php $defalt_arg =array('class' => "img-polaroid" )?>
					<?php if(has_post_thumbnail()):?>
					<div class="blog_img">
					<a href="<?php the_permalink(); ?>"title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('', $defalt_arg); ?></a>
					</div>
					<?php endif;?>
					<!-- <img src="images/large.jpg"> -->
					<div class="app-page-content">
					<p><?php the_content(); ?></p>
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