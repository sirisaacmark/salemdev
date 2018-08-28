<?php
/*
 * Change the default contentorder for a compressed, minimal view
 */
function ecs_default_contentorder_table( $contentorder, $atts, $post ) {
	return 'thumbnail, title, venue, date, time, excerpt, button';

}

/*
 * Change any other default attributes
 */
function ecs_shortcode_atts_table( $default_atts, $atts, $post ) {
	$default_atts['thumb'] = 'true';
	$default_atts['eventdetails'] = 'true';
	$default_atts['venue'] = 'false';
	$default_atts['thumbwidth'] = '300';
	$default_atts['thumbheight'] = '200';
	$default_atts['timeonly'] = 'false';
	$default_atts['excerpt'] = 'true';
	$default_atts['limit'] = '30';
	$default_atts['button'] = 'false';
	$default_atts['columns'] = 3;
	return $default_atts;
}

function ecs_get_table_width( $columns ) {
	$widths = apply_filters( 'ecs_table_widths', array(
		1 => 100,
		2 => 50,
		3 => 33,
		4 => 25,
		5 => 20,
		6 => 16.5,
	) );
	if ( isset( $widths[$columns] ) )
		return $widths[$columns];
	return $widths[3];
}

/*
 * Global start/end tags
 */

function ecs_start_tag_table( $output, $atts, $post ) {
	global $ecs_table_design_count;
	if ( ! $ecs_table_design_count )
		$ecs_table_design_count = 0;
	$ecs_table_design_count++;

	$output = '<style>';
	$output .= '.ecs-events.ecs-clearfix {zoom:1;overflow:auto;}';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' {display:table;table-layout:fixed;width:100%;}';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-table-row {display:table-row;width:100%;}';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-event {display:table-cell;vertical-align:top;width:' . esc_html( ecs_get_table_width( intval( $atts['columns'] ) ) ) . '%;padding:10px;}';
	$output .= '@media only screen and (max-width: 600px) { .ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-event {display:inline-block;width:100%;} .ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ', .ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-table-row {display:block;} }';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-event img {width:100%;}';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-event .ecs-venue {margin-bottom:10px;}';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-event .ecs-excerpt {margin-bottom:10px;}';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-event .ecs-wrap {margin-bottom:20px;}';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-event .ecs-date {margin-bottom:10px;font-weight:bold;}';
	$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-button a {background-color:' . esc_html( $atts['buttonbg'] ) . ';background-image:none;border-radius:3px;border:0;box-shadow:none;color: ' . esc_html( $atts['buttonfg'] ) . ';cursor:pointer;display:inline-block;font-size:11px;font-weight:700;letter-spacing:1px;line-height:normal;padding:6px 9px;text-align:center;text-decoration:none;text-transform:uppercase;vertical-align:middle;zoom:1;}';
	if ( isset( $atts['titlesize'] ) and $atts['titlesize'] )
		$output .= '.ecs-events.ecs-table.ecs-table-' . intval( $ecs_table_design_count ) . ' .ecs-event .summary a {font-size:' . esc_html( $atts['titlesize'] ) . ';}';
	$output = apply_filters( 'ecs_columns_styles', $output, $atts, $post );
	$output .= '</style>';
	$output .= '<div class="ecs-events ecs-clearfix ecs-table ecs-table-' . intval( $ecs_table_design_count ) . '"' . ( ( isset( $atts['id'] ) and $atts['id'] ) ? ' id="' . esc_attr( $atts['id'] ) . '"' : '' ) . '><div class="ecs-table-row">';
	return $output;
}

function ecs_end_tag_table( $output, $atts, $post ) {
	return '</div></div>';
}
