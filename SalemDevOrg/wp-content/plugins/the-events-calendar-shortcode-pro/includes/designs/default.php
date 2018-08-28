<?php
/*
 * Change the default contentorder to look like default TEC list view
 */
function ecs_default_contentorder_default( $contentorder, $atts, $post ) {
	return 'title, thumbnail, date, time, venue, excerpt, button';
}

/*
 * Change any other default attributes
 */
function ecs_shortcode_atts_default( $default_atts, $atts, $post ) {
	$default_atts['thumb'] = 'true';
	$default_atts['venue'] = 'true';
	$default_atts['button'] = 'false';
	$default_atts['groupby'] = '';
	return $default_atts;
}

/*
 * Global start/end tags
 */

function ecs_start_tag_default( $output, $atts, $post ) {
	global $ecs_default_design_count;
	if ( ! $ecs_default_design_count )
		$ecs_default_design_count = 0;
	$ecs_default_design_count++;

	$output = '<style>';
	$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .ecs-event {padding: 1.75em 0;}';
	$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .ecs-event .duration.time {font-weight:bold;}';
	$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .ecs-venue-details {margin-bottom:1em;}';
	$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .ecs-event .recurringinfo {display:inline-block;font-weight:normal;}';
	$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .ecs-event .recurringinfo .tribe-events-event-body {display:none;}';
	$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .ecs-venue-location address.ecs-events-address {display:inline;}';
	$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .ecs-button a {background-color:' . esc_html( $atts['buttonbg'] ) . ';background-image:none;border-radius:3px;border:0;box-shadow:none;color: ' . esc_html( $atts['buttonfg'] ) . ';cursor: pointer;display: inline-block;font-size: 11px;font-weight: 700;letter-spacing: 1px;line-height: normal;padding: 6px 9px;text-align: center;text-decoration: none;text-transform: uppercase;vertical-align: middle;zoom: 1;}';
	$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .cost {display: block;float:right;border:1px solid #dddddd;padding:5px 10px;background-color: #f2f2f2;}';
	if ( isset( $atts['titlesize'] ) and $atts['titlesize'] )
		$output .= '.ecs-events.default.default-' . intval( $ecs_default_design_count ) . ' .ecs-event .summary a {font-size:' . esc_html( $atts['titlesize'] ) . ';}';
	$output = apply_filters( 'ecs_default_styles', $output, $atts, $post );
	$output .= '</style>';
	$output .= '<div class="ecs-events default default-' . intval( $ecs_default_design_count ) . '"' . ( ( isset( $atts['id'] ) and $atts['id'] ) ? ' id="' . esc_attr( $atts['id'] ) . '"' : '' ) . '>';
	return $output;
}

function ecs_end_tag_default( $output, $atts, $post ) {
	return '</div>';
}

/*
 * Thumbnail
 */

function ecs_event_thumbnail_link_start_default( $output, $atts, $post ) {
	return '<div class="ecs-thumbnail">' . $output;
}

function ecs_event_thumbnail_link_end_default( $output, $atts, $post ) {
	return $output . '</div>';
}

/*
 * Title
 */

function ecs_event_title_tag_start_default( $output, $atts, $post ) {
	$output = '<h2 class="entry-title summary">';
	return $output;
}

function ecs_event_title_tag_end_default( $output, $atts, $post ) {
	$output = '</h2>';
	if ( Events_Calendar_Shortcode::isValid( $atts['cost'] ) and tribe_get_cost( null, true ) )
		$output .= '<span class="cost">' . tribe_get_cost( null, true ) . '</span>';
	return $output;
}

/*
 * Start/end tag (per event)
 */

function ecs_event_start_tag_default( $output, $atts, $post ) {
	// Replace the beginning tag so we maintain any CSS classes added by the core ECS function
	$output = str_replace( '<li', '<div', $output );

	/*
	 * Group by date functionality for the default view
	 */
	$atts['groupby'] = trim( $atts['groupby'] );
	if ( ! $atts['groupby'] )
		return $output;

	// see if we have a new start date
	$display_start_date = false;
	$ecs_date_format = ( 'month' === $atts['groupby'] ? 'Y-m' : 'Y-m-d' );
	if ( ! isset( $GLOBALS['ecn_default_last_date'] ) or ( tribe_get_start_date( null, false, $ecs_date_format ) != $GLOBALS['ecn_default_last_date'] ) )
		$display_start_date = true;
	$GLOBALS['ecn_default_last_date'] = tribe_get_start_date( null, false, $ecs_date_format );

	if ( $display_start_date ) {
		$output = '<h3 class="ecs-date">' . date_i18n( ( 'month' === $atts['groupby'] ? apply_filters( 'ecs_default_group_by_month_format', 'F Y' ) : apply_filters( 'ecs_default_group_by_day_format', get_option( 'date_format' ) ) ), strtotime( tribe_get_start_date( null, false, 'Y-m-d' ) ) ) . '</h3>' . $output;
	}

	return $output;
}

function ecs_event_end_tag_default( $output, $atts, $post ) {
	return '</div>';
}

/*
 * Excerpt (adds the 'find out more' link after the excerpt)
 */

function ecs_event_excerpt_tag_start_default( $output, $atts, $post ) {
	return '<div class="ecs-events-list-event-description">' . $output;
}

function ecs_event_excerpt_tag_end_default( $output, $atts, $post ) {
	if ( ! Events_Calendar_Shortcode::isValid( $atts['button'] ) )
		$output .= '<a href="' . esc_url( ( 'website' == $atts['buttonlink'] && tribe_get_event_website_url() ) ? tribe_get_event_website_url() : tribe_get_event_link() ) . '" class="tribe-events-read-more" rel="bookmark">' . esc_html( __( 'Find out more', 'the-events-calendar' ) ) . ' &raquo;</a>';
	$output .= '</div>';
	return $output;
}


/*
 * Venue/address details
 */

function ecs_event_venue_tag_start_default( $output, $atts, $post ) {
	return '<div class="ecs-venue-details">' . $output;
}

function ecs_event_venue_at_tag_start_default( $output, $atts, $post ) {
	return '<span class="ecs-venue-address-separator">';
}

function ecs_event_venue_at_text_default( $output, $atts, $post ) {
	return '';
}

function ecs_event_venue_at_tag_end_default( $output, $atts, $post ) {
	return '</span>';
}

function ecs_event_venue_tag_end_default( $output, $atts, $post ) {
	$address = '';
	if ( tribe_address_exists() ) {
		$address .= '<span class="ecs-venue-location">';
		$address .= '<address class="ecs-events-address">';
		$address .= tribe_get_full_address();
		if ( tribe_show_google_map_link() )
			$address .= tribe_get_map_link_html();
		$address .= '</address>';
		$address .= '</span>';
	}
	return $address . $output . '</div>';
}