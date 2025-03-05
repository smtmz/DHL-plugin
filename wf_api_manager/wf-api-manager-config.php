<?php

$product_name        = 'dhl'; // name should match with 'Software Title' configured in server, and it should not contains white space
$product_version     ='7.0.2';
$product_slug        = 'dhl-woocommerce-shipping/dhl-woocommerce-shipping.php'; //product base_path/file_name
$serve_url           = 'https://elextensions.com/';
$plugin_settings_url = admin_url('admin.php?page=wc-settings&tab=shipping&section=wf_dhl_woocommerce_shipping_method');

$script_name = basename( isset( $_SERVER['PHP_SELF'] ) ? sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) : '' );
if ( in_array( $script_name, array( 'plugins.php', 'update-core.php' ) ) ) {
	$current       = get_site_transient( 'update_core' );
	$timeout       = 1 * HOUR_IN_SECONDS;
	$need_to_check = isset( $current->last_checked ) && $timeout < ( time() - $current->last_checked );
	if ( $need_to_check ) {
		wp_clean_update_cache();
	}
}
require_once __DIR__ . '/wf_api_manager.php';

//include api manager
new \Elex\DHL\WF_API_Manager( $product_name, $product_version, $product_slug, $serve_url, $plugin_settings_url );
