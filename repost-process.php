<?php /* Template Name: Repost Process */ 

if(!is_user_logged_in()) {
	session_start();
	$_SESSION['loginmessage'] = '<p class="deposit-reposts">To deposit your reposts, please link NSSC with your $oundCloud account</p><p class="nothanks"><a href="/?murkreposts">No Thanks</a></p>';
	wp_redirect(get_bloginfo('url')); exit;
}	

get_header(); ?>

<?php get_template_part('pagepart','top'); ?>
		
      	<?php get_template_part('content','repostprocess'); ?>
       
	<?php get_template_part('pagepart','bottom'); ?>
    
<?php get_footer(); ?>