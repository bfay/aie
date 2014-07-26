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
define('DB_NAME', 'aieDBltk60');

/** MySQL database username */
define('DB_USER', 'aieDBltk60');

/** MySQL database password */
define('DB_PASSWORD', '5jEPv5YmEP');

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
define('AUTH_KEY',         'O1dZp1S-[#Op5GCl|UQf<IrnyEf<.nyuAM.{Fg>73c$FBM,3cYjBMIr^mxu6L.;');
define('SECURE_AUTH_KEY',  '|lKZ|8koJY,7jFfbr3U$y,Nc|!}kzB7J!}YUg7JFn@iuq2T+{<Pq2;Em<Peb.EXUj');
define('LOGGED_IN_KEY',    '}!4Vk4JvVk0Fv,RUEq^Pf{q*Pf{m+Fv,Qf^br7Q${q]2x5O1S5OtSZ;T6DtWeH');
define('NONCE_KEY',        'nz}IXy<Xm2HuTi;Ht.Qq^Mb{AmMb<6i+Wl1HhxDS_1dtS~:Pe]9mp5Lx]WlLx#k-');
define('AUTH_SALT',        'zUv>U|4gzFV!|Rg0Fr!gAQ${bq6Mm$IX.6iyFrQf{Br^Mb${XqLa#l+HW#5ix1Ht');
define('SECURE_AUTH_SALT', 'nQg62E+<TPb2EAiyPbX*;mxu<7u^$Ef{73b$EAMy3bXjGS~-|do51C-|!NZ1C8g!5');
define('LOGGED_IN_SALT',   '+et9THx]Wpe;HiXm6P*u<Tm6{Eu<THa]E2w#Wpd:Cs|-KZ[C1h-K8Rp_SHa#9pexH');
define('NONCE_SALT',       'V0Co@k_:Zp1Gs_OZ|5hOdy>Qf{7jyEU${XnUr!NY,4gr7Nz>Qg}<6iyAP+{Ti;Dm+');

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
