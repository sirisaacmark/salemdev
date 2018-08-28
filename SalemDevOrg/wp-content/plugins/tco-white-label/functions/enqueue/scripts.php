<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/SCRIPTS.PHP
// -----------------------------------------------------------------------------
// Plugin scripts.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Admin Scripts
// =============================================================================

// Enqueue Admin Scripts
// =============================================================================

function tco_white_label_enqueue_admin_scripts( $hook ) {

  $hook_prefixes = array(
    'addons_page_x-extensions-white-label',
    'theme_page_x-extensions-white-label',
    'x_page_x-extensions-white-label',
    'x_page_tco-extensions-white-label',
    'x-pro_page_x-extensions-white-label',
    'pro_page_tco-extensions-white-label',
    'tco-extensions-white-label',
    'settings_page_tco-extensions-white-label',
  );

  if ( in_array($hook, $hook_prefixes) ) {

    wp_enqueue_script( 'postbox' );
    wp_enqueue_script( 'tco-white-label-admin-js', TCO_WHITE_LABEL_URL . '/js/admin/main.js', array( 'jquery' ), NULL, true );
    wp_enqueue_media();
  }

}

add_action( 'admin_enqueue_scripts', 'tco_white_label_enqueue_admin_scripts' );
