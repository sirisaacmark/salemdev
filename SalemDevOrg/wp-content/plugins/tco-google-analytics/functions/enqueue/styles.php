<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Enqueue all styles for the Google Analytics.
// =============================================================================

// Register and Enqueue Site Styles
// =============================================================================

function tco_google_analytics_enqueue_admin_styles( $hook ) {

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

    wp_enqueue_style( 'postbox' );
    wp_enqueue_style( 'tco-google-analytics-admin-css', TCO_GOOGLE_ANALYTICS_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'tco_google_analytics_enqueue_admin_styles' );
