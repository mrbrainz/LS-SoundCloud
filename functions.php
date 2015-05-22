<?php 

require_once 'inc/sc-functions.php';

require_once 'inc/comments.php';

$nsscu = parse_url(get_bloginfo('url'));
if ($nsscu['host'] == $_SERVER[HTTP_HOST]) {
	require_once 'inc/db-functions.php';
}
		
if ( ! isset( $content_width ) ) $content_width = 700;
	
//Enqueue scripts

add_action('wp_enqueue_scripts','lessshit_enqueue_script');
function lessshit_enqueue_script() {
	wp_enqueue_style('jquery');
	wp_enqueue_style('lessshit_style', get_template_directory_uri().'/style.css?v=forvagina171120140846');
	wp_enqueue_style('lessshit_bootstrap', get_template_directory_uri().'/css/bootstrap.css');
	wp_enqueue_style('lessshit_bootstrap-responsive', get_template_directory_uri().'/css/bootstrap-responsive.css');
	wp_enqueue_style('lessshit_docs', get_template_directory_uri().'/css/docs.css');
	wp_enqueue_style('font',get_template_directory_uri().'/css/font/font.css');
	
	if ( is_singular() ) wp_enqueue_script( "comment-reply" );
	
	if(!is_admin())	{
		wp_enqueue_script('bootstrap', get_template_directory_uri().'/js/bootstrap.js',array('jquery'));
		wp_enqueue_script('bootstrap-transition', get_template_directory_uri().'/js/bootstrap-transition.js');
	}
	
	//menu js
	wp_enqueue_script('menu', get_template_directory_uri().'/js/menu/menu.js');
	wp_enqueue_script('menus_1',get_template_directory_uri().'/js/menu/bootstrap.min.js'); 
}	


add_action('after_setup_theme', 'lessshit_setup_theme');
function lessshit_setup_theme() {

	if ( function_exists( 'add_theme_support' ) ) {	
		add_theme_support( 'post-thumbnails' );
		//add_theme_support( 'automatic-feed-links' );
	}

	register_nav_menus(	array( 'header-menu-logged-in' => __('Header Menu Logged In','lessshit') ));
	register_nav_menus(	array( 'header-menu-logged-out' => __('Header Menu Logged Out','lessshit') ));
	add_editor_style( get_template_directory_uri() . '/custom-editor-style.css' );

}

//code for register sidebar
add_action( 'widgets_init', 'lessshit_widgets_init');
function lessshit_widgets_init() {
/*sidebar*/
register_sidebar( array(
		'name' => 'Sidebar',
		'id' => 'sidebar-primary',
		'description' => 'The primary widget area',
		'before_widget' => '<div class="widget widget_archive">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>',
	) );
}

// Password Box

add_filter( 'the_password_form', 'lessshit_custom_password_form' );
function lessshit_custom_password_form() {	
	
	global $post;
	$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
	$output = '<form class="protected-post-form" action="' . get_option('siteurl') . '/wp-pass.php" method="post">
	' . __( "This post is password protected. To view it please enter your password below:",'lessshit' ) . '
	<label for="' . $label . '">' . __( "Password:",'lessshit' ) . ' </label><input name="search_form" id="' . $label . '" type="password" size="20" />
	<input type="submit" class="btn appo_btn"	name="Submit" value="' . esc_attr__( "Submit",'lessshit' ) . '" /></form>';
	return $output;
	
}