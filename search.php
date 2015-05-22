<?php
wp_redirect(get_bloginfo('url')); exit; 
get_header(); ?>

<?php get_template_part('pagepart','top'); ?>
		<div class="row-fluid appo_blog_post">
		<?php if ( have_posts() ) : ?>
			<h2><?php printf( __( 'Search Results for:%s', 'appointment' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
			<?php if(wp_link_pages(array('echo'=>0))):?>
            <div class="pagination_blog"><ul class="page-numbers"><?php 
				$args=array('before' => '<li>'.__('Pages:','appointment'),'after' => '</li>');
				wp_link_pages($args); ?></ul></div>
			<?php endif; ?>
			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
			<?php
			/* Include the Post-Format-specific template for the content.
			/* If you want to overload this in a child theme then include a file
			* called content-___.php (where ___ is the Post Format name) and that will be used instead.
			*/
			get_template_part( 'content', get_post_format() );
			?>
			<?php endwhile; ?>
			<?php else : ?>
			<h2><?php _e( 'Nothing Found', 'appointment' ); ?></h2>
			<div class="blog_con_mn">
			<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'appointment' ); ?>
			</p>
			<?php get_search_form(); ?>
			</div><!-- .blog_con_mn -->
			<?php endif; ?>
        </div>
	<?php get_template_part('pagepart','bottom'); ?>
<?php get_footer(); ?>