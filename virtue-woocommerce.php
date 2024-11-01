<?php

/**
 * The plugin init file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://virtueimpact.com
 * @since   1.0.0
 * @package Virtue_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Virtue for WooCommerce
 * Plugin URI:        https://virtueimpact.com/
 * Description:       Add instant social impact to your WooCommerce store. Choose from over 20,000 causes to add simply into your store and start using Virtue to make a difference!
 * Version:           1.0.0
 * Author:            Virtue Impact
 * Author URI:        https://virtueimpact.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       virtue-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC') ) {
	die;
}

/**
 * Current plugin version.
 */
define('VIRTUE_WOOCOMMERCE_VERSION', '1.0.1');

/**
 * Runs during plugin activation.
 */
function activate_virtue_woocommerce() {
	include_once plugin_dir_path(__FILE__) . 'includes/class-virtue-woocommerce-activator.php';
	Virtue_Woocommerce_Activator::activate();
}

/**
 * Runs during plugin deactivation.
 */
function deactivate_virtue_woocommerce() {
	include_once plugin_dir_path(__FILE__) . 'includes/class-virtue-woocommerce-deactivator.php';
	Virtue_Woocommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_virtue_woocommerce');
register_deactivation_hook(__FILE__, 'deactivate_virtue_woocommerce');

/**
 * Defines internationalization,
 */
require plugin_dir_path(__FILE__) . 'includes/class-virtue-woocommerce.php';

/**
 * Execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_virtue_woocommerce() {

	$plugin = new Virtue_Woocommerce();
	$plugin->run();

}
run_virtue_woocommerce();
