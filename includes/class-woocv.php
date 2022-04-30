<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Woocv
 * @subpackage Woocv/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woocv
 * @subpackage Woocv/includes
 * @author     junaidzx90 <admin@easeare.com>
 */
class Woocv {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woocv_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WOOCV_VERSION' ) ) {
			$this->version = WOOCV_VERSION;
		} else {
			$this->version = '1.0.2';
		}
		$this->plugin_name = 'woocv';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woocv_Loader. Orchestrates the hooks of the plugin.
	 * - Woocv_i18n. Defines internationalization functionality.
	 * - Woocv_Admin. Defines all hooks for the admin area.
	 * - Woocv_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocv-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocv-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocv-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocv-public.php';

		$this->loader = new Woocv_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woocv_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woocv_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woocv_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'woocv_admin_menus' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'woocv_admin_footer' );


		$this->loader->add_action( 'wp_ajax_get_woocv_form_data', $plugin_admin, 'get_woocv_form_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_woocv_form_data', $plugin_admin, 'get_woocv_form_data' );

		$this->loader->add_action( 'wp_ajax_save_woocv_data', $plugin_admin, 'save_woocv_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_save_woocv_data', $plugin_admin, 'save_woocv_data' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woocv_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_head', $plugin_public, 'wp_head_scripts' );
		
		$this->loader->add_action( 'woocommerce_before_add_to_cart_quantity', $plugin_public, 'woocv_variations' );
		// Custom variation fields to order
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'woocommerce_add_cart_item_data_process', 10, 3 ); //Add custom data to cart object

		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_public, 'woocv_get_item_data', 10, 2 ); ///Display the custom WooCommerce meta data in the cart

		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'woocv_checkout_create_order_line_item', 10, 4 ); // for order details

		$this->loader->add_filter( 'woocommerce_order_item_name', $plugin_public, 'woocv_order_item_name', 99, 2 ); //Adding WooCommerce custom meta data to emails & Invoice

		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'calculate_final_woocv_prices', 99 ); //Change price

		$this->loader->add_action( 'wp_ajax_get_woo_price', $plugin_public, 'get_woo_price' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_woo_price', $plugin_public, 'get_woo_price' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woocv_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
