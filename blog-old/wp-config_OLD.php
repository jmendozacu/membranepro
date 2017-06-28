<?php
/** 
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information by
 * visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
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
define('DB_NAME', 'blog_printcard_ro');

/** MySQL database username */
define('DB_USER', 'b_printcard_ro');

/** MySQL database password */
define('DB_PASSWORD', 'du3S9LjoXh9Rw');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',        'lu+-A#Py%9KF>}%4kN978NRqylAMNS|LlmKX~N9y|[?+#hoqS+x}rWc+y<WsJJ[S');
define('SECURE_AUTH_KEY', 'ab1w7Of0uD|lSo-VxZ8#r-NVOK`ykI*RIEjK2KcBg;ZPASd~K6%`}v  C|/>V!s_');
define('LOGGED_IN_KEY',   '+jK6MHy^a_KfqL.2%D1i9X-?|Soe3=BcX35+XSqx1:.PojfoNoKS8 _1l/Zrp7?3');
define('NONCE_KEY',       '?,yJ7+Fk2>m3|Z2p%CC3aDz{gXF?3BoFp@KMny+p;u-.v3wAp93Wv0LG,;TDry]C');
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
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', 'ro_RO');

/* That's all, stop editing! Happy blogging. */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
