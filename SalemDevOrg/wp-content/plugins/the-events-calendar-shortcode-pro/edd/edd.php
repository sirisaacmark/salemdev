<?php

// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'TECS_STORE_URL', 'https://eventcalendarnewsletter.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'TECS_ITEM_NAME', 'The Events Calendar Shortcode' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of the settings page for the license input to be displayed
define( 'TECS_LICENSE_PAGE', 'tecs-license' );

define( 'TECS_LICENSE_KEY_OPTION', 'tecs_license_key' );
define( 'TECS_LICENSE_STATUS_OPTION', 'tecs_license_status' );
define( 'TECS_LICENSE_DATA_OPTION', 'tecs_license_data' );

if ( ! class_exists( 'TECS_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/TECS_SL_Plugin_Updater.php' );
}

function tecs_sl_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( TECS_LICENSE_KEY_OPTION ) );

	// setup the updater
	$edd_updater = new TECS_SL_Plugin_Updater( TECS_STORE_URL, TECS_PLUGIN_FILE, array(
			'version'   => TECS_VERSION,                // current version number
			'license'   => $license_key,         // license key (used get_option above to retrieve from DB)
			'item_name' => TECS_ITEM_NAME, // name of this plugin
			'author'    => 'Brian Hogg Consulting'   // author of this plugin
		)
	);
}
add_action( 'admin_init', 'tecs_sl_plugin_updater', 0 );

function tecs_license_menu() {
	add_options_page( 'The Events Calendar Shortcode', 'The Events Calendar Shortcode', 'manage_options', TECS_LICENSE_PAGE, 'tecs_license_page' );
}
add_action( 'admin_menu', 'tecs_license_menu' );

function tecs_license_page() {
	$license = get_option( TECS_LICENSE_KEY_OPTION );
	$status  = get_option( TECS_LICENSE_STATUS_OPTION );
	?>
	<div class="wrap">
	<h2><?php _e( 'The Events Calendar Shortcode', 'the-events-calendar-shortcode' ); ?></h2>
	<form method="post" action="<?php echo admin_url( 'options-general.php?page=' . TECS_LICENSE_PAGE ) ?>">

		<table class="form-table">
			<tbody>
			<tr valign="top">
				<th scope="row" valign="top">
					<?php _e( 'License Key', 'the-events-calendar-shortcode' ); ?>
				</th>
				<td>
					<input id="<?= TECS_LICENSE_KEY_OPTION ?>" name="<?= TECS_LICENSE_KEY_OPTION ?>" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
					<label class="description" for="<?= TECS_LICENSE_KEY_OPTION ?>"><?php _e( 'Enter your license key', 'the-events-calendar-shortcode' ); ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" valign="top">
					<?php echo esc_html__( 'Activate License', 'the-events-calendar-shortcode' ); ?>
				</th>
				<td>
					<?php if( $status !== false && $status == 'valid' ) { ?>
						<span style="color:green;"><?php echo esc_html__( 'active', 'the-events-calendar-shortcode' ); ?></span>
						<?php wp_nonce_field( 'tecs_nonce', 'tecs_nonce' ); ?>
						<input type="submit" class="button-secondary" name="tecs_license_deactivate" value="<?php echo esc_html__( 'Deactivate License', 'the-events-calendar-shortcode' ); ?>"/>
					<?php } else {
						wp_nonce_field( 'tecs_nonce', 'tecs_nonce' ); ?>
						<input type="submit" class="button button-primary" name="tecs_license_activate" value="<?php echo esc_html__( 'Activate License', 'the-events-calendar-shortcode' ); ?>"/>
					<?php } ?>
				</td>
			</tr>
			</tbody>
		</table>

	</form>
	<?php
}

function tecs_sanitize_license( $new ) {
	$old = get_option( TECS_LICENSE_KEY_OPTION );
	if ( $old != $new ) {
		delete_option( TECS_LICENSE_STATUS_OPTION ); // new license has been entered, so must reactivate
		update_option( TECS_LICENSE_KEY_OPTION, trim( $new ) );
	}
	return $new;
}

/**
 * Show an error message that license needs to be activated
 */
function tecs_check_license() {
    if ( 'valid' != get_option( TECS_LICENSE_STATUS_OPTION ) ) {
        if ( ( ! isset( $_GET['page'] ) or TECS_LICENSE_PAGE != $_GET['page'] ) )
            add_action( 'admin_notices', 'tecs_activate_notice' );
    }
}
add_action( 'admin_init', 'tecs_check_license' );

function tecs_activate_notice() {
	echo '<div class="error"><p>' .
	     sprintf( esc_html__( 'The Events Calendar Shortcode PRO license needs to be activated. %sActivate Now%s or %sGet a License%s', 'the-events-calendar-shortcode' ), '<a href="' . admin_url( 'options-general.php?page=' . TECS_LICENSE_PAGE ) . '">', '</a>', '<a href="https://eventcalendarnewsletter.com/the-events-calendar-shortcode/?utm_campaign=activate-prompt&utm_source=plugin#pricing">', '</a>' ) .
	     '</p></div>';
}

function tecs_check_license_still_valid() {

	global $wp_version;

	$check_option_key = 'tecs_check_option_key';

	if ( get_option( $check_option_key ) and get_option( $check_option_key ) > current_time( 'timestamp' ) )
		return;

	$license = trim( get_option( TECS_LICENSE_KEY_OPTION ) );

	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( TECS_ITEM_NAME ),
		'url'       => home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( TECS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	if ( is_wp_error( $response ) ) {
		update_option( $check_option_key, current_time( 'timestamp' ) + ( 60 * 60 * 2 ) );
		return;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if ( ! is_object( $license_data ) ) {
		update_option( $check_option_key, current_time( 'timestamp' ) + ( 60 * 60 * 2 ) );
		return;
	}

	update_option( TECS_LICENSE_STATUS_OPTION, $license_data->license );
	update_option( TECS_LICENSE_DATA_OPTION, $license_data );

	update_option( $check_option_key, current_time( 'timestamp' ) + ( 60 * 60 * 24 ) );
}
add_action( 'admin_init', 'tecs_check_license_still_valid' );

function tecs_get_license_data( $key ) {
	$license_data = get_option( CHIMPBRIDGE_LICENSE_DATA );
	if ( is_object( $license_data ) and isset( $license_data->$key ) )
		return $license_data->$key;
	return false;
}

function tecs_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['tecs_license_activate'] ) ) {

		// run a quick security check
		if( ! check_admin_referer( 'tecs_nonce', 'tecs_nonce' ) )
			return; // get out if we didn't click the Activate button

		// see if there's a new license key to save
		if ( isset( $_POST[TECS_LICENSE_KEY_OPTION] ) )
			tecs_sanitize_license( sanitize_text_field( $_POST[TECS_LICENSE_KEY_OPTION] ) );

		// retrieve the license from the database
		$license = trim( esc_html( get_option( 'tecs_license_key' ) ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( TECS_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( TECS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'the-events-calendar-shortcode' );
			}

		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {

					case 'expired' :

						$message = sprintf(
							esc_html__( 'Your license key expired on %s.', 'the-events-calendar-shortcode' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked' :

						$message = esc_html__( 'Your license key has been disabled.', 'the-events-calendar-shortcode' );
						break;

					case 'missing' :

						$message = esc_html__( 'Invalid license.', 'the-events-calendar-shortcode' );
						break;

					case 'invalid' :
					case 'site_inactive' :

						$message = esc_html__( 'Your license is not active for this URL.', 'the-events-calendar-shortcode' );
						break;

					case 'item_name_mismatch' :

						$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'the-events-calendar-shortcode' ), TECS_ITEM_NAME );
						break;

					case 'no_activations_left':

						$message = esc_html__( 'Your license key has reached its activation limit.', 'the-events-calendar-shortcode' );
						break;

					default :

						$message = esc_html__( 'An error occurred, please try again.', 'the-events-calendar-shortcode' );
						break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'options-general.php?page=' . TECS_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'tecs_sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"

		update_option( 'tecs_license_status', $license_data->license );
		wp_redirect( admin_url( 'options-general.php?page=' . TECS_LICENSE_PAGE ) );
		exit();
	}
}
add_action( 'admin_init', 'tecs_activate_license' );


function tecs_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['tecs_license_deactivate'] ) ) {

		// run a quick security check
		if( ! check_admin_referer( 'tecs_nonce', 'tecs_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( esc_html( get_option( 'tecs_license_key' ) ) );


		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode( TECS_ITEM_NAME ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( TECS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = esc_html__( 'An error occurred, please try again.', 'the-events-calendar-shortcode' );
			}

			$base_url = admin_url( 'options-general.php?page=' . TECS_LICENSE_PAGE );
			$redirect = add_query_arg( array( 'tecs_sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'tecs_license_status' );
		}

		wp_redirect( admin_url( 'options-general.php?page=' . TECS_LICENSE_PAGE ) );
		exit();

	}
}
add_action( 'admin_init', 'tecs_deactivate_license' );


/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function tecs_admin_notices() {
	if ( isset( $_GET['tecs_sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['tecs_sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo esc_html( $message ); ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}
add_action( 'admin_notices', 'tecs_admin_notices' );