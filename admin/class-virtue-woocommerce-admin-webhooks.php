<?php

/**
 * Add webhooks for WooCommerce.
 *
 * @link  https://virtueimpact.com
 * @since 1.0.0
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/admin
 */

class Virtue_Woocommerce_Admin_Webhooks {


	/**
	 * Add in webhook for whenever a new product category is created.
	 *
	 * @since  1.0.0
	 
	 */
	public function create_product_cat( $term_id, $tt_id, $args ) {
		// Pass all the details of the new category
		$category = get_term_by('term_id', $term_id, 'product_cat');

  /**
	 * Hook into newly created product categories to update inside of Virtue.
	 *
	 * @since  1.0.0
	 
	 */
		do_action('wc_virtue_create_product_cat', $category);
	}

	/**
	 * Add in webhook for whenever a product category is edited.
	 *
	 * @since  1.0.0
	 
	 */
	public function edit_product_cat( $term_id, $tt_id, $args ) {
		// Pass all the details of the new category
		$category = get_term_by('term_id', $term_id, 'product_cat');
		$posts    = get_posts(
			array(
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_type'      => 'product',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $term_id
				))
			)
		);
		if ($posts ) {
			$category->postids = $posts;
		}
  /**
	 * Hook into edited product categories to update their name and products inside of Virtue.
	 *
	 * @since  1.0.0
	 
	 */
		do_action('wc_virtue_edit_product_cat', $category);
	}
	
	/**
	 * Add in webhook for whenever a product category is deleted.
	 *
	 * @since  1.0.0
	 
	 */
	public function delete_product_cat( $term, $tt_id, $deleted_term, $object_ids ) {
	/**
	 * Hook into deleted product categories to remove them from inside of Virtue.
	 *
	 * @since  1.0.0
	 
	 */
		do_action(
			'wc_virtue_delete_product_cat', array(
			'term_id' => $term 
			)
		);
	}

   /**
	 * Don't apply coupons to the Virtue donation product.
	 *
	 * @since  1.0.0
	 
	 */
	public function exclude_customer_donations_from_promotions( $valid, $product, $coupon, $values ) {
		if ('virtue-donation' == $product->get_sku()) {
			$valid = false;
		}
		return $valid;
	}
  
}
