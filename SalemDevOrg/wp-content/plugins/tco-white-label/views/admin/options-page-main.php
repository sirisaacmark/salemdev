<?php

// =============================================================================
// VIEWS/ADMIN/OPTIONS-PAGE-MAIN.PHP
// -----------------------------------------------------------------------------
// Plugin options page main content.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Main Content
// =============================================================================

// Main Content
// =============================================================================

?>

<div id="post-body-content">
  <div class="meta-box-sortables ui-sortable">

    <!--
    ENABLE
    -->

    <div id="meta-box-enable" class="postbox">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__tco__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Enable', '__tco__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select the checkbox below to enable the plugin.', '__tco__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="tco_white_label_enable">
                <strong><?php _e( 'Enable White Label', '__tco__' ); ?></strong>
                <span><?php _e( 'Select to enable the plugin and display options below.', '__tco__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="tco_white_label_enable" id="tco_white_label_enable" value="1" <?php checked( ! isset( $tco_white_label_enable ) ? '0' : $tco_white_label_enable, '1', true ); ?>>
              </fieldset>
            </td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    SETTINGS
    -->

    <div id="meta-box-settings" class="postbox" style="display: <?php echo ( isset( $tco_white_label_enable ) && $tco_white_label_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__tco__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Settings', '__tco__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select your plugin settings below.', '__tco__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="tco_white_label_login_image">
                <strong><?php _e( 'Login Image', '__tco__' ); ?></strong>
                <span><?php _e( 'Enter the URL to an image that you would like to use in place of the standard WordPress login image (must be less than 320px wide).', '__tco__' ); ?></span>
              </label>
            </th>
            <td>
              <input type="text" class="file large-text" name="tco_white_label_login_image" id="tco_white_label_login_image" value="<?php echo ( isset( $tco_white_label_login_image ) ) ? $tco_white_label_login_image : ''; ?>">
              <input type="button" id="_tco_white_label_login_image_image_upload_btn" data-id="tco_white_label_login_image" class="button-secondary tco-upload-btn-wl" value="Upload Image">
              <div class="tco-meta-box-img-thumb-wrap" id="_tco_white_label_login_image_thumb">
                  <?php if ( isset( $tco_white_label_login_image ) && ! empty( $tco_white_label_login_image ) ) : ?>
                     <div class="tco-uploader-image"><img src="<?php echo $tco_white_label_login_image ?>" alt="" /></div>
                  <?php endif ?>
              </div>
            </td>
          </tr>

          <tr>
            <th>
              <label for="tco_white_label_retina_enabled">
                <strong><?php _e( 'Retina support for logo', '__tco__' ); ?></strong>
                <span><?php _e( 'Enable retina support for logo. Size will be divided by 2 in non-retina devices.', '__tco__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="tco_white_label_retina_enabled" id="tco_white_label_retina_enabled" value="1" <?php checked( ! isset( $tco_white_label_retina_enabled ) ? '0' : $tco_white_label_retina_enabled, '1', true ); ?>>
              </fieldset>
            </td>
          </tr>

          <tr>
	          <th>
		          <label for="tco_white_label_login_bg_image">
		          	<strong><?php _e( 'Login Background Image', '__tco__' ); ?></strong>
		          	<span><?php _e( 'Enter the URL to an image that you would like to use as a background image on the WordPress login screen,', '__tco__' ); ?></span>
		          </label>
	          </th>
	          <td>
              <input type="text" class="file large-text" name="tco_white_label_login_bg_image" id="tco_white_label_login_bg_image" value="<?php echo ( isset( $tco_white_label_login_bg_image ) ) ? $tco_white_label_login_bg_image : ''; ?>">
              <input type="button" id="_tco_white_label_login_bg_image_image_upload_btn" data-id="tco_white_label_login_bg_image" class="button-secondary tco-upload-btn-wl" value="Upload Image">
              <div class="tco-meta-box-img-thumb-wrap" id="_tco_white_label_login_bg_image_thumb">
                  <?php if ( isset( $tco_white_label_login_bg_image ) && ! empty( $tco_white_label_login_bg_image ) ) : ?>
                     <div class="tco-uploader-image"><img src="<?php echo $tco_white_label_login_bg_image ?>" alt="" /></div>
                  <?php endif ?>
              </div>
            </td>
          </tr>

        </table>
      </div>
    </div>

  </div>
</div>
