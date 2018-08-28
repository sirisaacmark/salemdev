<?php

/*

Plugin Name: Google Analytics
Plugin URI: http://theme.co/
Description: Simply drop in your Google Analytics code snippet, select where you'd like it to be output, and you're good to go! Google Analytics made easy.
Version: 3.0.0
Author: Themeco
Author URI: http://theme.co/
Text Domain: __tco__
Themeco Plugin: tco-google-analytics

*/

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Define Constants and Global Variables
//   02. Setup Menu
//   03. Initialize
// =============================================================================

// Define Constants and Global Variables
// =============================================================================

//
// Constants.
//

define( 'TCO_GOOGLE_ANALYTICS_VERSION', '3.0.0' );
define( 'TCO_GOOGLE_ANALYTICS_URL', plugins_url( '', __FILE__ ) );
define( 'TCO_GOOGLE_ANALYTICS_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );


//
// Global variables.
//

$tco_google_analytics_options = array();



// Setup Menu
// =============================================================================

function tco_google_analytics_options_page() {
  require( 'views/admin/options-page.php' );
}

function tco_google_analytics_menu() {
  add_options_page( __( 'Google Analytics', '__tco__' ), __( 'Google Analytics', '__tco__' ), 'manage_options', 'tco-extensions-google-analytics', 'tco_google_analytics_options_page' );
}

function x_tco_google_analytics_menu() {
  add_submenu_page( 'x-addons-home', __( 'Google Analytics', '__tco__' ), __( 'Google Analytics', '__tco__' ), 'manage_options', 'tco-extensions-google-analytics', 'tco_google_analytics_options_page' );
}

$theme = wp_get_theme(); // gets the current theme
$is_pro_theme = ( 'Pro' == $theme->name || 'Pro' == $theme->parent_theme );
$is_x_theme = function_exists( 'CS' );
add_action( 'admin_menu', ( $is_pro_theme || $is_x_theme ) ? 'x_tco_google_analytics_menu' : 'tco_google_analytics_menu', 100 );



// Initialize
// =============================================================================

function tco_google_analytics_init() {

  //
  // Textdomain.
  //

  load_plugin_textdomain( '__tco__', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );


  //
  // Styles and scripts.
  //

  require( 'functions/enqueue/styles.php' );
  require( 'functions/enqueue/scripts.php' );


  //
  // Notices.
  //

  require( 'functions/notices.php' );


  //
  // Output.
  //

  require( 'functions/output.php' );

}

add_action( 'init', 'tco_google_analytics_init' );

//
// Activate hook.
//

function tco_google_analytics_activate () {
  $x_plugin_basename = 'x-google-analytics/x-google-analytics.php';

  if ( is_plugin_active( $x_plugin_basename ) ) {
    $tco_data = get_option('tco_google_analytics');
    $x_data = get_option('x_google_analytics');
    if (empty($tco_data) && !empty($x_data)) {
      $tco_data = array();
      foreach($x_data as $key => $value) {
        $key = str_replace('x_', 'tco_', $key);
        $tco_data[ $key ] = $value;
      }
      update_option( 'tco_google_analytics', $tco_data );
    }
    deactivate_plugins( $x_plugin_basename );
  }
}

register_activation_hook( __FILE__, 'tco_google_analytics_activate' );
