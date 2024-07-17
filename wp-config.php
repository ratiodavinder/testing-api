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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testingapi' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '~3g0&+<DvBcmG(RJM-<DtTw_d3Q0DGU:n}cSq4ms^DIhztgX!a++[^,fBYtr.;]v' );
define( 'SECURE_AUTH_KEY',  'W<]&AmTb<JbOKKuuVMOf*x?+vPjGx3(>/+u5BYfNXmVcKP$F8TB0nq0-{G8vlb?h' );
define( 'LOGGED_IN_KEY',    'E&0U-<eC/4v^$X>-sg&> T V,O]vVHx=epdElk_7[n~D/?wX7xBYdS$ahuydTdcP' );
define( 'NONCE_KEY',        '0z1&6J#IvcXdNEFFU?;KJa=g19C6( )@R]^aKQf{A(5eWP(F5zR11=b>qmt/3xt<' );
define( 'AUTH_SALT',        'QPx[(;uq4hU@|oIL|j!!RP)zI</jFDItq$ZM&-8qnqZS$lQ*J_[,e$EVIBk(-1S7' );
define( 'SECURE_AUTH_SALT', '&s/^2<4NTn s#-ERskTW6Ob&x&uE25xN|o+!x0Q4KTt9}rc2IumlYn3l?<2-XX,_' );
define( 'LOGGED_IN_SALT',   '?/$|F<Mn8]G7kR;cKbh-4hiG<YI`U%0d3`lK>,Kgz,Co1H+$?.vl7!|{FEmH*+B)' );
define( 'NONCE_SALT',       '`9,>Nq2vQ,Ix0SukT~1&*F[b<F~p:mC!):TOhML7e!_Cc{;A)199}lQ#[o%y z?{' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
	
/* Add any custom values between this line and the "stop editing" line. */


define('JWT_AUTH_SECRET_KEY', '].&.}dDeMjf-;$r)hLdfJ)!vkG-N-hHg{T.wvWAs2ewpgq8U<;G2K~)`}5^Q 5+a');
define('JWT_AUTH_CORS_ENABLE', true);


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';




