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
	 * @param string $page current page.
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
			wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css', array(), '5.12.1', 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-member-keeper-admin.css', array(), $this->version, 'all' );
		endif;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $page current page.
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
	 * This creates the plugin menu.
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

	/**
	 * To display the setting page for this plugin.
	 */
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
		$links[] = '<a href="https://paypal.me/Wonkasoft" target="blank"><img src="' . WP_MEMBER_KEEPER_URI . 'admin/img/wonka-logo.svg" style="width: 20px; height: 20px; display: inline-block;
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
			$links[] = '<a href="https://paypal.me/Wonkasoft" target="blank">Donate <img src="' . WP_MEMBER_KEEPER_URI . 'admin/img/wonka-logo.svg" style="width: 20px; height: 20px; display: inline-block;
	    vertical-align: text-top;" /></a>';
		}
		return $links;
	}

	/**
	 * This function adds new members to the keeper via ajax
	 */
	public function add_member_to_keeper() {

		$nonce = ( isset( $_POST['_wpmk_nonce'] ) ) ? stripcslashes( $_POST['_wpmk_nonce'] ) : '';
		wp_verify_nonce( $nonce, 'wpmk_add_member_form' ) || die( 'Busted!' );

		global $wpdb;

		$info = array();

		$info['last_modified'] = new DateTime( 'now', new DateTimeZone( 'America/Los_Angeles' ) );
		$info['last_modified'] = $info['last_modified']->format( 'Y-m-d H:i:s' );

		foreach ( $_POST as $key => $value ) :
			if ( 'family_id' === $key ) :
				$info[ $key ] = ( ! empty( $value ) ) ? stripcslashes( $value ) : 0;
			else :
				$info[ $key ] = ( ! empty( $value ) ) ? stripcslashes( $value ) : '';
			endif;
		endforeach;

		$info = json_decode( json_encode( $info ) );

		$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );

		$results = $wpdb->get_results(
			$wpdb->prepare(	"SELECT * FROM %s WHERE first_name = '%s' AND last_name = '%s'", $table_name, array( $info->first_name, $info->last_name ) ),
			OBJECT
		);

		$return = array(
			'msg'  => 'Member already exits',
			'data' => $results,
		);

		if ( empty( $results ) ) :
			$data    = array(
				'last_modified'  => $info->last_modified,
				'first_name'     => $info->first_name,
				'last_name'      => $info->last_name,
				'email'          => $info->email,
				'phone'          => $info->phone,
				'street_address' => $info->street_address,
				'city'           => $info->city,
				'state'          => $info->state,
				'zip_code'       => $info->zip_code,
				'birth_date'     => $info->birth_date,
				'family_id'      => $info->family_id,
				'ministries'     => $info->ministries,
			);
			$format  = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' );
			$results = $wpdb->insert( $table_name, $data, $format );

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

		wp_send_json_success( $return, 200 );
	}

	/**
	 * This function is the ajax request that allows for member edits.
	 */
	public function edit_member_to_keeper() {
		$nonce = isset( $_POST['_wpmk_edit_nonce'] ) ? wp_kses_post( wp_unslash( $_POST['_wpmk_edit_nonce'] ) ) : '';
		wp_verify_nonce( $nonce, 'wpmk_edit_member_form' ) || die( 'Busted!' );

		global $wpdb;

		$fields = array(
			'last_modified',
			'first_name',
			'last_name',
			'email',
			'phone',
			'street_address',
			'city',
			'state',
			'zip_code',
			'birth_date',
			'family_id',
			'id',
			'ministries',
		);

		$info = array();

		$member_id = ( isset( $_POST['id'] ) ) ? wp_unslash( $_POST['id'] ) : 0;

		$info['last_modified'] = new DateTime( 'now', new DateTimeZone( 'America/Los_Angeles' ) );
		$info['last_modified'] = $info['last_modified']->format( 'Y-m-d H:i:s' );

		foreach ( $_POST as $key => $value ) :
			if ( 'family_id' === $key ) :
				$info[ $key ] = ( isset( $_POST[ $key ] ) ) ? wp_unslash( $value ) : $member_id;
			else :
				if ( in_array( $key, $fields ) ) :
					$info[ $key ] = ( ! empty( $value ) ) ? wp_unslash( $value ) : '';
				endif;
			endif;
		endforeach;
			
		$where = array(
			'id' => $info['id'],
		);

		unset( $info['id'] );

		$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );
			
		$results = $wpdb->update(
			$table_name,
			$info,
			$where,
		);

		if ( ! empty( $results ) ) :
			$results = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM %s", $table_name ),
				OBJECT
			);
			$return  = array(
				'msg'  => 'Member was recorded.',
				'data' => $results,
			);
		else :
			$return = array(
				'msg'  => 'Error trying to record member.',
				'data' => $results,
			);
		endif;

		wp_send_json_success( $return, 200 );
	}

	/**
	 * This function is the ajax request that gets member info.
	 */
	public function get_member_from_keeper() {
		$nonce = isset( $_REQUEST['_wpmk_member_table'] ) ? wp_kses_post( wp_unslash( $_REQUEST['_wpmk_member_table'] ) ) : '';
		wp_verify_nonce( $nonce, '_wpmk_member_table' ) || die( __( 'Busted!', 'wp-member-keeper' ) );
		global $wpdb;
		$member_id = ( isset( $_REQUEST['member_id'] ) ) ? wp_kses_post( wp_unslash( $_REQUEST['member_id'] ) ) : 0;
			
		$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );

		$results = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM %s WHERE id = %d", $table_name, array( $member_id ) ),
			OBJECT
		);
			
		if ( ! empty( $results ) ) :
			$results = ( 1 < count( $results ) ) ? json_encode( $results ): json_decode( json_encode( $results[0] ), true );
			$return = array(
				'msg'  => 'Members info retrived.',
				'data'  => $results,
			);
		else :
			$return = array(
				'msg'  => 'No Member found by that ID.',
				'data' => 'No members found.',
			);
		endif;

		wp_send_json_success( $return, 200 );
	}

	/**
	 * This function is the ajax request that deletes member info.
	 */
	public function delete_member_from_keeper() {
		$nonce = isset( $_POST['_wpmk_member_table'] ) ? wp_unslash( $_POST['_wpmk_member_table'] ) : '';
		wp_verify_nonce( $nonce, '_wpmk_member_table' ) || die( 'Busted!' );

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
			$results = $wpdb->get_results( "SELECT * FROM $table_name", OBJECT );
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

		wp_send_json_success( $return, 200 );
	}
}
