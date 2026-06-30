/* global jQuery, gepoFront */
(function ($) {
	'use strict';

	function formatMoney(num) {
		var d = parseInt(gepoFront.decimals, 10);
		if (isNaN(d)) {
			d = 2;
		}
		var fixed = num.toFixed(d);
		var parts = fixed.split('.');
		var intp = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, gepoFront.thousandSep || '');
		var decp = parts.length > 1 ? (gepoFront.decimalSep || '.') + parts[1] : '';
		var amount = intp + (parts.length > 1 ? decp : '');
		var sym = gepoFront.currencySym || '';
		var pos = gepoFront.currencyPos || 'left';
		if (pos === 'right') {
			return amount + sym;
		}
		if (pos === 'left_space') {
			return sym + ' ' + amount;
		}
		if (pos === 'right_space') {
			return amount + ' ' + sym;
		}
		return sym + amount;
	}

	function getAddonTotal() {
		var total = 0;
		$('.gepo-fields-wrap .gepo-input').each(function () {
			var $el = $(this);
			if ($el.is('select')) {
				var $o = $el.find('option:selected');
				total += parseFloat($o.data('add')) || 0;
			} else if ($el.is(':radio') && $el.is(':checked')) {
				total += parseFloat($el.data('add')) || 0;
			} else if ($el.is(':checkbox') && $el.is(':checked')) {
				total += parseFloat($el.data('add')) || 0;
			}
		});
		return total;
	}

	var baseSimple = parseFloat(gepoFront.basePrice) || 0;
	var variationBase = null;

	function isVariableProduct() {
		return $('form.variations_form').length > 0;
	}

	/** Preço principal (produto simples ou variável com preço único no topo). */
	function findSimpleProductPriceEl() {
		var selectors = [
			'.product .entry-summary .price',
			'.product .summary .price',
			'div.product div.summary .price',
			'.wp-block-woocommerce-product-price .wc-block-components-product-price',
			'.wp-block-woocommerce-product-price',
			'.wc-block-single-product .wc-block-components-product-price',
			'.woocommerce .product .price'
		];
		var i;
		for (i = 0; i < selectors.length; i++) {
			var $c = $(selectors[i]).first();
			if ($c.length) {
				return $c;
			}
		}
		if ($('body').is('.single-product')) {
			return $('#primary .price, main .price, .site-main .price').first();
		}
		return $();
	}

	/** Preço da variação selecionada (container nativo WooCommerce). */
	function findVariationPriceEl() {
		var selectors = [
			'.woocommerce-variation-price .price',
			'.single_variation_wrap .woocommerce-variation-price .price'
		];
		var i;
		for (i = 0; i < selectors.length; i++) {
			var $c = $(selectors[i]).first();
			if ($c.length) {
				return $c;
			}
		}
		return $();
	}

	function findProductPriceEl() {
		if (isVariableProduct()) {
			var $variationPrice = findVariationPriceEl();
			if ($variationPrice.length) {
				var $variationContainer = $variationPrice.closest('.woocommerce-variation-price');
				if (
					$variationPrice.is(':visible') ||
					($variationContainer.length && $variationContainer.is(':visible'))
				) {
					return $variationPrice;
				}
			}
		}
		return findSimpleProductPriceEl();
	}

	function updatePrice() {
		var $wrap = $('.gepo-fields-wrap');
		if (!$wrap.length) {
			return;
		}
		var $price = findProductPriceEl();
		if (!$price.length) {
			return;
		}
		if (isVariableProduct() && variationBase === null) {
			return;
		}
		var b = variationBase !== null ? variationBase : baseSimple;
		var total = b + getAddonTotal();
		var html =
			'<span class="woocommerce-Price-amount amount"><bdi>' +
			formatMoney(total) +
			'</bdi></span>' +
			(gepoFront.priceSuffix || '');
		$price.html(html);
	}

	function validateRequiredMultiple() {
		var ok = true;
		$('.gepo-fields-wrap fieldset[data-required="1"]').each(function () {
			var $fs = $(this);
			if ($fs.find('select.gepo-input').length) {
				return;
			}
			if ($fs.find('input.gepo-input[type="radio"]').length) {
				if (!$fs.find('input.gepo-input[type="radio"]:checked').length) {
					ok = false;
					return false;
				}
				return;
			}
			if (!$fs.find('.gepo-input:checkbox:checked').length) {
				ok = false;
				return false;
			}
		});
		return ok;
	}

	document.addEventListener(
		'click',
		function (e) {
			if (!$('.gepo-fields-wrap').length) {
				return;
			}
			var el = e.target;
			if (!el || typeof el.closest !== 'function') {
				return;
			}
			var btn = el.closest('.single_add_to_cart_button');
			if (!btn) {
				return;
			}
			if (!btn.closest('form.cart') && !btn.closest('form.variations_form')) {
				return;
			}
			if (!validateRequiredMultiple()) {
				var msg = gepoFront.strings && gepoFront.strings.requiredMultiple ? gepoFront.strings.requiredMultiple : '';
				if (msg) {
					window.alert(msg);
				}
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();
			}
		},
		true
	);

	$(function () {
		$(document).on('change', '.gepo-fields-wrap .gepo-input', updatePrice);

		var $form = $('form.variations_form');
		if ($form.length) {
			$form.on('show_variation', function (event, variation) {
				variationBase = parseFloat(variation.display_price) || 0;
				updatePrice();
			});
			$form.on('hide_variation', function () {
				variationBase = null;
			});
		}
	});
})(jQuery);
