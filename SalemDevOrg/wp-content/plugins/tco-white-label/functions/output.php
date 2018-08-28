<?php

// =============================================================================
// FUNCTIONS/OUTPUT.PHP
// -----------------------------------------------------------------------------
// Plugin output.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. White Label
//   02. Output
// =============================================================================

// White Label
// =============================================================================

function tco_white_label_output() {

  ob_start();

  require( TCO_WHITE_LABEL_PATH . '/views/admin/white-label.php' );

  $output = ob_get_clean();

  echo $output;

}



// Output
// =============================================================================

require( TCO_WHITE_LABEL_PATH . '/functions/options.php' );

if ( isset( $tco_white_label_enable ) && $tco_white_label_enable == 1 ) {

  add_action( 'tco_addons_main_content_start', 'tco_white_label_output' );

}
