<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', '2011279_destsal' );

/** MySQL database username */
define( 'DB_USER', '2011279_destsal' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Dest1nat0n--S4l3m' );

/** MySQL hostname */
define( 'DB_HOST', 'mariadb-052.wc1.lan3.stabletransit.com' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );



/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '{%tCzh!/n4lYnKk]vwz-DsjGi}P@N<%Y2G77i)htB2{]OwQ|vGK#x<NF.*78>,]L');
define('SECURE_AUTH_KEY',  '7e{N0jj(K:J_w+|IzYDhKq]-dNiw7#<5<FX[gnPkgjoPSPJ]7wGl:!(IF-iB-|!N');
define('LOGGED_IN_KEY',    'k.F-8|T,ooP:d1Hm@oMh{88~7 tT1vF>B<~{~_ES:w?XLucr%:W-KDN]HAtC:}L_');
define('NONCE_KEY',        'z>6dIq1L}9-CKd(NR5hXfj3qm+O@#3k(Dd,cT?vIQL?+|ZOlKYTo:W/)c4#q!z?j');
define('AUTH_SALT',        '8I=|&vlQ0:R+j=U^.DhW?E0-SQLA~NG>m[1H]cJug+P,!uRi^B2TdYcxe5 =Yp#%');
define('SECURE_AUTH_SALT', 'KNoQGCUwqc_4p`N0#BDX>*y~0ZmqF:ur=PK*u f-Dox|g8LTjV[v_l5.5%(ceNEJ');
define('LOGGED_IN_SALT',   '|Hc{}blEo>PF-:6M+O6zzv#KgOIE5%]|[(+QP0yU_0d=ti};jSY1vE+^104hP;z0');
define('NONCE_SALT',       '73.4Z:3*67+Ga!Mh4K5X[;#kqju7cWV=`PreO9AVxVI+EDM5baDnq4,k&mT~zSrT');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

define('WP_CACHE', false);

define( 'WP_MEMORY_LIMIT', '256M' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
