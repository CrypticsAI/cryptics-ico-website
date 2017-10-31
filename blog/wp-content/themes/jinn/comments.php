<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Jinn
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<?php 
// If there are no comments at all, don't even show this area
if ( have_comments() ) : ?>
<div id="comments" class="comments-area <?php echo ! comments_open() ? 'comments-closed' : ''; ?>">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				$comment_number = get_comments_number();
                                $comment_locale = number_format_i18n( $comment_number );
                                if ( '1' === $comment_number ) {
                                        esc_html_e( 'One thought', 'jinn' );
                                } else {
                                        /* translators: %s: number of comments */
                                        printf( esc_html( _n( '%s thought', '%s thoughts', $comment_locale, 'jinn' ) ), $comment_locale ); // WPCS: XSS OK.
                                }
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'jinn' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'jinn' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'jinn' ) ); ?></div>

			</div>
		</nav>
		<?php endif; // Check for comment navigation. ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'      => 'ol',
					'short_ping' => true,
                                        'avatar_size'=> 80,
				) );
			?>
		</ol>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
			<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'jinn' ); ?></h2>
			<div class="nav-links">

				<div class="nav-previous"><?php previous_comments_link( esc_html__( 'Older Comments', 'jinn' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments', 'jinn' ) ); ?></div>

			</div>
		</nav>
		<?php
		endif; // Check for comment navigation.

	endif; // Check for have_comments().


	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>

		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'jinn' ); ?></p>
	<?php
	endif;

	
	?>

</div><!-- #comments -->
<?php endif; ?>

<?php if ( comments_open() ) { ?>
    <div class="comment-reply-form">
        <?php comment_form(); ?>
    </div><!-- .comment-reply-form -->
<?php } ?>