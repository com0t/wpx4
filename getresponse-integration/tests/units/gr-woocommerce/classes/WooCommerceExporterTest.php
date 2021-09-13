<?php

namespace Getresponse\WordPress\Tests;

use Getresponse\WordPress\CartService;
use Getresponse\WordPress\CustomerService;
use Getresponse\WordPress\ExportCustomer;
use Getresponse\WordPress\ExportedContact;
use Getresponse\WordPress\OrderService;
use Getresponse\WordPress\OrdersMap;
use Getresponse\WordPress\ProductService;
use Getresponse\WordPress\ProductsMap;
use Getresponse\WordPress\ScheduleJobService;
use Getresponse\WordPress\WooCommerceExporter;

/**
 * Class WooCommerceExporterTest
 * @package Getresponse\WordPress\Tests
 */
class WooCommerceExporterTest extends BaseTestCase
{
    /** @var CustomerService | \PHPUnit_Framework_MockObject_MockObject */
    private $customerServiceMock;
    /** @var CartService | \PHPUnit_Framework_MockObject_MockObject */
    private $cartServiceMock;
    /** @var OrderService | \PHPUnit_Framework_MockObject_MockObject */
    private $orderServiceMock;
    /** @var ProductService | \PHPUnit_Framework_MockObject_MockObject */
    private $productServiceMock;
    /** @var OrdersMap | \PHPUnit_Framework_MockObject_MockObject */
    private $ordersMapMock;
    /** @var ProductsMap | \PHPUnit_Framework_MockObject_MockObject */
    private $productsMapMock;
    /** @var ScheduleJobService | \PHPUnit_Framework_MockObject_MockObject */
    private $scheduleJobServiceMock;
    /** @var \WC_Customer | \PHPUnit_Framework_MockObject_MockObject */
    private $wcCustomerMock;

    /** @var WooCommerceExporter */
    private $wooCommerceExporter;

    protected function setUp()
    {
        $this->customerServiceMock = $this->createMockWithoutConstructor(CustomerService::class);
        $this->cartServiceMock = $this->createMockWithoutConstructor(CartService::class);
        $this->orderServiceMock = $this->createMockWithoutConstructor(OrderService::class);
        $this->productServiceMock = $this->createMockWithoutConstructor(ProductService::class);
        $this->ordersMapMock = $this->createMockWithoutConstructor(OrdersMap::class);
        $this->productsMapMock = $this->createMockWithoutConstructor(ProductsMap::class);
        $this->scheduleJobServiceMock = $this->createMockWithoutConstructor(ScheduleJobService::class);
        $this->wcCustomerMock = $this->createMockWithoutConstructor(\WC_Customer::class);

        $this->wooCommerceExporter = new WooCommerceExporter(
            $this->customerServiceMock,
            $this->orderServiceMock,
            $this->scheduleJobServiceMock
        );
    }

    /**
     * @test
     */
    public function shouldExportCustomerWithoutOrders()
    {
        $this->customerServiceMock
            ->expects(self::once())
            ->method('createOrGetContact')
            ->with('cpId', 'firstname lastname', 'name@gmail.com', null, []);

        $exportCustomer = new ExportCustomer('cpId', 26, 1, [], null, 'stId');

        $exportedContact = new ExportedContact(
            ExportedContact::TYPE_CUSTOMER,
            '1',
            'firstname',
            'lastname',
            'name@gmail.com',
            'address',
            'city',
            'state',
            'phone',
            'country',
            'company',
            'postcode'
        );

        $this->wooCommerceExporter->export_customer(
            $exportCustomer,
            $exportedContact,
            []
        );
    }
}
