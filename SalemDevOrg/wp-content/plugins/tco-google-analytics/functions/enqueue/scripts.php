<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Enqueue all scripts for the Google Analytics.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Site Scripts
// =============================================================================

// Enqueue Site Scripts
// =============================================================================

function tco_google_analytics_enqueue_admin_scripts( $hook ) {

  $hook_prefixes = array(
    'addons_page_x-extensions-google-analytics',
    'theme_page_x-extensions-google-analytics',
    'x_page_x-extensions-google-analytics',
    'x_page_tco-extensions-google-analytics',
    'x-pro_page_x-extensions-google-analytics',
    'pro_page_tco-extensions-google-analytics',
    'tco-extensions-google-analytics',
    'settings_page_tco-extensions-google-analytics',
  );

  if ( in_array($hook, $hook_prefixes) ) {

    wp_enqueue_script( 'postbox' );
    wp_enqueue_script( 'tco-google-analytics-admin-js', TCO_GOOGLE_ANALYTICS_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );

  }

}

add_action( 'admin_enqueue_scripts', 'tco_google_analytics_enqueue_admin_scripts' );
