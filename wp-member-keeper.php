<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wonkasoft.com/
 * @since             1.0.0
 * @package           Wp_Member_Keeper
 *
 * @wordpress-plugin
 * Plugin Name:       WP Member Keeper
 * Plugin URI:        https://wonkasoft.com/wp-member-keeper/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Wonkasoft, LLC
 * Author URI:        https://wonkasoft.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-member-keeper
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || exit;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_MEMBER_KEEPER_VERSION', '1.0.0' );
define( 'WP_MEMBER_KEEPER_PATH', plugin_dir_path(__FILE__) );
define( 'WP_MEMBER_KEEPER_NAME', ucwords( str_replace( 'wp', 'WP', str_replace( '-', ' ', plugin_basename( dirname(__FILE__) ) ) ) ) );
define( 'WP_MEMBER_KEEPER_BASENAME', plugin_basename(__FILE__) );
define( 'WP_MEMBER_KEEPER_URI', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-member-keeper-activator.php
 */
function activate_wp_member_keeper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-member-keeper-activator.php';
	Wp_Member_Keeper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-member-keeper-deactivator.php
 */
function deactivate_wp_member_keeper() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-member-keeper-deactivator.php';
	Wp_Member_Keeper_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_member_keeper' );
register_deactivation_hook( __FILE__, 'deactivate_wp_member_keeper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-member-keeper.php';

if ( get_option( 'wp_member_keeper_database_version' ) !== WP_MEMBER_KEEPER_VERSION ) :
	activate_wp_member_keeper();
endif; 
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_member_keeper() {

	$plugin = new Wp_Member_Keeper();
	$plugin->run();

}
run_wp_member_keeper();
