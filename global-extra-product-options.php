<?php
/**
 * Plugin Name: Global Extra Product Options for WooCommerce
 * Description: This plugin adds extra product options to WooCommerce products with pricing and conditional display rules.
 * Version: 1.4.0
 * Author: Dante Marinho
 * Author URI: https://profiles.wordpress.org/dantiii
 * Text Domain: global-extra-product-options
 * Domain Path: /languages
 * Requires at least: 6.5
 * Tested up to: 7.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 8.2
 * WC tested up to: 10.8.1
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @package Global_Extra_Product_Options
 * @license GPLv2
 */

defined( 'ABSPATH' ) || exit;

define( 'GEPO_VERSION', '1.4.0' );
define( 'GEPO_FILE', __FILE__ );
define( 'GEPO_PATH', plugin_dir_path( __FILE__ ) );
define( 'GEPO_URL', plugin_dir_url( __FILE__ ) );

/**
 * Bootstrap plugin after all plugins are loaded.
 *
 * Checks for WooCommerce dependency and verifies version compatibility.
 * Loads plugin classes (Core, Admin, Frontend, Cart).
 *
 * @return void
 */
function gepo_bootstrap() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>' . esc_html__( 'Global Extra Product Options requires WooCommerce to be active.', 'global-extra-product-options' ) . '</p></div>';
			}
		);
		return;
	}

	if ( is_admin() && defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '8.2', '<' ) ) {
		add_action(
			'admin_notices',
			static function () {
				if ( ! defined( 'WC_VERSION' ) ) {
					return;
				}
				echo '<div class="notice notice-warning"><p>';
				echo esc_html(
					sprintf(
						/* translators: %s: WooCommerce version */
						__( 'Global Extra Product Options was tested with WooCommerce 8.2 or higher (recommended for WordPress 6.9 and WooCommerce 10.6). Your version is %s.', 'global-extra-product-options' ),
						WC_VERSION
					)
				);
				echo '</p></div>';
			}
		);
	}

	require_once GEPO_PATH . 'includes/class-gepo-core.php';
	require_once GEPO_PATH . 'includes/class-gepo-admin.php';
	require_once GEPO_PATH . 'includes/class-gepo-frontend.php';
	require_once GEPO_PATH . 'includes/class-gepo-cart.php';

	GEPO_Core::init();
	if ( is_admin() ) {
		GEPO_Admin::init();
	}
	if ( ! is_admin() ) {
		GEPO_Frontend::init();
		GEPO_Cart::init();
	}
}
add_action( 'plugins_loaded', 'gepo_bootstrap', 11 );

/**
 * Add Settings link to plugin action links.
 *
 * Adds a "Settings" link in the plugin listing that redirects to the GEPO settings page.
 *
 * @param string[] $links Existing plugin action links.
 * @return string[] Modified links array with Settings link prepended.
 */
function gepo_plugin_action_links( $links ) {
	$url = admin_url( 'admin.php?page=gepo-settings' );
	$links = (array) $links;
	array_unshift(
		$links,
		'<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'global-extra-product-options' ) . '</a>'
	);
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( GEPO_FILE ), 'gepo_plugin_action_links' );

add_action(
	'before_woocommerce_init',
	function () {
		if ( ! class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			return;
		}
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', GEPO_FILE, true );

		// Carrinho/checkout em blocos (desde WooCommerce 7.6; recomendado em 8.3+ e 10.x).
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '7.6', '>=' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', GEPO_FILE, true );
		}
	}
);
