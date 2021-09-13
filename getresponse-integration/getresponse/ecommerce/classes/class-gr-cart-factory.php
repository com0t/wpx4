<?php
namespace Getresponse\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Class CartFactory
 * @package Getresponse\WordPress
 */
class CartFactory {

	/**
	 * @param int $store_id
	 * @param int $customer_id
	 * @param float $total_price
	 * @param int $external_id
	 * @param float $tax_price
	 * @param CartVariant[] $products
	 * @param string $url
	 * @param string $currency
	 *
	 * @return Cart
	 */
	public static function create_from_params(
	    $store_id,
		$customer_id,
		$total_price,
		$external_id,
		$tax_price,
		$products,
		$url,
		$currency
	) {
		return new Cart(
		    $store_id,
			$customer_id,
			$total_price,
			$external_id,
			$tax_price,
			$products,
			$url,
			$currency
		);
	}
}
