<?php

// =============================================================================
// FUNCTIONS/OUTPUT.PHP
// -----------------------------------------------------------------------------
// Plugin output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Google Analytics
//   02. Output
// =============================================================================

// Google Analytics
// =============================================================================

function tco_google_analytics_output() {

  require( TCO_GOOGLE_ANALYTICS_PATH . '/views/site/google-analytics.php' );

}



// Output
// =============================================================================

require( TCO_GOOGLE_ANALYTICS_PATH . '/functions/options.php' );

if ( isset( $tco_google_analytics_enable ) && $tco_google_analytics_enable == 1 ) {

  add_action( 'wp_' . $tco_google_analytics_position, 'tco_google_analytics_output', 9999 );

}