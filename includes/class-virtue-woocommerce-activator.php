<?php

/**
 * Fired during plugin activation
 *
 * @link  https://virtueimpact.com
 * @since 1.0.0
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/includes
 */
class Virtue_Woocommerce_Activator {


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		set_transient('virtue-woocommerce-admin-notice', true, 3600);
	}

}
