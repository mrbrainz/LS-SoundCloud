<?php // Posts

add_action('init', 'beta_key_register'); 

// ==========================
// = Register the post type =
// ==========================

function beta_key_register() {
 
	$labels = array(
		'name' => _x('Beta Keys', 'post type general name'),
		'singular_name' => _x('Beta Key', 'post type singular name'),
		'add_new' => _x('Add New', 'Beta Key'),
		'add_new_item' => __('Add New Beta Key'),
		'edit_item' => __('Edit Beta Key'),
		'new_item' => __('New Beta Key'),
		'view_item' => __('View Beta Key'),
		'search_items' => __('Search Beta Keys'),
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
  
	register_post_type( 'beta_key' , $args );
	remove_post_type_support( 'beta_key', 'trackbacks' );
	remove_post_type_support( 'beta_key', 'editor' );
	remove_post_type_support( 'beta_key', 'thumbnail' );
	remove_post_type_support( 'beta_key', 'excerpt' );
	remove_post_type_support( 'beta_key', 'trackbacks' );
	remove_post_type_support( 'beta_key', 'comments' );
	remove_post_type_support( 'beta_key', 'revisions' );
	remove_post_type_support( 'beta_key', 'page-attributes' );
	remove_post_type_support( 'beta_key', 'post-formats' );
	remove_post_type_support( 'beta_key', 'author' );
}