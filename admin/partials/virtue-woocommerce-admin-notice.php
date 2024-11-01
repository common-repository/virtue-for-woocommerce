<?php
/**
 * Admin Notice
 *
 * Markup of the admin notice.
 *
 * @link  https://virtueimpact.com
 * @since 1.0.0
 *
 * @package    Virtue_Woocommerce
 * @subpackage Virtue_Woocommerce/admin/partials
 */

// check user capabilities
if (! current_user_can('manage_options') ) {
	return;
}
?>
<div class="notice notice-warning is-dismissible">
  <?php /* translators: %s: the domain name of the WooCommerce store */ ?>
	<p>Before you can connect your WooCommerce store to Virtue and start giving, you'll need to <a href="<?php echo esc_html(sprintf(__('https://app.virtueimpact.com/users/sign_up?shop_domain=%s&platform_type=woocommerce', 'virtue-woocommerce'), get_site_url()))?>" target=”_blank”>create an account at virtueimpact.com</a>. </p>
</div>
