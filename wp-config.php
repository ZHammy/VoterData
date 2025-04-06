<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db' );

/** Database username */
define( 'DB_USER', 'db' );

/** Database password */
define( 'DB_PASSWORD', 'db' );

/** Database hostname */
define( 'DB_HOST', 'db' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'b-LsNfdzvXg-n1@x5vX@]]f}[sTRqK[_V a^f?#?$Sk|Yw#J!1Z[nno8W!H5`1:3' );
define( 'SECURE_AUTH_KEY',  '<1B&!fZ;#;{h[K.6tst;=cqN:7,w~VJrGH:aWS~o;le1}MXs::kMKe/tq?Hy;3%#' );
define( 'LOGGED_IN_KEY',    ' F/e/P8 18hZQu$/p)AaY0xb{%7NA8Um#U0CBpZf,?AWF6oW=Z4FtU^yM1>o{R6t' );
define( 'NONCE_KEY',        'OCFdSm8<Xy|O8-W!?10dt2%KR=~?]*,sxHJDoP)]JB?kY:r=rv~i1_ Z#@DNTd#8' );
define( 'AUTH_SALT',        'I}~7>;t>Fj`JD(_16k_k61uE<k7elCf,`j:HsC.d;/n 2Q=-%0W--`(qO+-O1<)@' );
define( 'SECURE_AUTH_SALT', 'o[BNs>^ H+eKbOj{l (MR+:0)Y_P`pf#oa}%^{GW_QH[HXt7*p;+w 8i&KHh|CBV' );
define( 'LOGGED_IN_SALT',   'SdMA-,Xv=_P/]QW-!_5|_@_PPs;vFOnCry-d(_ XLj4WfLY ,E|M`q:tyLMOo,<`' );
define( 'NONCE_SALT',       '8*nOaU%7mT/^!DxlY7yt`g!a~~.0/%<ct>T%sKu,qb!Z&Mog+UD._%b.1.FG;lV6' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );
@ini_set( 'display_errors', 1 );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
