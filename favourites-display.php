<?php /* Template Name: Favourites Display */ 
if(!is_user_logged_in()) {
	wp_redirect(get_bloginfo('url'));
}	
get_header(); ?>

<?php get_template_part('pagepart','top'); ?>
		<?php get_template_part('content','favouritesdisplay'); ?>
	<?php get_template_part('pagepart','bottom'); ?>
<?php get_footer(); ?>