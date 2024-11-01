<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://virtueimpact.com
 * @since 1.0.0
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/public
 */
class Virtue_Woocommerce_Public {


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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->formatted_name = str_replace(' ', '-', strtolower($this->plugin_name));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style($this->formatted_name, plugin_dir_url(__FILE__) . 'css/virtue-woocommerce-public.css', array(), $this->version, 'all');

	}

	/**
	 * Register the Product Page Script.
	 *
	 * @since 1.0.0
	 */
	public function product_page_script() {
		
		if (is_product() ) {

			$script        = $this->formatted_name . '-product-page';
			$merchant_id   = get_option('virtue_woocommerce_store_id');
			$product_id    = wc_get_product()->get_id();
			$product_price = wc_get_product()->get_price();
			$currency       = get_woocommerce_currency();

			wp_enqueue_script($script, 'https://app.virtueimpact.com/widgets/' . $merchant_id . '/product-page.js', array( 'jquery' ), $this->version, false);
		
			wp_localize_script(
				$script, 'VirtueWoocommerce', array( 
				'ajax_url'        => admin_url('admin-ajax.php'),
				'merchantId'   => $merchant_id,
				'productId'    => $product_id,
				'productPrice' => $product_price,
				'currency'       => $currency
				)
			);
		}
	}

	/**
	 * Register the Cart Page Script.
	 *
	 * @since 1.0.0
	 */
	public function cart_page_script() {
		
		if (is_cart() ) {

			$script        = $this->formatted_name . '-cart';
			$merchant_id   = get_option('virtue_woocommerce_store_id');
			//            $product_id    = wc_get_product()->get_id();
			//            $product_price = wc_get_product()->get_price();
			$currency       = get_woocommerce_currency();

			wp_enqueue_script($script, 'https://app.virtueimpact.com/widgets/' . $merchant_id . '/customer-donations.js', array( 'jquery' ), $this->version, false);
			
			wp_localize_script(
				$script, 'VirtueWoocommerce', array( 
				'ajax_url'        => admin_url('admin-ajax.php'),
				'merchantId'   => $merchant_id,
				//                'productId'    => $product_id,
				//                'productPrice' => $product_price,
				'currency'       => $currency
				)
			);
		}
	}    

	/**
	 * Register the Post Purchase script.
	 *
	 * @since 1.0.0
	 */
	public function post_purchase_script() {
		global $wp;

		if (is_wc_endpoint_url('order-received') ) {
			
			$script        = $this->formatted_name . '-order-received';
			$merchant_id   = get_option('virtue_woocommerce_store_id');
			$order_id        = absint($wp->query_vars['order-received']);

			if (! $order_id ) {
				return false;
			}
			
			$order = wc_get_order($order_id); 

			if (! $order ) {
				return false;
			}

			foreach ( $order->get_items() as  $item_key => $item_values ) {
				$item = $item_values->get_data();
				$collect_pids[]      = $item['product_id'];
				$collect_line_tots[] = $item['total'];
				 $collect_line_qtys[] = $item['quantity'];
				$collect_line_data[] = array(
				 'product_id' => $item['product_id'],
				 'variant_id' => $item['variation_id'],
				 'quantity'     => $item['quantity'],
				 'tax'         => $item['total_tax'],
				 'total'         => $item['total'],
				 'subtotal'     => $item['subtotal']                    
				);
			}

			$product_ids = ( !empty($collect_pids) ) ? implode(',', $collect_pids) : false;
			$quantities = ( !empty($collect_line_qtys) ) ? implode(',', $collect_line_qtys) : false;
			$line_item_amounts = ( !empty($collect_line_tots) ) ? implode(',', $collect_line_tots) : false;
			$subtotal = $order->subtotal;
			$total = $order->total;
			$customer_id = ( null !== $order->customer_id ) ? $order->customer_id : false; // false is a guest
			$customer_email = $order->get_billing_email();

			$currency       = $order->currency;

			wp_enqueue_script($script, 'https://app.virtueimpact.com/widgets/' . $merchant_id . '/post-purchase-widget.js', array( 'jquery' ), $this->version, false);
			wp_localize_script(
				$script, 'VirtueWoocommerce', array( 
				'ajax_url'                => admin_url('admin-ajax.php'),
				'merchantId'           => $merchant_id,
				'order_id'            => $order_id,
				'line_items'        => json_encode($collect_line_data),
				'product_ids'        => $product_ids,
				'line_item_amounts' => $line_item_amounts,
				'subtotal_amount'    => $subtotal,
				'total_amount'         => $total,
				'customer_id'        => $customer_id,
				'currency'              => $currency,
				'customer_email' => $customer_email,
				'quantities' => $quantities
				)
			);
		}
	}

	/**
	 * Register the Learn More Embed Script.
	 *
	 * @since 1.0.0
	 */
	public function learn_more_embed() {
		$merchant_id   = get_option('virtue_woocommerce_store_id');
		wp_enqueue_script($this->formatted_name . '-learn-more-embed', 'https://app.virtueimpact.com/widgets/' . $merchant_id . '/learn-more-embed.js', array(), $this->version, false);        
	}    

	/**
	 * Register the Learn More Embed Script.
	 *
	 * @since 1.0.0
	 */
	public function impact_calculator_embed() {
		$merchant_id   = get_option('virtue_woocommerce_store_id');
		$impact_widget_enabled   = get_option('virtue_woocommerce_impact_widget_enabled');
		if ($impact_widget_enabled || 'true' === $impact_widget_enabled ) {
			$currency       = get_woocommerce_currency();
			$script        = $this->formatted_name . '-impact-calculator';
			wp_enqueue_script($script, 'https://app.virtueimpact.com/widgets/' . $merchant_id . '/impact-calculator-widget.js', array(), $this->version, false);        
			wp_localize_script(
				$script, 'VirtueWoocommerce', array( 
				'currency'              => $currency
				)
			); 
		}
	}    


	/**
	 * Returns cart items.
	 *
	 * @since 1.0.0
	 */
	public function get_cart_items() {
		$cart = WC()->cart;
		if ($cart ) {
			wp_send_json_success($cart->get_cart_contents());
			wp_die();
		}
	}

	/**
	 * Returns cart total.
	 *
	 * @since 1.0.0
	 */
	public function get_cart_total() {
		$cart = WC()->cart;
		if ($cart ) {
			wp_send_json_success($cart->subtotal * 100);
			wp_die();
		}
	}    

	/**
	 * Endpoint to add or change variation in cart.
	 *
	 * @since 1.0.0
	 */
	public function update_cart() {
		/**
	 * The donation product id is being added to the cart.
	 *
	 * @since 1.0.0
	 */
		if (isset( $_POST['variation_id'], $_POST['quantity'], $_POST['product_id'], $_POST['_wpnonce'] ) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'add_donation')) {
	  /**
	   * Calls the WooCommerce add to cart product id hook.
	   *
	   * @since 1.0.0
	   */
		  $product_id        = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));        
		  $quantity          = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
		  $variation_id      = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : '';
		  $prev_variation_id = isset($_POST['previous_variation_id']) ? absint($_POST['previous_variation_id']) : false;

		  // Remove current item if included
			if ($prev_variation_id ) {
			$cart_id = WC()->cart->generate_cart_id($product_id, $prev_variation_id);
			$item_key = WC()->cart->find_product_in_cart($cart_id);
			WC()->cart->remove_cart_item($item_key);
			}

		  // Add variation to cart
		  self::add_variation_to_cart($product_id, $quantity, $variation_id);
		}
		wp_die();
	}

	/**
	 * Add a product variation to the cart
	 *
	 * @since 1.0.0
	 */
	public function add_variation_to_cart( $product_id, $quantity = 1, $variation_id ) {
	/**
	 * Calls the WooCommerce add to cart validation to check it passed.
	 *
	 * @since 1.0.0
	 */
		$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id);

		if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) ) {

	 /**
	 * Calls the ajax added to cart hook.
	 *
	 * @since 1.0.0
	 */
			do_action('woocommerce_ajax_added_to_cart', $product_id);

			if (get_option('woocommerce_cart_redirect_after_add') == 'yes' ) {
				wc_add_to_cart_message($product_id);
			}

			// Return fragments
			WC_AJAX::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart
			$error = new WP_Error('001', __('oops something went wrong', 'virtue-woocommerce'));
			wp_send_json_error($error);            
		}        
	}

	/**
	 * Insert the product page widget div.
	 *
	 * @woocommerce_after_add_to_cart_button
	 *
	 * @since 1.0.0
	 */
	public function product_page_widget() {
		
		if (is_product() ) {
			include_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/product-page-widget.php';        
		}
	}    

	/**
	 * Insert the cart page widget div.
	 *
	 * @woocommerce_after_cart
	 *
	 * @since 1.0.0
	 */
	public function cart_page_widget() {
		if (is_cart() ) {
			include_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/cart-page-widget.php';        
		}
	}

	public function reload_cart_page_widget( array $array) {
		// Your logic …
		$array['#reload-cart-widget'] = '<script id="reload-cart-widget">customerDonation.loadFrame();</script>';
		return $array;
	}


	/**
	 * Insert the post purchase widget div.
	 *
	 * @wp_footer
	 *
	 * @since 1.0.0
	 */
	public function post_purchase_widget() {

		if (is_wc_endpoint_url('order-received') ) {
			include_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/post-purchase-widget.php';
		}
	}
	
	/**
	 * Add Defer tag to some specific scripts.
	 *
	 * @woocommerce_after_add_to_cart_button
	 *
	 * @since 1.0.0
	 */
	public function defer_scripts( $tag, $handle, $src ) {
		$defer = array( 
		'virtue-for-woocommerce-product-page',
		'virtue-for-woocommerce-cart',
		'virtue-for-woocommerce-order-received',
		'virtue-for-woocommerce-learn-more-embed',
		 'virtue-for-woocommerce-impact-calculator'
		);

		if (in_array($handle, $defer) ) {
			$tag = str_replace(' src', ' defer src', $tag); // defer the script
		}
			
		return $tag;
	}  

	/**
	 * Update product variation cart names.
	 *
	 * @woocommerce_before_calculate_totals
	 *
	 * @since 1.0.0
	 */
	public function cart_item_names( $cart ) {

		if (is_admin() && ! defined('DOING_AJAX') ) {
			return;
		}
	
		if (did_action('woocommerce_before_calculate_totals') >= 2 ) {
			return;
		}
	
		// Loop through cart items
		foreach ( $cart->get_cart() as $cart_item ) {
	
			if (0 === $cart_item['variation_id']  ) {
				continue;
			}

			if ('virtue-donation' !== $cart_item['data']->sku) { 
				continue;
			}

			$product_desc = get_post_meta($cart_item['variation_id'], '_variation_description', true);
	
			if (! $product_desc ) {
				continue;
			}
				
			// Get an instance of the WC_Product object
			$product = $cart_item['data'];
			
			// Get the product name (Added Woocommerce 3+ compatibility)
			$original_name = method_exists($product, 'get_name') ? $product->get_name() : $product->post->post_title;
	
			// SET THE NEW NAME
			$new_name = $original_name . ' - ' . $product_desc;
	
			// Set the new name (WooCommerce versions 2.5.x to 3+)
			if (method_exists($product, 'set_name') ) {
				$product->set_name($new_name);
			} else {
				$product->post->post_title = $new_name;
			}
		}
	}
}
