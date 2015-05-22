<?php get_header();	get_template_part('orange','header'); ?><!-- Main_area -->
<div class="container">
	<div class="row-fluid">
	<div class="span12 main_space">
<!-- Main_content -->
	<div class="span12 appo_main_content"> 
		<?php if (have_posts()) : 
		while (have_posts()) : the_post(); ?>
		<div class="row-fluid appo_blog">
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>       
				<h3 class="main_title"><a class="blog_title-anchor" href="<?php the_permalink(); ?>"><?php the_title();?>
				<?php  echo  get_template_part( 'post-meta-page' ); ?>	</a></h3>
				<?php if ( has_post_thumbnail()) : ?>
				<div class="blog_img">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
				<?php the_post_thumbnail(); ?>
				</a>
				</div>
				<?php endif; ?>				
				<p><?php the_content(); ?></p>
			</div>
			<?php comments_template( '', true );?>
		</div>
		<?php endwhile; ?>
		<div class="pagination">	
				<ul>
				<li><?php previous_posts_link(); ?></li>
				<li><?php next_posts_link(); ?></li>
				</ul>
		</div>
	<?php endif;?>
	</div><!--appo_main_content-->
<!-- sidebar section -->
	<?php get_sidebar();?>  
	</div>
</div>
</div>
<?php get_footer(); ?>