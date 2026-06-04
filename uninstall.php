<?php
/**
 * Plugin Uninstall: Clean up plugin data.
 *
 * @package WC_Extra_Product_Options
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Remove plugin configuration option from database.
 */
delete_option( 'wc_extra_product_options_config' );
