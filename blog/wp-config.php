<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
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
define( 'DB_NAME', 'u856136346_i8367304_wp6' );

/** Database username */
define( 'DB_USER', 'u856136346_i8367304_wp6' );

/** Database password */
define( 'DB_PASSWORD', 'y?AXzUU8?4V' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY',         'H2DzdjH7zPfQxNi2DdCk0R3kynyC1rvlBjSmZv7f3wyBDFhYXSmOKhHQaGpWMQ6z');
define('SECURE_AUTH_KEY',  'POYz4DVm2hWWLkErSMcYmqYzicoG6audryzCjzZOmIUdFB6ra6um1E4kV74zO2Ib');
define('LOGGED_IN_KEY',    'vObcXWLIQU5optkYRoiW8FJXyV6YOrKwf3dswdy9y4fdJqFNkn1Pzy72sSmuG2ad');
define('NONCE_KEY',        'bqyRdTt9JcHNMR12qPVfoiuw42bB9zDafEsnw4ShTwg4e2psBKLabMi3Z3X3idY2');
define('AUTH_SALT',        'Ccpa2eohv0qec8zzQjRoYPtywsnltOrKIalTXurBw1ijFTLwU6sh5Xwt6PxA6za1');
define('SECURE_AUTH_SALT', 'KXAXXGbFU5WegVxsjnK1wbCWSPoflUWJdnFBpZdL83ak4uH6CXxvmEPu4UKNDEJ8');
define('LOGGED_IN_SALT',   '5RTVOFqjNU8hdU0OI35Xxd6piY9AqV0doRoDZ33Ao9o4FzUM7Z8IpbZPoTsim0TH');
define('NONCE_SALT',       'xn4ujrtBblecQxRSHKfckt82tb54ZFjlzMHjE17fh6ZPnG4r3y7X4bdN4YcQ1bac');

/**
 * Other customizations.
 */
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');


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



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
