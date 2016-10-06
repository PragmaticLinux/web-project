<?php
/**
 * The base configuration for WordPress
 * =====
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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'pragmatic');

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
define('AUTH_KEY',         '}eHxinGD|QduQpQh0m67FDf;nZ cFUcZ]^N*RD4fDGHq)lV[^F<IZx_XjOLLm/BO');
define('SECURE_AUTH_KEY',  'VslyWyb<U.-!IB7/b=tT|rWH~pohprUDr%~uB>UY7UfVR8uq@3jV2|0Y^6oa4}ij');
define('LOGGED_IN_KEY',    'AV@&G5QbCr_&/BT5EM|AO%Y}B#li}UX/=<x~bUgi?S,Bx;b+N_+gf-7O6N=g(t9A');
define('NONCE_KEY',        '^ d8jM{?{dZH/{DFsWpfj<#BeO^YI|L$([i[}n3?VMQr|ov<G#kK53. y$KxX^^}');
define('AUTH_SALT',        'CL4lU9m$m6mg^90vrKoGjq[PnAP{8_)QnKdZ&5aH%k|1M_%$lAq[FxH/ZeASXop7');
define('SECURE_AUTH_SALT', 'ED]3n/n?!5,DXP5q)w`:;e.@KIC,<s)-q@=*wy4$|BAB+3M-todq;Mw$XVUKNPH(');
define('LOGGED_IN_SALT',   'Ki:.@kV3iFm)4`Glj}w56}iAy:_:`ig$M~4+K(sS>>Ik}exm8E:&dt>~ymPzqhNx');
define('NONCE_SALT',       'K OlRge98uL`~yR-NUMrE!J*mh!e50oKH2cc=%d#B6FHyN-I<1y~FdFOr0p7G@nG');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'pl_';

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
