<?php
/*
 * Change the default contentorder for a compressed, minimal view
 */
function ecs_default_contentorder_grouped( $contentorder, $atts, $post ) {
	return 'title, venue, button';
}

/*
 * Change any other default attributes
 */
function ecs_shortcode_atts_grouped( $default_atts, $atts, $post ) {
	$default_atts['thumb'] = 'false';
	$default_atts['venue'] = 'false';
	$default_atts['thumbwidth'] = '75';
	$default_atts['thumbheight'] = '75';
	$default_atts['groupby'] = 'day';
	$default_atts['timeonly'] = 'true';
	return $default_atts;
}

/*
 * Global start/end tags
 */

function ecs_start_tag_grouped( $output, $atts, $post ) {
	$output = '<style>';
	$output .= '.grouped .ecs-event {width:100%;margin-bottom:5px;}';
	$output .= '.grouped .ecs-event .date {font-weight: bold;}';
	if ( isset( $atts['titlesize'] ) and $atts['titlesize'] )
		$output .= '.grouped .ecs-event .summary a {font-size:' . esc_html( $atts['titlesize'] ) . ';}';
	$output = apply_filters( 'ecs_grouped_styles', $output, $atts, $post );
	$output .= '</style>';
	$output .= '<div class="ecs-events grouped"' . ( ( isset( $atts['id'] ) and $atts['id'] ) ? ' id="' . esc_attr( $atts['id'] ) . '"' : '' ) . '>';
	return $output;
}

function ecs_end_tag_grouped( $output, $atts, $post ) {
	return '</div>';
}
