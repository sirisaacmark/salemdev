<?php
/**
 * Searches for the blog language in the locale folder
 * Defaults to en (the default fullcalendar language)
 *
 * Sets $GLOBALS['ecs_calendar_language']
 */
function ecs_set_fullcalendar_language() {
	$GLOBALS['ecs_calendar_language'] = 'en';
	$language = str_replace( '_', '-', get_locale() );
	if ( file_exists( trailingslashit( dirname( __FILE__ ) ) . 'assets/js/locale/' . strtolower( basename( $language ) ) . '.js' ) )
		$GLOBALS['ecs_calendar_language'] = strtolower( basename( $language ) );
	else {
		$roots = explode( '-', $language );
		$language = $roots[0];
		if ( file_exists( trailingslashit( dirname( __FILE__ ) ) . 'assets/js/locale/' . strtolower( basename( $language ) ) . '.js' ) ) {
			$GLOBALS['ecs_calendar_language'] = strtolower( basename( $language ) );
		}
	}
}

function ecs_register_scripts_calendar() {
	wp_register_script( 'tecs-full-calendar-moment', plugins_url( '/assets/js/moment.min.js', __FILE__ ), array(), '2.18.1', true );
	wp_register_script( 'tecs-full-calendar', plugins_url( '/assets/js/fullcalendar.min.js', __FILE__ ), array( 'jquery', 'tecs-full-calendar-moment' ), '3.4.0', true );

	// Load the exact locale for the calendar if available, otherwise try for the root language
	ecs_set_fullcalendar_language();
	if ( $GLOBALS['ecs_calendar_language'] and 'en' !== $GLOBALS['ecs_calendar_language'] ) {
		wp_register_script( 'tecs-full-calendar-language', plugins_url( '/assets/js/locale/' . basename( $GLOBALS['ecs_calendar_language'] ) . '.js', __FILE__ ), array( 'tecs-full-calendar' ), '3.4.0', true );
		wp_register_script( 'tecs-calendar-init', plugins_url( '/assets/js/tecs-calendar.min.js', __FILE__ ), array( 'tecs-full-calendar-language' ), TECS_VERSION, true );
	} else {
		wp_register_script( 'tecs-calendar-init', plugins_url( '/assets/js/tecs-calendar.min.js', __FILE__ ), array( 'tecs-full-calendar' ), TECS_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'ecs_register_scripts_calendar' );

/*
 * Remove default content order elements as they won't be rendered anyway
 */
function ecs_default_contentorder_calendar( $contentorder, $atts, $post ) {
	return 'title';
}

function ecs_always_show_calendar( $always_show, $atts ) {
	if ( isset( $atts['design'] ) and 'calendar' == $atts['design'] )
		$always_show = true;
	return $always_show;
}
add_action( 'ecs_always_show', 'ecs_always_show_calendar', 10, 2 );

/*
 * Change any other default attributes
 */
function ecs_shortcode_atts_calendar( $default_atts, $atts, $post ) {
	$default_atts['thumb'] = 'true';
	$default_atts['venue'] = 'false';
	$default_atts['eventbg'] = '';
	$default_atts['eventfg'] = '#fff';
	$default_atts['eventborder'] = '';
	$default_atts['defaultview'] = 'month';
	$default_atts['hide_extra_days'] = '';
	$default_atts['limit'] = 500;
	$default_atts['first_day_of_week'] = '';
	$default_atts['firstload'] = true;
	$default_atts['startdate'] = current_time( 'Y-m-d' );
	$default_atts['fromdate'] = date( 'Y-m-d', strtotime( date( 'Y-m' ) . '-01' ) - DAY_IN_SECONDS * 7 );
	$default_atts['todate'] = date( 'Y-m-d', strtotime( date( 'Y-m' ) . '-01' ) + DAY_IN_SECONDS * 45 );
	return $default_atts;
}

function ecs_shortcode_set_initial_date_range( $atts ) {
	if ( ! isset( $atts['design'] ) or 'calendar' !== $atts['design'] or ! isset( $atts['firstload'] ) or ! $atts['firstload'] or $atts['firstload'] == 'false' )
		return $atts;
	$atts['startdate'] = ( false !== strtotime( $atts['startdate'] ) ? date( 'Y-m-d', strtotime( $atts['startdate'], current_time( 'timestamp' ) ) ) : current_time( 'Y-m-d' ) );
	$atts['fromdate'] = date( 'Y-m-d', strtotime( date( 'Y-m', strtotime( $atts['startdate'] ) ) . '-01' ) - DAY_IN_SECONDS * 7 );
	$atts['todate'] = date( 'Y-m-d', strtotime( date( 'Y-m', strtotime( $atts['startdate'] ) ) . '-01' ) + DAY_IN_SECONDS * 45 );
	return $atts;
}
add_filter( 'shortcode_atts_ecs-list-events', 'ecs_shortcode_set_initial_date_range' );
/**
 * The default query only really captures events that start AND end within the range.
 * Do a more complex query from TEC Month.php and get all events that fall within our month.
 *
 * @param $args
 * @param $atts
 *
 * @return mixed
 */
function ecs_calendar_add_multispan_events( $args, $atts ) {
	if ( 'calendar' == $atts['design'] ) {
		global $wpdb;

		$start_date = ( false !== strtotime( $atts['fromdate'], current_time( 'timestamp' ) ) ) ? date( 'Y-m-d', strtotime( $atts['fromdate'], current_time( 'timestamp' ) ) ) : false;
		$end_date = ( false !== strtotime( $atts['todate'], current_time( 'timestamp' ) ) ) ? date( 'Y-m-d', strtotime( $atts['todate'], current_time( 'timestamp' ) ) ) . ' 23:59:59' : false;

		$start_date_sql = esc_sql( $start_date );
		$end_date_sql = esc_sql( $end_date );

		// Despite the method name, this obtains a list of post IDs to be hidden from *all* event listings
		$ignore_events = Tribe__Events__Query::getHideFromUpcomingEvents();

		// If it is empty we don't need to do anything further
		if ( empty( $ignore_events ) ) {
			$ignore_hidden_events_AND = '';
		} else {
			// Let's ensure they are all absolute integers then collapse into a string
			$ignore_events = implode( ',', array_map( 'absint', $ignore_events ) );

			// Terminate with AND so it can easily be combined with the rest of the WHERE clause
			$ignore_hidden_events_AND = " $wpdb->posts.ID NOT IN ( $ignore_events ) AND ";
		}

		$post_stati = 'publish';

		$events_request = "SELECT tribe_event_start.post_id as ID,
							tribe_event_start.meta_value as EventStartDate,
							tribe_event_end_date.meta_value as EventEndDate
					FROM $wpdb->postmeta AS tribe_event_start
					LEFT JOIN $wpdb->posts ON tribe_event_start.post_id = $wpdb->posts.ID
					LEFT JOIN $wpdb->postmeta as tribe_event_end_date ON ( tribe_event_start.post_id = tribe_event_end_date.post_id AND tribe_event_end_date.meta_key = '_EventEndDate' )
					WHERE $ignore_hidden_events_AND tribe_event_start.meta_key = '_EventStartDate'
					AND (
						(
							tribe_event_start.meta_value >= '{$start_date_sql}'
							AND tribe_event_start.meta_value <= '{$end_date_sql}'
						)
						OR (
							tribe_event_end_date.meta_value >= '{$start_date_sql}'
							AND tribe_event_end_date.meta_value <= '{$end_date_sql}'
						)
						OR (
							tribe_event_start.meta_value < '{$start_date_sql}'
							AND tribe_event_end_date.meta_value > '{$end_date_sql}'
						)
					)
					AND $wpdb->posts.post_status IN('$post_stati')
					ORDER BY $wpdb->posts.menu_order ASC, DATE(tribe_event_start.meta_value) ASC, TIME(tribe_event_start.meta_value) ASC;
					";

		$events_in_month = $wpdb->get_results( $events_request );
		$args['meta_query'] = array(
			'relation' => 'AND',
		);
		$args['post__in'] = wp_list_pluck( $events_in_month, 'ID' );
	}
	return $args;
}
add_filter( 'ecs_get_events_args', 'ecs_calendar_add_multispan_events', 10, 5 );

/*
 * Global start/end tags
 */
function ecs_start_tag_calendar( $output, $atts, $post ) {
	$output = '';

	// Set an indicator so we don't get a trailing ,
	$GLOBALS['tecs_calendar_events_first'] = true;

	if ( ! defined( 'DOING_AJAX' ) or ! DOING_AJAX ) {
		// Create a unique ID in case there are multiple calendars on the page
		if ( ! isset( $GLOBALS['ecs-calendar-id'] ) )
			$GLOBALS['ecs-calendar-id'] = 0;
		$GLOBALS['ecs-calendar-id']++;
		$calendar_id = 'ecs-calendar-' . intval( $GLOBALS['ecs-calendar-id'] );

		// Add the CSS styling.  Don't want to enqueue on all pages nor have a regex for every
		// single page request to detect if the plugin with design="calendar" is loading.
		$output .= '<style>';
		if ( 1 == intval( $GLOBALS['ecs-calendar-id'] ) ) {
			ob_start();
			include( trailingslashit( dirname( __FILE__) ) . 'assets/css/fullcalendar.min.css' );
			$output .= ob_get_contents();
			ob_end_clean();
		}

		$output .= ' #' . $calendar_id . ' table {margin: 0;}
	#' . $calendar_id . ' .fc-widget-header table {margin: 0;}
	#' . $calendar_id . ' th {font-weight: normal;}
	#' . $calendar_id . ' a.fc-event {box-shadow:none;}
	#' . $calendar_id . '-container {position:relative;}
	#' . $calendar_id . '-loading {position:absolute;width:100%;height:100%;z-index:100;top:0;left:0;background:#fff;opacity:0.7;text-align:center;line-height:90px;display:none;}';

		if ( isset( $atts['eventbg'] ) and $atts['eventbg'] )
			$output .= ' #' . $calendar_id . ' a.fc-event {background-color:' . esc_html( $atts['eventbg'] ) . ';border:1px solid ' . esc_html( $atts['eventbg'] ) . ';}';
		if ( isset( $atts['eventborder'] ) and $atts['eventborder'] )
			$output .= ' #' . $calendar_id . ' a.fc-event {border:1px solid ' . esc_html( $atts['eventborder'] ) . ';}';
		if ( isset( $atts['eventfg'] ) and $atts['eventfg'] )
			$output .= ' #' . $calendar_id . ' a.fc-event {color:' . esc_html( $atts['eventfg'] ) . ';}';

		$output .= ' #tecs-tooltipevent.tooltip-' . $calendar_id . ' h4 {font-size:18px;letter-spacing:0;margin:0;color:#0a0a0a;}';
		$output .= ' #tecs-tooltipevent.tooltip-' . $calendar_id . ' .ecs-calendar-event-body {font-size:11px;color:#0a0a0a;}';
		$output = apply_filters( 'ecs_calendar_styles', $output, $atts, $post );
		$output .= '</style>';

		// Enqueue the necessary scripts if they're not already
		if ( ! wp_script_is( 'tecs-calendar-init' ) ) {
			wp_enqueue_script( 'tecs-calendar-init' );
		}
		$output .= '<script type="text/javascript">var tecsEvents = tecsEvents || {}; var tecEventCalendarSettings = tecEventCalendarSettings || {};</script>';

		// Create the container element for the calendar and the "loading" overlay
		$output .= '<div id="' . $calendar_id . '-container">';
		$output .= '<div id="' . $calendar_id . '" class="ecs-events calendar"></div>';
		$output .= '<div id="' . $calendar_id . '-loading">Loading...</div>';
		$output .= '</div>';
		$output .= "<script type='text/javascript'>";

		// Set any options via the attributes, to pass into the calendar
		$atts['height'] = is_numeric( $atts['height'] ) ? intval( $atts['height'] ) : $atts['height'];
		$atts['ajaxurl'] = admin_url( 'admin-ajax.php' );
		$atts['action'] = 'ecs_calendar_events';
		$atts['firstload'] = false;
		$output .= "tecEventCalendarSettings['" . $calendar_id . "'] = " . json_encode( $atts ) . ";";

		// Start the JS array for the events in this calendar.  Create a unique ID for each.
		$output .= "tecsEvents['" . $calendar_id . "'] = [";
	} else {
		$output .= '[';
	}
	return $output;
}

function ecs_end_tag_calendar( $output, $atts, $post ) {
	$output = '';
	if ( ! defined( 'DOING_AJAX' ) or ! DOING_AJAX ) {
		// End the array from the start tag
		$output = '];</script>';
	} else {
		$output .= ']';
	}
	return $output;
}
