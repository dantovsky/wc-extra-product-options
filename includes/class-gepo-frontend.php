<?php
/**
 * Frontend product page rendering and price calculation scripts.
 *
 * @package Global_Extra_Product_Options
 * @license GPLv2
 * @link https://wordpress.org/plugins/global-extra-product-options/
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'GEPO_Frontend' ) ) {
	class GEPO_Frontend {

	/**
	 * Initialize the Frontend class.
	 *
	 * Registers hooks for rendering extras and enqueueing frontend assets.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'render_extras' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ) );
	}

	/**
	 * Conditionally enqueue frontend assets.
	 *
	 * Only loads CSS and JavaScript on product pages with visible option sets.
	 * Prepares product data and pricing configuration for frontend scripts.
	 *
	 * @return void
	 */
	public static function maybe_enqueue() {
		if ( ! is_product() ) {
			return;
		}
		$pid = get_queried_object_id();
		$product = wc_get_product( $pid );
		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return;
		}
		$pid = (int) $product->get_id();
		$sets = GEPO_Core::get_visible_sets_for_product( $pid );
		if ( empty( $sets ) ) {
			return;
		}

		wp_register_style(
			'gepo-frontend',
			GEPO_URL . 'assets/css/frontend-gepo.css',
			array(),
			GEPO_VERSION
		);
		wp_enqueue_style( 'gepo-frontend' );

		wp_register_script(
			'gepo-frontend',
			GEPO_URL . 'assets/js/frontend-gepo.js',
			array( 'jquery', 'wc-add-to-cart-variation' ),
			GEPO_VERSION,
			true
		);

		$base_display = (float) wc_get_price_to_display( $product );
		$config       = GEPO_Core::get_config();

		$price_suffix = '';
		if ( $product && is_a( $product, 'WC_Product' ) && method_exists( $product, 'get_price_suffix' ) ) {
			$price_suffix = $product->get_price_suffix();
		}

		$sets_payload = array();
		foreach ( $sets as $set ) {
			$opts = array();
			foreach ( $set['options'] as $idx => $o ) {
				$opts[] = array(
					'index' => (int) $idx,
					'label' => $o['label'],
					'add'   => (float) wc_format_decimal( $o['price'] ),
				);
			}
			$sets_payload[] = array(
				'id'          => $set['id'],
				'name'        => isset( $set['name'] ) ? $set['name'] : '',
				'choice_type' => $set['choice_type'],
				'required'    => ! empty( $set['required'] ),
				'options'     => $opts,
			);
		}

		wp_enqueue_script( 'gepo-frontend' );
		wp_localize_script(
			'gepo-frontend',
			'gepoFront',
			array(
				'isVariable'   => $product->is_type( 'variable' ),
				'basePrice'    => $base_display,
				'sets'         => $sets_payload,
				'currencySym'  => get_woocommerce_currency_symbol(),
				'decimalSep'   => wc_get_price_decimal_separator(),
				'thousandSep'  => wc_get_price_thousand_separator(),
				'decimals'     => wc_get_price_decimals(),
				'currencyPos'  => get_option( 'woocommerce_currency_pos', 'left' ),
				'priceSuffix'  => wp_kses_post( $price_suffix ),
				'strings'      => array(
					'requiredMultiple' => __( 'Please select at least one option in each required extra (multiple type).', 'global-extra-product-options' ),
				),
			)
		);
	}

	/**
	 * Render product option sets on the single product page.
	 *
	 * Outputs HTML form fields for each visible option set (select, radio, or checkbox).
	 * Respects set configuration (name, type, options, CSS classes/IDs).
	 *
	 * @return void
	 */
	public static function render_extras() {
		if ( ! is_product() ) {
			return;
		}
		global $product;
		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		$pid  = (int) $product->get_id();
		$sets = GEPO_Core::get_visible_sets_for_product( $pid );
		if ( empty( $sets ) ) {
			return;
		}

		$config = GEPO_Core::get_config();

		echo '<div class="gepo-fields-wrap">';

		if ( ! empty( $config['show_global_label'] ) && ! empty( $config['global_label'] ) ) {
			echo '<div class="gepo-global-label">' . esc_html( $config['global_label'] ) . '</div>';
		}

		foreach ( $sets as $set ) {
			$cid = isset( $set['css_id'] ) ? $set['css_id'] : '';
			$cc  = isset( $set['css_class'] ) ? $set['css_class'] : '';
			$sid = isset( $set['id'] ) ? $set['id'] : '';

			$classes = trim( 'gepo-set gepo-set-' . sanitize_html_class( $sid, 'set' ) . ' ' . $cc );
			$req     = ! empty( $set['required'] );

			echo '<fieldset class="' . esc_attr( $classes ) . '"';
			if ( '' !== $cid ) {
				echo ' id="' . esc_attr( $cid ) . '"';
			}
			if ( $req ) {
				echo ' data-required="1"';
			}
			echo '>';
			if ( ! empty( $set['name'] ) ) {
				echo '<legend class="gepo-set-legend">' . esc_html( $set['name'] ) . '</legend>';
			}

			$fname = 'gepo_selection[' . esc_attr( $sid ) . ']';
			$type  = isset( $set['choice_type'] ) ? $set['choice_type'] : 'exclusive';

			if ( 'multiple' === $type ) {
				echo '<ul class="gepo-options gepo-options-multiple">';
				foreach ( $set['options'] as $idx => $opt ) {
					$lid = 'gepo-' . $sid . '-' . $idx;
					echo '<li><label for="' . esc_attr( $lid ) . '">';
					echo '<input type="checkbox" id="' . esc_attr( $lid ) . '" name="' . esc_attr( $fname ) . '[]" value="' . esc_attr( (string) $idx ) . '" class="gepo-input" data-add="' . esc_attr( wc_format_decimal( $opt['price'] ) ) . '" /> ';
					echo esc_html( $opt['label'] );
					echo ' <span class="gepo-option-suffix">(+' . wp_kses_post( wc_price( $opt['price'] ) ) . ')</span>';
					echo '</label></li>';
				}
				echo '</ul>';
			} elseif ( 'exclusive_radio' === $type ) {
				echo '<ul class="gepo-options gepo-options-radio">';
				$first_radio = true;
				foreach ( $set['options'] as $idx => $opt ) {
					$lid = 'gepo-' . $sid . '-' . $idx;
					echo '<li><label for="' . esc_attr( $lid ) . '">';
					echo '<input type="radio" id="' . esc_attr( $lid ) . '" name="' . esc_attr( $fname ) . '" value="' . esc_attr( (string) $idx ) . '" class="gepo-input" data-add="' . esc_attr( wc_format_decimal( $opt['price'] ) ) . '"';
					if ( $req && $first_radio ) {
						echo ' required aria-required="true"';
						$first_radio = false;
					}
					echo ' /> ';
					echo esc_html( $opt['label'] );
					echo ' <span class="gepo-option-suffix">(+' . wp_kses_post( wc_price( $opt['price'] ) ) . ')</span>';
					echo '</label></li>';
				}
				echo '</ul>';
			} else {
				echo '<select name="' . esc_attr( $fname ) . '" class="gepo-input gepo-select" data-exclusive="1"';
				if ( $req ) {
					echo ' required aria-required="true"';
				}
				echo '>';
				echo '<option value="">' . esc_html__( '— Select —', 'global-extra-product-options' ) . '</option>';
				foreach ( $set['options'] as $idx => $opt ) {
					echo '<option value="' . esc_attr( (string) $idx ) . '" data-add="' . esc_attr( wc_format_decimal( $opt['price'] ) ) . '">';
					echo esc_html( $opt['label'] );
					echo ' (+' . esc_html( wp_strip_all_tags( wc_price( $opt['price'] ) ) ) . ')';
					echo '</option>';
				}
				echo '</select>';
			}

			echo '</fieldset>';
		}

		echo '</div>';
	}
	}
}
