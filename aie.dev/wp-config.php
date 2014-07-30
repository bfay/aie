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
define('DB_NAME', 'aieDBxoz3i');

/** MySQL database username */
define('DB_USER', 'aieDBxoz3i');

/** MySQL database password */
define('DB_PASSWORD', '4Od4JoguLa');

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
define('AUTH_KEY',         'iWH5#laeSD_x-pO9:1#lZdOC_w-pZ8:1|lZKOC!w-oZ8:0||zkocC04[zYNRF|vko');
define('SECURE_AUTH_KEY',  '5lVKO8:sdRVK[-oshG48[@dRVGV4[}!sRFJ8@rcgU4>z@rQFJ7@ncgU3,z$rQF7$');
define('LOGGED_IN_KEY',    'N[@oZG0!scNJ4|scN84|vcN8>,vgQ7>zjgQB>zjUFB}$jUFB{$nUE{$nXI3^qbXI');
define('NONCE_KEY',        'V|oZN8[oZN8[zYJ4|zkUF,vgUF0^gQB}^rcB}$nbM7>nXI3I3.uTE*qbPA{qbL6<');
define('AUTH_SALT',        'b3,^qXufMI{yifMI{yueLI;{+ieLH;*+ieL62*+iTP62*+ieP52~+ieO51~-hSO');
define('SECURE_AUTH_SALT', '<iPA6.*mXTA6.*qXTA6.*pWT96_*pWS95_~pWSaWH:]wtdKG:[wtdKG:[-hdKG:~-');
define('LOGGED_IN_SALT',   'p9:tdO9:~dO8[~oZK[-kVK4|kVG0!wgRC}scN8}@cN8>znYJ4,,vfUF^fQB{^rbM');
define('NONCE_SALT',       'aH1_plSO9#_tZWC9#_sZVC8|wsZVC8|wsZVC:[wsdJG:[wgcJF}[zgcJF}@zgcJ40');

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
define( 'WP_MEMORY_LIMIT', '256M' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
