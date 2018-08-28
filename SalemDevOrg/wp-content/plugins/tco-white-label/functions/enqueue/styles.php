<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Plugin styles.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Output Login Styles
//   02. Enqueue Admin Styles
// =============================================================================

// Output Login Image Styles
// =============================================================================

function tco_white_label_output_login_styles() {

  require( TCO_WHITE_LABEL_PATH . '/functions/options.php' );

  if ( isset( $tco_white_label_enable ) && $tco_white_label_enable == 1 ) {
    if ( $tco_white_label_login_image != '' ) {

      $image  = getimagesize( $tco_white_label_login_image );
      $width   = ( ( $tco_white_label_retina_enabled ) ? $image[0] / 2 : $image[0] ) . 'px';
      $height  = ( ( $tco_white_label_retina_enabled ) ? $image[1] / 2 : $image[1] ) . 'px';
      $size   = $width . ' ' . $height;

      ?>

      <style id="tco-white-label-login-css" type="text/css">

        body.login div#login h1 a {
          width: <?php echo $width; ?>;
          height: <?php echo $height; ?>;
          background-image: url(<?php echo $tco_white_label_login_image; ?>);
          -webkit-background-size: <?php echo $size; ?>;
                  background-size: <?php echo $size; ?>;
        }

      </style>

    <?php }
  }

}

add_action( 'login_enqueue_scripts', 'tco_white_label_output_login_styles' );

// Output Background image styles
// =============================================================================

function tco_white_label_output_background_styles() {

	require( TCO_WHITE_LABEL_PATH . '/functions/options.php' );

	if ( isset( $tco_white_label_enable) && $tco_white_label_enable == 1 ) {
		if ( $tco_white_label_login_bg_image != '' ) {

			$image	= getimagesize( $tco_white_label_login_bg_image );
			$width	= $image[0] . 'px';
			$height	= $image[1] . 'px';
			$size		= $width . ' ' . $height;

			?>

			<style id="tco-white-label-background-css" type="text/css">

				body.login {
					background-image: url(<?php echo $tco_white_label_login_bg_image; ?>);
					background-repeat: no-repeat;
					background-attachement: fixed;
					background-position: center;
				}

			</style>

		<?php }
	}

}

add_action( 'login_enqueue_scripts', 'tco_white_label_output_background_styles' );

// Enqueue Admin Styles
// =============================================================================

function tco_white_label_enqueue_admin_styles( $hook ) {

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

    wp_enqueue_style( 'postbox' );
    wp_enqueue_style( 'tco-white-label-admin-css', TCO_WHITE_LABEL_URL . '/css/admin/style.css', NULL, NULL, 'all' );

  }

}

add_action( 'admin_enqueue_scripts', 'tco_white_label_enqueue_admin_styles' );
