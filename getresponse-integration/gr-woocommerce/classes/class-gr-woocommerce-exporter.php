<?php
namespace Getresponse\WordPress;

defined( 'ABSPATH' ) || exit;

/**
 * Class WooCommerceExporter
 * @package Getresponse\WordPress
 */
class WooCommerceExporter {

	/** @var CustomerService */
	private $customerService;
    /** @var OrderService */
	private $orderService;
	/** @var ScheduleJobService */
	private $scheduleJobService;

    /**
     * @param CustomerService $customerService
     * @param OrderService $orderService
     * @param ScheduleJobService $scheduleJobService
     */
	public function __construct($customerService, $orderService, $scheduleJobService)
    {
	    $this->customerService = $customerService;
	    $this->orderService = $orderService;
	    $this->scheduleJobService = $scheduleJobService;
	}

	/**
	 * @param array|ExportedContact[] $contacts
	 * @param $campaign_id
	 * @param $autoresponder_id
	 * @param $customs
	 * @param $store_id
	 * @param $use_schedule
	 *
	 * @throws \Exception
	 */
	public function export_customers(
		$contacts,
		$campaign_id,
		$autoresponder_id,
		$customs,
		$store_id,
		$use_schedule
	) {
		foreach ($contacts as $contact) {
			try {
				$export_customer = ExportCustomerFactory::create_from_params(
					$campaign_id,
					$contact->get_type(),
					$contact->get_id(),
					$customs,
					$autoresponder_id,
					$store_id
				);

				if ($use_schedule) {
					$this->scheduleJobService->add_schedule_job(
						ScheduleJob::EXPORT_CUSTOMER,
						$export_customer
					);
				} else {
					$this->export_customer(
					    $export_customer,
                        $contact,
                        $this->getOrders($contact)
                    );
				}
			} catch ( ApiException $e ) {
			}
		}
	}

    /**
     * @param ExportCustomer $exportCustomerCommand
     * @param ExportedContact $contact
     * @param \WC_Order[] $orders
     * @return bool|void
     */
    public function export_customer($exportCustomerCommand, $contact, $orders)
    {
        $customs = array();
        $data = $contact->to_array();

        foreach ($exportCustomerCommand->get_custom_fields() as $woo_custom_name => $gr_custom_name) {
            if (!empty($data[$woo_custom_name])) {
                $customs[$gr_custom_name] = $data[$woo_custom_name];
            }
        }

        $grCustomerId = $this->customerService->createOrGetContact(
            $exportCustomerCommand->get_campaign_id(),
            $contact->get_name(),
            $contact->get_email(),
            $exportCustomerCommand->get_autoresponder_id(),
            $customs
        );

        /**
         * first condition means that contact was added and is waiting in queue
         * second condition is obvious
         * third condition is obvious
         */
        if (null === $grCustomerId || empty($orders) || empty($exportCustomerCommand->get_store_id())) {
            return;
        }

        foreach ($orders as $order) {
            try {
                $this->export_order($order, $exportCustomerCommand->get_store_id(), $grCustomerId);
            } catch (ApiException $e) {
            } catch (EcommerceException $e) {
            } catch (ProductVariantsNotFoundException $e) {}
        }
	}

    /**
     * @param \WC_Order $order
     * @param string $store_id
     * @param string $contact_id
     * @throws EcommerceException
     * @throws ApiException
     * @throws ProductVariantsNotFoundException
     */
	public function export_order($order, $store_id, $contact_id)
    {
        $this->orderService->upsert_order(
            OrderFactory::create_from_params(
                $store_id,
                null,
                $contact_id,
                $order->get_id(),
                true
            )
        );
	}

    /**
     * @param ExportedContact $contact
     */
    private function getOrders($contact)
    {
        if (ExportedContact::TYPE_CUSTOMER === $contact->get_type()) {
            return wc_get_orders(array(
                'meta_key' => '_customer_user',
                'meta_value' => $contact->get_id(),
            ));
        }

        return [wc_get_order($contact->get_id())];
    }
}
