<?php 
/**
 * Content Template
 *
 *
 * @file           content.php
 * @package        Appointment
 * @author         webriti
 * @copyright      2014 Appointment
 * @license        license.txt
 * @version        Release: 1.1
 * @filesource     wp-content/themes/appoinment/content.php
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( is_sticky() ) : ?>
			
	<h3 class="main_title">
	<a class="blog_title-anchor" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
	</h3>
	<?php else : ?>
				
  	<h3 class="main_title">
	<a class="blog_title-anchor" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
	</h3>
	<?php endif;?>         
	<ul class="the-icons clearfix">
	<li><i class="icon-calendar"></i> <?php the_time('M j,Y');?></li>
	<li><i class="icon-comment"></i>  <?php  comments_popup_link( __( 'Leave a comment', 'appointment' ),__( '1 Comment', 'appointment' ), __( 'Comments', 'appointment' ),'name' ); ?></li>
	<li><i class="icon-edit"></i><?php edit_post_link( __( 'Edit', 'appointment' ), '<span class="meta-sep"></span> <span class="name">', '</span>' ); ?>
    <li><i class="icon-ok-circle">  </i></li> <li><?php the_category(); ?><li>
	</ul>
	<?php if(has_post_thumbnail()):?>					
		<div class="blog_img">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
			<?php the_post_thumbnail('large',array('class' => 'img-polaroid'));?>
			</a>
		</div>
	<?php endif;?>	
	<?php if ( post_password_required() ) : ?>
		<p ><?php  the_content(); ?></p>
		<div class="blog_bot_mn">
		<span> <?php the_tags('<b>Tags:</b>','');?> </span>
		</div>
	<?php else:?>
	<p><?php  the_excerpt(); ?></p>
	<?php if(wp_link_pages(array('echo'=>0))):?>
	<div class="pagination">
	<ul><?php 
	$args=array('before' => '<li>', ' after' => '</li>');
	wp_link_pages($args); ?>
	</ul>
	</div>
	<?php endif;?>				
	<button class="btn appo_btn" type="button">
	<a href="<?php the_permalink(); ?>" class="blog_rdmore"> <?php _e('Read More','appointment'); ?>
	</a>
	</button>
	<p class="tag-element"> <?php the_tags('<b>'.__('Tags:','appointment').'</b>','');?> </p>
	<?php endif;?>
</article><br/>