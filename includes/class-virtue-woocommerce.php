<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  https://virtueimpact.com
 * @since 1.0.0
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/includes
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
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/includes
 */
class Virtue_Woocommerce {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since  1.0.0
	 * @var    Virtue_Woocommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since  1.0.0
	 * @var    string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if (defined('VIRTUE_WOOCOMMERCE_VERSION') ) {
			$this->version = VIRTUE_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'Virtue for WooCommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_admin_webhooks();
		$this->define_public_hooks();
		$this->define_rest_controller_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Virtue_Woocommerce_Loader. Orchestrates the hooks of the plugin.
	 * - Virtue_Woocommerce_I18n. Defines internationalization functionality.
	 * - Virtue_Woocommerce_Admin. Defines all hooks for the admin area.
	 * - Virtue_Woocommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since  1.0.0
	 
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		include_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-virtue-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		include_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-virtue-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		include_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-virtue-woocommerce-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the admin webhooks.
		 */
		include_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-virtue-woocommerce-admin-webhooks.php';

		/**
		 * The class responsible for defining all actions that create the endpoints required to connect with Vitue.
		 */
		include_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-virtue-woocommerce-rest-controller.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		include_once plugin_dir_path(dirname(__FILE__)) . 'public/class-virtue-woocommerce-public.php';

		$this->loader = new Virtue_Woocommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Virtue_Woocommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since  1.0.0
	 
	 */
	private function set_locale() {

		$plugin_i18n = new Virtue_Woocommerce_I18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Virtue_Woocommerce_Admin($this->get_plugin_name(), $this->get_version());

		// init an admin settings page.
		$this->loader->add_action('admin_menu', $plugin_admin, 'init_settings_page');
		
		// Enqueue admin styles.
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		// add link to settings page
		$this->loader->add_filter('plugin_action_links', $plugin_admin, 'settings_link', 10, 2);

		// add activation notification
		$this->loader->add_action('admin_notices', $plugin_admin, 'admin_notice');
	}

	/**
	 * Register all of the hooks related to the admin webhooks functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 
	 */
	private function define_admin_webhooks() {

		$plugin_webhooks = new Virtue_Woocommerce_Admin_Webhooks();

		$this->loader->add_action('create_product_cat', $plugin_webhooks, 'create_product_cat', 10, 3);
		$this->loader->add_action('edit_product_cat', $plugin_webhooks, 'edit_product_cat', 10, 3);
		$this->loader->add_action('delete_product_cat', $plugin_webhooks, 'delete_product_cat', 10, 4);
		$this->loader->add_filter('woocommerce_coupon_is_valid_for_product', $plugin_webhooks, 'exclude_customer_donations_from_promotions', 9999, 4);
	// $wc_payment_gateways = WC_Payment_Gateways::instance();
	// foreach ( $wc_payment_gateways->payment_gateways() as $gateway ) {
	//   $this->loader->add_action('woocommerce_update_options_payment_gateways_' . $gateway->id, $plugin_webhooks, 'edit_payment_gateway', 10, 3);
	// }
	}    

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since  1.0.0
	 
	 */
	private function define_public_hooks() {

		$plugin_public = new Virtue_Woocommerce_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');

		// Insert Virtue Scripts
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'learn_more_embed');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'impact_calculator_embed');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'product_page_script');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'cart_page_script');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'post_purchase_script');

		// WooCommerce public hooks
		$this->loader->add_action('woocommerce_after_add_to_cart_button', $plugin_public, 'product_page_widget');
		// $this->loader->add_action( 'woocommerce_after_cart', $plugin_public, 'cart_page_widget' );
		$this->loader->add_action('woocommerce_after_cart_totals', $plugin_public, 'cart_page_widget');
		// $this->loader->add_filter('woocommerce_add_to_cart_fragments', 'reload_cart_page_widget', 10, 1);
		$this->loader->add_action('wp_footer', $plugin_public, 'post_purchase_widget');
		
		// Ajax Endpoints
		$this->loader->add_action('wp_ajax_virtue_woocommerce_update_cart', $plugin_public, 'update_cart');
		$this->loader->add_action('wp_ajax_nopriv_virtue_woocommerce_update_cart', $plugin_public, 'update_cart');

		$this->loader->add_action('wp_ajax_virtue_woocommerce_get_cart_items', $plugin_public, 'get_cart_items');
		$this->loader->add_action('wp_ajax_nopriv_virtue_woocommerce_get_cart_items', $plugin_public, 'get_cart_items');
	
		$this->loader->add_action('wp_ajax_virtue_woocommerce_get_cart_total', $plugin_public, 'get_cart_total');
		$this->loader->add_action('wp_ajax_nopriv_virtue_woocommerce_get_cart_total', $plugin_public, 'get_cart_total');

		// Defer scripts
		$this->loader->add_filter('script_loader_tag', $plugin_public, 'defer_scripts', 10, 3);

		// Update cart names
		$this->loader->add_filter('woocommerce_before_calculate_totals', $plugin_public, 'cart_item_names', 10, 1);


	}
	/**
	 * Register all of the hooks related to the rest controller.
	 *
	 * @since  1.0.0
	 
	 */
	private function define_rest_controller_hooks() {

		$rest_controller = new Virtue_Woocommerce_Rest_Controller();

		$this->loader->add_action('woocommerce_rest_api_get_rest_namespaces', $rest_controller, 'woo_virtuesetup_api');
	}    

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since  1.0.0
	 * @return string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since  1.0.0
	 * @return Virtue_Woocommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
