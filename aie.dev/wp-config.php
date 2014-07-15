<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'aieDBilz71');

/** MySQL database username */
define('DB_USER', 'aieDBilz71');

/** MySQL database password */
define('DB_PASSWORD', 'lmDPMaSx8A');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '5x;~teqaL62LH2*+62*pm*+iTPbXEA<EA<.q;{yueyubXIUQ73^I{$j3^qnT$jUQ');
define('SECURE_AUTH_KEY',  'X2+{>ufbvfbIEfbM3UE{<u0^njQnkQN3jQB7,M3$z>vsYVsZVC8YUB7>B8,vr@');
define('LOGGED_IN_KEY',    'd4N8}8:s#lZKa9[S1|C:sdthSDP9]OD:~]-laqaLXH2.L6#x.teTiTEPA{*E;ue+q');
define('NONCE_KEY',        'G:~;+xi+xiPLliS95PL2;*{<yebuqbIEeaHD]HE]<u.^njQnjQM3PM2.*3$yfvr');
define('AUTH_SALT',        'opaWD9SD9#_G:[wh]wtZWmSP62L62*+52~pl*+iSObXEA<EA<.qm]xtaxuaXDAMI3');
define('SECURE_AUTH_SALT', 'mHDTA6.^7,^qX<uqbIqXTA6M3$y}$zfc$njQMfQM3B7,^r,!oYUvrYUFYUB7,40');
define('LOGGED_IN_SALT',   'sZJnURBVF0}@[-wg-dZKoVR|[-pZwWG1VK5|1_sD:sd-pZKdO8VK5|5+laxiHlWK5');
define('NONCE_SALT',       '2u{+yiTxePLiPL2;E{<uq<uqXUyfbIEXTA{<3$yj$zfQMnjQM3IF{$y,vrYUoYU');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
