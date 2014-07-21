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
define('DB_NAME', 'aieDB8s0ve');

/** MySQL database username */
define('DB_USER', 'aieDB8s0ve');

/** MySQL database password */
define('DB_PASSWORD', 'VPYVgpgpmx');

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
define('AUTH_KEY',         '~wO15#;.PW9DDL;mtWeaiLS]t+x~hpH]1.<qyP2AAE<;bPXXeDLx*ut+ai;6]bjIQ');
define('SECURE_AUTH_KEY',  'mpa;*+pDH5.y$PEA;2qPbLP<mximE.JNBjUgjY^v$^7>37{UIMJ>cRVgFzor@c4,>');
define('LOGGED_IN_KEY',    '+A{A*TIBF3nMUIM,uqfj7{<yQTI0^YcQMBF@cjYc3z,>yQ}0|NCFR0oYcoN|>z,');
define('NONCE_KEY',        'H9tieO#]~#lDH5iXb2y.uxP;AD2aPTePxmqnM.<y.jAE3.<fPLAE$bYcQyjuyn,');
define('AUTH_SALT',        'X$yrY}^rF}3,jUcM^ngRszk4|}@cNUFvgVGzoZ}z!rRCJ4oVO8shR|w-k84C:gNH1');
define('SECURE_AUTH_SALT', 'H52mSaL~te;~xuI2A]bLTDxmW]xqbA{3.TEM6qbP.u8[cNgR|vzk8>@RB1!VGNK~o');
define('LOGGED_IN_SALT',   '1C_d~p1eSdd~xm_;iPI2miqb{+qE;2.XTM7rXfQ,yj7.{$bMF0jQYJjrb}$uUF@k');
define('NONCE_SALT',       'L3nMXIM,jqfjA^{$^X637}gFQUJrgnrf>$^>$F374|NCFR0orcoN,>@,kBF4hR[:!');

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
