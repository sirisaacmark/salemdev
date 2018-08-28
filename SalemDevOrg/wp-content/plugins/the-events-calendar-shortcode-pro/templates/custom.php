<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
<?php
/**
 * you have access to:
 * $post (WP_Post) for the current event (and the tribe_get_... functions)
 * $post_index (int) of the current post, starting at 0 (ie. $post_index == ( count( $posts ) - 1 ) for last post)
 * $posts (WP_Post array) for all events
 * $atts (array) for your shortcode attributes
 * $event_output (string) for the current HTML output for the event
 */
?>
<?php tribe_get_template_part( 'modules/meta' ); ?>
<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>