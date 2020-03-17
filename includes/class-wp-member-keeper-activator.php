<?php
/**
 * Fired during plugin activation
 *
 * @link       https://wonkasoft.com/
 * @since      1.0.0
 *
 * @package    Wp_Member_Keeper
 * @subpackage Wp_Member_Keeper/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Member_Keeper
 * @subpackage Wp_Member_Keeper/includes
 * @author     Wonkasoft, LLC <support@wonkasoft.com>
 */
class Wp_Member_Keeper_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );

			$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				id INT(10) NOT NULL AUTO_INCREMENT,
				created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				first_name VARCHAR(50) NULL,
				last_name VARCHAR(50) NULL,
				street_address VARCHAR(150) NULL,
				city VARCHAR(150) NULL,
				state VARCHAR(50) NULL,
				zip VARCHAR(10) NULL,
				phone VARCHAR(10) NULL,
				email VARCHAR(150) NULL,
				birth_date DATE NULL,
				ministries VARCHAR(150) NULL,
				family_id INT(10) NULL,
				PRIMARY KEY (id) ) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );

			update_option( 'wp_member_keeper_database_version', WP_MEMBER_KEEPER_VERSION );
	}

}