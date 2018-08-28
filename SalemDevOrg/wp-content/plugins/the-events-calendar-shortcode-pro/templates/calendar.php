<?php
global $post;
if ( $GLOBALS['tecs_calendar_events_first'] ) {
	$GLOBALS['tecs_calendar_events_first'] = false;
} else {
	echo ',';
}
$tribe_ecp = Tribe__Events__Main::instance();
$category_slugs = array();
$event_categories = get_the_terms( $post->ID, $tribe_ecp->get_event_taxonomy() );
if ( is_array( $event_categories ) ) {
	foreach ( (array) $event_categories as $category ) {
		$category_slugs[] = ' ' . $category->slug . '_ecs_calendar_category';
	}
}
?><?php echo json_encode( array(
        'title' => get_the_title(),
        'start' => tribe_get_start_date( null, false, 'Y-m-d' ) . ( ( ! tribe_event_is_all_day() ) ? 'T' : '' ) . tribe_get_start_time( null, 'H:i:s' ),
        'end' => tribe_get_end_date( null, false, 'Y-m-d' ) . 'T' . ( tribe_event_is_all_day() ? '23:59:59' : tribe_get_end_time( null, 'H:i:s' ) ),
        'url' => tribe_get_event_link(),
		'excerpt' => tribe_events_get_the_excerpt(),
		'details' => tribe_events_template_data( $post ),
		'allDay' => tribe_event_is_all_day(),
		'categories' => implode( '', $category_slugs ),
) );
?>