<?php if ( $atts['groupby'] ): ?>
	<?php
	$display_start_date = false;
	$ecs_date_format = ( 'month' === $atts['groupby'] ? 'Y-m' : 'Y-m-d' );
	if ( ! isset( $GLOBALS['ecn_default_last_date_tpl'] ) or ( tribe_get_start_date( null, false, $ecs_date_format ) != $GLOBALS['ecn_default_last_date_tpl'] ) )
		$display_start_date = true;
	$GLOBALS['ecn_default_last_date_tpl'] = tribe_get_start_date( null, false, $ecs_date_format );
	?>
	<?php if ( $display_start_date ): ?>
        <h3 class="ecs-date"><?php echo date_i18n( ( 'month' === $atts['groupby'] ? apply_filters( 'ecs_default_group_by_month_format', 'F Y' ) : apply_filters( 'ecs_default_group_by_day_format', get_option( 'date_format' ) ) ), strtotime( tribe_get_start_date( null, false, 'Y-m-d' ) ) ) ?></h3>
	<?php endif; ?>
<?php endif; ?>
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
<div class="ecs-event<?php implode( '', $category_slugs ) ?>">
    <h2 class="entry-title summary">
        <a href="<?php echo tribe_get_event_link() ?>" rel="bookmark">
			<?php echo get_the_title() ?>
        </a>
    </h2>
	<?php if ( Events_Calendar_Shortcode::isValid( $atts['cost'] ) and tribe_get_cost( null, true ) ): ?>
        <span class="cost"><?php echo tribe_get_cost( null, true ) ?></span>
	<?php endif; ?>
    <?php if ( Events_Calendar_Shortcode::isValid( $atts['thumb'] ) ): ?>
        <div class="ecs-thumbnail">
            <?php $thumbWidth = is_numeric($atts['thumbwidth']) ? $atts['thumbwidth'] : ''; ?>
            <?php $thumbHeight = is_numeric($atts['thumbheight']) ? $atts['thumbheight'] : ''; ?>
            <?php if ( ! empty( $thumbWidth ) && ! empty( $thumbHeight ) ): ?>
                <?php the_post_thumbnail( get_the_ID(), array( $thumbWidth, $thumbHeight ) ); ?>
            <?php else: ?>
                <?php if ( $thumb = get_the_post_thumbnail( get_the_ID(), trim( $atts['thumbsize'] ) ? trim( $atts['thumbsize'] ) : 'medium' ) ): ?>
                    <a href="<?php echo tribe_get_event_link() ?>"><?php echo $thumb; ?></a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <span class="duration time">
        <?php echo tribe_events_event_schedule_details(); ?>
    </span>
    <div class="ecs-venue-details">
        <span class="duration venue">
            <?php echo tribe_get_venue() ?>
	        <?php if ( tribe_address_exists() ): ?>
                <span class="ecs-venue-location">
		            <address class="ecs-events-address">
		                <?php echo tribe_get_full_address(); ?>
		                <?php if ( tribe_show_google_map_link() ): ?>
			                <?php echo tribe_get_map_link_html(); ?>
		                <?php endif; ?>
                    </address>
                </span>
	        <?php endif; ?>
        </span>
    </div>
    <div class="ecs-events-list-event-description">
        <p class="ecs-excerpt">
			<?php if ( Events_Calendar_Shortcode::isValid( $atts['raw_description'] ) ): ?>
				<?php echo get_the_content(); ?>
			<?php else: ?>
				<?php echo Events_Calendar_Shortcode::get_excerpt( is_numeric($atts['excerpt']) ? $atts['excerpt'] : 100, 'content' ); ?>
			<?php endif; ?>
        </p>
		<?php if ( ! Events_Calendar_Shortcode::isValid( $atts['button'] ) ): ?>
            <a href="<?php echo esc_url( ( 'website' == $atts['buttonlink'] && tribe_get_event_website_url() ) ? tribe_get_event_website_url() : tribe_get_event_link() ) ?>" class="tribe-events-read-more" rel="bookmark"><?php echo esc_html( __( 'Find out more', 'the-events-calendar' ) ) ?> &raquo;</a>
		<?php else: ?>
            <div class="ecs-button">
                <a href="<?php echo ( ( 'website' == $atts['buttonlink'] && tribe_get_event_website_url() ) ? tribe_get_event_website_url() : tribe_get_event_link() ) ?>" rel="bookmark">
					<?php echo esc_html( $atts['button'] ) ?>
                </a>
            </div>
		<?php endif; ?>

    </div>

</div>