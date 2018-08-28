<?php
/*
Plugin Name: The Events Calendar Shortcode PRO
Plugin URI: https://eventcalendarnewsletter.com/the-events-calendar-shortcode/
Description: Adds shortcode functionality with design options for <a href="http://wordpress.org/plugins/the-events-calendar/">The Events Calendar Plugin by Modern Tribe</a>.
Version: 1.13.2
Author: Event Calendar Newsletter
Author URI: https://eventcalendarnewsletter.com/
License: GPL2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: the-events-calendar-shortcode
*/

define( 'TECS_VERSION', '1.13.2' );
define( 'TECS_PLUGIN_FILE', __FILE__ );

/**
 * Load in any language files that we have setup
 */
function tecsp_load_textdomain() {
	load_plugin_textdomain( 'the-events-calendar-shortcode', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'tecsp_load_textdomain' );

function ecsp_deactivate_free_version_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php echo sprintf( __( 'You need to deactivate and delete The Events Calendar Shortcode plugin on the %splugins page%s', 'the-events-calendar-shortcode' ), '<a href="' . wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=the-events-calendar-shortcode%2Fthe-events-calendar-shortcode.php&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_the-events-calendar-shortcode/the-events-calendar-shortcode.php' ) . '">', '</a>' ); ?></p>
	</div>
	<?php
}

function ecsp_add_core() {
	if ( class_exists( 'Events_Calendar_Shortcode' ) ) {
		add_action( 'admin_notices', 'ecsp_deactivate_free_version_notice' );
		return;
	}

	require_once( dirname( __FILE__ ) . '/core/the-events-calendar-shortcode.php' );
    require_once( dirname( __FILE__ ) . '/includes/custom-fields.php' );
	require_once( dirname( __FILE__ ) . '/includes/filters.php' );
	require_once( dirname( __FILE__ ) . '/includes/designs.php' );
	require_once( dirname( __FILE__ ) . '/edd/edd.php' );

	// Check that The Events Calendar is installed
	global $events_calendar_shortcode;
	$events_calendar_shortcode->verify_tec_installed();
}
add_action( 'plugins_loaded', 'ecsp_add_core' );

// AJAX functionality for efficient loading of many events in the calendar design
require_once( dirname( __FILE__ ) . '/includes/ajax-endpoint.php' );

function ecsp_add_action_links( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'edit.php?post_type=tribe_events&page=ecs-admin' ) . '">' . __( 'Settings', 'the-events-calendar-shortcode' ) . '</a>',
	);
	return array_merge( $links, $mylinks );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ecsp_add_action_links' );

function escp_remove_upgrades( $show_upgrade ) {
	return false;
}
add_filter( 'ecs_show_upgrades', 'escp_remove_upgrades' );