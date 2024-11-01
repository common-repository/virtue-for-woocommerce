<?php

/**
 * Endpoints to connect the Virtue Platform
 *
 * @link  https://virtueimpact.com
 * @since 1.0.0
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/includes
 */

/**
 *
 * Endpoints used to pass store details back from the Virtue
 * and save them to this store.
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/includes
 */
class Virtue_Woocommerce_Rest_Controller {


	protected $namespace = 'wc/v3';
	protected $rest_base = 'virtuesetup';

	/**
	 * Register a new api route to setup a virtue store id.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
			'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_store_id' ),
				'permission_callback' => array( $this, 'permission_check' )
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/impact_widget',
			array(
			'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_impact_widget' ),
				'permission_callback' => array( $this, 'permission_check' )
			)
		);
	}

	/**
	 * Check the permissions of the api call.
	 *
	 * @since 1.0.0
	 */
	public function permission_check() {

		// WC not enabled, target reflection class is probably not registered
		if (! function_exists('WC') ) {
			return false;
		}

		$method = new ReflectionMethod('WC_REST_Authentication', 'perform_basic_authentication');
		$method->setAccessible(true);

		return $method->invoke(new WC_REST_Authentication()) !== false;    
	}

	/**
	 * Save the store id to the WordPress options table
	 *
	 * @since 1.0.0
	 * @param array    Request data
	 */
	public function save_impact_widget( \WP_REST_Request $request ) {
		// Get the store id param
		$impact_widget_enabled = $request->get_param('impact_widget_enabled');        
		$option_name = 'virtue_woocommerce_impact_widget_enabled';
		$existing_value = get_option($option_name);
		$result = false;

		// Check if it exists
		if (null !== $impact_widget_enabled ) {
			if (null !== $existing_value ) {
				$action = 'update';
				$result = update_option($option_name, $impact_widget_enabled);
			} else {
				$action = 'add';
				$result = add_option($option_name, $impact_widget_enabled);
			}
		} 

		if (false !== $result ) {
			return wp_send_json_success($action);    
		} else {
			if ($existing_value == $impact_widget_enabled) {
				return wp_send_json_success($action);
			} else {
				return wp_send_json_error('ERROR: Something went wrong!');
			}
		}
	}

	/**
	 * Save the store id to the WordPress options table
	 *
	 * @since 1.0.0
	 * @param array    Request data
	 */
	public function save_store_id( \WP_REST_Request $request ) {
		// Get the store id param
		$store_id = $request->get_param('store_id');        
		$option_name = 'virtue_woocommerce_store_id';
		$existing_value = get_option($option_name);
		$result = false;
		
		// Check if it exists
		if (null !== $store_id ) {
			if (false !== $existing_value ) {
				$action = 'update';
				$result = update_option($option_name, $store_id);
			} else {
				$action = 'add';
				$result = add_option($option_name, $store_id);
			}
		} else {
			return wp_send_json_error('ERROR: Store ID not found');
		}
		
		if (false !== $result ) {
			return wp_send_json_success($action);    
		} else {
			if ($existing_value == $store_id ) {
				return wp_send_json_success($action);
			} else {
				return wp_send_json_error('ERROR: Something went wrong!');
			}
		}
	}

	/**
	 * Add the api endpoint to WooCommerce.
	 *
	 * @since 1.0.0
	 * @param array    controllers
	 */
	public function woo_virtuesetup_api( $controllers ) {
		$controllers['wc/v3']['virtuesetup'] = 'Virtue_Woocommerce_Rest_Controller';
		return $controllers;
	}    
}
