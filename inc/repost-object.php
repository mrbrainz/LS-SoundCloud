<?php // Posts

add_action('init', 'repost_register'); 

// ==========================
// = Register the post type =
// ==========================

function repost_register() {
 
	$labels = array(
		'name' => _x('Reposts', 'post type general name'),
		'singular_name' => _x('Repost', 'post type singular name'),
		'add_new' => _x('Add New', 'Repost'),
		'add_new_item' => __('Add New Repost'),
		'edit_item' => __('Edit Repost'),
		'new_item' => __('New Repost'),
		'view_item' => __('View Repost'),
		'search_items' => __('Search Reposts'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'show_ui' => false,
		'query_var' => true,
		'menu_icon' => false,
		'rewrite' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title'),
		'has_archive' => true,
		'exclude_from_search' => true,
		'show_in_nav_menus' => false,
		'show_in_menu' => false,
	  ); 
  
	register_post_type( 'repost' , $args );
	remove_post_type_support( 'repost', 'trackbacks' );
	remove_post_type_support( 'repost', 'editor' );
	remove_post_type_support( 'repost', 'thumbnail' );
	remove_post_type_support( 'repost', 'excerpt' );
	remove_post_type_support( 'repost', 'trackbacks' );
	remove_post_type_support( 'repost', 'comments' );
	remove_post_type_support( 'repost', 'revisions' );
	remove_post_type_support( 'repost', 'page-attributes' );
	remove_post_type_support( 'repost', 'post-formats' );

}