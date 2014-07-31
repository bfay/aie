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
define('DB_NAME', 'aieDBgsdx2');

/** MySQL database username */
define('DB_USER', 'aieDBgsdx2');

/** MySQL database password */
define('DB_PASSWORD', 'WcQCbNBK6K');

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
define('AUTH_KEY',         'fEQE3<y3<ynb<+qaPqePE2TI2<+6{+qeiWtiWL9WL9]+m:~l_eSD1SH5]-K8:~s:');
define('SECURE_AUTH_KEY',  '^^j$jUBYF^M2.A{yi^mXIbM7ePA]E;*m.ubLiTElWD;H2_t#xe+mTDpWG:K5#9]-');
define('LOGGED_IN_KEY',    '<iTuiTH6lWK9:O9;_x2_xla+laPDsdSG5WK9:~9:~th_thWK-kZNCdNC0|G1|-o[-');
define('NONCE_KEY',        'x*qb$qfPEfQE3<WL9]*A;.ti*uiTHiXL6]aODD1#xl#xlWLmaL9lVK8[G1#w1#wlW');
define('AUTH_SALT',        '6xi.yiXL+laODaPD2#H2#+p#+qaP~tdSGhWG5[K9]~t;~thW!wkZNlZOC1SC1|-5[');
define('SECURE_AUTH_SALT', '#;*peTteTDthWK9WK9:_9;_xh_xlaL-odRCdSG5[G5[~p[~tdS!wkVJkZJ8}NC0!w');
define('LOGGED_IN_SALT',   'cJ3>J7}^v4>@rgU<$rfUvjbQE3UIB,y3}^vj2<$qfXymbPI6XME3<I7.yn6;.xm');
define('NONCE_SALT',       'jEAP...eL1~2_p.pWtaH;ZG:G:-1~lSpWDoVCVG:-:-l~lVCkVCZG}@0@k@kR@jQk');

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
