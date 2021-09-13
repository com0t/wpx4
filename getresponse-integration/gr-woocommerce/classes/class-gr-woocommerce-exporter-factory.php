<?php

namespace Getresponse\WordPress;

defined( 'ABSPATH' ) || exit;

class WooCommerceExporterFactory
{
    /**
     * @param Api $api
     */
    public static function create(Api $api)
    {
        return new WooCommerceExporter(
            new CustomerService($api),
            new OrderService($api),
            new ScheduleJobService(
                new ScheduleJobRepository(),
                new Configuration()
            )
        );
    }

}