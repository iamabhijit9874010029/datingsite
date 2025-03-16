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
define( 'DB_NAME', 'datesite' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Nopass@123' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'Py:5!8cPT^zzI;MxK:rk&#X1qwOv AVL|nLN(0v:%$lIJM_LoiT.`)XV%qs+~jQ%' );
define( 'SECURE_AUTH_KEY',  '|WG|^hyP+y*Q_7)uPW/b?R)6 uGY~]#=F^*9&cs$D>RW+Do9;?%zf*61=Yuj>ta0' );
define( 'LOGGED_IN_KEY',    ':ul{H F(cz}7NYHYseO}fd4[pho{{V**25XN9NKY?f6{3qc[%zp` 1ZR]1``{BCH' );
define( 'NONCE_KEY',        'dZ|^biwW}iTAYo=P.tR~7jN7YvZVy<Q+ZNuayL2zsBbHR kb9(hWh[brO|,KiAl ' );
define( 'AUTH_SALT',        '[g8Mzne*j/%E0ho$:4}N5M?#m.+sv|{;3#^aPS_1J%vsrjwa!:*NMN,ZHh.d7-&$' );
define( 'SECURE_AUTH_SALT', 'c wO5sE?YsP,HTEAT17c8sv=qr2ym V;@,1k2>|cXE9&-vjQHFy#!#GLefpM_n0e' );
define( 'LOGGED_IN_SALT',   'Nd^7>A=*xvtc@ZDH>L;?*zIEiYb5VN*z?}2VPOV;V=%=sQQdYg)Lb6!*NRZ}_cdY' );
define( 'NONCE_SALT',       '>u]$>zad;{]LX0vK f=adU13t&$9~|9jeP]edY!gS@MtuR<R `0/ko#r}^tSOswN' );

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
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

