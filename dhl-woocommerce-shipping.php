<?php
/*
	Plugin Name: ELEXtensions DHL Express/DHL Paket WooCommerce Shipping with Print Label
	Plugin URI: https://elextensions.com/plugin/woocommerce-dhl-express-ecommerce-paket-shipping-plugin-with-print-label/
	Description: Obtain real time shipping rates and Print shipping labels and Print shipping labels via DHL Paket Shipping API.
	Version: 7.0.2
	WC requires at least: 2.6.0
	WC tested up to: 8.9
	Author: ELEXtensions
	Author URI: https://elextensions.com/
	Copyright: 2019 ELEXtensions.
	Text Domain: wf-shipping-dhl
*/

if (!defined('WF_DHL_PAKET_PATH')) {
	define('WF_DHL_PAKET_PATH', plugins_url('', __FILE__));
}

if (!defined('WF_DHL_PAKET_EXPRESS_ROOT_PATH')) {
	define('WF_DHL_PAKET_EXPRESS_ROOT_PATH', plugin_dir_path(__FILE__));
}
if (!defined('WF_DHL_PARCEL_EXPRESS_ROOT_PATH')) {
	define('WF_DHL_PARCEL_EXPRESS_ROOT_PATH', plugin_dir_path(__FILE__));
}
	define('WF_DHL_PAKET_ID', 'wf_dhl_paket_shipping');
	define('WF_DHL_PARCEL_ID', 'wf_dhl_parcel_shipping');
	define('EXPRESS_FPDF_FONTPATH', plugin_dir_path(__FILE__) . 'dhl_express/includes/fpdf/font/');
	define('ELEX_DHL_SOFTWARE_VERSION', '4.0.8');

if (!defined('WF_DHL_ID')) {
	define('WF_DHL_ID', 'wf_dhl_shipping');
}
if (!defined('WF_DHL_ECOMMERCE_ID')) {
	define('WF_DHL_ECOMMERCE_ID', 'wf_dhl_ecommerce_shipping');
}
if ( ! defined( 'WF_DHL_PAKET_EXPRESS_ROOT_URL' ) ) {
	define( 'WF_DHL_PAKET_EXPRESS_ROOT_URL', plugin_dir_url( __FILE__ ) );
}

if (!defined('ELEX_DHL_EXPRESS_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION')) {
	if (in_array('elex-dhl-express-auto-label-generate-email-add-on/elex-dhl-express-auto-label-generate-email-add-on.php', get_option('active_plugins'))) {
			define('ELEX_DHL_EXPRESS_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION_PATH', WP_PLUGIN_DIR . '/elex-dhl-express-auto-label-generate-email-add-on/');
			define('ELEX_DHL_EXPRESS_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION', true);
	} else {
		define('ELEX_DHL_EXPRESS_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION', false);
	}
}

if (!defined('ELEX_DHL_PAKET_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION')) {
	if (in_array('elex-dhl-paket-auto-label-generate-email-add-on/elex-dhl-paket-auto-label-generate-email-add-on.php', get_option('active_plugins'))) {
			define('ELEX_DHL_PAKET_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION_PATH', WP_PLUGIN_DIR . '/elex-dhl-paket-auto-label-generate-email-add-on/');
			define('ELEX_DHL_PAKET_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION', true);
	} else {
		define('ELEX_DHL_PAKET_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION', false);
	}
}

if (!defined('ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION')) {
	if (in_array('elex-dhl-india-add-on/elex-dhl-india-add-on.php', get_option('active_plugins'))) {
		define('ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION_PATH', WP_PLUGIN_DIR . '/elex-dhl-india-add-on/');
		define('ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION', true);
	} else {
		define('ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION', false);
	}
}

// review component
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once  ABSPATH . 'wp-admin/includes/plugin.php';
}
include_once __DIR__ . '/review_and_troubleshoot_notify/review-and-troubleshoot-notify-class.php';
$data                      = get_plugin_data( __FILE__ );
$data['name']              = $data['Name'];
$data['basename']          = plugin_basename( __FILE__ );
$data['documentation_url'] = 'https://elextensions.com/knowledge-base/set-up-woocommerce-dhl-express-elex-woocommerce-dhl-express-ecommerce-paket-shipping-plugin-print-label/';
$data['support_url']       = 'http://support.elextensions.com/';
$data['rating_url']        = 'https://elextensions.com/plugin/woocommerce-dhl-express-ecommerce-paket-shipping-plugin-with-print-label/#7';

new \Elex_Review_Components( $data );
// High performance order tables compatibility.
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

function wf_merge_pre_activation() {
	// Checking whether WooCommerce is activated or not
	// if ( !is_plugin_active('woocommerce/woocommerce.php') ){
	//     deactivate_plugins( basename( __FILE__ ) );
	//     wp_die( __("Please activate WooCommerce", "wf-usps-stamps-woocommerce" ), "", array('back_link' => 1 ));
	// }
	

	//check if basic version is there
	if (is_plugin_active('dhl-woocommerce-shipping-method/dhl-woocommerce-shipping.php') || is_plugin_active('elex-woo-dhl-express-shipping/elex-woo-dhl-express-shipping.php') ) {
		deactivate_plugins(basename(__FILE__));
		wp_die(__('Oops! You tried installing the premium version without deactivating and deleting the basic version. Kindly deactivate and delete DHL(Basic) Woocommerce Extension and then try again', 'wf-shipping-dhl'), '', array('back_link' => 1));
	}
}

	register_activation_hook(__FILE__, 'wf_merge_pre_activation');

	/**
	 * Check if WooCommerce is active
	 */
	require_once ABSPATH . '/wp-admin/includes/plugin.php';

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) || ( is_multisite() && is_plugin_active_for_network('woocommerce/woocommerce.php') )) {

	include_once 'dhl-deprecated-functions.php';

	if (!function_exists('wf_dhl_paket_is_eu_country')) {
		function wf_dhl_paket_is_eu_country( $countrycode, $destinationcode) {
			$eu_countrycodes = array(
				'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE',
				'ES', 'FI', 'FR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV',
				'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
				'HR', 'GR',

			);
			return ( in_array($countrycode, $eu_countrycodes) && in_array($destinationcode, $eu_countrycodes) );
		}
	}

	if (!function_exists('wf_get_settings_url')) {
		function wf_get_settings_url() {
			return version_compare(WC()->version, '2.1', '>=') ? 'wc-settings' : 'woocommerce_settings';
		}
	}

	if (!function_exists('wf_dhl_is_eu_country')) {
		function wf_dhl_is_eu_country( $sourcecode, $destinationcode) {
			$eu_countrycodes = array(
				'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE',
				'ES', 'FI', 'FR',  'GR', 'HR', 'HU', 'IE', 
				'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 
				'RO', 'SE', 'SI', 'SK'
			);
			return ( in_array($sourcecode, $eu_countrycodes) && in_array($destinationcode, $eu_countrycodes) );
		}
	}

	if (!class_exists('wf_dhl_wooCommerce_shipping_setup')) {

		class wf_dhl_wooCommerce_shipping_setup {
		
			public function __construct() {
				$this->wf_init();
				//  add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
				add_action('woocommerce_shipping_init', array($this, 'wf_dhl_wooCommerce_shipping_init'));
				add_filter('woocommerce_shipping_methods', array($this, 'wf_dhl_wooCommerce_shipping_methods'));
				add_filter('admin_enqueue_scripts', array($this, 'wf_dhl_scripts'));
				add_filter('admin_notices', array($this, 'wf_dhl_key_check'), 99);

				add_filter('elex_mv_add_product_carrier_addition', array($this, 'add_dhl_field_elex_multivendor'));

				add_filter('elex_mv_products_specific_plugin_fields_to_save', array($this, 'add_dhl_meta_fields_for_elex_mv' ), 10, 2 );

				add_filter('elex_mv_products_specific_plugin_fields_to_show', array($this,'add_dhl_meta_fields_for_elex_mv_to_show'), 10, 1);
					
				add_action('admin_footer', array($this, 'wf_add_bulk_action_links'), 10); //to add bulk option to orders page
				add_action('woocommerce_admin_order_actions_end', array($this, 'wf_add_print_label_buttons')); //to add print option at the end of each orders in orders page
				//Add Receiver EORI number
				 add_action('woocommerce_checkout_fields', array($this, 'elex_dhl_eori_checkout_fields'));
				add_action('woocommerce_checkout_update_order_meta', array( $this ,'elex_dhl_eori_field'));
				//Save EORI in Account Field
				add_filter('woocommerce_edit_account_form', array($this, 'my_account_address_eori_number'));
				add_action('woocommerce_save_account_details', array( $this , 'save_account_details'), 12, 1);

				if (is_admin()) {
					add_action('woocommerce_product_options_shipping', array($this, 'wf_additional_product_shipping_options'));
					add_action('woocommerce_process_product_meta', array($this, 'wf_save_additional_product_shipping_options'));
				}
				add_filter('woocommerce_billing_fields', array($this, 'elex_require_wc_company_field'), 10);

				add_filter( "elex_mv_shipping_info_shipping_method" , array( $this, "elex_mv_shipping_values" ), 10, 2 );


				add_filter("elex_mv_shipping_validate_vendor_credentials_woocommerce_dhl_shipping", array($this, 'shipping_validate_vendor_credentials'), 10, 3 );

				add_action("elex_mv_shipping_save_vendor_credentials_woocommerce_dhl_shipping", array($this, "shipping_save_vendor_credentials"), 10 , 2);


				add_action("wp_ajax_woocommerce_dhl_shipping_services_changed_to_save", array($this, 'save_vendor_service_changes'));

				add_action("wp_ajax_woocommerce_dhl_shipping_save_carriers_for_rate",  array($this, 'save_carriers_enabled_for_vendor' ));
				//debug log message in the console.
				add_action('wp_enqueue_scripts', array($this, 'wf_log_scripts'));
				add_action( 'wp_ajax_dhl_get_rates_request_logs', array( $this, 'dhl_get_rates_request_logs' ) );
				include_once 'dhl_express/includes/dhl-extra-fields-show.php';


			}
			//debug log message in the console.
			public function dhl_get_rates_request_logs() {
				$settings = get_option('woocommerce_' . WF_DHL_ID . '_settings', null);

				try {
					check_ajax_referer( 'elex_dhl_cart_checkout_logs_nonce' );
					$response_array = array();
					if($settings['debug'] === 'yes'){
						$response_array = array(
							'dhl_rates_logs'   => WC()->session->get( 'debug_message' ),
						);
			
						WC()->session->set( 'debug_message', null );
					}

					wp_send_json_success( $response_array );
				} catch ( Exception $e ) {
					wp_send_json_error();
				}
			}
			//debug log message in the console.
			public function wf_log_scripts(){
				wp_enqueue_script( 'dhl_cart_checkout_debug_logs', plugins_url(basename(plugin_dir_path(__FILE__)) . '/dhl_express/resources/js/dhl_cart_checkout_scripts.js', basename(__FILE__)), array(), '2.0.0', true  );
			    wp_localize_script(
				   'dhl_cart_checkout_debug_logs',
				   'dhl_cart_checkout',
				   array(
					   'ajax_url' => admin_url( 'admin-ajax.php' ),
					   'nonce'    => wp_create_nonce( 'elex_dhl_cart_checkout_logs_nonce' ),
				   )
			   );
		   }
			public function save_carriers_enabled_for_vendor(){

	

				$store_id = isset($_POST['store_id']) ? $_POST['store_id'] : '' ;
				$formdata = isset($_POST['form']) ? $_POST['form'] : '' ;
	
				parse_str($formdata, $all_enabled_carrier);
	
	
			

				update_post_meta($store_id, 'woocommerce_dhl_shipping_enabled_carriers_for_rate', $all_enabled_carrier );
	
				wp_send_json_success('saved', 200 );
				die;
	
			}

			public function shipping_save_vendor_credentials($store, $credentials){
			
				$credentials =  apply_filters('override_credentials_mv',$credentials);

			

				update_post_meta($store->get_id(),'woocommerce_wf_dhl_shipping_credentials_settings', $credentials);
				

			}

			public function shipping_validate_vendor_credentials( $data, $store_id, $credentials ){

			

				
					if(!isset($credentials['site_id'])){
						$data['errors']['site_id'] = __( 'Fill Site Id.', 'elex_mv' );
					}	
					if(!isset($credentials['site_password'])){
						$data['errors']['site_password'] = __( 'Fill Site Password.', 'elex_mv' );
					}	
					if(!isset($credentials['account_number'])){
						$data['errors']['account_number'] = __( 'Fill Account Number.', 'elex_mv' );
					}
					if(!isset($credentials['base_country'])){
						$data['errors']['base_country'] = __( 'Country Not Given.', 'elex_mv' );
					}
					
				$data = apply_filters( 'elex_mv_credentials_validation', $data );

		

				if(!empty($data['errors'])){
					return $data;
				}


				require_once 'dhl_express/includes/settings/validate_credentials.php';

				$validate = wf_validate_crendials($credentials['production'], $credentials['site_id'], $credentials['site_password'], $credentials['base_country'], 'yes' );

				if($validate == false ){
					$validation_errors['errors']['validation'] = "Validation Not successful";
					wp_send_json_error( $validation_errors['errors'], 422 );

				}else{

					return $data;
				}

			}
		
			public function save_vendor_service_changes(){
			
			
				if(isset($_POST['store_id']) && isset($_POST['services'])){
					$services_req = $_POST['services'];
					$validation_errors = $this->validating_services($services_req);
		
					

					if ( isset( $validation_errors['errors'] ) && count( $validation_errors['errors'] ) ) {
						wp_send_json_error( $validation_errors['errors'], 422 );
						die;
					}
					update_post_meta( $_POST['store_id'],'woocommerce_wf_dhl_shipping_settings', $_POST['services']);
					wp_send_json_success('saved', 200 );
					die;

				}
			} 

			public function validating_services($services){

				$data = array(
					'errors'  => array(),
				);	


				
				foreach($services['DHL']['services'] as  $service){
					if($service['price_adjustment']['value'] && !preg_match('/^[0-9 ]*$/', $service['price_adjustment']['value'])){
						$data['errors'][$service['id']]['price_adjustment'] = __( 'Only numbers are allowed.', 'elex_mv' );
					}
					if(!$service['name']){
						$data['errors'][$service['id']]['name'] = __( 'Fill Name.', 'elex_mv' );
					}
				}
				$data = apply_filters( 'elex_mv_services_validation', $data );
				return $data;
			}

			public function elex_mv_shipping_values($shipping_values,$store) {

				$my_services = get_option('woocommerce_wf_dhl_shipping_settings');

				$service_req = array();
			
				$services_array                      = include  'dhl_express/includes/data-wf-service-codes.php' ;

				$carrier_status = get_post_meta( $store->get_id(), 'woocommerce_dhl_shipping_enabled_carriers_for_rate', true );


			
				foreach( $my_services['services'] as $service => $value ){

					$var = array(
						'id'=>'',
						'name'=>'',
						'enabled'=>'',
						'price_adjustment'=>array(
							'type'=>'',
							'value'=>''
						)
					);

					$var['id'] =  $service;
					$var['name'] = $services_array[$service];
					$var['enabled'] = isset($value['enabled']) ? $value['enabled'] : '';
					

					if( isset( $value['adjustment'] ) && !empty( $value['adjustment'] ) ){
						$var['price_adjustment']['type'] = 'flat' ;
						$var['price_adjustment']['value'] = $value['adjustment'] ;

					}
					if( isset( $value['adjustment'] ) && !empty( $value['adjustment'] ) ){
						$var['price_adjustment']['type'] = 'percent' ;
						$var['price_adjustment']['value'] = $value['adjustment'] ;
					}

					$service_req[] = $var;

				}
			

				if( ! get_post_meta( $store->get_id(), 'woocommerce_wf_dhl_shipping_settings', true ) ){

			
					
					// update_post_meta($store->get_id(),'woocommerce_wf_dhl_shipping_settings', $service_req);

					$service_req = array(
						'DHL'=>array(
							'name'=>'DHL',
							'enabled' => isset($carrier_status['DHL']) ? $carrier_status['DHL'] : '' ,
							'services' => $service_req
							)
						);
				
				}elseif( get_post_meta( $store->get_id(), 'woocommerce_wf_dhl_shipping_settings', true ) ){

					// update_post_meta($store->get_id(),'woocommerce_wf_dhl_shipping_settings', '');

					$service_req= get_post_meta( $store->get_id(), 'woocommerce_wf_dhl_shipping_settings', true );	

					

				
				}

			

				if(get_post_meta($store->get_id(), 'woocommerce_wf_dhl_shipping_credentials_settings', true )){

					$vendor_credentials = get_post_meta($store->get_id(), 'woocommerce_wf_dhl_shipping_credentials_settings', true );

			
					$credentials = array (

						array(
							'id' => '_custom_account_number_field',
							'placeholder' => __('Enter Account Number', 'elex-mv'),
							'label' => __('Account  Number', 'elex-mv'),
							'type' => 'text',
							'required'=> true,
							'value' => $vendor_credentials['account_number']
						
						),
						array(
							'id' => '_custom_test_mode_field',
							'placeholder' => __('yes', 'elex-mv'),
							'label' => __('Live Mode', 'elex-mv'),
							'type' => 'select',
							'options' => array(
								'' => 'Select',
								'yes' => 'Yes',
								'no' => 'No',
							),
							'default' => isset($vendor_credentials['production'])? $vendor_credentials['production'] : '',
							'required'=> true,
						
						),
						array(
							'id' => '_custom_site_id_field',
							'placeholder' => __('Site Id', 'elex-mv'),
							'label' => __('Site Id', 'elex-mv'),
							'type' => 'text',
							'required'=> true,
							'value' => $vendor_credentials['site_id']
						
						),
						array(
							'id' => '_custom_site_password_field',
							'placeholder' => __('Enter Site Password','elex-mv'),
							'label' => __('Site Password', 'elex-mv'),
							'type' => 'text',
							'required'=> true,
							'value' => $vendor_credentials['site_password']

						
						),
						array(
							'id' => '_custom_base_country_field',
							'placeholder' => __('Enter Account Payment Country','elex-mv'),
							'label' => __('Account Payment Country', 'elex-mv'),
							'type' => 'text',
							'required'=> true,
							'value' => $vendor_credentials['base_country']

						
						)
						);
				}else{
					 $credentials = array (

						array(
							'id' => '_custom_account_number_field',
							'placeholder' => __('Enter Account Number', 'elex-mv'),
							'label' => __('Account  Number', 'elex-mv'),
							'type' => 'text',
							'required'=> true,
							'value' => ''
						
						),
						array(
							'id' => '_custom_test_mode_field',
							'placeholder' => __('yes', 'elex-mv'),
							'label' => __('Live Mode', 'elex-mv'),
							'type' => 'select',
							'required'=> true,
							'value' => '',
							'options' => array(
								'' => 'Select',
								'yes' => 'Yes',
								'no' => 'No',
							), 
							'default' => '',

						),
						array(
							'id' => '_custom_site_id_field',
							'placeholder' => __('Site Id', 'elex-mv'),
							'label' => __('Site Id', 'elex-mv'),
							'type' => 'text',
							'required'=> true,
							'value' => ''
						
						),
						array(
							'id' => '_custom_site_password_field',
							'placeholder' => __('Enter Site Password','elex-mv'),
							'label' => __('Site Password', 'elex-mv'),
							'type' => 'text',
							'required'=> true,
							'value' => ''

						
						),
						array(
							'id' => '_custom_base_country_field',
							'placeholder' => __('Enter Account Payment Country','elex-mv'),
							'label' => __('Account Payment Country', 'elex-mv'),
							'type' => 'text',
							'required'=> true,
							'value' => ''

						
						)
						); 
				}

				$shipping_values[]  = array ( 
										   'name' => 'Elextensions Woocoomerce DHL Shipping',
										   'description' => 'For DHL services',
										   'slug' => 'woocommerce_dhl_shipping',
										   'carriers' => $service_req,
										   'credentials' => $credentials,
									  	);

							  

							
				 return $shipping_values;
   
			}

			public function elex_require_wc_company_field( $fields) {
				$general_settings = get_option('woocommerce_wf_dhl_shipping_settings');

				if (isset($general_settings['billing_company_format']) && $general_settings['billing_company_format'] === 'mandatory') {
					$fields['billing_company']['required'] = true;
				} else {
					$fields['billing_company']['required'] = false;
				}
				return $fields;
			}

			function wf_dhl_key_check() {
				$activation_check = get_option('dhl_activation_status', 'no_key');
				if (empty($activation_check) || $activation_check != 'active') {
					echo sprintf('<div id="message" class="error"><p>' . __('%1$s - Your license is expired/not activated. Please <a href="%2$s">update your License</a> to avail latest updates and stability improvements.', 'wf-woocommerce-packing-list') . '</p></div>', '<b>DHL Express / eCommerce / Paket / Parcel Shipping Plugin with Print Label </b>', admin_url('admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_shipping&subtab=licence'));
				}
			}

			function wf_additional_product_shipping_options() {
				//HS code field
				woocommerce_wp_text_input(array(
					'id' => '_wf_hs_code',
					'label' => __('HS Tariff Number / Commodity Code (DHL)', 'wf-shipping-dhl'),
					'description' => __('The Harmonized Commodity Description and Coding System, also known as the Harmonized System (HS) of tariff nomenclature is an internationally standardized system of names and numbers to classify traded products.'),
					'desc_tip' => 'true',
					'placeholder' => '',
				));

				  //Product Description for International Purpose
				woocommerce_wp_text_input(array(
					'id' => '_wf_product_description',
					'label' => __('Waybill goods description', 'wf-shipping-dhl'),
					'description' => __('A note on product description can be updated here. This will be part of the commercial invoice. ', 'wf-shipping-dhl'),
					'desc_tip' => 'true',
					'placeholder' => '',
				));

				//Country of manufacture
				woocommerce_wp_text_input(array(
					'id' => '_wf_manufacture_country',
					'label' => __('Manufacture Country Code (DHL)', 'wf-shipping-dhl'),
					'description' => __('A note on the country of manufacture can be updated here. This will be part of the commercial invoice. ', 'wf-shipping-dhl'),
					'desc_tip' => 'true',
					'placeholder' => '',
				));

				woocommerce_wp_select(array(
					'id' => '_wf_dhl_signature',
					'label' => __('Signature options (DHL)', 'wf-shipping-dhl'),
					'options' => array(
						'0' => __('No Signature Required', 'wf-shipping-dhl'),
						'1' => __('Content Signature', 'wf-shipping-dhl'),
						'2' => __('Named Signature', 'wf-shipping-dhl'),
						'3' => __('Adult Signature', 'wf-shipping-dhl'),
						'4' => __('Contract Signature', 'wf-shipping-dhl'),
						'5' => __('Alternative Signature', 'wf-shipping-dhl'),
					),
					'description' => __('All international shipments require a signature for delivery. Please choose the signature service required for this product.', 'wf-shipping-dhl'),
					'desc_tip' => 'true',
				));

				   
				   

				/*
				* Providing Special service types for restricted commodities and dangerous goods
				*/
				woocommerce_wp_select(array(
					'id' => '_wf_product_special_service',
					'label' => __('Special Service (DHL)', 'wf-shipping-dhl'),
					'options' => array(
						'N' => __('NONE', 'wf-shipping-dhl'),
						'NA' => __('NOT APPLICABLE', 'wf-shipping-dhl'),
						'HECAOI1A' => __('DANGEROUS GOODS (HE) PI965 1A', 'wf-shipping-dhl'),
						'HECAOI1B' => __('DANGEROUS GOODS (HE) PI965 1B', 'wf-shipping-dhl'),
						'HEDGDI966' => __('DANGEROUS GOODS (HE) PI966', 'wf-shipping-dhl'),
						'HEDGDI967' => __('DANGEROUS GOODS (HE) PI967', 'wf-shipping-dhl'),
						'HB' => __('LITHIUM ION PI965 SECTION II (HB)', 'wf-shipping-dhl'),
						'HD' => __('LITHIUM ION PI966 SECTION II (HD)', 'wf-shipping-dhl'),
						'HV' => __('LITHIUM ION PI967 SECTION II (HV)', 'wf-shipping-dhl'),
						'HECAOM1A' => __('DANGEROUS GOODS (HE) PI968 1A', 'wf-shipping-dhl'),
						'HECAOM1B' => __('DANGEROUS GOODS (HE) PI968 1B', 'wf-shipping-dhl'),
						'HEDGDM969' => __('DANGEROUS GOODS (HE) PI969', 'wf-shipping-dhl'),
						'HEDGDM970' => __('DANGEROUS GOODS (HE) PI970', 'wf-shipping-dhl'),
						'HM' => __('LITHIUM METAL PI969 SECTION II (HM)', 'wf-shipping-dhl'),
						'HW' => __('LITHIUM METAL PI970 SECTION II (HW)', 'wf-shipping-dhl'),
						'HVHW' => __('LITHIUM ION PI967 SECTION II (HV)
                                            LITHIUM METAL PI970 SECTION II (HW)', 'wf-shipping-dhl'),
						'HH' => __('DANGEROUS GOODS IN EXCEPTED QUANTITIES (HH)', 'wf-shipping-dhl'),
						'HK' => __('CONSUMER GOODS ID8000 (HK)', 'wf-shipping-dhl'),
						'HY' => __('BIOLOGICAL UN3373 (HY)', 'wf-shipping-dhl'),
						'HEFG' => __('DANGEROUS GOODS (HE) FLAMMABLE GAS', 'wf-shipping-dhl'),
						'HENFG' => __('DANGEROUS GOODS (HE) NON-FLAMMABLE, NON-TOXIC GAS', 'wf-shipping-dhl'),
						'HEFL' => __('DANGEROUS GOODS (HE) FLAMMABLE LIQUID', 'wf-shipping-dhl'),
						'HEFS' => __('DANGEROUS GOODS (HE) FLAMMABLE SOLIDS', 'wf-shipping-dhl'),
						'HESCS' => __('DANGEROUS GOODS (HE) SPONTANEOUS COMBUSTION SUBSTANCES', 'wf-shipping-dhl'),
						'HESDWW' => __('DANGEROUS GOODS (HE) SUBSTANCES DANGEROUS WHEN WET', 'wf-shipping-dhl'),
						'HEO' => __('DANGEROUS GOODS (HE) OXIDIZER', 'wf-shipping-dhl'),
						'HEOPO' => __('DANGEROUS GOODS (HE) Organic Peroxides', 'wf-shipping-dhl'),
						'HETS' => __('DANGEROUS GOODS (HE) TOXIC SUBSTANCES', 'wf-shipping-dhl'),
						'HEC' => __('DANGEROUS GOODS (HE) CORROSIVES', 'wf-shipping-dhl'),
						'HEM' => __('DANGEROUS GOODS (HE) MISCELLANEOUS', 'wf-shipping-dhl'),
						'IUP' => __('LITHIUM ION PI967 Section II (LiBa in equipment) UNDER PROVISO', 'wf-shipping-dhl'),
						'MUP' => __('LITHIUM METAL PI970 Section II (LiBa in equipment) UNDER PROVISO', 'wf-shipping-dhl'),
					),
					'description' => __('Special service types for dangerous goods or Restricted commodities. By selecting one of the types, a compliance warning will be displayed on DHL labels. ', 'wf-shipping-dhl'),
					'desc_tip' => 'true',
					'placeholder' => '',
				));

				//Product UN Number for Restricted Commodities and Dangerous Goods
				woocommerce_wp_text_input(array(
					'id' => '_wf_product_un_number',
					'label' => __('UN Number (DHL)', 'wf-shipping-dhl'),
					'description' => __('You have selected Special Service. Please enter the UN number for the product. ', 'wf-shipping-dhl'),
					'desc_tip' => 'true',
					'placeholder' => '',
				));

				?>

					<script>
						jQuery(document).ready(function(){
							jQuery('#_wf_product_un_number').hide();
							jQuery('._wf_product_un_number_field').hide();
							jQuery('.woocommerce-help-tip').hide();
							var special_service_value = jQuery('#_wf_product_special_service').val();

							if((special_service_value != 'NA') && (special_service_value != 'N')){
								jQuery('#_wf_product_un_number').show();
								jQuery('._wf_product_un_number_field').show();
								jQuery('.woocommerce-help-tip').show();
							}

							jQuery('#_wf_product_special_service').change(function(){
								if((jQuery('#_wf_product_special_service').val() != 'NA') && (jQuery('#_wf_product_special_service').val() != 'N')){
									jQuery('#_wf_product_un_number').show();
									jQuery('._wf_product_un_number_field').show();
									jQuery('.woocommerce-help-tip').show();
								}else{
									jQuery('#_wf_product_un_number').hide();
									jQuery('._wf_product_un_number_field').hide();
									jQuery('.woocommerce-help-tip').hide();
								}
							});
						});
					</script>

				<?php
			}

			function wf_save_additional_product_shipping_options( $post_id) {
				$product = wc_get_product( $post_id );
				if ( !$product ) {
					return;
				}
				//HS code value
				if (isset($_POST['_wf_hs_code'])) {
					$product->update_meta_data( '_wf_hs_code', esc_attr($_POST['_wf_hs_code']));
				}

				//Country of manufacture
				if (isset($_POST['_wf_manufacture_country'])) {
					$product->update_meta_data( '_wf_manufacture_country', esc_attr($_POST['_wf_manufacture_country']));
				}


				//Product Description 
				if (isset($_POST['_wf_product_description'])) {
					$product->update_meta_data( '_wf_product_description', esc_attr($_POST['_wf_product_description']));

				}

				//Signature option
				if (isset($_POST['_wf_dhl_signature'])) {
					$product->update_meta_data( '_wf_dhl_signature', esc_attr($_POST['_wf_dhl_signature']));

				}

				/*
					Saving user selected special service type for dangerous goods
				*/
				if (empty($_POST['_wf_product_special_service'])) {
					$product->update_meta_data( '_wf_product_special_service', '');

				} else {
					$product->update_meta_data( '_wf_product_special_service', esc_attr($_POST['_wf_product_special_service']));

				}

				/*
					Saving user selected default special service type for dangerous goods
				*/
				if (empty($_POST['_wf_product_default_special_service'])) {
					$product->update_meta_data( '_wf_product_special_service', '');

				} else {
					$product->update_meta_data( '_wf_product_default_special_service', esc_attr($_POST['_wf_product_default_special_service']));

				}

				/*
					Saving UN number entered by the user for selected default special service type for dangerous goods
				*/
				if (empty($_POST['_wf_product_un_number'])) {
					$product->update_meta_data( '_wf_product_un_number', '');

				} else {
					$product->update_meta_data( '_wf_product_un_number', esc_attr($_POST['_wf_product_un_number']));
				}
				$product->save();
			}

			function wf_add_print_label_buttons( $order) {
				global $post_type;
				if( empty($post_type) && ( isset($_GET['page']) && !empty( $_GET['page']))){
					$post_type = $_GET['page'];
				}
				if ('shop_order' == $post_type || 'wc-orders' == $post_type) {
					$shipmentIds        = $order->get_meta('wf_woo_dhl_shipmentId');
					$available_order_id = $order->get_id();
					if( !empty($shipmentIds) && !is_array( $shipmentIds )){
						$shipmentIds = (array) $shipmentIds;
					}
					if (!empty($shipmentIds)) {
						$i = 0;
						foreach ($shipmentIds as $shipmentId) {
							$i++;
								
							$shipping_label = $order->get_meta('wf_woo_dhl_shippingLabel_' . $available_order_id);
								
							$download_url = admin_url('/post.php?wf_dhl_viewlabel=' . base64_encode($shipmentId . '|' . $available_order_id));
							?>
								<a disabled class="button tips "
								target="_blank"
								data-tip="<?php esc_attr_e('Download DHL Express Label', 'wf-woocommerce-packing-list'); ?>"
								href="<?php echo $download_url; ?>">
								<img src="<?php echo untrailingslashit(plugins_url('/', __FILE__)) . '/dhl_express/resources/images/label-icon.png'; ?>"
								alt="<?php esc_attr_e('Print Shipping Label', 'wf-woocommerce-packing-list'); ?>" width="14"/>
							</a>
							<?php
						}
					}

					$return_shipmentIds = $order->get_meta('wf_woo_dhl_return_shipmentId');
					if (!empty($return_shipmentIds)) {
						$i = 0;
						foreach ($return_shipmentIds as $shipmentId) {
							$i++;
							$shipping_label = $order->get_meta('wf_woo_dhl_shippingLabel_');
							$download_url   = admin_url('/post.php?wf_dhl_viewreturnlabel=' . base64_encode($shipmentId . '|' . $available_order_id ));
							?>
								<a disabled class="button tips "
								target="_blank"
								data-tip="<?php esc_attr_e('Download DHL Express Return Label', 'wf-woocommerce-packing-list'); ?>"
								href="<?php echo $download_url; ?>">
								<img src="<?php echo untrailingslashit(plugins_url('/', __FILE__)) . '/dhl_express/resources/images/label-icon.png'; ?>"
								alt="<?php esc_attr_e('Print Shipping Label', 'wf-woocommerce-packing-list'); ?>" width="14"/>
							</a>
							<?php
						}
					}
			    }
			}   

			//Create EORI field at checkout 
			function elex_dhl_eori_checkout_fields( $fields ) {
				 $general_settings = get_option('woocommerce_wf_dhl_shipping_settings');
				 $user_ein        = !empty( get_post_meta(get_current_user_id() , 'user_dhl_receiver_ein' , true) ) ? get_post_meta(get_current_user_id() , 'user_dhl_receiver_ein' , true) : '';
				 $user_eori        = !empty( get_post_meta(get_current_user_id() , 'user_dhl_receiver_eori' , true) ) ? get_post_meta(get_current_user_id() , 'user_dhl_receiver_eori' , true) : '';
				 $user_vat         = !empty( get_post_meta(get_current_user_id() , 'user_dhl_receiver_vat', true ) ) ? get_post_meta(get_current_user_id() , 'user_dhl_receiver_vat', true ) : '';
				if ( isset( $general_settings['include_receiver_eori_vat_number'] ) && $general_settings['include_receiver_eori_vat_number'] === 'yes' ) {
					  $fields['billing']['dhl_receiver_eori'] = array(
						  'label' =>  __('EORI Number', 'wf-shipping-dhl'),
						  'type'  => 'text',
						  'required' => 0,
						  'default'   => $user_eori,
						  'class' => array ( 'update_totals_on_change' )
						  );

					  $fields['billing']['dhl_receiver_vat'] = array(
						  'label' => __('VAT Number (DHL)', 'wf-shipping-dhl' ),
						  'type'  => 'text',
						  'required' => 0,
						  'default'   => $user_vat,
						  'class' => array ( 'update_totals_on_change' )
						  );
				}


			

				if ( isset( $general_settings['include_ein_number'] ) && $general_settings['include_ein_number'] === 'yes' ) {
					$fields['billing']['dhl_receiver_ein'] = array(
						'label' =>  __('EIN Number', 'wf-shipping-dhl'),
						'type'  => 'text',
						'required' => 0,
						'default'   => $user_ein,
						'class' => array ( 'update_totals_on_change' )
						);

				
			  }


				 return $fields;
			}

			//Save the EORI checkOut field
			public function elex_dhl_eori_field( $order_id ) {
				$order = new WC_Order( $order_id );
				if (isset($_POST['dhl_receiver_eori'])) {
					$dhl_eori = $_POST['dhl_receiver_eori'];
					$dhl_vat  = $_POST['dhl_receiver_vat'];

					if ( !empty( $dhl_eori) ) { 
						update_post_meta(get_current_user_id() , 'user_dhl_receiver_eori' , $dhl_eori);  
						$order->update_meta_data( 'elex_dhl_receiver_eori', $dhl_eori);
					} else {
						$order->update_meta_data(  'elex_dhl_receiver_eori', '');
					}


					if ( !empty( $dhl_vat) ) { 
						update_post_meta(get_current_user_id() , 'user_dhl_receiver_vat' , $dhl_vat);  
						$order->update_meta_data( 'elex_dhl_receiver_vat', $dhl_vat);
					} else {
						$order->update_meta_data( 'elex_dhl_receiver_vat', '');
					}
				}  
				$dhl_ein  = '';
				if( isset($_POST['dhl_receiver_ein']) ){
					$dhl_ein  = $_POST['dhl_receiver_ein'];
				}

				if ( !empty( $dhl_ein) ) { 

					update_post_meta(get_current_user_id() , 'user_dhl_receiver_ein' , $dhl_ein);  
					$order->update_meta_data( 'elex_dhl_receiver_ein', $dhl_ein);
				} else {
					$order->update_meta_data( 'elex_dhl_receiver_ein', '');
				}
				$order->save();
			}

			
			public function add_dhl_meta_fields_for_elex_mv($product_id, $all_meta_fields){

				$all_dhl_fields_meta =  array( '_wf_product_description', '_wf_hs_code', '_wf_manufacture_country' , '_wf_dhl_signature', '_wf_product_special_service', '_wf_dhlp_age_check' );
				
				
				update_post_meta( $product_id, '_wf_dhlp_age_check', '' );

				foreach($all_meta_fields as $key=>$value){

					if( in_array($key, $all_dhl_fields_meta) ){
						if($key == '_wf_dhlp_age_check' ){
							update_post_meta( $product_id, $key, 'yes' );

						}else{
							update_post_meta( $product_id, $key, $value );

						}

					}
				}

				return $all_meta_fields;

			}

			public function add_dhl_meta_fields_for_elex_mv_to_show($data){

				$all_dhl_fields_meta =  array( '_wf_product_description', '_wf_hs_code', '_wf_manufacture_country' , '_wf_dhl_signature', '_wf_product_special_service', '_wf_dhlp_age_check' );

				foreach($all_dhl_fields_meta as $value){

					$value_input = get_post_meta($data['prod_id'], $value, true);

					$data[$value] = $value_input; 
				}

				return $data;


			}

			public function add_dhl_field_elex_multivendor() {

 				?>
					<h6><b>DHL</b></h6>

							<div class="form-group mb-3">
								<h6>Waybill Goods Description</h6>
								<input type="text" class="form-control border-2 border-secondary" name="_wf_product_description" id="_wf_product_description">
								<div id="_wf_product_description_error"></div>

							</div>

							<div class="form-group mb-3">
								<h6>HS Tariff Number / Commodity Code (DHL)</h6>
								<input type="text" class="form-control border-2 border-secondary" name="_wf_hs_code" id="_wf_hs_code">
								<div id="_wf_hs_code_error"></div>
							</div>

							<div class="form-group mb-3">
								<h6>Manufacture Country Code</h6>
								<input type="text" class="form-control border-2 border-secondary" name="_wf_manufacture_country" id="_wf_manufacture_country">
								<div id="_wf_manufacture_country_error"></div>
							</div>

							<div class="form-group mb-3">
								<h6>Signature Options(DHl)</h6>
								<select class="form-control border-2 border-secondary" name="_wf_dhl_signature" id="_wf_dhl_signature">
								<?php 
								 $dhl_signature = array(
									'0' => __('No Signature Required', 'wf-shipping-dhl'),
									'1' => __('Content Signature', 'wf-shipping-dhl'),
									'2' => __('Named Signature', 'wf-shipping-dhl'),
									'3' => __('Adult Signature', 'wf-shipping-dhl'),
									'4' => __('Contract Signature', 'wf-shipping-dhl'),
									'5' => __('Alternative Signature', 'wf-shipping-dhl'),
								 );
								 foreach ($dhl_signature as $code=>$name) {
										echo '<option value="' . $code . '">' . $name . '</option>';
								 }
									?>
									
								</select>								
								<div id="_wf_dhl_signature_error"></div>
							</div>


							<div class="form-group mb-3">
								<h6>Special Service</h6>
								<select class="form-control border-2 border-secondary" name="_wf_product_special_service" id="_wf_product_special_service">
								<?php 
								 $dhl_goods_special_service = array(
									'N' => __('NONE', 'wf-shipping-dhl'),
									'NA' => __('NOT APPLICABLE', 'wf-shipping-dhl'),
									'HECAOI1A' => __('DANGEROUS GOODS (HE) PI965 1A', 'wf-shipping-dhl'),
									'HECAOI1B' => __('DANGEROUS GOODS (HE) PI965 1B', 'wf-shipping-dhl'),
									'HEDGDI966' => __('DANGEROUS GOODS (HE) PI966', 'wf-shipping-dhl'),
									'HEDGDI967' => __('DANGEROUS GOODS (HE) PI967', 'wf-shipping-dhl'),
									'HB' => __('LITHIUM ION PI965 SECTION II (HB)', 'wf-shipping-dhl'),
									'HD' => __('LITHIUM ION PI966 SECTION II (HD)', 'wf-shipping-dhl'),
									'HV' => __('LITHIUM ION PI967 SECTION II (HV)', 'wf-shipping-dhl'),
									'HECAOM1A' => __('DANGEROUS GOODS (HE) PI968 1A', 'wf-shipping-dhl'),
									'HECAOM1B' => __('DANGEROUS GOODS (HE) PI968 1B', 'wf-shipping-dhl'),
									'HEDGDM969' => __('DANGEROUS GOODS (HE) PI969', 'wf-shipping-dhl'),
									'HEDGDM970' => __('DANGEROUS GOODS (HE) PI970', 'wf-shipping-dhl'),
									'HM' => __('LITHIUM METAL PI969 SECTION II (HM)', 'wf-shipping-dhl'),
									'HW' => __('LITHIUM METAL PI970 SECTION II (HW)', 'wf-shipping-dhl'),
									'HVHW' => __('LITHIUM ION PI967 SECTION II (HV)
                                                        LITHIUM METAL PI970 SECTION II (HW)', 'wf-shipping-dhl'),
									'HH' => __('DANGEROUS GOODS IN EXCEPTED QUANTITIES (HH)', 'wf-shipping-dhl'),
									'HK' => __('CONSUMER GOODS ID8000 (HK)', 'wf-shipping-dhl'),
									'HY' => __('BIOLOGICAL UN3373 (HY)', 'wf-shipping-dhl'),
									'HEFG' => __('DANGEROUS GOODS (HE) FLAMMABLE GAS', 'wf-shipping-dhl'),
									'HENFG' => __('DANGEROUS GOODS (HE) NON-FLAMMABLE, NON-TOXIC GAS', 'wf-shipping-dhl'),
									'HEFL' => __('DANGEROUS GOODS (HE) FLAMMABLE LIQUID', 'wf-shipping-dhl'),
									'HEFS' => __('DANGEROUS GOODS (HE) FLAMMABLE SOLIDS', 'wf-shipping-dhl'),
									'HESCS' => __('DANGEROUS GOODS (HE) SPONTANEOUS COMBUSTION SUBSTANCES', 'wf-shipping-dhl'),
									'HESDWW' => __('DANGEROUS GOODS (HE) SUBSTANCES DANGEROUS WHEN WET', 'wf-shipping-dhl'),
									'HEO' => __('DANGEROUS GOODS (HE) OXIDIZER', 'wf-shipping-dhl'),
									'HEOPO' => __('DANGEROUS GOODS (HE) Organic Peroxides', 'wf-shipping-dhl'),
									'HETS' => __('DANGEROUS GOODS (HE) TOXIC SUBSTANCES', 'wf-shipping-dhl'),
									'HEC' => __('DANGEROUS GOODS (HE) CORROSIVES', 'wf-shipping-dhl'),
									'HEM' => __('DANGEROUS GOODS (HE) MISCELLANEOUS', 'wf-shipping-dhl'),
									'IUP' => __('LITHIUM ION PI967 Section II (LiBa in equipment) UNDER PROVISO', 'wf-shipping-dhl'),
									'MUP' => __('LITHIUM METAL PI970 Section II (LiBa in equipment) UNDER PROVISO', 'wf-shipping-dhl'),
								);
								 foreach ($dhl_goods_special_service as $code=>$name) {
										echo '<option value="' . $code . '">' . $name . '</option>';
								 }
									?>
									
								</select>
								<div id="_wf_product_special_service_error"></div>

							</div>

							<div class="row mb-3 align-items-center">
								<div class="col-md-3">
									<small><b>Visual Check age</b></small>
								</div>
								<div class="col-md-2">
									<label class="elex-enable-disable-switch">
										<input type="checkbox" checked="true" name="_wf_dhlp_age_check" id="_wf_dhlp_age_check">
										<div class="switch round"></div>
									</label>
								</div>
								<div class="col-md-6">
									<small class="text-secondary">Order recipient's age must be over 18</small>

								</div>

							</div>
					<?php
							

			}
			//Function to create a field in My-Account Page
			function my_account_address_eori_number ( $address ) {
				  $general_settings = get_option('woocommerce_wf_dhl_shipping_settings');

				if ( isset( $general_settings['include_receiver_eori_vat_number'] ) && 'yes' === $general_settings['include_receiver_eori_vat_number']  ) {     
				  $eori_text  = _e( 'EORI Number & ', 'wf-shipping-dhl' );
				  $vat_text   = _e( 'VAT Number (DHL)', 'wf-shipping-dhl' );
				  $label_eori = get_post_meta( get_current_user_id() , 'user_dhl_receiver_eori' , true);
				  $label_vat  = get_post_meta( get_current_user_id() , 'user_dhl_receiver_vat' , true);
					echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    
                                    <label for="eori_number"> ' . $eori_text . ' </label>
                                   
                                        
                                        <input type="text" placeholder="Enter EORI Number" id="user_shipment_eori_number_dhl_elex" name="user_shipment_eori_number_dhl_elex" style="width: 100%" value=' . $label_eori . '> 

                                    <label for="vat_number"> ' . $vat_text . ' </label>
                                   
                                        
                                        <input type="text" placeholder="Enter VAT Number" id="user_shipment_vat_number_dhl_elex" name="user_shipment_vat_number_dhl_elex" style="width: 100%" value=' . $label_vat . '> 
                                    
                                </p>';
				}

			}

				//Save Eori in My-Account 
			function save_account_details ( $user_id ) {
		
				if ($_POST['user_shipment_eori_number_dhl_elex']) {
					update_post_meta($user_id , 'user_dhl_receiver_eori' , $_POST['user_shipment_eori_number_dhl_elex'] );
				}
				if ($_POST['user_shipment_vat_number_dhl_elex']) {
					update_post_meta($user_id , 'user_dhl_receiver_vat' , $_POST['user_shipment_vat_number_dhl_elex'] );
				}
			
			}


			function wf_add_bulk_action_links() {
				global $post_type;
				if( empty($post_type) && ( isset($_GET['page']) && !empty( $_GET['page']))){
					$post_type = $_GET['page'];
				}
				if ('shop_order' == $post_type || 'wc-orders' == $post_type) {
					$settings = get_option('woocommerce_' . WF_DHL_ID . '_settings', null);

					if (!empty($settings)) {
						$enable_shipping_label = isset($settings['enabled_label']) ? $settings['enabled_label'] : 'yes';
						if ($enable_shipping_label === 'yes') {
							?>
								<script type="text/javascript">
									jQuery(document).ready(function() {
										jQuery('<option>').val('create_shipment_dhl').text('<?php _e('Create DHL Express Shipment', 'wf-shipping-dhl'); ?>'
										).appendTo("select[name='action']");

										jQuery('<option>').val('create_shipment_dhl').text('<?php _e('Create DHL Express Shipment', 'wf-shipping-dhl'); ?>'
										).appendTo("select[name='action2']");

										jQuery('<option>').val('create_shipment_return_dhl').text('<?php _e('Create DHL Express Return Shipment', 'wf-shipping-dhl'); ?>'
										).appendTo("select[name='action']");

										jQuery('<option>').val('create_shipment_return_dhl').text('<?php _e('Create DHL Express Return Shipment', 'wf-shipping-dhl'); ?>'
										).appendTo("select[name='action2']");

										jQuery('<option>').val('create_pickup_dhl').text('<?php _e('Create DHL Express Pickup Request', 'wf-shipping-dhl'); ?>'
										).appendTo("select[name='action']");

										jQuery('<option>').val('create_pickup_dhl').text('<?php _e('Create DHL Express Pickup Request', 'wf-shipping-dhl'); ?>'
										).appendTo("select[name='action2']");

									});
								</script>
								<?php
						}
					}
				}
			}

			public function wf_init() {
				include_once 'dhl_express/includes/class-wf-tracking-admin.php';
				if (is_admin()) {
					include_once 'dhl_express/includes/class-wf-dhl-woocommerce-shipping-admin.php';
					//include api manager
					include_once 'wf_api_manager/wf-api-manager-config.php';
				}

				$site_settings = get_option('woocommerce_wf_dhl_shipping_settings');
				if (!isset($site_settings['boxes']) && !isset($site_settings['weight_boxes'])) {
					return;
				}
				$site_settings['boxes'] = isset($site_settings['boxes']) ?  array_values($site_settings['boxes']) : array();
				$site_settings['weight_boxes'] = isset($site_settings['weight_boxes']) ?  array_values($site_settings['weight_boxes']) : array(); 
				update_option('woocommerce_wf_dhl_shipping_settings', $site_settings);


			}

			public function wf_dhl_scripts() {
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('common-script', plugins_url('/dhl_express/resources/js/wf_common.js', __FILE__), array('jquery'));
				wp_enqueue_style('dhl-style', plugins_url('/dhl_express/resources/css/wf_common_style.css', __FILE__));
				wp_enqueue_media();
			}

			public function wf_dhl_wooCommerce_shipping_init() {
				include_once 'dhl_express/includes/class-wf-dhl-woocommerce-shipping.php';
			}

			public function wf_dhl_wooCommerce_shipping_methods( $methods) {
				$methods[] = 'wf_dhl_woocommerce_shipping_method';
				return $methods;
			}

		}
		new wf_dhl_wooCommerce_shipping_setup();
	}

	if (!class_exists('wf_dhl_paket_wooCommerce_shipping_setup')) {
		class wf_dhl_paket_wooCommerce_shipping_setup {

			public function __construct() {
				add_action('init', array($this, 'load_plugin_textdomain'));

				$this->wf_init();
				add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
				add_action('woocommerce_shipping_init', array($this, 'wf_dhl_paket_wooCommerce_shipping_init'));
				add_filter('woocommerce_shipping_methods', array($this, 'wf_dhl_paket_wooCommerce_shipping_methods'));
				add_filter('admin_enqueue_scripts', array($this, 'wf_dhl_paket_scripts'));
			}

			public function wf_init() {
				include_once 'dhl_paket/includes/class-wf-tracking-admin.php';
				include_once 'dhl_paket/includes/class-wf-packstation.php';
				include_once 'dhl_paket/includes/class-wf-soap.php';
				if (is_admin()) {
					// Add Notice Class
					include_once 'dhl_paket/includes/class-wf-admin-notice.php';

					// Admin functionality
					include_once 'dhl_paket/includes/class-wf-dhl-paket-woocommerce-shipping-admin.php';

					// Include api manager
					include_once 'wf_api_manager/wf-api-manager-config.php';

					include_once 'dhl_paket/includes/class-wf-admin-options.php';
				}

			}

			public function wf_dhl_paket_scripts() {
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('wf-dhl_pak-script', plugins_url('/dhl_paket/resources/js/wf_common.js', __FILE__), array('jquery'));
				wp_enqueue_style('wf-dhl-pak-style', plugins_url('/dhl_paket/resources/css/wf_common_style.css', __FILE__));
			}

			public function plugin_action_links( $links) {
				$plugin_links = array(
					'<a href="' . admin_url('admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_shipping') . '">' . __('DHL Express', 'wf-shipping-dhl') . '</a>',

					'<a href="' . admin_url('admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_paket_woocommerce_shipping_method') . '">' . __('DHL Paket', 'wf-shipping-dhl') . '</a>',

					'<a href="' . admin_url('admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_ecommerce_shipping_method') . '">' . __('DHL Ecommerce', 'wf-shipping-dhl') . '</a>',

					'<a href="' . admin_url('admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_parcel_woocommerce_shipping_method') . '">' . __('DHL Parcel', 'wf-shipping-dhl') . '</a>',

					'<a href="https://elextensions.com/documentation/#elex-dhl-shipping" target="_blank">' . __('Documentation', 'wf-shipping-dhl') . '</a>',
					'<a href="https://elextensions.com/support/" target="_blank">' . __('Support', 'wf-shipping-dhl') . '</a>',
				);
				return array_merge($plugin_links, $links);
			}

			public function wf_dhl_paket_wooCommerce_shipping_init() {
				include_once 'dhl_paket/includes/class-wf-dhl-paket-woocommerce-shipping.php';
			}

			public function wf_dhl_paket_wooCommerce_shipping_methods( $methods) {
				$methods[] = 'wf_dhl_paket_woocommerce_shipping_method';
				return $methods;
			}

			/**
			 * Handle localization
			 */
			public function load_plugin_textdomain() {
				load_plugin_textdomain('wf-shipping-dhl', false, dirname(plugin_basename(__FILE__)) . '/i18n/');
			}
		}
		new wf_dhl_paket_wooCommerce_shipping_setup();
	}

	if (!class_exists('WF_DHL_Ecommerce_Shipping_Setup')) {
		class WF_DHL_Ecommerce_Shipping_Setup {

			public function __construct() {
				$this->wf_init();
				//  add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
				add_action('woocommerce_shipping_init', array($this, 'wf_dhl_eCommerce_shipping_init'));
				add_filter('woocommerce_shipping_methods', array($this, 'wf_dhl_eCommerce_shipping_methods'));
				add_filter('admin_enqueue_scripts', array($this, 'wf_dhl_ecommerce_scripts'));

				add_action('admin_footer', array($this, 'wf_add_bulk_ecommerce_action_links'), 10); //to add bulk option to orders page
				add_action('woocommerce_admin_order_actions_end', array($this, 'wf_add_ecommerce_print_label_buttons')); //to add print option at the end of
				add_filter('woocommerce_my_account_my_orders_actions', array($this, 'add_custom_actions_my_account'), 10, 2);
				   
			}
	 
			 /* Filter function to print DHL label from the store side */
			public function add_custom_actions_my_account( $actions, $order) {
				global $woocommerce;
				$label_check = get_option('woocommerce_wf_dhl_shipping_settings');
				if ( ( isset($label_check['option_print_label_by_customers']) && $label_check['option_print_label_by_customers'] == 'yes' ) && $order->has_status( 'completed' ) ) {
					$orderid = $order->get_id();
					update_option('cart_side_print_label_request_express_dhl_elex', true);
					$shipmentId = $order->get_meta('wf_woo_dhl_shipmentId');
					if (!empty($shipmentId)) {
						$actions['print-label-express-dhl-elex'] = array(
							'url'  => admin_url('/post.php?wf_dhl_viewlabel=' . base64_encode($shipmentId . '|' . $orderid)),
							'name' => __( 'DHL Label', 'wf-shipping-dhl' ),
						);
					}
				}

				return $actions;
			}
			function wf_add_ecommerce_print_label_buttons( $order) {
				global $post_type;
				if( empty($post_type) && ( isset($_GET['page']) && !empty( $_GET['page']))){
					$post_type = $_GET['page'];
				}
				if ('shop_order' == $post_type || 'wc-orders' == $post_type) {
					$shipmentIds = $order->get_meta('wf_woo_dhl_ecommerce_shipmentId');
					$available_order_id = $order->get_id();
					if (!empty($shipmentIds)) {
						$i = 0;
						foreach ($shipmentIds as $shipmentId) {
							$i++;
							$shipping_label = $order->get_meta( 'wf_woo_dhl_eccommerce_shippingLabel_' . $shipmentId, true);
							$download_url   = admin_url('/post.php?wf_dhl_eccommerce_viewlabel=' . base64_encode($shipmentId . '|' . $available_order_id));
							?>
								<a disabled class="button tips "
								target="_blank"
								data-tip="<?php esc_attr_e('Download DHL Ecommerce Label', 'wf-woocommerce-packing-list'); ?>"
								href="<?php echo $download_url; ?>">
								<img src="<?php echo untrailingslashit(plugins_url('/', __FILE__)) . '/dhl_eccommerce/resources/images/label-icon.png'; ?>"
								alt="<?php esc_attr_e('Print Shipping Label', 'wf-woocommerce-packing-list'); ?>" width="14"/>
							</a>
							<?php
						}
					}
				}	
			}

			function wf_add_bulk_ecommerce_action_links() {
				global $post_type;
				if( empty($post_type) && ( isset($_GET) && !empty($_GET['page']))){
					$post_type = $_GET['page'];
				}
				if ('shop_order' == $post_type) {
					$settings = get_option('woocommerce_' . WF_DHL_ECOMMERCE_ID . '_settings', null);
					if (!empty($settings) && isset($settings['enabled']) && $settings['enabled'] === 'yes') {
						?>
							<script type="text/javascript">
								jQuery(document).ready(function() {
									jQuery('<option>').val('create_ecommerce_shipment_dhl').text('<?php _e('Create DHL Ecommerce Shipment', 'wf-shipping-dhl'); ?>').appendTo("select[name='action']");

									jQuery('<option>').val('create_ecommerce_shipment_dhl').text('<?php _e('Create DHL Ecommerce Shipment', 'wf-shipping-dhl'); ?>').appendTo("select[name='action2']");
								});
							</script>
						<?php
					}
				}
			}

			public function wf_init() {
				if (is_admin()) {
					include_once 'dhl_eccommerce/includes/class-wf-dhl-woocommerce-shipping-admin.php';
					//include api manager
					include_once 'wf_api_manager/wf-api-manager-config.php';
				}
			}

			public function wf_dhl_ecommerce_scripts() {
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('common-script', plugins_url('/dhl_eccommerce/resources/js/wf_common.js', __FILE__), array('jquery'));
				wp_enqueue_style('dhl-style', plugins_url('/dhl_eccommerce/resources/css/wf_common_style.css', __FILE__));
			}

			public function wf_dhl_eCommerce_shipping_init() {
				include_once 'dhl_eccommerce/includes/class-wf-dhl-woocommerce-shipping.php';
			}

			public function wf_dhl_eCommerce_shipping_methods( $methods) {
				$methods[] = 'wf_dhl_ecommerce_shipping_method';
				return $methods;
			}

		}
		new WF_DHL_Ecommerce_Shipping_Setup();
	} 
		//DHL Parcel Setup
	if (!class_exists('WF_DHL_Parcel_Shipping_Setup')) {
		class WF_DHL_Parcel_Shipping_Setup {

			public function __construct() {
				add_action('init', array($this, 'load_plugin_textdomain'));

				$this->wf_init();
				// add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
				add_action('woocommerce_shipping_init', array($this, 'wf_dhl_parcel_wooCommerce_shipping_init'));
				add_filter('woocommerce_shipping_methods', array($this, 'wf_dhl_parcel_wooCommerce_shipping_methods'));
				add_filter('admin_enqueue_scripts', array($this, 'wf_dhl_parcel_scripts'));
			}

			public function wf_init() {
				include_once 'dhl_parcel/includes/class-wf-tracking-admin.php';
				include_once 'dhl_parcel/includes/class-wf-packstation.php';
				include_once 'dhl_parcel/includes/class-wf-soap.php';
				if (is_admin()) {
					// Add Notice Class
					include_once 'dhl_parcel/includes/class-wf-admin-notice.php';

					// Admin functionality
					include_once 'dhl_parcel/includes/class-wf-dhl-parcel-woocommerce-shipping-admin.php';

					// Include api manager
					include_once 'wf_api_manager/wf-api-manager-config.php';

					include_once 'dhl_parcel/includes/class-wf-admin-options.php';
				}

			}

			public function wf_dhl_parcel_scripts() {
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('wf-dhl_parcel-script', plugins_url('/dhl_parcel/resources/js/wf_common.js', __FILE__), array('jquery'));
				wp_enqueue_style('wf-dhl-parcel-style', plugins_url('/dhl_parcel/resources/css/wf_common_style.css', __FILE__));
			}


			public function wf_dhl_parcel_wooCommerce_shipping_init() {
				include_once 'dhl_parcel/includes/class-wf-dhl-parcel-woocommerce-shipping.php';
			}

			public function wf_dhl_parcel_wooCommerce_shipping_methods( $methods) {
				$methods[] = 'wf_dhl_parcel_woocommerce_shipping_method';
				return $methods;
			}

			/**
			 * Handle localization
			 */
			public function load_plugin_textdomain() {
				load_plugin_textdomain('wf-shipping-dhl', false, dirname(plugin_basename(__FILE__)) . '/i18n/');
			}
		}
			new WF_DHL_Parcel_Shipping_Setup();
	}


	add_filter( 'switch_account_number_action_express_dhl_elex_mv_woocommerce_dhl_shipping',  'change_account_number_accc_vendor' , 9999, 4 );
	
	function change_account_number_accc_vendor( $cred_info, $package, $plugin, $order ) {
		$store_id     = '';
		$vendor_creds = array();
	
		if ( ! is_plugin_active('multi-vendor/elex-multi-vendor.php')  ) {
			return $cred_info;
		}
		if(!empty($order)){
			$store_id = $order->get_meta( 'elex_mv_store_id' );
			$carriers_enable_list = get_post_meta( $store_id, 'enabling_shipping_method_plugins', true );
	
			
			if ( ! $store_id || ! isset( $carriers_enable_list[ $plugin ] ) || ! $carriers_enable_list[ $plugin ] ) {
				return $cred_info;
			}
			$permission_override_creds = get_post_meta( $store_id, 'override_credentials', true );
			if ( 'false' == $permission_override_creds || empty($permission_override_creds) ) {
				return $cred_info;
			}
			$vendor_creds  = get_post_meta( $store_id, 'woocommerce_wf_dhl_shipping_credentials_settings' );
		}
		
		if(isset($package['contents'])){
			foreach ( $package['contents'] as $pack ) {
	
				$prod_id = $pack['data']->get_id();
				$store_id = get_post_meta( $prod_id, 'store_id', true );
				if(!$store_id && !empty($order)){
					$store_id = $order->get_meta( 'elex_mv_store_id');
				}
				$carriers_enable_list = get_post_meta( $store_id, 'enabling_shipping_method_plugins', true );
	
			
				if ( ! $store_id || ! isset( $carriers_enable_list[ $plugin ] ) || ! $carriers_enable_list[ $plugin ] ) {
					return $cred_info;
				}
				$permission_override_creds = get_post_meta( $store_id, 'override_credentials', true );
				if ( 'false' == $permission_override_creds || empty($permission_override_creds) ) {
					return $cred_info;
				}
	
	
				$vendor_creds  = get_post_meta( $store_id, 'woocommerce_wf_dhl_shipping_credentials_settings' );
	
	
			}
		}
		
			
		$cred_info['site_id'] = $vendor_creds[0]['site_id'];
		$cred_info['site_password'] = $vendor_creds[0]['site_password'];
		$cred_info['payment_account_number'] = $vendor_creds[0]['account_number'];
		$cred_info['base_country'] = $vendor_creds[0]['base_country'];
		$cred_info['payment_country_code'] = $vendor_creds[0]['base_country'];
		return $cred_info;
	}

}
