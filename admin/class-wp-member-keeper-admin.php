<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wonkasoft.com/
 * @since      1.0.0
 *
 * @package    Wp_Member_Keeper
 * @subpackage Wp_Member_Keeper/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Member_Keeper
 * @subpackage Wp_Member_Keeper/admin
 * @author     Wonkasoft, LLC <support@wonkasoft.com>
 */
class Wp_Member_Keeper_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $page ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Member_Keeper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Member_Keeper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( strpos( $page, WONKASOFT_PLUGIN_ADMIN_PAGE ) !== false ) :
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-member-keeper-admin.css', array(), $this->version, 'all' );
		endif;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $page ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Member_Keeper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Member_Keeper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( strpos( $page, WONKASOFT_PLUGIN_ADMIN_PAGE ) !== false ) :
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-member-keeper-admin.js', array( 'jquery' ), $this->version, true );
		endif;

	}

	/**
	 * wp_member_keeper_admin_display
	 */
	public function wp_member_keeper_admin_display() {

		/**
		 * This will check for Wonkasoft Tools Menu, if not found it will make it.
		 */
		if ( empty( $GLOBALS['admin_page_hooks']['wonkasoft_menu'] ) ) {

			define( 'WONKASOFT_PLUGIN_ADMIN_PAGE', 'wonkasoft_menu' );
			add_menu_page(
				'Wonkasoft',
				'Wonkasoft Tools',
				'edit_posts',
				'wonkasoft_menu',
				array( $this, 'wp_member_keeper_page_show_settings_page' ),
				plugins_url( '/img/wonka-logo-2.svg', __FILE__ ),
				100
			);

			add_submenu_page(
				'wonkasoft_menu',
				'Member Keeper',
				'Member Keeper',
				'edit_posts',
				'wonkasoft_menu',
				array( $this, 'wp_member_keeper_page_show_settings_page' )
			);

		} else {

			/**
			 * This creates option page in the settings tab of admin menu
			 */
			define( 'WONKASOFT_PLUGIN_ADMIN_PAGE', 'wp_member_keeper_settings_page' );
			add_submenu_page(
				'wonkasoft_menu',
				'Member Keeper',
				'Member Keeper',
				'edit_posts',
				'wp_member_keeper_settings_page',
				array( $this, 'wp_member_keeper_page_show_settings_page' )
			);

		}
	}

	// To display the setting page for this plugin
	public function wp_member_keeper_page_show_settings_page() {
		include plugin_dir_path( __FILE__ ) . 'partials/wp-member-keeper-admin-display.php';
	}

	/**
	 * This function filters the links on the left side of the plugins page in admin menu.
	 *
	 * @param  array $links contains the links.
	 * @return array        returns the filtered links.
	 */
	public function wp_member_keeper_add_settings_link_filter( $links ) {
		$links_addon = '<a href="' . menu_page_url( WONKASOFT_PLUGIN_ADMIN_PAGE, 0 ) . '" target="_self">Settings</a>';
		array_unshift( $links, $links_addon );
		$links[] = '<a href="https://paypal.me/Wonkasoft" target="blank"><img src="' . WP_MEMBER_KEEPER_URI . 'admin/img/wonka-logo.svg' . '" style="width: 20px; height: 20px; display: inline-block;
	    vertical-align: text-top; float: none;" /></a>';
		return $links;
	}

	/**
	 * This function filters the links on the right side of the plugins page in admin menu.
	 *
	 * @param  array  $links contains the links under the description.
	 * @param  string $file  contains the file name of the plugin.
	 * @return array        returns the filtered links.
	 */
	public function wp_member_keeper_add_description_link_filter( $links, $file ) {
		if ( strpos( $file, 'wp-member-keeper.php' ) !== false ) {
			$links[] = '<a href="' . menu_page_url( WONKASOFT_PLUGIN_ADMIN_PAGE, 0 ) . '" target="_self">Settings</a>';
			$links[] = '<a href="https://paypal.me/Wonkasoft" target="blank">Donate <img src="' . WP_MEMBER_KEEPER_URI . 'admin/img/wonka-logo.svg' . '" style="width: 20px; height: 20px; display: inline-block;
	    vertical-align: text-top;" /></a>';
		}
		return $links;
	}

	/**
	 * This function adds new members to the keeper via ajax
	 */
	public function add_member_to_keeper() {

		wp_verify_nonce( wp_unslash( $_POST['_wpmk_nonce'] ), 'wpmk_add_member_form' ) || die( 'Busted!' );

		global $wpdb;

		$info = array();

		foreach ( $_POST as $key => $value ) :
			if ( 'family_id' === $key ) :
				$info[ $key ] = ( ! empty( $value ) ) ? wp_unslash( $value ) : 0;
			else :
				$info[ $key ] = ( ! empty( $value ) ) ? wp_unslash( $value ) : '';
			endif;
		endforeach;

		$info['last_modified'] = new DateTime( null, new DateTimeZone( 'AMERICA/Denver' ) );
		$info['last_modified'] = $info['last_modified']->format( 'Y-m-d H:i:s' );

		$info = json_decode( json_encode( $info ) );

		$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );

		$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE first_name = '$info->first_name' AND last_name = '$info->last_name'", OBJECT );

		$return = array(
			'msg'  => 'Member already exits',
			'data' => $results,
		);
		if ( empty( $results ) ) :
			$results = $wpdb->query(
				$wpdb->prepare(
					"
			   INSERT INTO $table_name
			   ( last_modified, first_name, last_name, street_address, city, state, zip, phone, email, birth_date, ministries, family_id )
			   VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d )
			   ",
					array(
						$info->last_modified,
						$info->first_name,
						$info->last_name,
						$info->street_address,
						$info->city,
						$info->state,
						$info->zip,
						$info->phone,
						$info->email,
						$info->birth_date,
						$info->ministries,
						$info->family_id,
					)
				)
			);

			if ( $results ) :
				$results = $wpdb->get_results( "SELECT * FROM $table_name", OBJECT );
				$return  = array(
					'msg'  => 'Member was recorded. Here is all members.',
					'data' => $results,
				);
			else :
				$return = array(
					'msg'  => 'Error trying to record member.',
					'data' => $results,
				);
			endif;

		endif;

		return wp_send_json_success( $return, 200 );
	}

	/**
	 * This function is the ajax request that allows for member edits.
	 */
	public function edit_member_to_keeper() {

		wp_verify_nonce( wp_unslash( $_POST['_wpmk_edit_nonce'] ), 'wpmk_edit_member_form' ) || die( 'Busted!' );

		global $wpdb;

		$fields = array(
			'id',
			'first_name',
			'last_name',
			'email',
			'phone',
			'street_address',
			'city',
			'zip',
			'birth_date',
			'family_id',
			'ministries',
		);

		$info = array();

		foreach ( $_POST as $key => $value ) :
			if ( 'family_id' === $key ) :
				$info[ $key ] = ( ! empty( $value ) ) ? wp_unslash( $value ) : wp_unslash( $_POST['id'] );
			else :
				if ( in_array( $key, $fields ) ) :
					$info[ $key ] = ( ! empty( $value ) ) ? wp_unslash( $value ) : '';
				endif;
			endif;
		endforeach;

		$info['last_modified'] = new DateTime( null, new DateTimeZone( 'AMERICA/Denver' ) );
		$info['last_modified'] = $info['last_modified']->format( 'Y-m-d H:i:s' );
		$info['state']         = 'CA';

		$where = array(
			'id' => $info['id'],
		);

		$format = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%d',
			'%s',
			'%s',
		);

		$where_format = array(
			'%d',
		);

		$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );

		$results = $wpdb->update(
			$table_name,
			$info,
			$where,
			$format,
			$where_format,
		);

		if ( $results ) :
			$results = $wpdb->get_results( "SELECT * FROM $table_name", OBJECT );
			$return  = array(
				'msg'  => 'Member was recorded. Here is all members.',
				'data' => $results,
			);
		else :
			$return = array(
				'msg'  => 'Error trying to record member.',
				'data' => $results,
			);
		endif;

		return wp_send_json_success( $return, 200 );
	}

	/**
	 * This function is the ajax request that gets member info.
	 */
	public function get_member_from_keeper() {

		wp_verify_nonce( wp_unslash( $_GET['_wpmk_member_table'] ), '_wpmk_member_table' ) || die( 'Busted!' );

		global $wpdb;

		$member_id = ( isset( $_GET['member_id'] ) ) ? wp_unslash( $_GET['member_id'] ) : 0;

		$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );

		$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE id = '$member_id'", OBJECT );

		if ( ! empty( $results ) ) :
			$return = array(
				'msg'  => 'Members info retrived.',
				'data' => $results,
			);
		else :
			$return = array(
				'msg'  => 'No Member found by that ID.',
				'data' => 'none members found.',
			);
		endif;

		return wp_send_json_success( $return, 200 );
	}

	/**
	 * This function is the ajax request that deletes member info.
	 */
	public function delete_member_from_keeper() {

		wp_verify_nonce( esc_html( $_POST['_wpmk_member_table'] ), '_wpmk_member_table' ) || die( 'Busted!' );

		global $wpdb;

		$member_id = ( isset( $_POST['member_id'] ) ) ? wp_unslash( $_POST['member_id'] ) : 0;

		$where = array(
			'id' => $member_id,
		);

		$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );

		$results = $wpdb->delete(
			$table_name,
			$where,
		);

		if ( ! empty( $results ) ) :
			$results = $wpdb->get_results( 'SELECT * FROM $table_name', OBJECT );
			$return  = array(
				'msg'  => 'Member info deleted.',
				'data' => $results,
			);
		else :
			$return = array(
				'msg'  => 'Error deleting member.',
				'data' => $results,
			);
		endif;

		return wp_send_json_success( $return, 200 );
	}
}
