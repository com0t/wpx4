<?php
namespace Getresponse\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Class ProductService
 * @package Getresponse\WordPress
 */
class ProductService {

	const PRODUCTS_CACHE_KEY = 'gr-product';
	const CACHE_TIME = 300;

	/** @var Api */
	private $api;

	/**
	 * @param Api $api
	 */
	public function __construct( $api ) {
		$this->api = $api;
	}

	/**
	 * @param string $store_id
	 * @param int $product_id
	 *
	 * @return bool
	 */
	public function get_gr_product_id( $store_id, $product_id ) {

		$product_map = new ProductsMap();

		return $product_map->get_gr_product_id( $store_id, $product_id );
	}

    /**
     * @param string $store_id
     * @param \WC_Product $product
     *
     * @return void
     * @throws ApiException
     * @throws EcommerceException
     */
    public function update_product( $store_id, $product )
    {
        if ($product->is_type('variable')) {
            $this->update_variable_product($store_id, $product);
        }

        if ($product->is_type('simple')) {
            $this->update_simple_product($store_id, $product);
        }
    }

    /**
     * @param int $storeId
     * @param \WC_Product $product
     * @return array
     * @throws ApiException
     * @throws EcommerceException
     * @throws ProductVariantsNotFoundException
     */
	public function add_product($storeId, $product)
    {
		if ($product->is_type('variable')) {
			return $this->add_variable_product($storeId, $product);
		}

		if ($product->is_type('simple')) {
			return $this->add_simple_product($storeId, $product);
		}
	}

	public function clear_mapping($store_id, $product_id)
    {
        $key = self::PRODUCTS_CACHE_KEY . '-' . $store_id . '-' . $product_id;
        gr_cache_delete($key);

        $product_map = new ProductsMap();
        $product_map->removeProductsByGrStoreAndProductId($store_id, $product_id);
    }

	/**
	 * @param int $store_id
	 * @param \WC_Product $product
	 *
	 * @return array
	 * @throws EcommerceException
	 * @throws ApiException
	 */
	public function add_simple_product( $store_id, $product ) {

		if ( ! $product->is_type( 'simple' ) ) {
			throw new EcommerceException( 'Incorrect product type' );
		}
		$gr_product    = ProductFactory::buildFromSimpleProduct( $product );
		$gr_product_id = $this->create_product(
		    $store_id,
            $gr_product->to_array(),
            $product->get_id(),
            $gr_product->get_hash()
        );

		return $this->api->get_product( $store_id, $gr_product_id );
	}

    /**
     * @param $store_id
     * @param \WC_Product $product
     *
     * @return array
     * @throws ApiException
     * @throws EcommerceException
     * @throws ProductVariantsNotFoundException
     */
	public function add_variable_product( $store_id, $product ) {

		$product_factory = new \WC_Product_Factory();

		if ( ! $product->is_type( 'variable' ) ) {
			throw new EcommerceException( 'Incorrect product type' );
		}

		$gr_product = ProductFactory::buildFromVariableProduct(
			$product_factory->get_product( $product )
		);

		$gr_product_id = $this->create_product(
		    $store_id,
            $gr_product->to_array(),
            $product->get_id(),
            $gr_product->get_hash()
        );

		return $this->get_gr_product($store_id, $gr_product_id);
	}

	/**
	 * @param string $store_id
	 * @param string $product_id
	 *
	 * @return array
	 * @throws ApiException
	 */
	public function get_gr_product( $store_id, $product_id ) {

		$key = self::PRODUCTS_CACHE_KEY . '-' . $store_id . '-' . $product_id;

		$product = gr_cache_get( $key );

		if (false === $product || empty($product)) {

			$product = $this->api->get_product($store_id, $product_id);

			if (empty( $product)) {
				return array();
			}
			gr_cache_set($key, $product, self::CACHE_TIME);
		}

		return $product;
	}

	/**
	 * @param string $store_id
	 * @param int $selected_variant
	 * @param int $quantity
	 * @param string $product_type
	 * @param array $gr_product_variants
	 *
	 * @return CartVariant
	 */
	public function add_product_variant(
		$store_id,
		$selected_variant,
		$quantity,
		$product_type,
		$gr_product_variants
	) {
		$variant_map = new VariantsMap();

		if ( $product_type === 'variable' ) {
			$gr_variant_id = $variant_map->get_gr_variant_id( $store_id, $selected_variant );

			foreach ( $gr_product_variants as $_variant ) {
				$_variant = (array) $_variant;
				if ( $_variant['variantId'] === $gr_variant_id ) {
					$variant = $_variant;
				}
			}
		} else {
			$variant = (array) reset( $gr_product_variants );
		}

		$variant['quantity'] = $quantity;

		return CartVariantFactory::create_from_gr_variant( $variant );

	}

	/**
	 * @param string $store_id
	 * @param array $params
	 * @param int $ext_product_id
	 * @param string $gr_product_hash
	 *
	 * @return string
	 * @throws ApiException
	 */
	public function create_product($store_id, $params, $ext_product_id, $gr_product_hash) {

        $variant_map = new VariantsMap();
        $product_map = new ProductsMap();

        $result = $this->api->create_product($store_id, $params);

        if (!empty($result['variants'])) {
            foreach($result['variants'] as $_variant) {
                $_variant = (array) $_variant;
                $variant_map->add_variant( $store_id, $_variant['externalId'], $_variant['variantId'] );
            }
        }

        $product_map->add_product( $store_id, $result['productId'], $ext_product_id, $gr_product_hash );

        return $result['productId'];
	}

    /**
     * @param $store_id
     * @param \WC_Product $product
     * @return void
     * @throws ApiException
     * @throws EcommerceException
     */
    private function update_variable_product($store_id, $product)
    {
        $product_factory = new \WC_Product_Factory();

        if ( ! $product->is_type( 'variable' ) ) {
            throw new EcommerceException( 'Incorrect product type' );
        }

        $gr_product = ProductFactory::buildFromVariableProduct(
            $product_factory->get_product( $product )
        );

        $this->update_existing_product(
            $store_id,
            $gr_product->to_array(),
            $product->get_id(),
            $gr_product->get_hash()
        );
    }

    /**
     * @param $store_id
     * @param \WC_Product $product
     * @return void
     * @throws ApiException
     * @throws EcommerceException
     */
    private function update_simple_product($store_id, $product)
    {
        $product_factory = new \WC_Product_Factory();

        if ( ! $product->is_type( 'simple' ) ) {
            throw new EcommerceException( 'Incorrect product type' );
        }

        $gr_product = ProductFactory::buildFromSimpleProduct(
            $product_factory->get_product( $product )
        );

        $this->update_existing_product(
            $store_id,
            $gr_product->to_array(),
            $product->get_id(),
            $gr_product->get_hash()
        );
    }

    /**
     * @param int $store_id
     * @param array $params
     * @param int $product_id
     * @throws ApiException
     */
    private function update_existing_product($store_id, array $params, $product_id, $gr_product_hash)
    {
        $variant_map = new VariantsMap();
        $product_map = new ProductsMap();

        $gr_product_id = $product_map->get_gr_product_id($store_id, $product_id);

        if ($gr_product_hash === $product_map->get_gr_product_hash($store_id, $product_id)) {
            return;
        }

        $result = $this->api->update_product($store_id, $gr_product_id, $params);
        $product_map->update_gr_product_hash($store_id, $gr_product_id, $gr_product_hash);

        if (!empty($result['variants'])) {
            foreach($result['variants'] as $_variant) {
                $_variant = (array) $_variant;

                if (empty($variant_map->get_gr_variant_id($store_id, $_variant['externalId']))) {
                    $variant_map->add_variant($store_id, $_variant['externalId'], $_variant['variantId']);
                }
            }
        }
    }
}
