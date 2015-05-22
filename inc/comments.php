<?php
	// code for comment
	if ( ! function_exists( 'appointment_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own appointment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since appointment
 */
	function appointment_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	//get theme data
	global $comment_data;

	//translations
$leave_reply = $comment_data['translation_reply_to_coment'] ? $comment_data['translation_reply_to_coment'] : __('Reply','appointment');
?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-body <?php if ($comment->comment_approved == '0') echo 'pending-comment'; ?> clearfix">
                <div class="comment_mn_row_mn">
                    <div class="comment_mn_row_mn_thu comment_john_bg">
                        <?php echo get_avatar($comment, $size = '65'); ?>
                    </div><!-- /comment-avatar -->
                    <div class="comment_mn_row_sub">
						<?php printf(__('<cite class="author">%s</cite>'), get_comment_author_link()) ?>
						<span class="comment-date"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">- <?php echo get_comment_date(); ?></a></span>
                    </div><!-- /comment-meta -->
                    <div class="comment-content">
									<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'appointment' ); ?></em>
					<br/>
				<?php endif; ?>
    	                <div class="comment_mn_row_sub1">
    	                    <?php comment_text() ?>
    	                </div><!-- /comment-text -->
    	              <div class="comment_mn_row_sub2">
    	                    <?php comment_reply_link(array_merge( $args, array('reply_text' => $leave_reply. '&rarr;','depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    	              </div><!-- /reply -->
                    </div><!-- /comment-content -->
				</div><!-- /comment-details -->
		</div><!-- /comment -->
<?php
}
endif;
?>