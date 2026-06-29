=== Extra Product Options for WooCommerce ===
Contributors: dantiii
Tags: woocommerce, product options, add-ons, product addons
Requires at least: 6.5
Tested up to: 7.0
Stable tag: 1.3.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/paypalme/dantemarinho

Add customizable extra product options to WooCommerce with pricing and display rules based on product, category, or tag.

== Description ==

Extra Product Options for WooCommerce allows store administrators to create custom sets of add-on options for products with flexible pricing and conditional display rules. Perfect for offering product customizations, warranties, gift wrapping, and other add-ons directly on the product page.

**Key Features:**

* Create unlimited sets of customizable product options
* Set prices for each option (positive or negative adjustments)
* Support for three selection types: exclusive (select one), radio buttons, and multiple selections
* Display rules based on specific products, categories, or tags
* Conditional logic: AND/OR operators for complex rule combinations
* Requires field validation with user-friendly error messages
* Seamless integration with WooCommerce cart, checkout, and orders
* Full support for simple and variable products
* Compatible with WooCommerce blocks and classic cart/checkout
* Custom CSS class and ID support for advanced styling
* Order administration display and order metadata
* Comprehensive input sanitization and security checks
* Full translation support with .pot file

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install via WordPress.org plugin directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to WooCommerce → Extra Product Options to configure
4. Create sets of options and define display rules
5. Options will automatically appear on qualifying product pages

== Usage ==

1. Go to WooCommerce → Extra Product Options
2. Click "Add Set" to create a new set of options
3. Configure:
   - Set name (displayed to customers)
   - Choice type (single select, radio buttons, or multiple)
   - Options (labels and prices)
   - Display rules (which products show this set)
4. Save your configuration
5. Options will appear on product pages before the "Add to Cart" button

== Display Rules ==

Control where each set of options appears:

* **Product Rule**: Show on specific products
* **Category Rule**: Show on products in specific categories
* **Tag Rule**: Show on products with specific tags

Combine rules with AND/OR logic for advanced control.

== FAQ ==

**Can I use negative prices?**
Yes! Negative prices reduce the product total. Useful for discounts or fee reductions.

**Do options work with variable products?**
Yes! Options apply to variable products based on the parent product rules.

**Are the options translated?**
Yes! The plugin supports full internationalization. Enable the plugin's language pack in your WordPress language settings.

**Can I customize the styling?**
Yes! Assign custom CSS classes or IDs to sets for complete styling control.

**What happens to extra options in the cart?**
Extra options and their prices are displayed in the cart, checkout, and order details.

**Is there a limit to the number of options per set?**
No! Create as many options as needed per set.

== Changelog ==

= 1.3.2 =
* Fix :: not removing when only one extra option
* Change file names (CSS and JS)

= 1.3.1 =
* Fix :: price not updating on variable product with only one price

= 1.3.0 =
* Implemented internationalization (i18n) » EN as default
* Added translations for pt_BR
* Added translations for pt_PT
* Added translations for es_ES

= 1.2.2 =
* Changed CART_KEY from woo_extra_selection to wceo_selection
* Improved documentation

= 1.2.1 =
* Refactoring config name from woo_extra_config to wc_extra_product_options_config

= 1.2.0 =
* Changed plugin name, the slug and text domain 
* Improve security with guards on classes
* Improve code with best practices from WordPress.org
* Added file: uninstall.php — delete_option

= 1.1.1 =
* Improved conditional loading for better performance
* Added class_exists() guards for plugin compatibility
* Updated to match WordPress.org plugin repository standards
* Fixed typo in plugin name
* Enhanced code documentation

= 1.1.0 =
* Added support for WooCommerce blocks (cart and checkout)
* Declared HPOS (High-Performance Order Storage) compatibility
* Improved price calculation accuracy
* Better handling of product variations

= 1.0.0 =
* Initial release
* Create custom product option sets
* Support for exclusive, radio, and multiple selection types
* Display rules with AND/OR logic
* Full WooCommerce integration

== Screenshots ==

1. Main settings page showing sets configuration
2. Adding a new set with options and pricing
3. Display rules editor with product, category, and tag selection
4. Product page showing extra options before "Add to Cart"
5. Cart display with selected extras and prices

== Upgrade Notice ==

= 1.1.1 =
Recommended update with WordPress.org compatibility improvements and better code practices.

== Support ==

For support, please visit the [plugin support forum](https://wordpress.org/support/plugin/wc-extra-product-options/) or contact the developer.

== Screenshots ==

1. Location of the "Extra Product Options for WooCommerce" plugin menu.
2. View of the extra fields management page showing several added fields.
3. View of the extra fields management page showing the requirement of an extra field.
4. Example of extra fields added to the frontend.