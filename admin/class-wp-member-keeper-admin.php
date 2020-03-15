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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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
		if ( empty ( $GLOBALS['admin_page_hooks']['wonkasoft_menu'] ) ) {
			
			define( 'WONKASOFT_PLUGIN_ADMIN_PAGE', 'wonkasoft_menu' );
			add_menu_page(
				'Wonkasoft',
				'Wonkasoft Tools',
				'edit_posts',
				'wonkasoft_menu',
				array( $this,'wp_member_keeper_page_show_settings_page' ),
				plugins_url( "/img/wonka-logo-2.svg", __FILE__ ),
				100
			);

			add_submenu_page(
				'wonkasoft_menu',
				'Member Keeper',
				'Member Keeper',
				'edit_posts',
				'wonkasoft_menu',
				array( $this,'wp_member_keeper_page_show_settings_page' )
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
				array( $this,'wp_member_keeper_page_show_settings_page' )
			);

		}
	}

	// To display the setting page for this plugin
	public function wp_member_keeper_page_show_settings_page() {
		include plugin_dir_path( __FILE__ ) . 'partials/wp-member-keeper-admin-display.php';
	}

	public function wp_member_keeper_add_settings_link_filter( $links ) { 
		$links_addon = '<a href="' . menu_page_url( WONKASOFT_PLUGIN_ADMIN_PAGE, 0 ) . '" target="_self">Settings</a>';
		array_unshift($links, $links_addon);
		$links[] = '<a href="https://paypal.me/Wonkasoft" target="blank"><img src="' . WP_MEMBER_KEEPER_URI . 'admin/img/wonka-logo.svg' . '" style="width: 20px; height: 20px; display: inline-block;
	    vertical-align: text-top; float: none;" /></a>';
	 return $links; 
	}

	public function wp_member_keeper_add_description_link_filter( $links, $file ) {
		if ( strpos($file, 'wp-member-keeper.php') !== false ) {
			$links[] = '<a href="' . menu_page_url( WONKASOFT_PLUGIN_ADMIN_PAGE, 0 ) . '" target="_self">Settings</a>';
			$links[] = '<a href="https://paypal.me/Wonkasoft" target="blank">Donate <img src="' . WP_MEMBER_KEEPER_URI . 'admin/img/wonka-logo.svg' . '" style="width: 20px; height: 20px; display: inline-block;
	    vertical-align: text-top;" /></a>';
		}
	 return $links; 
	}

}
