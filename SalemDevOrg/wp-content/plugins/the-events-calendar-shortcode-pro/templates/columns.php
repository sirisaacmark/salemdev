<?php $contentorder = array_map( 'trim', $atts['contentorder'] ); ?>

<div class="ecs-event">
	<div class="ecs-wrap">
		<?php if ( Events_Calendar_Shortcode::isValid( $atts['thumb'] ) ): ?>
			<div class="ecs-thumbnail">
                <a href="<?php echo tribe_get_event_link(); ?>" rel="bookmark">
                    <?php if ( isset( $atts['thumbwidth'], $atts['thumbheight'] ) and is_numeric( $atts['thumbwidth'] ) and is_numeric( $atts['thumbheight'] ) ): ?>
                        <?php echo get_the_post_thumbnail( get_the_ID(), array( intval( $atts['thumbwidth'] ), intval( $atts['thumbheight'] ) ) ); ?>
                    <?php elseif ( $event_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' ) ): ?>
                        <img src="<?php echo esc_url( $event_image[0] ) ?>" />
                    <?php endif; ?>
                </a>
			</div>
		<?php endif; ?>
		<div class="summary">
			<h2 class="ecs-event-title">
				<a href="<?php echo tribe_get_event_link(); ?>" rel="bookmark"><?php echo apply_filters( 'ecs_event_list_title', get_the_title(), $atts, $post ) ?></a>
			</h2>
			<?php if ( in_array( 'date', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['eventdetails'] ) ): ?>
				<div class="ecs-date">
					<?php if ( class_exists( 'Tribe__Events__Pro__Main' ) ) { $ecp = Tribe__Events__Pro__Main::instance(); $ecp->disable_recurring_info_tooltip(); } ?>
					<?php echo tribe_events_event_schedule_details(); ?>
					<?php if ( class_exists( 'Tribe__Events__Pro__Main' ) ) { $ecp->enable_recurring_info_tooltip(); } ?>
				</div>
			<?php endif; ?>
			<?php if ( Events_Calendar_Shortcode::isValid( $atts['timeonly'] ) ): ?>
				<div class="duration time ecs_start_time">
					<?php echo tribe_get_start_time() ?>
				</div>
			<?php endif; ?>
			<?php if ( in_array( 'venue', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['venue'] ) ): ?>
				<div class="ecs-venue"><?php echo tribe_get_venue(); ?></div>
			<?php endif; ?>
			<?php if ( in_array( 'excerpt', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['excerpt'] ) ): ?>
				<?php $excerptLength = is_numeric( $atts['excerpt'] ) ? $atts['excerpt'] : 100; ?>
				<div class="ecs-excerpt">
					<?php echo Events_Calendar_Shortcode::get_excerpt( $excerptLength ) ?>
				</div>
			<?php endif; ?>
            <?php if ( Events_Calendar_Shortcode::isValid( $atts['button'] ) ): ?>
                <div class="ecs-button">
                    <a href="<?php echo ( ( 'website' == $atts['buttonlink'] && tribe_get_event_website_url() ) ? tribe_get_event_website_url() : tribe_get_event_link() ) ?>" rel="bookmark"><?php echo esc_html( $atts['button'] ) ?></a>
                </div>
            <?php endif; ?>
        </div>
	</div>
</div>