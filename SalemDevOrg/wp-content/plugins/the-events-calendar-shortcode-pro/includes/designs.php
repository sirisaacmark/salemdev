<?php

class ECSDesigns {
	const DEFAULT_DESIGN = 'default';

	function __construct() {
		$this->add_filters();
		$this->add_designs();
	}

	function add_filters() {
		add_filter( 'ecs_shortcode_atts', array( $this, 'add_design_attributes' ) );

		// allow the design to change the default attributes
		add_filter( 'ecs_shortcode_atts', array( $this, 'handle_design_filter' ), 10, 2 );

		// beginning/end of the event listing
		add_filter( 'ecs_start_tag', array( $this, 'handle_design_filter' ), 10, 2 );
		add_filter( 'ecs_end_tag', array( $this, 'handle_design_filter' ), 10, 2 );

		// beginning/end of each event
		add_filter( 'ecs_event_start_tag', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_end_tag', array( $this, 'handle_design_filter' ), 10, 3 );

		// venue content
		add_filter( 'ecs_event_venue_tag_start', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_venue_at_tag_start', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_venue_at_text', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_venue_at_tag_end', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_list_venue', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_venue_tag_end', array( $this, 'handle_design_filter' ), 10, 3 );

		// title content
		add_filter( 'ecs_event_title_tag_start', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_list_title', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_title_tag_end', array( $this, 'handle_design_filter' ), 10, 3 );

		// thumbnail
		add_filter( 'ecs_event_thumbnail_link_start', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_thumbnail', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_thumbnail_size', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_thumbnail_link_end', array( $this, 'handle_design_filter' ), 10, 3 );

		// excerpt
		add_filter( 'ecs_event_excerpt_tag_start', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_excerpt', array( $this, 'handle_excerpt_filter' ), 10, 4 );
		add_filter( 'ecs_event_excerpt_tag_end', array( $this, 'handle_design_filter' ), 10, 3 );

		// date/time
		add_filter( 'ecs_event_date_tag_start', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_list_details', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_date_tag_end', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_time_tag_start', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_list_time', array( $this, 'handle_design_filter' ), 10, 3 );
		add_filter( 'ecs_event_time_tag_end', array( $this, 'handle_design_filter' ), 10, 3 );

		// date_thumb content
		add_filter( 'ecs_event_date_thumb', array( $this, 'handle_design_filter' ), 10, 3 );

		// any custom content
		add_filter( 'ecs_event_list_output_custom_button', array( $this, 'handle_design_filter' ), 10, 3 );

		// changing the default and per-event content order
		add_filter( 'ecs_default_contentorder', array( $this, 'handle_design_filter' ), 10, 2 );
		add_filter( 'ecs_event_contentorder', array( $this, 'handle_design_filter' ), 10, 3 );
	}

	function add_designs() {
		require_once( dirname( __FILE__ ) . '/designs/default.php' );
		require_once( dirname( __FILE__ ) . '/designs/compact.php' );
		require_once( dirname( __FILE__ ) . '/designs/grouped.php' );
		require_once( dirname( __FILE__ ) . '/designs/calendar.php' );
		require_once( dirname( __FILE__ ) . '/designs/columns.php' );
		require_once( dirname( __FILE__ ) . '/designs/table.php' );
		do_action( 'add_ecsp_designs' );
	}

	/**
	 * Adds the ability to specify a design in the shortcode attributes via something like:
	 *
	 * [ecs-list-events design="default"]
	 *
	 * also add other attributes like background and foreground colors
	 *
	 * @param $default_atts
	 *
	 * @return mixed
	 */
	function add_design_attributes( $default_atts ) {
		$default_atts['design'] = self::DEFAULT_DESIGN;
		$default_atts['id'] = '';
		$default_atts['bgthumb'] = '#eeeeee';
		$default_atts['fgthumb'] = '#050505';
		$default_atts['titlesize'] = '';
		$default_atts['height'] = 'auto';
		return $default_atts;
	}

	/**
	 * Handle the excerpt filter (4 arguments)
	 *
	 * @param $output
	 * @param $atts
	 * @param $post
	 * @param $excerptLength
	 *
	 * @return mixed
	 */
	function handle_excerpt_filter( $output, $atts, $post, $excerptLength ) {
		if ( ! isset( $atts['design'] ) ) {
			$atts['design'] = self::DEFAULT_DESIGN;
		}
		if ( $atts['design'] and function_exists( current_filter() . '_' . basename( $atts['design'] ) ) )
			$output = call_user_func_array( current_filter() . '_' . basename( $atts['design'] ), array( $output, $atts, $post, $excerptLength ) );
		return $output;
	}

	/**
	 * Handles calling the appropriate function for the current shortcode's design
	 * For example for the ecs_start_tag filter, and design of default, it will
	 * check if function ecs_start_tag_default exists and call it if it does.
	 *
	 * @param $output
	 * @param $atts
	 * @param $post
	 *
	 * @return string
	 */
	function handle_design_filter( $output, $atts, $post = false ) {
		if ( ! isset( $atts['design'] ) ) {
			$atts['design'] = self::DEFAULT_DESIGN;
		}
		if ( $atts['design'] and function_exists( current_filter() . '_' . basename( $atts['design'] ) ) )
			$output = call_user_func_array( current_filter() . '_' . basename( $atts['design'] ), array( $output, $atts, $post ) );
		return $output;
	}
}

$GLOBALS['ecsp_designs'] = new ECSDesigns();