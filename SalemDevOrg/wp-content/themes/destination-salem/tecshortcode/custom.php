
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
<?php $contentorder = array_map( 'trim', $atts['contentorder'] ); ?>

<div class="ecs-event">
	<div class="date_thumb title">
		<div class="month"><?php echo tribe_get_start_date( null, false, 'l' ) ?>, <?php echo tribe_get_start_date( null, false, 'M' ) ?> <?php echo tribe_get_start_date( null, false, 'j' ) ?>
		</div>
	</div>
	<?php if ( Events_Calendar_Shortcode::isValid( $atts['thumb'] ) ): ?>
		<div class="ecs-thumbnail">
			<a href="<?php echo tribe_get_event_link(); ?>"  class="img-container">
			<?php if ( isset( $atts['thumbwidth'], $atts['thumbheight'] ) and is_numeric( $atts['thumbwidth'] ) and is_numeric( $atts['thumbheight'] ) ): ?>
				<?php echo get_the_post_thumbnail( get_the_ID(), array( intval( $atts['thumbwidth'] ), intval( $atts['thumbheight'] ) ) ); ?>
			<?php elseif ( $event_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' ) ): ?>
				<img src="<?php echo esc_url( $event_image[0] ) ?>" />
			<?php endif; ?>

			</a>
			<?php wpfp_link(); ?>
		</div>
	<?php endif; ?>
	<div class="desc">
		<a href="<?php echo tribe_get_event_link(); ?>"><?php echo apply_filters( 'ecs_event_list_title', get_the_title(), $atts, $post ) ?></a>
		<?php if ( Events_Calendar_Shortcode::isValid( $atts['timeonly'] ) ): ?>
			<div class="duration time ecs_start_time">
				<?php echo tribe_get_start_time() ?>
			</div>
		<?php endif; ?>
		<?php the_excerpt(); ?>
		<!--<div class="ecs-venue"><?php echo tribe_get_venue(); ?></div>-->
		
	</div>
	<a class="readmore" href="<?php echo tribe_get_event_link(); ?>">More Info <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
    <?php if ( in_array( 'button', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['button'] ) ): ?>
        <div class="ecs-button">
            <a href="<?php echo tribe_get_event_link(); ?>" rel="bookmark"><?php echo esc_html( $atts['button'] ); ?></a>
        </div>
    <?php endif; ?>
</div>