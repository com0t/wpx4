<?php
namespace Getresponse\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Class CustomFieldsService
 * @package Getresponse\WordPress
 */
class CustomFieldsService {

	/** @var Api */
	private $api;

	/**
	 * @param Api $api
	 */
	public function __construct( $api ) {
		$this->api = $api;
	}

	/**
	 * @param array $user_customs
	 *
	 * @return array
	 * @throws ApiException
	 */
	public function create_custom_fields( $user_customs ) {

		$customs = array();

		foreach ( $user_customs as $name => $value ) {
            $gr_custom = null;

			$result = (array) $this->api->get_custom_fields( array(
				'query' => array(
					'name' => $name
				)
			) );

			foreach ($result as $custom) {
				if ($custom['name'] === $name) {
					$gr_custom = (array) $custom;
				}
			}
			
			if ( ! isset( $gr_custom['customFieldId'] ) ) {

                $gr_custom = $this->api->add_custom_field( array(
					'name'   => $name,
					'type'   => "text",
					'hidden' => "false",
					'values' => array( $value )
				) );
			}

			$customs[] = array(
				'customFieldId' => $gr_custom['customFieldId'],
				'value'         => array( $value )
			);
		}

		return $customs;
	}

	/**
	 * @param array $custom_fields
	 *
	 * @return array
	 */
	public function validate_custom_fields( $custom_fields ) {

		$errors = array();

		foreach ($custom_fields as $field => $value) {

			if (false == preg_match( '/^[_a-zA-Z0-9]{2,32}$/m',
					stripslashes( $value ) ) ) {
				$errors[] = $value;
			}
		}

		return $errors;
	}
}
