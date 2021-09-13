<?php
namespace Getresponse\WordPress;

use WC_Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class ExportedContact
 * @package Getresponse\WordPress
 */
class ExportedContact {

    const TYPE_CUSTOMER = 'customer';
    const TYPE_GUEST = 'guest';

    /** @var string */
    private $type;

    /** @var int */
    private $id;

    /** @var string */
    private $first_name;

    /** @var string */
    private $last_name;

    /** @var string */
    private $email;

    /** @var string */
    private $address;

    /** @var string */
    private $city;

    /** @var string */
    private $state;

    /** @var string */
    private $phone;

    /** @var string */
    private $country;

    /** @var string */
    private $company;

    /** @var string */
    private $postcode;

    public function __construct(
        $type,
        $id,
        $first_name,
        $last_name,
        $email,
        $address,
        $city,
        $state,
        $phone,
        $country,
        $company,
        $postcode
    ) {
        $this->type = $type;
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->phone = $phone;
        $this->country = $country;
        $this->company = $company;
        $this->postcode = $postcode;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_email()
    {
        return $this->email;
    }

    public function get_name()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function get_type()
    {
        return $this->type;
    }

    public function to_array()
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'phone' => $this->phone,
            'country' => $this->country,
            'company' => $this->company,
            'postcode' => $this->postcode
        ];
    }

    public static function createFromWcCustomer(WC_Customer $customer)
    {
        return new self(
            self::TYPE_CUSTOMER,
            $customer->get_id(),
            $customer->get_first_name(),
            $customer->get_last_name(),
            $customer->get_email(),
            trim($customer->get_billing_address_1() . ' ' . $customer->get_billing_address_2()),
            $customer->get_billing_city(),
            $customer->get_billing_state(),
            $customer->get_billing_phone(),
            $customer->get_billing_country(),
            $customer->get_billing_company(),
            $customer->get_billing_postcode()
        );
    }

    public static function createFromGuestParams(array $data, $post_id)
    {
        $params = array();

        foreach ($data as $row) {

            if (preg_match('/\_billing\_([a-z\_0-9]+)/', $row['meta_key'], $result) && isset($result[1])) {
                $params[$result[1]] = $row['meta_value'];
            }
        }

        return new self(
          self::TYPE_GUEST,
            $post_id,
            isset($params['first_name']) ? $params['first_name'] : '',
            isset($params['last_name']) ? $params['last_name'] : '',
            isset($params['email']) ? $params['email'] : '',
            isset($params['address_1']) ? $params['address_1'] : '',
            isset($params['city']) ? $params['city'] : '',
            isset($params['state']) ? $params['state'] : '',
            isset($params['phone']) ? $params['phone'] : '',
            isset($params['country']) ? $params['country'] : '',
            isset($params['company']) ? $params['company']: '',
            isset($params['postcode']) ? $params['postcode'] : ''
        );
    }
}
