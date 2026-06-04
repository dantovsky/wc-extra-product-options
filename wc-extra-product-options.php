<?php
/**
 * Plugin Name: Extra Product Options for WooCommerce
 * Description: Opções extra para produtos do WooCommerce com preço e regras de exibição com base em produto, categoria ou tag.
 * Version: 1.2.0
 * Author: Dante Marinho
 * Author URI: https://profiles.wordpress.org/dantiii
 * Text Domain: wc-extra-product-options
 * Domain Path: /languages
 * Requires at least: 6.5
 * Tested up to: 6.8
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
 * @package WC_Extra_Product_Options
 * @license GPLv2
 */

defined( 'ABSPATH' ) || exit;

define( 'WCEO_VERSION', '1.2.0' );
define( 'WCEO_FILE', __FILE__ );
define( 'WCEO_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCEO_URL', plugin_dir_url( __FILE__ ) );

/**
 * Inicialização após plugins carregados (WooCommerce).
 */
function wceo_bootstrap() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>' . esc_html__( 'Woo Extras necessita do WooCommerce ativo.', 'wc-extra-product-options' ) . '</p></div>';
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
						__( 'Woo Extras foi testado com WooCommerce 8.2 ou superior (recomendado para WordPress 6.9 e WooCommerce 10.6). A tua versão é %s.', 'wc-extra-product-options' ),
						WC_VERSION
					)
				);
				echo '</p></div>';
			}
		);
	}

	require_once WCEO_PATH . 'includes/class-wceo-core.php';
	require_once WCEO_PATH . 'includes/class-wceo-admin.php';
	require_once WCEO_PATH . 'includes/class-wceo-frontend.php';
	require_once WCEO_PATH . 'includes/class-wceo-cart.php';

	WCEO_Core::init();
	if ( is_admin() ) {
		WCEO_Admin::init();
	}
	if ( ! is_admin() ) {
		WCEO_Frontend::init();
		WCEO_Cart::init();
	}
}
add_action( 'plugins_loaded', 'wceo_bootstrap', 11 );

/**
 * Ligação "Configurações" na listagem de plugins.
 *
 * @param string[] $links Ligações existentes.
 * @return string[]
 */
function wceo_plugin_action_links( $links ) {
	$url = admin_url( 'admin.php?page=wceo-settings' );
	$links = (array) $links;
	array_unshift(
		$links,
		'<a href="' . esc_url( $url ) . '">' . esc_html__( 'Configurações', 'wc-extra-product-options' ) . '</a>'
	);
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( WCEO_FILE ), 'wceo_plugin_action_links' );

add_action(
	'before_woocommerce_init',
	function () {
		if ( ! class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			return;
		}
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WCEO_FILE, true );

		// Carrinho/checkout em blocos (desde WooCommerce 7.6; recomendado em 8.3+ e 10.x).
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '7.6', '>=' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', WCEO_FILE, true );
		}
	}
);
