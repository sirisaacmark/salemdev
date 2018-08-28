<?php $contentorder = array_map( 'trim', $atts['contentorder'] ); ?>
<?php
$category_slugs = array();
$category_list = get_the_terms( $post, 'tribe_events_cat' );
$featured_class = ( get_post_meta( get_the_ID(), '_tribe_featured', true ) ? ' ecs-featured-event' : '' );
if ( is_array( $category_list ) ) {
	foreach ( (array) $category_list as $category ) {
		$category_slugs[] = ' ' . $category->slug . '_ecs_category';
	}
}
?>
<div class="ecs-event<?php echo implode( $category_slugs, '' ) . $featured_class ?>">
	<div class="date_thumb">
		<div class="month"><?php echo tribe_get_start_date( null, false, 'M' ) ?></div>
		<div class="day"><?php echo tribe_get_start_date( null, false, 'j' ) ?></div>
	</div>
	<?php if ( Events_Calendar_Shortcode::isValid( $atts['thumb'] ) ): ?>
		<div class="ecs-thumbnail">
			<?php if ( isset( $atts['thumbwidth'], $atts['thumbheight'] ) and is_numeric( $atts['thumbwidth'] ) and is_numeric( $atts['thumbheight'] ) ): ?>
				<?php echo get_the_post_thumbnail( get_the_ID(), array( intval( $atts['thumbwidth'] ), intval( $atts['thumbheight'] ) ) ); ?>
			<?php elseif ( $event_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' ) ): ?>
				<img src="<?php echo esc_url( $event_image[0] ) ?>" />
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="summary">
		<a href="<?php echo tribe_get_event_link(); ?>" rel="bookmark"><?php echo apply_filters( 'ecs_event_list_title', get_the_title(), $atts, $post ) ?></a>
		<?php if ( Events_Calendar_Shortcode::isValid( $atts['timeonly'] ) ): ?>
			<div class="duration time ecs_start_time">
				<?php echo tribe_get_start_time() ?>
			</div>
		<?php endif; ?>
		<?php if ( in_array( 'venue', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['venue'] ) ): ?>
			<div class="ecs-venue"><?php echo tribe_get_venue(); ?></div>
		<?php endif; ?>
		<?php if ( in_array( 'date', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['eventdetails'] ) ): ?>
			<div class="ecs-date">
				<?php echo tribe_events_event_schedule_details(); ?>
			</div>
		<?php endif; ?>
		<?php if ( in_array( 'excerpt', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['excerpt'] ) ): ?>
			<?php $excerptLength = is_numeric( $atts['excerpt'] ) ? $atts['excerpt'] : 100; ?>
			<div class="ecs-excerpt">
				<?php echo Events_Calendar_Shortcode::get_excerpt( $excerptLength ) ?>
			</div>
		<?php endif; ?>
	</div>
    <?php if ( in_array( 'button', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['button'] ) ): ?>
        <div class="ecs-button">
            <a href="<?php echo tribe_get_event_link(); ?>" rel="bookmark"><?php echo esc_html( $atts['button'] ); ?></a>
        </div>
    <?php endif; ?>
</div>
