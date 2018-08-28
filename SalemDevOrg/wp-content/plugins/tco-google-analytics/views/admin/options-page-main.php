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
              <label for="tco_google_analytics_enable">
                <strong><?php _e( 'Enable Google Analytics', '__tco__' ); ?></strong>
                <span><?php _e( 'Select to enable the plugin and display options below.', '__tco__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="checkbox"</span></legend>
                <input type="checkbox" class="checkbox" name="tco_google_analytics_enable" id="tco_google_analytics_enable" value="1" <?php checked( ! isset( $tco_google_analytics_enable ) ? '0' : $tco_google_analytics_enable, '1', true ); ?>>
              </fieldset>
            </td>
          </tr>

        </table>
      </div>
    </div>

    <!--
    SETTINGS
    -->

    <div id="meta-box-settings" class="postbox" style="display: <?php echo ( isset( $tco_google_analytics_enable ) && $tco_google_analytics_enable == 1 ) ? 'block' : 'none'; ?>;">
      <div class="handlediv" title="<?php _e( 'Click to toggle', '__tco__' ); ?>"><br></div>
      <h3 class="hndle"><span><?php _e( 'Settings', '__tco__' ); ?></span></h3>
      <div class="inside">
        <p><?php _e( 'Select your plugin settings below.', '__tco__' ); ?></p>
        <table class="form-table">

          <tr>
            <th>
              <label for="tco_google_analytics_position">
                <strong><?php _e( 'Position', '__tco__' ); ?></strong>
                <span><?php _e( 'Choose which section of your site you want your Google Analytics code to be output.', '__tco__' ); ?></span>
              </label>
            </th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                <label class="radio-label"><input type="radio" class="radio" name="tco_google_analytics_position" value="head" <?php echo ( isset( $tco_google_analytics_position ) && checked( $tco_google_analytics_position, 'head', false ) ) ? checked( $tco_google_analytics_position, 'head', false ) : 'checked="checked"'; ?>> <span><?php _e( 'Head', '__tco__' ); ?></span></label><br>
                <label class="radio-label"><input type="radio" class="radio" name="tco_google_analytics_position" value="footer" <?php echo ( isset( $tco_google_analytics_position ) && checked( $tco_google_analytics_position, 'footer', false ) ) ? checked( $tco_google_analytics_position, 'footer', false ) : ''; ?>> <span><?php _e( 'Footer', '__tco__' ); ?></span></label>
              </fieldset>
            </td>
          </tr>
          
          <tr>
            <th>
              <label for="tco_google_analytics_code">
                <strong><?php _e( 'Google Analytics ID', '__tco__' ); ?></strong>
                <span><?php _e( 'Input your Google Analytics ID only.', '__tco__' ); ?></span>                
              </label>
            </th>
            <td><input type="text" name="tco_google_analytics_id" id="tco_google_analytics_id" class="code" value="<?php echo ( isset( $tco_google_analytics_id ) ) ?  $tco_google_analytics_id  : ''; ?>">
            </td>
          </tr>

          <tr>
            <th>
              <label for="tco_google_analytics_code">
                <strong><?php _e( 'Meta Tag (optional)', '__tco__' ); ?></strong>
                <span><?php _e( 'If you require meta tags for your google verification you can add them here.', '__tco__' ); ?></span>
              </label>
            </th>
            <td><textarea name="tco_meta_tag" id="tco_meta_tag" class="code"><?php echo ( isset( $tco_meta_tag ) ) ? esc_textarea( $tco_meta_tag ) : ''; ?></textarea>
            </td>
          </tr>


        </table>
      </div>
    </div>

  </div>
</div>