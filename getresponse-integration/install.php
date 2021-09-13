<?php

function install_getresponse_tables() {
	global $wpdb;
	global $charset_collate;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "gr_orders_map (
			  `store_id` varchar(16) NOT NULL DEFAULT '',
			  `gr_order_id` varchar(16) NOT NULL DEFAULT '',
			  `woocommerce_order_id` bigint(20) unsigned NOT NULL,
			  UNIQUE KEY `store_id` (`store_id`,`gr_order_id`),
			  KEY `woocommerce_order_id` (`woocommerce_order_id`)
			)" . $charset_collate;

	dbDelta( $sql );

	$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "gr_products_map (
			  `store_id` varchar(16) NOT NULL DEFAULT '',
			  `gr_product_id` varchar(16) NOT NULL DEFAULT '',
			  `woocommerce_product_id` bigint(20) unsigned NOT NULL,
			  UNIQUE KEY `store_id` (`store_id`,`gr_product_id`),
			  KEY `woocommerce_product_id` (`woocommerce_product_id`)
			) " . $charset_collate;

	dbDelta( $sql );

	$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "gr_variants_map (
	  `store_id` varchar(16) NOT NULL DEFAULT '',
	  `woocommerce_variant_id` bigint(20) NOT NULL,
	  `gr_variant_id` varchar(16) NOT NULL DEFAULT '',
  UNIQUE KEY `store_id` (`store_id`,`woocommerce_variant_id`),
  KEY `gr_variant_id` (`gr_variant_id`)
) " . $charset_collate;

	dbDelta( $sql );

	$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "gr_schedule_jobs_queue (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `customer_id` varchar(8) DEFAULT NULL,
		  `type` varchar(16) DEFAULT NULL,
		  `payload` text,
		  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		) " . $charset_collate;

	dbDelta( $sql );

	$sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "gr_configuration (
                `name` varchar(32) NOT NULL DEFAULT '',
                `value` text,
                UNIQUE KEY `name` (`name`)
			)" . $charset_collate;

	dbDelta( $sql );
	
    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}gr_products_map';" ) ) {
        if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}gr_products_map` LIKE 'gr_product_hash';" ) ) {
            $wpdb->query( "ALTER TABLE `" . $wpdb->prefix . "gr_products_map`
                ADD `gr_product_hash` VARCHAR(128)  NOT NULL  DEFAULT ''
                AFTER `woocommerce_product_id`;" );
        }
    }
}
