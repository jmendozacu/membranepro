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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'printcard_new');

/** MySQL database username */
define('DB_USER', 'printcard_mage');

/** MySQL database password */
define('DB_PASSWORD', '[hz"~_T16]J"L<W');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '[=m?rS%9$?D*_I->f7uWXzG]Gt_^3 ku&Q{)*XM7Z|akQ&iH5zZ3=_/j1T>||Uq1');
define('SECURE_AUTH_KEY',  'Lu#COsA[$)x-6#d80r6!rU?])8Otu1I[z.ahW%-$cG*z<Ke~=,m%JKM>QlFGoRP%');
define('LOGGED_IN_KEY',    'kaO{Ud5g ?h$R%H-+&p4#&(2<|ogHXMXKbPA?qR>YKNffDB)~)yyfej5zy6j.r2v');
define('NONCE_KEY',        '-!C/ryeS.[8S%>X&}oRIvrM@Q]ofl VZ(9d#-ZYt]+:5cTJoDryL@~MU>Z[)+Wc[');
define('AUTH_SALT',        '-UvPQ g}81s_c:TjOX oh`!U Dg`!v^BG{yBFxgBD1;!+J._B^z4?@9q}NJ`eM&@');
define('SECURE_AUTH_SALT', '4p%)?e[Y! ^<vxZk`XtE|S|s?EwN<PUn=Z9x!&>,@m>IcA3.u5;bxwv-G%o2QS^5');
define('LOGGED_IN_SALT',   ';NqyGbKDF~vh.EgZT-2DTc`uW=1,EJ1 !:G&|/d#sx8NW`3$gOJ#)z95V1-|`Z+n');
define('NONCE_SALT',       'Z?7jaYezD4L^|vDCx|*vR%=PJ s.|%c+rJD`tTFY |kp.Ab>i[:}e-oD,$jG,.&|');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
