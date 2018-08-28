<?php
/*
 * Change the default contentorder for a compressed, minimal view
 */
function ecs_default_contentorder_compact( $contentorder, $atts, $post ) {
	return 'date_thumb, thumbnail, title, venue, time, excerpt, button';
}

/*
 * Change any other default attributes
 */
function ecs_shortcode_atts_compact( $default_atts, $atts, $post ) {
	$default_atts['thumb'] = 'false';
	$default_atts['venue'] = 'false';
	$default_atts['excerpt'] = 'false';
	$default_atts['thumbwidth'] = '75';
	$default_atts['thumbheight'] = '75';
	return $default_atts;
}

/*
 * Global start/end tags
 */

function ecs_start_tag_compact( $output, $atts, $post ) {
	global $ecs_compact_design_count;
	if ( ! $ecs_compact_design_count )
		$ecs_compact_design_count = 0;
	$ecs_compact_design_count++;

	$output = '<style>';
	$output .= '.ecs-events.compact.compact-' . intval( $ecs_compact_design_count ) . ' {display:table;border-spacing:5px;}';
	$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event {width:100%;display:table-row;margin-bottom:5px;height:75px;}';
	$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event .date_thumb {width:80px;display:table-cell;text-align:center;vertical-align:middle;background-color:' . esc_html( $atts['bgthumb'] ) . ';color:' . esc_html( $atts['fgthumb'] ) .';}';
	$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event .date_thumb .month {font-size:16px;text-transform:uppercase;margin:0;padding:0;line-height:1;}';
	$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event .date_thumb .day {font-size:26px;font-weight:bold;margin:0;padding:0;line-height:1;}';
	$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event .summary, .compact .ecs-event .ecs-thumbnail, .compact .ecs-event .ecs-button {display:table-cell;vertical-align:middle;}';
	if ( isset( $atts['titlesize'] ) and $atts['titlesize'] )
		$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event .summary a {font-size:' . esc_html( $atts['titlesize'] ) . ';}';
	$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event .ecs-button {padding-left:10px;}';
	$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event .ecs-thumbnail img {max-width:none;display:table-cell;margin:0;padding:0;}';
	$output .= '.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-event .ecs-excerpt {margin-top: 5px;}';
	$output .= '.ecs-events.compact.compact-' . intval( $ecs_compact_design_count ) . ' .ecs-button a {background-color:' . esc_html( $atts['buttonbg'] ) . ';background-image:none;border-radius:3px;border:0;box-shadow:none;color: ' . esc_html( $atts['buttonfg'] ) . ';cursor: pointer;display: inline-block;font-size: 11px;font-weight: 700;letter-spacing: 1px;line-height: normal;padding: 6px 9px;text-align: center;text-decoration:none;text-transform:uppercase;vertical-align:middle;zoom:1;white-space:nowrap;}';
	$output = apply_filters( 'ecs_compact_styles', $output, $atts, $post );
	$output .= '</style>';
	$output .= '<div class="ecs-events compact compact-' . intval( $ecs_compact_design_count ) . '"' . ( ( isset( $atts['id'] ) and $atts['id'] ) ? ' id="' . esc_attr( $atts['id'] ) . '"' : '' ) . '>';
	return $output;
}

function ecs_end_tag_compact( $output, $atts, $post ) {
	return '</div>';
}

/**
 * Date/time - add to summary part
 */

function ecs_event_date_tag_start_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_list_details_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_date_tag_end_compact( $output, $atts, $post ) {
	return '';
}


function ecs_event_time_tag_start_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_list_time_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_time_tag_end_compact( $output, $atts, $post ) {
	return '';
}



/*
 * Title
 */

function ecs_event_title_tag_start_compact( $output, $atts, $post ) {
	return '<div class="summary">';
}

function ecs_event_title_tag_end_compact( $output, $atts, $post ) {
	$output = '</div>';
	$contentorder = array_map( 'trim', $atts['contentorder'] );
	if ( in_array( 'excerpt', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['excerpt'] ) ) {
		$excerptLength = is_numeric( $atts['excerpt'] ) ? $atts['excerpt'] : 100;
		$output = '<div class="ecs-excerpt">' . apply_filters( 'ecs_excerpt_compact', Events_Calendar_Shortcode::get_excerpt( $excerptLength ), $atts, $post, $excerptLength ) . '</div>' . $output;
	}
	if ( in_array( 'date', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['eventdetails'] ) ) {
		$output = '<div class="ecs-date">' . apply_filters( 'ecs_date_compact', tribe_events_event_schedule_details(), $atts, $post ) . '</div>' . $output;
	}
	if ( in_array( 'time', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['timeonly'] ) ) {
		$output = '<div class="ecs-time">' . apply_filters( 'ecs_time_compact', tribe_get_start_time(), $atts, $post ) . '</div>' . $output;
	}
	if ( in_array( 'venue', $contentorder ) and Events_Calendar_Shortcode::isValid( $atts['venue'] ) ) {
		$output = '<div class="ecs-venue">' . apply_filters( 'ecs_venue_compact', tribe_get_venue(), $atts, $post ) . '</div>' . $output;
	}
	return $output;
}

/*
 * Thumbnail
 */
function ecs_event_thumbnail_compact( $output, $atts, $post ) {
	return '<div class="ecs-thumbnail">' . $output . '</div>';
}

/*
 * Excerpt - override to show under the title
 */
function ecs_event_excerpt_tag_start_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_excerpt_compact( $output, $atts, $post, $excerptLength ) {
	return '';
}

function ecs_event_excerpt_tag_end_compact( $output, $atts, $post ) {
	return '';
}

/*
 * Start/end tag (per event)
 */

function ecs_event_start_tag_compact( $output, $atts, $post ) {
	$output = str_replace( '<li', '<div', $output );
	return $output;
}

function ecs_event_end_tag_compact( $output, $atts, $post ) {
	return '</div>';
}

/*
 * Venue/address details
 *
 * Override to show under title
 */

function ecs_event_venue_tag_start_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_venue_at_tag_start_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_venue_at_text_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_venue_at_tag_end_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_venue_tag_end_compact( $output, $atts, $post ) {
	return '';
}

function ecs_event_list_venue_compact( $output, $atts, $post ) {
	return '';
}