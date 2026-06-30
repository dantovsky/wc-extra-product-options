<?php
/**
 * Plugin Uninstall: Clean up plugin data.
 *
 * @package Global_Extra_Product_Options
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Remove plugin configuration options from database.
 */
delete_option( 'global_extra_product_options_config' );
delete_option( 'wc_extra_product_options_config' );
