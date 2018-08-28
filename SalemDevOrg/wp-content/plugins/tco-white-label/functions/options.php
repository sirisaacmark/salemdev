<?php

// =============================================================================
// FUNCTIONS/OPTIONS.PHP
// -----------------------------------------------------------------------------
// Plugin options.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Set Options
//   02. Get Options
// =============================================================================

// Set Options
// =============================================================================

//
// Set $_POST variables to options array and update option.
//

GLOBAL $tco_white_label_options;

if ( isset( $_POST['tco_white_label_form_submitted'] ) ) {
  if ( strip_tags( $_POST['tco_white_label_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) {

    $kses_allowed_tags = array(
      'div'    => array( 'class' => array() ),
      'p'      => array( 'class' => array() ),
      'h1'     => array( 'class' => array() ),
      'h2'     => array( 'class' => array() ),
      'h3'     => array( 'class' => array() ),
      'h4'     => array( 'class' => array() ),
      'h5'     => array( 'class' => array() ),
      'h6'     => array( 'class' => array() ),
      'a'      => array( 'class' => array(), 'href' => array(), 'target' => array() ),
      'img'    => array( 'class' => array(), 'src' => array() ),
      'span'   => array( 'class' => array() ),
      'em'     => array( 'class' => array() ),
      'strong' => array( 'class' => array() ),
      'style'  => array(),
    );

    $tco_white_label_options['tco_white_label_enable']               = ( isset( $_POST['tco_white_label_enable'] ) ) ? strip_tags( $_POST['tco_white_label_enable'] ) : '';
    $tco_white_label_options['tco_white_label_login_image']          = strip_tags( $_POST['tco_white_label_login_image'] );
    $tco_white_label_options['tco_white_label_login_bg_image']	     = strip_tags( $_POST['tco_white_label_login_bg_image'] );
    $tco_white_label_options['tco_white_label_retina_enabled']       = ( isset( $_POST['tco_white_label_retina_enabled'] ) ) ? strip_tags( $_POST['tco_white_label_retina_enabled'] ) : '';

    update_option( 'tco_white_label', $tco_white_label_options );

  }
}



// Get Options
// =============================================================================

$tco_white_label_options = apply_filters( 'tco_white_label_options', get_option( 'tco_white_label' ) );

if ( $tco_white_label_options != '' ) {

  $tco_white_label_enable               = isset($tco_white_label_options['tco_white_label_enable']) ? $tco_white_label_options['tco_white_label_enable'] : false;
  $tco_white_label_login_image          = isset($tco_white_label_options['tco_white_label_login_image']) ? $tco_white_label_options['tco_white_label_login_image'] : null;
  $tco_white_label_login_bg_image				= isset($tco_white_label_options['tco_white_label_login_bg_image']) ? $tco_white_label_options['tco_white_label_login_bg_image'] : null;
  $tco_white_label_retina_enabled       = isset($tco_white_label_options['tco_white_label_retina_enabled']) ? $tco_white_label_options['tco_white_label_retina_enabled'] : null;
}
