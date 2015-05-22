<?php
/**
 * Comments Template
 * @file           comments.php
 * @package        Appointment
 * @author         webriti
 * @copyright      2014 Appointment
 * @license        license.txt
 * @filesource     wp-content/themes/appoinment/comments.php
 */
?>
<?php if ( post_password_required() ) : ?>
		<p class="nopassword">
		<?php _e( 'This post is password protected. Enter the password to view any comments.', 'appointment' ); ?>
		</p>
<?php return;endif;?>
        <?php if ( have_comments() ) : ?>
		<div class="row-fluid comment_mn">
		
			<?php   printf( _n( '<p id="comment_apt">One thought on &ldquo;%2$s&rdquo;', '<p id="comment_apt">%1$s thoughts on &ldquo;%2$s&rdquo;</p>', get_comments_number(), 'appointment' ),
		number_format_i18n( get_comments_number() ),  get_the_title()  );?>
		
			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :  ?>
			<nav id="comment-nav-above">
				<h1 class="assistive-text"><?php _e( 'Comment navigation', 'appointment' ); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'appointment' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'appointment' ) ); ?></div>
			</nav>
			<?php endif;  ?>
		<?php wp_list_comments( array( 'callback' => 'appointment_comment' ) );?>
		
		</div><!-- comment_mn -->
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below">
			<h1 class="assistive-text"><?php _e( 'Comment navigation', 'appointment' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'appointment' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'appointment' ) ); ?></div>
		</nav>
		<?php endif; 
		elseif ( ! comments_open() /* &&  ! is_page() */  && post_type_supports( get_post_type(), 'comments' ) ) :
		  // echo "comments are closed.";
?>
<?php endif; ?>
<?php if ('open' == $post->comment_status) : ?>
<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p><?php _e('You must be','appointment' );?><a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php _e('logged in','appointment' );?></a><?php _e('to post a comment.','appointment' );?></p>
<?php else : ?>
<div class="span12 comment_form">
	<?php
 
	$fields=array(
    'author' => '<input class="input-xlarge" type="text" value="" placeholder="Your name" tabindex="1" />',
    'email'  => '<input class="input-xlarge" type="text" placeholder="Email Id">',
    );
 
 function my_fields($fields) {
	return $fields;
}
add_filter('comment_form_default_fields','my_fields');
 
	$defaults = array(
     'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
   'comment_field'        => '<textarea class="input-xxxlarge" id="comment" name="comment" type="text" placeholder="Message" rows="3"></textarea>',
 
   'logged_in_as' => '<p class="logged-in-as">' . __( "Logged in as ",'appointment' ).'<a href="'. admin_url( 'profile.php' ).'">'.$user_identity.'</a>'. '<a href="'. wp_logout_url( get_permalink() ).'" title="Log out of this account">'.__(" Log out?",'appointment').'</a>' . '</p>',
   'comment_notes_after'  => '<dl class="">',
    'id_form'              => 'commentform',
    'id_submit'            => 'appo-form-post',
);
comment_form($defaults);
?>
</div><!-- leave_comment_mn -->
<?php endif; // If registration required and not logged in ?>
<?php endif;  ?>