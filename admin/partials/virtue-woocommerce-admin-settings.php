<?php
/**
 * Admin Settings Page
 *
 * Markup of the admin settings page.
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

<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<h2>Thanks for installing Virtue!</h2>
	<div class="d-flex">
		<div class="card">
			<h2 class="title">Getting Started</h2>
			<p>To get started with Virtue you'll need to connect your Woocommerce store with Virtue.</p>
			<p>The first step is to create an account below:</p>
			<h4><a href="<?php echo esc_html(sprintf('https://app.virtueimpact.com/users/sign_up?plugin_installed=1&shop_domain=%s&platform_type=woocommerce', get_site_url())); ?>" class="button button-primary" target=”_blank”>Sign up to Virtue </a></h4>
			<p>Already have an account?</p>
			<p><a href="https://app.virtueimpact.com/onboard/shop?plugin_installed=1&shop_domain=<?php echo esc_html(get_site_url()); ?>&platform_type=woocommerce" target=”_blank”>Connect your Woocommerce Store to Virtue</a>.</p>
		</div>
		<div class="text-center">
		  <img src="<?php echo  plugins_url( '../img/virtue-hero.png', __FILE__ ) ?>" style="width:60%;" />
		</div>
	  </div>
</div>
