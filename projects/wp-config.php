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
define('DB_NAME', 'jdavisbl_wor7');

/** MySQL database username */
define('DB_USER', 'jdavisbl_wor7');

/** MySQL database password */
define('DB_PASSWORD', 'TQ0qA4cO');

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
define('AUTH_KEY',         's]|N0Xb-ec=+vB;.a#XAOPSEu06Q45u=UEYp_Lj[cc#-2(_NGBj:ao4#9xOleaTB');
define('SECURE_AUTH_KEY',  'GRC.:{):BWr/:+w~%VN>}|lRRS{woK!{=sCe;]OLmK+$+Kc+~a5~+<<siUvKr#Np');
define('LOGGED_IN_KEY',    '554f wbfLwnU(Cn=o~5)n1zcfy|5x j@U@V-Nab 2N3@}(Ctn4L34^<7)Mk?~~lT');
define('NONCE_KEY',        'yV/3#{,k9GLN,17W-<zWhs$T:5YSzrNxXuE~sOetT$|ugs(EaLoLQw|/,GoW4!&m');
define('AUTH_SALT',        '`!.Q;os2-b+0Cma`wP!vH,f^4dq>UINGgB)*Gl#-ewnkosUcBSE5oaV+xzehb0;Z');
define('SECURE_AUTH_SALT', 'nx;n;ljM&1Tux*}F`%sou`J)(?DT] :np7UiI[m2uy>[p-j+}03zwX{Z`,@|ArG:');
define('LOGGED_IN_SALT',   '-(7oQ&^zn3F*w^m14el(/25UKCy>&>Arf,+G|Q7wTU5?N|^d=UEEw@Z 9Gk6Pt-9');
define('NONCE_SALT',       'J5G!q3sKS|j8y..ErI!=<Rk/}+)^_jF|5jS}Is{-_=xgvaGquV^`7U{Ij{/v9K|e');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'vrq_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
