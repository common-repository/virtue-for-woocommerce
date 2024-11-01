<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://virtueimpact.com
 * @since 1.0.0
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/admin
 */

class Virtue_Woocommerce_Admin {


	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The system ID of this plugin.
	 *
	 * @since  1.0.0
	 
	 * @var    string    $formatted_name    The system ID of this plugin.
	 */
	private $formatted_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->formatted_name = str_replace(' ', '-', strtolower($this->plugin_name));
	}

	/**
	 * Initiate the options page under Settings.
	 *
	 * @since 1.0.0
	 */
	public function init_settings_page() {

		add_options_page(
			__('Virtue for WooCommerce Setup', 'virtue-for-woocommerce'),
			__('Virtue', 'virtue-for-woocommerce'),
			'manage_options',
			'virtue-for-woocommerce',
			array($this, 'settings_page_display')
		);
	}

	/**
	 * Callback function for the settings page display.
	 *
	 * @since 1.0.0
	 */
	public function settings_page_display() {
		include_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/virtue-woocommerce-admin-settings.php';
	}

	/**
	 * Add settings link on plugin page.
	 *
	 * @since 1.0.0
	 */
	public function settings_link( array $links, $file ) {
		if ('virtue-for-woocommerce/virtue-woocommerce.php' === $file ) {
			$url = esc_url(
				add_query_arg(
					'page',
					'virtue-for-woocommerce',
					get_admin_url() . 'options-general.php'
				) 
			);
			$settings_link = "<a href='$url'>" . __('Settings', 'virtue-for-woocommerce') . '</a>';
			array_unshift(
				$links,
				$settings_link
			);
		}
		return $links;
	}

	/**
	 * Display admin notice on activation.
	 *
	 * @since 1.0.0
	 */
	public function admin_notice() {
		$whitelist_admin_pages = array( 'plugins' );
		$admin_page = get_current_screen();
		if (in_array($admin_page->base, $whitelist_admin_pages) && get_transient('virtue-woocommerce-admin-notice') ) {
			include_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/virtue-woocommerce-admin-notice.php';
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style($this->formatted_name, plugin_dir_url(__FILE__) . 'css/virtue-woocommerce-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/virtue-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

	}
}
