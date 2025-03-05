<?php

if (!defined('ABSPATH')) {
	exit;
}

class wf_dhl_woocommerce_shipping_method extends WC_Shipping_Method {

	private $found_rates;
	private  $services;
    public $id;
	public $method_title;
    public $method_description;
	public $enabled;
	public $title;
	public $availability;
	public $countries;
	public $origin;
	public $origin_country;
	public $origin_country_1;
	public $account_number;
	public $site_id;
	public $site_password;
	public $show_dhl_extra_charges;
	public $show_dhl_insurance_charges;
	public $freight_shipper_city;
	public $delivery_time;
	public $latin_encoding;
	public $production;
	public $service_url;
	public $debug;
	public $insure_contents;
	public $request_type;
	public $packing_method;
	public $boxes;
	public $weight_boxes;
	public $custom_services;
	public $offer_rates;
	public $exclude_dhl_tax;
	public $dutypayment_type;
	private $dutyaccount_number;
	public $dimension_unit;
	public $site_dimensional_unit;
	public $site_weight_unit;
	public $quoteapi_dimension_unit;
	public $quoteapi_weight_unit;
	public $aelia_activated;
	public $conversion_rate;
	public $shop_currency;
	public $timezone_offset;
	public $insure_currency;
	public $insure_converstion_rate;
	public $ship_from_address;
	public $weight_packing_process;
	public $dhl_insurance_at_checkout;
	public $http_req_referer;
	public 	$general_settings;
	public $woo_countries;
	public $is_woocommerce_composite_products_installed;
	public $is_woocommerce_multi_currency_installed;
	public $weight_unit;
	public $is_woocommerce_product_bundles_installed;
	public $ordered_services;
	public $debug_message;
	
	public function __construct() {
		if ( !function_exists('is_plugin_active') ) {        
			include_once  ABSPATH . 'wp-admin/includes/plugin.php' ;
		}
		$this->id                 = WF_DHL_ID;
		$this->method_title       = __('DHL Express', 'wf-shipping-dhl');
		$this->method_description = '';
		$this->services           = include  'data-wf-service-codes.php' ;
		$this->init();  
		
		
		// $this->dokan_litr_activated = is_plugin_active('dokan-lite/dokan.php')? true: false;
		// $this->dokan_pro_activated = is_plugin_active('dokan-pro/dokan-pro.php')? true: false;
		// $this->xa_multivendor_activated = is_plugin_active('multi-vendor-add-on-for-thirdparty-shipping/multi-vendor-add-on-for-thirdparty-shipping.php')? true: false;
	}
	
	private function init() {
	   
		include_once 'data-wf-default-values.php';
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->enabled                    = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'no';
		$this->title                      = $this->get_option('title', $this->method_title);
		$this->availability               = isset( $this->settings['availability'] ) ? $this->settings['availability'] : 'all';
		$this->countries                  = isset( $this->settings['countries'] ) ? $this->settings['countries'] : array();
		$this->origin                     = apply_filters('woocommerce_dhl_origin_postal_code', str_replace(' ', '', strtoupper($this->get_option('origin'))));
		$selected_country                 = isset($this->settings['base_country']) ? $this->settings['base_country'] : WC()->countries->get_base_country();
		$this->origin_country             = apply_filters('woocommerce_dhl_origin_country_code', $selected_country);
		$this->origin_country_1           = $this->origin_country;
		$this->account_number             = $this->get_option('account_number');
		$this->site_id                    = $this->get_option('site_id');
		$this->site_password              = $this->get_option('site_password');
		$this->show_dhl_extra_charges     = $this->get_option('show_dhl_extra_charges');
		$this->show_dhl_insurance_charges = $this->get_option('show_dhl_insurance_charges');
		$this->freight_shipper_city       = htmlspecialchars($this->get_option('freight_shipper_city'));
		$del_bool                         =  $this->get_option( 'delivery_time' );
		$this->delivery_time              = ( $del_bool == 'yes' ) ? true : false;
		$this->latin_encoding             = isset($this->settings['latin_encoding']) && $this->settings['latin_encoding'] == 'yes' ? true : false;
		$utf8_support                     = $this->latin_encoding ? '?isUTF8Support=true' : '';

		$_stagingUrl    = 'https://xmlpitest-ea.dhl.com/XMLShippingServlet' . $utf8_support;
		$_productionUrl = 'https://xmlpi-ea.dhl.com/XMLShippingServlet' . $utf8_support;

		$this->production  = ( !empty($this->settings['production']) && $this->settings['production']  === 'yes' ) ? true : false;
		$this->service_url = ( $this->production == true ) ? $_productionUrl : $_stagingUrl;

		$debug_bool            = $this->get_option('debug');
		$this->debug           = ( $debug_bool  == 'yes' ) ? true : false;
		$insurance_bool        = $this->get_option('insure_contents');
		$this->insure_contents = ( $insurance_bool == 'yes' ) ? true : false;
		
		$this->request_type    = $this->get_option('request_type', 'LIST');
		$this->packing_method  = $this->get_option('packing_method', 'per_item');
		$this->boxes           = $this->get_option('boxes');
		$this->weight_boxes    = $this->get_option('weight_boxes') ? $this->get_option('weight_boxes') : array();
		$this->custom_services = $this->get_option('services', array());
		$this->offer_rates     = $this->get_option('offer_rates', 'all');
		$this->exclude_dhl_tax = $this->get_option('exclude_dhl_tax', '');

		$this->dutypayment_type   = $this->get_option('dutypayment_type', '');
		$this->dutyaccount_number = $this->get_option('dutyaccount_number', '');

		$this->dimension_unit = $this->get_option('dimension_weight_unit') == 'LBS_IN' ? 'IN' : 'CM';
		$this->weight_unit    = $this->get_option('dimension_weight_unit') == 'LBS_IN' ? 'LBS' : 'KG';

		$this->site_dimensional_unit = strtolower(get_option('woocommerce_dimension_unit'));
		$this->site_weight_unit      = strtolower(get_option('woocommerce_weight_unit'));

		$this->quoteapi_dimension_unit = $this->dimension_unit;
		$this->quoteapi_weight_unit    = $this->weight_unit == 'LBS' ? 'LB' : 'KG';

		$this->aelia_activated = is_plugin_active('woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php')? true: false;
		
		$this->conversion_rate = ( !empty($this->settings['conversion_rate']) && !$this->aelia_activated ) ? $this->settings['conversion_rate'] : 1;

		$this->shop_currency = $this->wf_get_currency_based_on_country_code(WC()->countries->get_base_country());

		if ($this->shop_currency != '') {
			$this->conversion_rate = apply_filters('wf_dhl_conversion_rate', $this->conversion_rate, $this->settings['dhl_currency_type'], $this->shop_currency);
		}
		
		//Time zone adjustment, which was configured in minutes to avoid time diff with server. Convert that in seconds to apply in date() functions.
		$this->timezone_offset = !empty($this->settings['timezone_offset']) ? intval($this->settings['timezone_offset']) * 60 : 0;
		
		$this->insure_currency         = isset( $this->settings['insure_currency'] ) ?  $this->settings['insure_currency'] : '';
		$this->insure_converstion_rate = !empty($this->settings['insure_converstion_rate']) ? $this->settings['insure_converstion_rate'] : '';
		
		if (class_exists('wf_vendor_addon_setup')) {
			if (isset($this->settings['vendor_check']) && $this->settings['vendor_check'] === 'yes') {
				$this->ship_from_address = 'vendor_address'; 
			} else {
				$this->ship_from_address = 'origin_address';
			}
		} else {
			$this->ship_from_address = 'origin_address';
		}
		
		$this->weight_packing_process = !empty($this->settings['weight_packing_process']) ? $this->settings['weight_packing_process'] : 'pack_descending';
		//$this->box_max_weight           = !empty($this->settings['box_max_weight']) ? $this->settings['box_max_weight'] : '';
		$this->dhl_insurance_at_checkout = 'no';
		$this->http_req_referer          = '';
		$this->general_settings          = get_option('woocommerce_wf_dhl_shipping_settings');

		add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
		$this->woo_countries                               = new WC_Countries();
		$this->is_woocommerce_composite_products_installed = ( in_array('woocommerce-composite-products/woocommerce-composite-products.php', get_option('active_plugins')) )? true: false;

		$this->is_woocommerce_multi_currency_installed = ( in_array('woocommerce-multicurrency/woocommerce-multicurrency.php', get_option('active_plugins')) )? true: false;
		
		$this->is_woocommerce_product_bundles_installed = ( in_array('woocommerce-product-bundles/woocommerce-product-bundles.php', get_option('active_plugins')) )? true: false;

	}

	/**
	 * is_available function.
	 *
	 * @param array $package
	 * @return bool
	 */
	public function is_available( $package ) {
		$post_data_string = '';

		if (isset($_POST['post_data'])) {
			parse_str($_POST['post_data'], $post_data_string);
		}

		$hide_shipping_methods_for_specific_countries = isset($this->general_settings['countries_to_hide_selected'])? $this->general_settings['countries_to_hide_selected']: 'no';
		if ($this->general_settings['availability'] == 'all' && $hide_shipping_methods_for_specific_countries === 'yes') {
			$countries_to_hide_shipping_services = isset($this->general_settings['countries_to_hide_services'])? $this->general_settings['countries_to_hide_services']: array();

			if ( is_array( $countries_to_hide_shipping_services ) && !empty($countries_to_hide_shipping_services) && in_array( $package['destination']['country'], $countries_to_hide_shipping_services ) ) {
				return false;
			}
		}
		
		/*Handling insurance and delivery signature charges when DHL real time option is disabled*/
		if ($post_data_string != '') {
			$dhl_insurance_checkout = isset($post_data_string['wf_dhl_insurance'])? 'yes': 'no';
			update_option('wf_dhl_insurance_enabled_checkout_no_real_time_enabled', $dhl_insurance_checkout);
		} else {
			/* while creating order */
			$dhl_insurance_checkout = isset($_POST['wf_dhl_insurance'])? 'yes': 'no';
			update_option('wf_dhl_insurance_enabled_checkout_no_real_time_enabled', $dhl_insurance_checkout);
		}

		if ( $this->enabled === 'no' || empty($this->enabled ) ) {
			return false;
		}

		if ( $this->availability === 'specific' ) {
			if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {
				return false;
			}
		} elseif ( $this->availability === 'excluding' ) {
			if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
				return false;
			}
		}
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	}

	public function debug( $message, $type = 'notice') {
		if ($this->debug) {
			wc_add_notice($message, $type);
		}

	}

	public function admin_options() {
	   
		// Show settings
		parent::admin_options();
	}

	public function init_form_fields() {
		if (isset($_GET['page']) && $_GET['page'] === 'wc-settings') {
			$this->form_fields = include  'data-wf-settings.php' ;
		}
		//Express Auto Label Generate Add-on.
		if (ELEX_DHL_EXPRESS_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION) {
			$auto_add_on_fields = include ELEX_DHL_EXPRESS_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION_PATH . 'includes/data-wf-settings.php' ;
			
			if (is_array($auto_add_on_fields)) {   
				$this->form_fields = array_merge($this->form_fields, $auto_add_on_fields);
			}
		}
		

		//DHL India Add-on.
		if (ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION) {
			$auto_add_on_fields = include ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION_PATH . 'includes/elex-dhl-india-addon-settings.php' ;
			if (is_array($auto_add_on_fields)) {
				$this->form_fields = array_merge($this->form_fields, $auto_add_on_fields);
			}
		}
	}

	public function generate_wf_dhl_tab_box_html() {

		$tab = ( !empty($_GET['subtab']) ) ? esc_attr($_GET['subtab']) : 'general';

				echo '
                <div class="wrap">
                    <style>
                        .woocommerce-help-tip{color:darkgray !important;}
                        <style>
                        .woocommerce-help-tip {
                            position: relative;
                            display: inline-block;
                            border-bottom: 1px dotted black;
                        }

                        .woocommerce-help-tip .tooltiptext {
                            visibility: hidden;
                            width: 120px;
                            background-color: black;
                            color: #fff;
                            text-align: center;
                            border-radius: 6px;
                            padding: 5px 0;

                            /* Position the tooltip */
                            position: absolute;
                            z-index: 1;
                        }

                        .woocommerce-help-tip:hover .tooltiptext {
                            visibility: visible;
                        }
                        </style>
                    </style>
                    <hr class="wp-header-end">';
				$this->wf_dhl_shipping_page_tabs($tab);
		if ($tab != 'auto-generate-add-on') {
			echo'<script>
                        jQuery(document).ready(function(){
                            jQuery(".dhl_express_addon_auto_tab_field").closest("tr,h3").hide();
                            jQuery(".dhl_express_addon_auto_tab_field").next("p").hide();
                        });
                    </script>';  
		}
		if ($tab != 'dhl-india-add-on') {
			echo'<script>
                        jQuery(document).ready(function(){
                            jQuery(".dhl_india_tab_field").closest("tr,h3").hide();
                            jQuery(".dhl_india_tab_field").next("p").hide();
                        });
                    </script>';  
		}
		if ($tab != 'auto-generate-add-on' && $tab != 'dhl-india-add-on') {
			echo'<script>
                        jQuery(document).ready(function(){
                            jQuery(".woocommerce-save-button").hide();
                        });
                    </script>';
		}
		switch ($tab) {
			case 'general':
				echo '<div class="table-box table-box-main" id="general_section" style="margin-top: 0px;border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
				require_once 'settings/dhl_general_settings.php';
				echo '</div>';
				break;
			case 'rates':
				echo '<div class="table-box table-box-main" id="rates_section" style="margin-top: 0px;border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
				require_once 'settings/dhl_rates_settings.php';
				echo '</div>';
				break;
			case 'labels':
				echo '<div class="table-box table-box-main" id="labels_section" style="margin-top: 0px;border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
				require_once 'settings/dhl_labels_settings.php';
				echo '</div>';
				break;
			case 'packing':
				echo '<div class="table-box table-box-main" id="packing_section" style="margin-top: 0px;border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
				require_once 'settings/dhl_packing_settings.php';
				echo '</div>';
				break;
			case 'licence':
				echo '<div class="table-box table-box-main" id="licence_section" style="margin-top: 0px;border: 1px solid #ccc;border-top: unset !important;padding: 5px;">';
				$plugin_name = 'dhl';
				include  WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'wf_api_manager/html/html-wf-activation-window.php' ;
				include_once WF_DHL_PAKET_EXPRESS_ROOT_PATH . '/wf_api_manager/html/related_products.php';
				echo '</div>';
				break;
			case 'dhl-india-add-on':
				include ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION_PATH . 'includes/elex-dhl-india-seperate_sections.php' ;
				$plugin_name = 'dhl-india-addon';
				include ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION_PATH . 'wf_api_manager/html/html-wf-activation-window.php' ;
		}
				echo '
                </div>';
	}
	
	public function wf_dhl_shipping_page_tabs( $current = 'general') {
		 $activation_check = get_option('dhl_activation_status');
		if (!empty($activation_check) && $activation_check === 'active') {
			  $acivated_tab_html =  "<small style='color:green;font-size:xx-small;'>(Activated)</small>";

		} else {
			 $acivated_tab_html =  "<small style='color:red;font-size:xx-small;'>(Activate)</small>";
		}
			$tabs = array(
					 'general'   => __('General', 'wf-shipping-dhl'),
					 'rates'     => __('Rates & Services', 'wf-shipping-dhl'),
					 'labels'    => __('Label & Tracking', 'wf-shipping-dhl'),
					 'packing'   => __('Packaging', 'wf-shipping-dhl'),
					 'licence'   => __('License ' . $acivated_tab_html, 'wf-shipping-dhl')
				 );
			$html = '<h2 class="nav-tab-wrapper">';
			if (ELEX_DHL_EXPRESS_AUTO_LABEL_GENERATE_ADDON_WOOCOMMERCE_EXTENSION) {
				$tabs['auto-generate-add-on'] =  __('Express Auto Label Generate Add-on', 'wf-shipping-dhl');
			}
			if (ELEX_DHL_INDIA_ADDON_WOOCOMMERCE_EXTENSION) {
				$tabs['dhl-india-add-on'] =  __('DHL India Add-on', 'wf-shipping-dhl');
			}
			foreach ($tabs as $tab => $name) {
				$class = ( $tab == $current ) ? 'nav-tab-active' : '';
				$style = ( $tab == $current ) ? 'border-bottom: 1px solid transparent !important;' : '';
				$html .= '<a style="text-decoration:none !important;' . $style . '" class="nav-tab ' . $class . '" href="?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_shipping&subtab=' . $tab . '">' . $name . '</a>';
			}
			$html .= '</h2>';
			echo $html;
	}
	

	public function generate_services_html() {
		ob_start();
		include  'html-wf-services.php' ;
		return ob_get_clean();
	}

	public function generate_box_packing_html() {
		ob_start();
		include  'html-wf-box-packing.php' ;
		return ob_get_clean();
	}

	public function validate_box_packing_field( $key) {
		$boxes_id           = isset($_POST['boxes_id']) ? $_POST['boxes_id'] : array();
		$boxes_name         = isset($_POST['boxes_name']) ? $_POST['boxes_name'] : array();
		$boxes_length       = isset($_POST['boxes_length']) ? $_POST['boxes_length'] : array();
		$boxes_width        = isset($_POST['boxes_width']) ? $_POST['boxes_width'] : array();
		$boxes_height       = isset($_POST['boxes_height']) ? $_POST['boxes_height'] : array();
		$boxes_inner_length = isset($_POST['boxes_inner_length']) ? $_POST['boxes_inner_length'] : array();
		$boxes_inner_width  = isset($_POST['boxes_inner_width']) ? $_POST['boxes_inner_width'] : array();
		$boxes_inner_height = isset($_POST['boxes_inner_height']) ? $_POST['boxes_inner_height'] : array();
		
		$boxes_box_weight = isset($_POST['boxes_box_weight']) ? $_POST['boxes_box_weight'] : array();
		$boxes_max_weight = isset($_POST['boxes_max_weight']) ? $_POST['boxes_max_weight'] : array();
		$boxes_enabled    = isset($_POST['boxes_enabled']) ? $_POST['boxes_enabled'] : array();
		$boxes_pack_type  = isset($_POST['boxes_pack_type']) ? $_POST['boxes_pack_type'] : array();

		$boxes = array();

		if (!empty($boxes_length) && sizeof($boxes_length) > 0) {
			for ($i = 0; $i <= max(array_keys($boxes_length)); $i ++) {

				if (!isset($boxes_length[$i])) {
					continue;
				}

				if ($boxes_length[$i] && $boxes_width[$i] && $boxes_height[$i]) {

					$boxes[] = array(
						'id' => $boxes_id[$i],
						'name' => $boxes_name[$i],
						'length' => floatval($boxes_length[$i]),
						'width' => floatval($boxes_width[$i]),
						'height' => floatval($boxes_height[$i]),
						'inner_length' => floatval($boxes_inner_length[$i]),
						'inner_width' => floatval($boxes_inner_width[$i]),
						'inner_height' => floatval($boxes_inner_height[$i]),
						'box_weight' => floatval($boxes_box_weight[$i]),
						'max_weight' => floatval($boxes_max_weight[$i]),
						'enabled' => isset($boxes_enabled[$i]) ? true : false,
						'pack_type' => $boxes_pack_type[$i]
					);
				}
			}
		}
		return $boxes;
	}

	public function validate_services_field( $key) {
		$services        = array();
		$posted_services = $_POST['dhl_service'];

		foreach ($posted_services as $code => $settings) {
			$services[$code] = array(
				'name' => wc_clean($settings['name']),
				'order' => wc_clean($settings['order']),
				'enabled' => isset($settings['enabled']) ? true : false,
				'adjustment' => wc_clean($settings['adjustment']),
				'adjustment_percent' => str_replace('%', '', wc_clean($settings['adjustment_percent']))
			);
		}

		return $services;
	}

	public function get_dhl_packages( $package) {
		switch ($this->packing_method) {
			case 'box_packing':
				return $this->box_shipping($package);
				break;
			case 'weight_based':
				return $this->weight_based_shipping($package);
				break;
			case 'per_item':
			default:
				return $this->per_item_shipping($package);
				break;
		}
	}

	/**
	* function to return composite data of a WC_Composite_Product as a wooCommerce packages array
	* For an assembled Composite product we are taking product's weight and dimensions
	* For a non-assembled Composite product, algorithm takes the components and sends as individual packages 
	*
	* @access private
	* @param mixed woocommerce shipping packges $packages
	* @return mixed woocommerce shipping packges $packages
	*/
	private function get_composite_product_data( $package ) {
		$package_composite_products_data = array();
		$shipping_package                = array();

		foreach ($package['contents'] as $item_id => $values) {
			if (!empty($values['data']->get_weight()) && !empty($values['data']->get_length()) && !empty($values['data']->get_width())&& !empty($values['data']->get_height())) {
				return $package;
			} else {
				$components_id_array = array();
				if (isset($values['composite_data'])) {
					$composite_data = $values['composite_data'];
					if (elex_dhl_get_product_length( $values['data'] ) && elex_dhl_get_product_height( $values['data'] ) && elex_dhl_get_product_width( $values['data'] ) && elex_dhl_get_product_weight( $values['data'] )) {
						foreach ($composite_data as $composite_datum) {
							if (!empty($components_id_array) && array_key_exists($composite_datum['product_id'], $components_id_array)) {
								$components_id_array[$composite_datum['product_id']] += 1;     
							} else {
								$components_id_array[$composite_datum['product_id']] = $composite_datum['quantity'];
							}
						}
						$composite_product_data                   = $values['data'];
						$composite_product_id                     = $composite_product_data->get_id();
						$components_id_array['parent_product_id'] = $composite_product_id;

						$package_composite_products_data[$item_id] = $components_id_array;
					} else {
						$package_composite_products_data[$item_id] = $values['composite_data'];
					}
				} else {
					$shipping_package['contents'][$item_id]['data']     = $values['data'];
					$shipping_package['contents'][$item_id]['quantity'] = $values['quantity'];
				}
			}
		}

		if (!empty($package_composite_products_data)) {
			$package_composite_products_data = $this->composite_data_unique(array_shift($package_composite_products_data));
			foreach ($package_composite_products_data as $package_composite_products_datum) {
				$composite_product_id = isset($package_composite_products_datum['variation_id'])? $package_composite_products_datum['variation_id']: $package_composite_products_datum['product_id'];
				$composite_product    = wc_get_product($composite_product_id);
				$shipping_package['contents'][$composite_product_id]['data']     = $composite_product;
				$shipping_package['contents'][$composite_product_id]['quantity'] = $package_composite_products_datum['quantity'];
			}
		}
		$package['contents'] = $shipping_package['contents'];

		return $package;
	}

	private function composite_data_unique( $package_composite_products_data) {
		$composite_data_unique = array();
		foreach ($package_composite_products_data as $package_composite_products_datum) {
			if (empty($composite_data_unique)) {
				$composite_data_unique[$package_composite_products_datum['product_id']] = $package_composite_products_datum;
			} else {
				$found = false;
				foreach ($composite_data_unique as $composite_data_element) {
					if ($composite_data_element['product_id'] == $package_composite_products_datum['product_id']) {
						$composite_data_unique[$package_composite_products_datum['product_id']]['quantity'] += 1;
						$found = true;
						break;
					}
				}

				if (!$found) {
					$composite_data_unique[$package_composite_products_datum['product_id']] = $package_composite_products_datum;
				}
			}
		}
		return $composite_data_unique;
	}

	/**
	 * weight_based_shipping function.
	 *
	 * @access private
	 * @param mixed $package
	 * @return void
	**/
	private function is_product_bundled_item( $values ) {
        $is_item_bundled_item = false;
        if (isset($values['bundled_by'])) {
                $is_item_bundled_item = true;
        }
        return $is_item_bundled_item;
    }
	private function weight_based_shipping( $package) {
		$debug_message = "";
		global $woocommerce;
		if ( ! class_exists( 'Elex_Weight_Boxpack_Express' ) ) {
			include_once 'weight_pack/class-wf-weight-packing.php';
		}
		//$weight_pack=new WeightPack($this->weight_packing_process);  $this->weight_packing_process,
		$weight_pack = new Elex_Weight_Boxpack_Express( $this->weight_packing_process );
		
		if ( $this->weight_boxes ) {
			foreach ( $this->weight_boxes as $key => $box ) {

				if (!$box['enabled']) {
					continue;
				}
				$newbox = $weight_pack->add_weight_box( $box['length'], $box['width'], $box['height'], $box['min_weight'] , $box['max_weight'] , $box['name']);
				

				if (isset($box['id'])) {
					$newbox->set_id($box['id']);
				}
				if (isset($box['name'])) {
					$newbox->set_name($box['name']);
				}
				$newbox->set_max_weight($box['max_weight']);

				$newbox->set_min_weight($box['min_weight']);
			}

		}
		$package_total_weight = 0;
		$insured_value        = 0;

		/* For WooCommerce Composite Products */
		if ($this->is_woocommerce_composite_products_installed) {
			$package = $this->get_composite_product_data($package);
		}
		$ctr = 0;

		foreach ($package['contents'] as $item_id => $values) {
			$ctr++;
			
			$skip_product = apply_filters('wf_shipping_skip_product_from_dhl_label', false, $values, $package['contents']);
			if ($skip_product) {
				continue;
			}
			if ($this->is_woocommerce_product_bundles_installed) {
                $is_item_bundled_item = $this->is_product_bundled_item($values);
                if ($is_item_bundled_item) {
                    continue;
                }
            }

			if (!( $values['quantity'] > 0 && isset($values['data']) && $values['data']->needs_shipping() )) {
				$debug_message .= sprintf(__('Product #%d is virtual. Skipping.', 'wf-shipping-dhl'), $ctr);
				WC()->session->set('debug_message', $debug_message);
				continue;
			}

			if (!$values['data']->get_weight()) {
				$debug_message .= sprintf(__('Product #%d is missing weight.', 'wf-shipping-dhl'), $ctr);
				WC()->session->set('debug_message', $debug_message);
				return;
			}
		  

			for ($i = 1; $i <= $values['quantity']; $i++) {
				$weight_pack->add_item(
					wc_get_weight( elex_dhl_get_product_weight( $values['data']), $this->weight_unit ),
					wc_get_dimension($values['data']->get_length(), $this->dimension_unit),
					wc_get_dimension($values['data']->get_width(), $this->dimension_unit),
					wc_get_dimension($values['data']->get_height(), $this->dimension_unit),
					array( 'data'=> $values['data'] ),
					$values['quantity'] ,
					$values['data']->get_price()
				);
			}
		}
		
		$weight_pack->pack(); 
		$pack     = $weight_pack->get_packages(); 
		$errors   =   '';
		$to_ship  = array();
		$group_id = 1;
 
		foreach ($pack as $package) {
			if ($package->unpacked === true) {
				$debug_message .=_('Item not packed in any box');
				WC()->session->set('debug_message', $debug_message);
				if (!$package->length > 0 ||  !$package->width > 0 || !$package->height > 0 ) {
					$debug_message .= sprintf(__('Product #%d is missing dimensions.', 'wf-shipping-dhl'), $ctr);
					WC()->session->set('debug_message', $debug_message);
					return;
				}
			} 

			$flag       =0;
			$dimensions = array($package->length, $package->width, $package->height);
			sort($dimensions);
			$insurance_array = array(
				'Amount' => round($package->value),
				'Currency' => get_woocommerce_currency()
			);

			$group = array(
				'GroupNumber' => $group_id,
				'GroupPackageCount' => 1,
				'Weight' => array(
					'Value' => round($package->weight, 3),
					'Units' => $this->weight_unit
				),
				'Dimensions' => array(
					'Length' =>  round($dimensions[2]),
					'Width' => round($dimensions[1]),
					'Height' => round($dimensions[0]),
					'Units' => $this->dimension_unit
				),
				'InsuredValue' => $insurance_array,
				'packed_products' => array(),
				'package_id' => $package->id,
				'package_name'=>  isset($package->name)?$package->name:'pack',
				'packtype' => isset($package->packtype)?$package->packtype:'BOX'
			);
		   
			if (!empty($package->packed) && is_array($package->packed)) {
				foreach ($package->packed as $packed) {
					$group['packed_products'][] = $packed->get_meta('data');
				}
			}
			$to_ship[] = $group;

			$group_id++;
		}
		return $to_ship;
	}
	private function wf_load_order( $orderId) {
		if (!class_exists('WC_Order')) {
			return false;
		}
		return wc_get_order($orderId);
	}
	private function per_item_shipping( $package ) {
		$debug_message = "";
		$to_ship  = array();
		$group_id = 1;

		/* For WooCommerce Composite Products */
		if ($this->is_woocommerce_composite_products_installed) {
			$package = $this->get_composite_product_data($package);
		}

		// Get weight of order
		foreach ($package['contents'] as $item_id => $values) {
			$skip_product = apply_filters('wf_shipping_skip_product_from_dhl_rate', false, $values, $package['contents']);
			if ($skip_product) {
				continue;
			}

			if (!( isset($values['data']) && $values['data']->needs_shipping() )) {
				$debug_message .= sprintf(__('Product # is virtual. Skipping.', 'wf-shipping-dhl'), $item_id);
				WC()->session->set('debug_message', $debug_message);
				continue;
			}

			if (!$values['data']->get_weight()) {
				$debug_message .= sprintf(__('Product %s is missing weight. Aborting.', 'wf-shipping-dhl'), $values['data']->get_name());
				WC()->session->set('debug_message', $debug_message);
				return;
			}

			if (!isset($values['quantity'])) {
				$values['quantity'] = 1;
			}

			$group = array();



			$insurance_array = array(
				'Amount' => round($values['data']->get_price()),
				'Currency' => get_woocommerce_currency()
			);

			$xa_per_item_weight = elex_dhl_get_product_weight( $values['data']);

			if ($this->site_weight_unit != $this->weight_unit) {
				$xa_per_item_weight = wc_get_weight($xa_per_item_weight, $this->weight_unit, $this->site_weight_unit);
			}

			if ($xa_per_item_weight < 0.001) {
				$xa_per_item_weight = 0.001;
			}

			$group = array(
				'GroupNumber' => $group_id,
				'GroupPackageCount' => 1,
				'Weight' => array(
					'Value' => round($xa_per_item_weight, 3),
					'Units' => $this->weight_unit
				),
				'packed_products' => array($values['data'])
			);
			
			if ( elex_dhl_get_product_length( $values['data'] ) && elex_dhl_get_product_height( $values['data'] ) && elex_dhl_get_product_width( $values['data'] )) {

				$dimensions = array( elex_dhl_get_product_length( $values['data'] ), elex_dhl_get_product_width( $values['data'] ), elex_dhl_get_product_height( $values['data'] ));

				sort($dimensions);

				if ($this->site_dimensional_unit != $this->dimension_unit) {
					foreach ($dimensions as $index => $dimension) {
						$dimensions[$index] = wc_get_dimension($dimension, $this->dimension_unit, $this->site_dimensional_unit);
					}
				}

				$group['Dimensions'] = array(
					'Length' => $dimensions[2],
					'Width' => $dimensions[1],
					'Height' => $dimensions[0],
					'Units' => $this->dimension_unit
				);
			}
			$group['packtype']     = isset($this->settings['shp_pack_type'])?$this->settings['shp_pack_type'] : 'BOX';
			$group['InsuredValue'] = $insurance_array;

			for ($i = 0; $i < $values['quantity']; $i++) {
				$to_ship[] = $group;
			}

			$group_id++;
		}

		return $to_ship;
	}

	private function box_shipping( $package) {
		if (!class_exists('WF_Boxpack_Express')) {
			include_once 'class-wf-packing.php';
		}

		$boxpack = new WF_Boxpack_Express();

		// Define boxes
		foreach ($this->boxes as $key => $box) {
			if (!$box['enabled']) {
				continue;
			}
			$box['pack_type'] = !empty($box['pack_type']) ? $box['pack_type'] : 'BOX' ;
			
			$newbox = $boxpack->add_box($box['length'], $box['width'], $box['height'], $box['box_weight'], $box['pack_type']);

			if (isset($box['id'])) {
				$newbox->set_id(current(explode(':', $box['id'])));
			}

			if ($box['max_weight']) {
				$newbox->set_max_weight($box['max_weight']);
			}
			if ($box['pack_type']) {
				$newbox->set_packtype($box['pack_type']);
			}
		}

		/* For WooCommerce Composite Products */
		if ($this->is_woocommerce_composite_products_installed) {
			$package = $this->get_composite_product_data($package);
		}

		// Add items
		if (isset($package['contents'])) {
			foreach ($package['contents'] as $item_id => $values) {

				$skip_product = apply_filters('wf_shipping_skip_product_from_dhl_rate', false, $values, $package['contents']);
				if ($skip_product) {
					continue;
				}
				
				if (!( isset($values['data']) && $values['data']->needs_shipping() )) {
					$debug_message .= sprintf(__('Product # is virtual. Skipping.', 'wf-shipping-dhl'), $item_id);
					WC()->session->set('debug_message', $debug_message);
					continue;
				}
			
				if (!$values['data']->get_weight()) {
					$debug_message .= sprintf(__('Product %s is missing weight. Aborting.', 'wf-shipping-dhl'), $values['data']->get_name());
					WC()->session->set('debug_message', $debug_message);
					return;
				}

				$product_label = '';

				if (WC()->version < '2.7.0') {
					$values_data_post       = $values['data']->post;
					$values_data_post_title = $values_data_post->post_title;
					$product_label          = $values_data_post_title;
				} else {
					$product_data  = $values['data']->get_data();
					$product_label = $product_data['name'];
				}

				if ( elex_dhl_get_product_length( $values['data'] ) && elex_dhl_get_product_height( $values['data'] ) && elex_dhl_get_product_width( $values['data'] ) && elex_dhl_get_product_weight( $values['data'] )) {

					$dimensions = array( elex_dhl_get_product_length( $values['data'] ), elex_dhl_get_product_height( $values['data'] ), elex_dhl_get_product_width( $values['data'] ));

				if ($this->site_weight_unit != $this->weight_unit) {
					$xa_per_item_weight = wc_get_weight(elex_dhl_get_product_weight($values['data']), $this->weight_unit, $this->site_weight_unit);
				}				

					for ($i = 0; $i < $values['quantity']; $i ++) {
						$boxpack->add_item(
								wc_get_dimension($dimensions[2], $this->dimension_unit), wc_get_dimension($dimensions[1], $this->dimension_unit), wc_get_dimension($dimensions[0], $this->dimension_unit),wc_get_weight( elex_dhl_get_product_weight($values['data']), $this->weight_unit), $values['data']->get_price(), array(
							'data' => $values['data']
								)
						);
					}
				} else {
					if (!elex_dhl_get_product_weight( $values['data'] )) {
						$product_weight = round(elex_dhl_get_product_weight( $values['data']), 3);

						for ($i = 0; $i < $values['quantity']; $i ++) {
							if (WC()->version < '2.7.0') {
								$boxpack->add_item(wc_get_dimension($values['data']['variation_has_length'], $this->dimension_unit), wc_get_dimension($values['data']['variation_has_width'], $this->dimension_unit), wc_get_dimension($values['data']['variation_has_height'], $this->dimension_unit), wc_get_weight($product_weight, $this->weight_unit), $values['data']->get_price(), array(
								'data' => $values['data']));
							} else {
								$boxpack->add_item(wc_get_dimension($product_data['length'], $this->dimension_unit), wc_get_dimension($product_data['width'], $this->dimension_unit), wc_get_dimension($product_data['height'], $this->dimension_unit), wc_get_weight($product_weight, $this->weight_unit), $values['data']->get_price(), array(
								'data' => $values['data']));
							}
						}

					}
				}

			}
		}
	

		// Pack it
		$boxpack->pack();
		$packages = $boxpack->get_packages();
		$to_ship  = array();
		$group_id = 1;

		foreach ($packages as $package) {
			if ($package->unpacked === true) {
				$debug_message .= ('Item not packed in any box');
		  		WC()->session->set('debug_message', $debug_message);
			} 

			$dimensions = array($package->length, $package->width, $package->height);

			sort($dimensions);
			$insurance_array = array(
				'Amount' => round($package->value),
				'Currency' => get_woocommerce_currency()
			);
			
		

			$group = array(
				'GroupNumber' => $group_id,
				'GroupPackageCount' => 1,
				'Weight' => array(
					'Value' => $package->weight,
					'Units' => $this->weight_unit
				),
				'Dimensions' => array(
					'Length' => max(1, round($dimensions[2])),
					'Width' => max(1, round($dimensions[1])),
					'Height' => max(1, round($dimensions[0])),
					'Units' => $this->dimension_unit
				),
				'InsuredValue' => $insurance_array,
				'packed_products' => array(),
				'package_id' => $package->id,
				'packtype' => isset($package->packtype)?$package->packtype:'BOX'
			);

			if (!empty($package->packed) && is_array($package->packed)) {
				foreach ($package->packed as $packed) {
					$group['packed_products'][] = $packed->get_meta('data');
				}
			}
			$to_ship[] = $group;

			$group_id++;
		}

	

		return $to_ship;
	}

	/**
	* function returns the collection of countries which different WooCommerce country codes and DHL accepted country codes
	 *
	* @access public
	* @return array countries
	*/
	public function dhl_country_codes_with_conflicts() {
		$countries = array( 
			'Bonaire' => array(
				'Woocommerce_country_code' => 'BQ',
				'dhl_country_code' => 'XB'
			),
			'Curacao' => array(
				'Woocommerce_country_code' => 'CW',
				'dhl_country_code' => 'XC'
			),
			'Saint Barthelemy' => array(
				'Woocommerce_country_code' => 'BL',
				'dhl_country_code' => 'XY'
			),
			'St. Maarten' => array(
				'Woocommerce_country_code' => 'MF',
				'dhl_country_code' => 'XM'
			),
		);

		return $countries;
	}

	/**
	* function returns DHL accepted country codes for a given WooCommerce country codes
	 *
	* @access public
	*/
	public function get_country_codes_mapped_for_dhl( $country_code) {
		$conflict_countries_codes = $this->dhl_country_codes_with_conflicts();

		foreach ($conflict_countries_codes as $conflict_countries_codes_key => $conflict_countries_codes_values) {
			if ($conflict_countries_codes_values['Woocommerce_country_code'] === $country_code) {
				return $conflict_countries_codes_values['dhl_country_code'];
			}
		}
		return $country_code;
	}

	/**
	* function to provide next working day if mailing day is a non-working day
	 *
	* @access public
	* @param string, string, boolean
	* @return boolean or date
	*/
	public function provide_next_working_day( $requested_shipment_mailing_day, $seller_store_working_days, $recurrsion = false) {
		if ($recurrsion == true) {
			foreach ($seller_store_working_days as $seller_store_working_day_name => $seller_store_working_day) {
				if ($seller_store_working_day['status'] == 'yes') {
					return date('Y-m-d', strtotime('next ' . $seller_store_working_day['name']));
				}
			}
			return false;
		} else {
			foreach ($seller_store_working_days as $seller_store_working_day_name => $seller_store_working_day) {
				if (( $seller_store_working_day['value'] > $seller_store_working_days[$requested_shipment_mailing_day]['value'] ) && ( $seller_store_working_day['status'] === 'yes' )) {
					return date('Y-m-d', strtotime('next ' . $seller_store_working_day['name']));
				}
			}
			return false;
		}
	}

	public function elex_dhl_get_mailing_date ( $mailing_date, $mailing_day) {
		/*Working days array with settings provided by user in settings*/
		$working_days        = array(
			'Mon'   => array('name' => 'monday', 'status'=> isset($this->settings['working_day_monday'])? $this->settings['working_day_monday']: 'no', 'value' => 1),
			'Tue'   => array('name' => 'tuesday', 'status'=> isset($this->settings['working_day_tuesday'])? $this->settings['working_day_tuesday']: 'no', 'value' => 2),           
			'Wed'   => array('name' => 'wednesday', 'status'=> isset($this->settings['working_day_wednesday'])? $this->settings['working_day_wednesday']: 'no', 'value' => 3),
			'Thu'   => array('name' => 'thursday', 'status'=> isset($this->settings['working_day_thursday'])? $this->settings['working_day_thursday']: 'no', 'value' => 4),            
			'Fri'   => array('name' => 'friday', 'status'=> isset($this->settings['working_day_friday'])? $this->settings['working_day_friday']: 'no', 'value' => 5),          
			'Sat'   => array('name' => 'saturday', 'status'=> isset($this->settings['working_day_saturday'])? $this->settings['working_day_saturday']: 'no', 'value' => 6),            
			'Sun'   => array('name' => 'sunday', 'status'=> isset($this->settings['working_day_sunday'])? $this->general_settings['working_day_sunday']: 'no', 'value' => 7),      
		);
		$current_time        = current_time('H:i');
		$mailing_cutoff_time = isset($this->settings['elex_dhl_cutoff_time'])? $this->settings['elex_dhl_cutoff_time']: '';

		

		if (strtotime($current_time) >= strtotime($mailing_cutoff_time) || $working_days[$mailing_day]['status'] !== 'yes') {
			$next_working_date = $this->provide_next_working_day($mailing_day, $working_days);
			if (!$next_working_date) {
				$recurrsion        = true;
				$next_working_date = $this->provide_next_working_day($mailing_day, $working_days, $recurrsion);
			}

			if ($next_working_date) {
				$mailing_date = $next_working_date;
			}
		}
		return $mailing_date;
	}

	private function get_dhl_requests( $dhl_packages, $package, $package_total_amt = '') {
		global $woocommerce;

		if ( ! class_exists( 'wf_dhl_woocommerce_shipping_admin_helper' ) ) {
		include_once 'class-wf-dhl-woocommerce-shipping-admin-helper.php';
		}

		$woo_dhl_wrapper = new wf_dhl_woocommerce_shipping_admin_helper();

		
		

		/*  According to WooCommerce The Canary Islands is a country, but according to DHL it is a part of Spain.
			If the postcodes belong to Canary Islands, we are providing country code as 'ES'
		*/

		// Time is modified to avoid date diff with server.
		$mailing_date     = current_time('Y-m-d');
		$mailing_day      = current_time('D', strtotime($mailing_date));
		$mailing_date     = $this->elex_dhl_get_mailing_date($mailing_date, $mailing_day);
		$mailing_datetime = date('Y-m-d', current_time('timestamp')) . 'T' . date('H:i:s', current_time('timestamp'));
		

		$destination_postcode = str_replace(' ', '', strtoupper($package['destination']['postcode']));
		$pieces               = $this->wf_get_package_piece($dhl_packages);

		$order_items_total = $woo_dhl_wrapper->get_order_items_total($package['contents']);
		if ($package_total_amt) {
			$total_value = $package_total_amt;
		} else {
			$total_value = $woocommerce->cart->cart_contents_total;
		}
		$currency              = get_woocommerce_currency();
		$total_insurance_value = 0;
		$is_dutiable           = '';
		$origin_postcode_city  = '';
		$paymentCountryCode    = '';
		
		if ($this->settings['insure_contents'] == 'yes' && !empty($this->insure_converstion_rate)  && isset($_POST['wf_dhl_insurance']) && $_POST['wf_dhl_insurance'] == 'yes') {
			$total_insurance_value = round(apply_filters('wc_aelia_cs_convert', $total_value, get_woocommerce_currency(), $this->shop_currency)) * $this->insure_converstion_rate;
		} else {
			$total_insurance_value = $total_value;
		}
		
		$total_insurance_value = apply_filters('wc_aelia_cs_convert', $total_insurance_value, $this->shop_currency, get_woocommerce_currency());

		$insurance_details            = '';
		$additional_insurance_details ='';
		$insurance_enabled            = ( isset($package['dhl_insurance']) && !empty($package['dhl_insurance']) )? $package['dhl_insurance'] : 'no';

		$this->insure_currency = ( !empty($this->insure_currency) ) ? $this->insure_currency : get_woocommerce_currency();

		if ($insurance_enabled == 'yes') {
			update_option('wf_dhl_insurance', 'yes');
		} else {
			update_option('wf_dhl_insurance', 'no');
		}
		
		if (is_shop()) {
			if ($insurance_enabled == 'yes') {
				$insurance_details            = $this->insure_contents ? "<InsuredValue>{$total_insurance_value}</InsuredValue><InsuredCurrency>{$this->insure_currency}</InsuredCurrency>" : '';
				$additional_insurance_details = ( $this->insure_contents && ( $this->conversion_rate || $this->insure_converstion_rate ) ) ? '<SpecialServiceType>II</SpecialServiceType><LocalSpecialServiceType>XCH</LocalSpecialServiceType>' : '';
			}
		} else {
			$this->insure_currency = ( !empty($this->insure_currency) ) ? $this->insure_currency : get_woocommerce_currency();
			if ($insurance_enabled == 'yes') {
				$insurance_details            = $this->insure_contents ? "<InsuredValue>{$total_insurance_value}</InsuredValue><InsuredCurrency>{$this->insure_currency}</InsuredCurrency>" : '';
				$additional_insurance_details = ( $this->insure_contents && ( $this->conversion_rate || $this->insure_converstion_rate ) ) ? '<SpecialServiceType>II</SpecialServiceType><LocalSpecialServiceType>XCH</LocalSpecialServiceType>' : '';
			}
		}

		$special_service_type_details = '';
		if ( $additional_insurance_details != '' ) {
			$special_service_type_details = '<QtdShp><QtdShpExChrg>';
	
			if ($additional_insurance_details != '') {
				$special_service_type_details .= $additional_insurance_details;
			}
			$special_service_type_details .= '</QtdShpExChrg></QtdShp>';
		}



		//If vendor country set, then use vendor address
		if (isset($this->settings['vendor_check']) && ( $this->settings['vendor_check'] === 'yes' )) {
			if (isset($package['origin'])) {
				if (isset($package['origin']['country'])) {
					$this->origin_country_1     =     $package['origin']['country'];
					$this->origin               =     $package['origin']['postcode'];
					$this->freight_shipper_city =     $package['origin']['city'];

					$origin_postcode_city = $this->wf_get_postcode_city($this->origin_country_1, $this->freight_shipper_city, $this->origin);
				}
			}
		} else {
			$origin_postcode_city = $this->wf_get_postcode_city($this->origin_country, $this->freight_shipper_city, $this->origin);
		}

		$paymentCountryCode = isset($this->general_settings['dutypayment_country']) && !empty($this->general_settings['dutypayment_country'])? $this->general_settings['dutypayment_country']: $this->general_settings['base_country'];// obtaining payment country code from label settings
		
		// For multi-vendor cases
		if ( isset( $this->settings['vendor_check'] ) && $this->settings['vendor_check'] === 'yes' ) {
			$is_dutiable = ( $package['destination']['country'] == $this->origin_country_1 || wf_dhl_is_eu_country($this->origin_country_1, $package['destination']['country']) ) ? 'N' : 'Y';
		} else {
			$is_dutiable = ( $package['destination']['country'] == $this->origin_country || wf_dhl_is_eu_country($this->origin_country, $package['destination']['country']) ) ? 'N' : 'Y';
		}
		if (isset($this->settings['rate_is_dutiable']) && $this->settings['rate_is_dutiable'] == 'N') {
			$is_dutiable = 'N';
		}
		
		if (( $package['destination']['country'] == 'ES' ) && ( $package['destination']['state'] == 'CE' || $package['destination']['state'] == 'ML' )) {
			 $is_dutiable 		= 'Y';
		}
	   
		if (isset($this->settings['dutypayment_type']) && $this->settings['dutypayment_type'] == '') {
			$is_dutiable 		= 'N';
		}
	 

		$order_dutiable_amount = $total_value != 0 ? $total_value : $order_items_total;

		$dutiable_content 		= $is_dutiable == 'Y' ? "<Dutiable><DeclaredCurrency>{$currency}</DeclaredCurrency><DeclaredValue>{$order_dutiable_amount}</DeclaredValue></Dutiable>" : '';

		$destination_city 		= htmlspecialchars(strtoupper($package['destination']['city']));

		/*There are different country codes for same country from WooCommerce and DHL. Here we are obtaining country code which is mapped to DHL for both source and destination countries*/
		$destination_country_code = $this->get_country_codes_mapped_for_dhl($package['destination']['country']);
		
		$state_as_city = apply_filters('elex_dhl_send_state_as_city_to_api', false);
		if ($state_as_city) {
			if (in_array($package['destination']['country'], $state_as_city)) {
				$destination_city 				= strtoupper($package['destination']['state']);
			}
		}
	

		$destination_postcode_city           	= $this->wf_get_postcode_city($package['destination']['country'], $destination_city, $destination_postcode);
		$source_country_code                 	= $this->get_country_codes_mapped_for_dhl($this->origin_country_1);
		$switch_account_number_action_input  	= array( 'site_id'=> $this->settings['site_id'], 'site_password'=> $this->settings['site_password'], 'account_number' => $this->settings['account_number'], 'source_country_code' => $source_country_code, 'payment_country_code' => $this->settings['dutypayment_country'] , 'destination_country_code' => $package['destination']['country']);
		$switch_account_number_action_result 	= apply_filters('switch_account_number_action_express_dhl_elex', $switch_account_number_action_input, $package);
		$switch_account_number_action_result_mv = apply_filters('switch_account_number_action_express_dhl_elex_mv_woocommerce_dhl_shipping', $switch_account_number_action_result, $package, 'woocommerce_dhl_shipping','');



		$this->account_number 	= isset($switch_account_number_action_result_mv['payment_account_number'])? $switch_account_number_action_result_mv['payment_account_number']: $this->settings['account_number'] ;

		$paymentCountryCode 	= isset($switch_account_number_action_result_mv['payment_country_code'])? $switch_account_number_action_result_mv['payment_country_code']: $switch_account_number_action_result_mv['source_country_code'];
		
		$fetch_accountrates    	= $this->request_type == 'ACCOUNT' ? '<PaymentAccountNumber>' . $this->account_number . '</PaymentAccountNumber>' : '';
		
		$this->account_number 	= isset($switch_account_number_action_result_mv['payment_account_number'])? $switch_account_number_action_result_mv['payment_account_number']: $switch_account_number_action_result_mv['account_number'];

		$message_reference_num 	= elex_dhl_generate_random_message_reference();

		
$xmlRequest         = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
  <GetQuote>
    <Request>
        <ServiceHeader>
            <MessageTime>{$mailing_datetime}</MessageTime>
            <MessageReference>{$message_reference_num}</MessageReference>
            <SiteID>{$switch_account_number_action_result_mv['site_id']}</SiteID>
            <Password>{$switch_account_number_action_result_mv['site_password']}</Password>
        </ServiceHeader>
    </Request>
    <From>
      <CountryCode>{$source_country_code}</CountryCode>
      {$origin_postcode_city}
    </From>
    <BkgDetails>
      <PaymentCountryCode>{$paymentCountryCode}</PaymentCountryCode>
      <Date>{$mailing_date}</Date>
      <ReadyTime>PT10H21M</ReadyTime>
      <DimensionUnit>{$this->quoteapi_dimension_unit}</DimensionUnit>
      <WeightUnit>{$this->quoteapi_weight_unit}</WeightUnit>
      <Pieces>
        {$pieces}
      </Pieces>
      {$fetch_accountrates}
      <IsDutiable>{$is_dutiable}</IsDutiable>
      <NetworkTypeCode>AL</NetworkTypeCode>
      {$special_service_type_details}
      {$insurance_details}
    </BkgDetails>
    <To>
      <CountryCode>{$destination_country_code}</CountryCode>
      {$destination_postcode_city}
    </To>
    {$dutiable_content}
  </GetQuote>
</p:DCTRequest>
XML;
		$xmlRequest = apply_filters('wf_dhl_rate_request', $xmlRequest, $package);
		return $xmlRequest;
	}

	private function wf_get_package_piece( $dhl_packages) {
		$pieces = '';
	

		if ($dhl_packages) {
			foreach ($dhl_packages as $key => $parcel) {
				$pack_type = $this->wf_get_pack_type($parcel['packtype']);
				$index     = $key + 1;
				$pieces   .= '<Piece><PieceID>' . $index . '</PieceID>';
				$pieces   .= '<PackageTypeCode>' . $pack_type . '</PackageTypeCode>';
				
				if ( !empty($parcel['Dimensions']['Height']) && !empty($parcel['Dimensions']['Length']) && !empty($parcel['Dimensions']['Width']) ) {
					$pieces .= '<Height>' . round($parcel['Dimensions']['Height']) . '</Height>';
					$pieces .= '<Depth>' . round($parcel['Dimensions']['Length']) . '</Depth>';
					$pieces .= '<Width>' . round($parcel['Dimensions']['Width']) . '</Width>';
				}
				
				$package_total_weight =(string) $parcel['Weight']['Value'];
				$package_total_weight = str_replace(',', '.', $package_total_weight);
				if ($package_total_weight < 0.001) {
					$package_total_weight = 0.001;
				} else {
					$package_total_weight = $package_total_weight;
				}
				$pieces .= '<Weight>' . round((float) $package_total_weight, 3) . '</Weight></Piece>';
			}
		}
	
		return $pieces;
	}

	private function wf_get_postcode_city( $country, $city, $postcode) {
		$no_postcode_country = array('AE', 'AF', 'AG', 'AI', 'AL', 'AN', 'AO', 'AW', 'BB', 'BF', 'BH', 'BI', 'BJ', 'BM', 'BO', 'BS', 'BT', 'BW', 'BZ', 'CD', 'CF', 'CG', 'CI', 'CK',
			'CL', 'CM', 'CR', 'CV', 'DJ', 'DM', 'DO', 'EC', 'EG', 'ER', 'ET', 'FJ', 'FK', 'GA', 'GD', 'GH', 'GI', 'GM', 'GN', 'GQ', 'GT', 'GW', 'GY', 'HK', 'HN', 'HT', 'IE', 'IQ', 'IR',
			'JM', 'JO', 'KE', 'KH', 'KI', 'KM', 'KN', 'KP', 'KW', 'KY', 'LA', 'LB', 'LC', 'LK', 'LR', 'LS', 'LY', 'ML', 'MM', 'MO', 'MR', 'MS', 'MT', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'NI',
			'NP', 'NR', 'NU', 'OM', 'PA', 'PE', 'PF', 'PY', 'QA', 'RW', 'SA', 'SB', 'SC', 'SD', 'SL', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'TC', 'TD', 'TG', 'TL', 'TO', 'TT', 'TV', 'TZ',
			'UG', 'UY', 'VC', 'VE', 'VG', 'VN', 'VU', 'WS', 'XA', 'XB', 'XC', 'XE', 'XL', 'XM', 'XN', 'XS', 'YE', 'ZM', 'ZW');

		$postcode_city = "<Postalcode>{$postcode}</Postalcode>";

		$postcode_city = !in_array( $country, $no_postcode_country ) ? $postcode_city : '';
		if ( !empty($city) ) {
			$postcode_city .= "<City>{$city}</City>";
		}
		return $postcode_city;
	}
	
	/**
	* @access private
	* Function to get country code for corresponding currencies
	* @return array currencies with countries' codes as values
	*/
	public function wf_get_currency_countries() {
		return array(
			'AFN' => array( 'AF' ),
			'ALL' => array( 'AL' ),
			'DZD' => array( 'DZ' ),
			'USD' => array( 'AS', 'IO', 'GU', 'MH', 'FM', 'MP', 'PW', 'PR', 'TC', 'US', 'UM', 'VI' ),
			'EUR' => array( 'AD', 'AT', 'BE', 'CY', 'EE', 'FI', 'FR', 'GF', 'TF', 'DE', 'GR', 'GP', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'MQ', 'YT', 'MC', 'ME', 'NL', 'PT', 'RE', 'PM', 'SM', 'SK', 'SI', 'ES' ),
			'AOA' => array( 'AO' ),
			'XCD' => array( 'AI', 'AQ', 'AG', 'DM', 'GD', 'MS', 'KN', 'LC', 'VC' ),
			'ARS' => array( 'AR' ),
			'AMD' => array( 'AM' ),
			'AWG' => array( 'AW' ),
			'AUD' => array( 'AU', 'CX', 'CC', 'HM', 'KI', 'NR', 'NF', 'TV' ),
			'AZN' => array( 'AZ' ),
			'BSD' => array( 'BS' ),
			'BHD' => array( 'BH' ),
			'BDT' => array( 'BD' ),
			'BBD' => array( 'BB' ),
			'BYR' => array( 'BY' ),
			'BZD' => array( 'BZ' ),
			'XOF' => array( 'BJ', 'BF', 'ML', 'NE', 'SN', 'TG' ),
			'BMD' => array( 'BM' ),
			'BTN' => array( 'BT' ),
			'BOB' => array( 'BO' ),
			'BAM' => array( 'BA' ),
			'BWP' => array( 'BW' ),
			'NOK' => array( 'BV', 'NO', 'SJ' ),
			'BRL' => array( 'BR' ),
			'BND' => array( 'BN' ),
			'BGN' => array( 'BG' ),
			'BIF' => array( 'BI' ),
			'KHR' => array( 'KH' ),
			'XAF' => array( 'CM', 'CF', 'TD', 'CG', 'GQ', 'GA' ),
			'CAD' => array( 'CA' ),
			'CVE' => array( 'CV' ),
			'KYD' => array( 'KY' ),
			'CLP' => array( 'CL' ),
			'CNY' => array( 'CN' ),
			'HKD' => array( 'HK' ),
			'COP' => array( 'CO' ),
			'KMF' => array( 'KM' ),
			'CDF' => array( 'CD' ),
			'NZD' => array( 'CK', 'NZ', 'NU', 'PN', 'TK' ),
			'CRC' => array( 'CR' ),
			'HRK' => array( 'HR' ),
			'CUP' => array( 'CU' ),
			'CZK' => array( 'CZ' ),
			'DKK' => array( 'DK', 'FO', 'GL' ),
			'DJF' => array( 'DJ' ),
			'DOP' => array( 'DO' ),
			'ECS' => array( 'EC' ),
			'EGP' => array( 'EG' ),
			'SVC' => array( 'SV' ),
			'ERN' => array( 'ER' ),
			'ETB' => array( 'ET' ),
			'FKP' => array( 'FK' ),
			'FJD' => array( 'FJ' ),
			'GMD' => array( 'GM' ),
			'GEL' => array( 'GE' ),
			'GHS' => array( 'GH' ),
			'GIP' => array( 'GI' ),
			'QTQ' => array( 'GT' ),
			'GGP' => array( 'GG' ),
			'GNF' => array( 'GN' ),
			'GWP' => array( 'GW' ),
			'GYD' => array( 'GY' ),
			'HTG' => array( 'HT' ),
			'HNL' => array( 'HN' ),
			'HUF' => array( 'HU' ),
			'ISK' => array( 'IS' ),
			'INR' => array( 'IN' ),
			'IDR' => array( 'ID' ),
			'IRR' => array( 'IR' ),
			'IQD' => array( 'IQ' ),
			'GBP' => array( 'IM', 'JE', 'GS', 'GB' ),
			'ILS' => array( 'IL' ),
			'JMD' => array( 'JM' ),
			'JPY' => array( 'JP' ),
			'JOD' => array( 'JO' ),
			'KZT' => array( 'KZ' ),
			'KES' => array( 'KE' ),
			'KPW' => array( 'KP' ),
			'KRW' => array( 'KR' ),
			'KWD' => array( 'KW' ),
			'KGS' => array( 'KG' ),
			'LAK' => array( 'LA' ),
			'LBP' => array( 'LB' ),
			'LSL' => array( 'LS' ),
			'LRD' => array( 'LR' ),
			'LYD' => array( 'LY' ),
			'CHF' => array( 'LI', 'CH' ),
			'MKD' => array( 'MK' ),
			'MGF' => array( 'MG' ),
			'MWK' => array( 'MW' ),
			'MYR' => array( 'MY' ),
			'MVR' => array( 'MV' ),
			'MRO' => array( 'MR' ),
			'MUR' => array( 'MU' ),
			'MXN' => array( 'MX' ),
			'MDL' => array( 'MD' ),
			'MNT' => array( 'MN' ),
			'MAD' => array( 'MA', 'EH' ),
			'MZN' => array( 'MZ' ),
			'MMK' => array( 'MM' ),
			'NAD' => array( 'NA' ),
			'NPR' => array( 'NP' ),
			'ANG' => array( 'AN' ),
			'XPF' => array( 'NC', 'WF' ),
			'NIO' => array( 'NI' ),
			'NGN' => array( 'NG' ),
			'OMR' => array( 'OM' ),
			'PKR' => array( 'PK' ),
			'PAB' => array( 'PA' ),
			'PGK' => array( 'PG' ),
			'PYG' => array( 'PY' ),
			'PEN' => array( 'PE' ),
			'PHP' => array( 'PH' ),
			'PLN' => array( 'PL' ),
			'QAR' => array( 'QA' ),
			'RON' => array( 'RO' ),
			'RUB' => array( 'RU' ),
			'RWF' => array( 'RW' ),
			'SHP' => array( 'SH' ),
			'WST' => array( 'WS' ),
			'STD' => array( 'ST' ),
			'SAR' => array( 'SA' ),
			'RSD' => array( 'RS' ),
			'SCR' => array( 'SC' ),
			'SLL' => array( 'SL' ),
			'SGD' => array( 'SG' ),
			'SBD' => array( 'SB' ),
			'SOS' => array( 'SO' ),
			'ZAR' => array( 'ZA' ),
			'SSP' => array( 'SS' ),
			'LKR' => array( 'LK' ),
			'SDG' => array( 'SD' ),
			'SRD' => array( 'SR' ),
			'SZL' => array( 'SZ' ),
			'SEK' => array( 'SE' ),
			'SYP' => array( 'SY' ),
			'TWD' => array( 'TW' ),
			'TJS' => array( 'TJ' ),
			'TZS' => array( 'TZ' ),
			'THB' => array( 'TH' ),
			'TOP' => array( 'TO' ),
			'TTD' => array( 'TT' ),
			'TND' => array( 'TN' ),
			'TRY' => array( 'TR' ),
			'TMT' => array( 'TM' ),
			'UGX' => array( 'UG' ),
			'UAH' => array( 'UA' ),
			'AED' => array( 'AE' ),
			'UYU' => array( 'UY' ),
			'UZS' => array( 'UZ' ),
			'VUV' => array( 'VU' ),
			'VEF' => array( 'VE' ),
			'VND' => array( 'VN' ),
			'YER' => array( 'YE' ),
			'ZMW' => array( 'ZM' ),
			'ZWD' => array( 'ZW' ),
		);
	}
	
	private function wf_get_package_total_value( $dhl_packages) {
		$total_value = 0;
		if ($dhl_packages) {
			foreach ($dhl_packages as $key => $parcel) {
				$total_value += $parcel['InsuredValue']['Amount'] * $parcel['GroupPackageCount'];
			}
		}
		return $total_value;
	}

	public function calculate_shipping( $packages = array() ) {


		$str          = '';
		$http_referer = '';
		$req_uri      = '';
		
		if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
			$http_referer = $_SERVER['HTTP_REFERER'];
			$req_uri      = $_SERVER['REQUEST_URI'];
		}
		
		$this->http_req_referer = $http_referer;
		
		if (isset($_POST['post_data'])) {
			parse_str($_POST['post_data'], $str);
		}

		$calculate_shipping_rates = apply_filters('disable_calculate_shipping_in_shop_page_express_dhl_elex', true);// Hook to enable/disable calculating shipping rates on the shop while adding products to the cart
		$is_checkout_page         = false;
		$is_cart_page             = false;
		if (strpos($req_uri, '/checkout') > -1 || is_checkout()) {
			$is_checkout_page = true;
		}
		
		if (strpos($req_uri, '/cart') > -1 || is_cart()) {
			$is_cart_page = true;
		}

		if ( !$calculate_shipping_rates && !( $is_cart_page || $is_checkout_page ) ) {
			return;
		}

		$debug_message = '';
		WC()->session->set('debug_message', $debug_message);

		global $woocommerce;
		// Clear rates
		$this->found_rates = array();

		/* For the Sweden, DHL accepted postcode format is 999 99 */
		if ($packages['destination']['country'] == 'SE') {
			$postcode_part_1 = substr($packages['destination']['postcode'], 0, 3);
			$postcode_part_2 = substr($packages['destination']['postcode'], 3, strlen($packages['destination']['country']));
			if ($postcode_part_2[0] != ' ') {
				$packages['destination']['postcode'] = $postcode_part_1 . ' ' . $postcode_part_2;
			}
		}
		
		// Debugging
		$debug_message .=__('dhl debug mode is on - to hide these messages, turn debug mode off in the settings.', 'wf-shipping-dhl');
		WC()->session->set('debug_message', $debug_message);
		// Packages returned should be an array regardless of filter added or not 
		$parcels = apply_filters('wf_filter_package_address', array($packages) , $this->ship_from_address);
		
	

		// Get requests
		$dhl_requests  =   array();
		$dhl_insurance = false;
		foreach ($parcels as $parcel) {

			$dhl_packs = $this->get_dhl_packages( $parcel );
			
			 /* Handling insurance charges */
			if ($this->settings['insure_contents_chk'] == 'yes') {
				/* on checkout page */
				if ($str != '' && ( strpos($http_referer, 'checkout') > 0 || is_checkout() )) {
					$parcel['dhl_insurance'] = isset($str['wf_dhl_insurance'])? 'yes': 'no';
				} elseif (is_cart() && $this->settings['insure_contents'] == 'yes' && isset($_POST['wf_dhl_insurance']) && $_POST['wf_dhl_insurance'] == 'yes') {
					$parcel['dhl_insurance'] = 'yes';
				} else {
					/* while creating order */
					$parcel['dhl_insurance'] = isset($_POST['wf_dhl_insurance'])? 'yes': 'no';
				}
			} elseif ($this->settings['insure_contents'] == 'yes') {
				$parcel['dhl_insurance'] = 'yes';
			}

			/* Handling insurance charge on shop and cart pages */
			if (is_shop() || strpos($req_uri, '/cart') > 0) {
				if ($this->settings['insure_contents'] == 'yes' && isset($_POST['wf_dhl_insurance']) && $_POST['wf_dhl_insurance'] == 'yes') {
					update_option('wf_dhl_insurance', 'yes');
					$parcel['dhl_insurance'] = 'yes';
				} else {
					$parcel['dhl_insurance'] = 'no';
					update_option('wf_dhl_insurance', 'no');
				}
			}
			
			/* Handling insurance charge while adding items to cart */
			if (strpos($req_uri, 'add_to_cart') > 0) {
				if ($this->settings['insure_contents'] == 'yes' && isset($_POST['wf_dhl_insurance']) && $_POST['wf_dhl_insurance'] == 'yes') {
					update_option('wf_dhl_insurance', 'yes');
					$parcel['dhl_insurance'] = 'yes';
				} else {
					$parcel['dhl_insurance'] = 'no';
					update_option('wf_dhl_insurance', 'no');
				}  
			}
			//For Switzerland we have to send each package as seperate request
			if ($packages['destination']['country'] == 'CH' && $this->settings['base_country'] == 'CH') {
				foreach ($dhl_packs as $key => $value) {
					$dhl_individual_pack       = array();
					$dhl_individual_pack[$key] = $value;
					$dhl_reqs                  = $this->get_dhl_requests( $dhl_individual_pack, $parcel, $value['InsuredValue']['Amount']);
					$dhl_requests[]            = $dhl_reqs;
				}
			} else {
				$dhl_reqs       = $this->get_dhl_requests( $dhl_packs, $parcel );
				$dhl_requests[] = $dhl_reqs;
			}
			$dhl_insurance = isset($parcel['dhl_insurance']) && ( $parcel['dhl_insurance'] == true )? true : false;
			
		}
		if ($dhl_requests) { 
			$this->run_package_request($dhl_requests, $dhl_insurance, $parcel);
		}


		// Ensure rates were found for all packages
		$packages_to_quote_count = sizeof($dhl_requests);
		
		if ($this->found_rates) {
			foreach ($this->found_rates as $key => $value) {
				if ($value['packages'] < $packages_to_quote_count) {
					unset($this->found_rates[$key]);
				}
			}
		}

		// Rate conversion
		if ($this->conversion_rate) {
			foreach ($this->found_rates as $key => $rate) {
				$this->found_rates[$key]['cost'] = $rate['cost'] * $this->conversion_rate;
			}
		}

		/* Handling code for WooCommerce Multi-Currency */
		if ($this->is_woocommerce_multi_currency_installed) {
			foreach ($this->found_rates as $key => $rate) {
				$custom_currency_data            = $this->get_exchange_rate_multicurrency_woocommerce();
				$this->found_rates[$key]['cost'] = $rate['cost'] * $custom_currency_data['exchange_rate'];
			}
		}


		$this->add_found_rates();
	}

	/**
	*   function to obtain exchange/conversion rate for the selected currency when WooCommerce multi currency is installed
	 *
	*   @access public
	*   @param selected currency string
	*   @return array conversion rate, currency symbol
	*/
	public function get_exchange_rate_multicurrency_woocommerce( $selected_currency = '') {
		$exchange_rate   = 1;
		$currency_symbol = get_woocommerce_currency_symbol();
		if (class_exists('WOOMC\MultiCurrency\Rate\Storage')) {
			$Storage      = 'WOOMC\MultiCurrency\Rate\Storage';
			$rate_storage = new $Storage();

			$Detector          = 'WOOMC\MultiCurrency\Currency\Detector';
			$currency_detector = new $Detector();
			$currency_detector->setup_hooks();

			$Rounder       = 'WOOMC\MultiCurrency\Price\Rounder';
			$price_rounder = new $Rounder();

			$Calculator      = 'WOOMC\MultiCurrency\Price\Calculator';
			$price_calcultor = new $Calculator( $rate_storage, $price_rounder );

			$WP     = 'WOOMC\MultiCurrency\DAO\WP';
			$obj_wp = new $WP();

			if (empty($selected_currency)) {
				$selected_currency = $currency_detector->currency();
			}

			$custom_currency_symbol = $obj_wp->getCustomCurrencySymbol($selected_currency);

			$default_currency = $currency_detector->getDefaultCurrency();

			$exchange_rate = $rate_storage->get_rate($selected_currency, $default_currency);
		}

		return array(
			'exchange_rate' => $exchange_rate,
			'currency_symbol' => $custom_currency_symbol
		);
	}

	public function run_package_request( $requests, $dhl_insurance, $parcel ) {
	   
		try {
			foreach ( $requests as $key => $request ) {
				$this->process_result($this->get_result($request), $request, $dhl_insurance, $parcel);
			}            
		} catch (Exception $e) {
			$this->debug(print_r($e, true), 'error');
			return false;
		}
	}

	private function get_result( $request) {
		$debug_message = "";
		$result = wp_remote_post($this->service_url, apply_filters( 'wf_dhl_express_request_data', array(
				'method' => 'POST',
				'timeout' => 70,
				'sslverify' => 0,
				'user_agent'=> '',
				//'headers'          => $this->wf_get_request_header('application/vnd.cpc.shipment-v7+xml','application/vnd.cpc.shipment-v7+xml'),
				'body' => $request
			))
		);

		wc_enqueue_js("
            jQuery('a.debug_reveal').on('click', function(){
                jQuery(this).closest('div').find('.debug_info').slideDown();
                jQuery(this).remove();
                return false;
            });
            jQuery('pre.debug_info').hide();
        ");
		//debug log message in the console.
		$debug_message .= "\n";
		$debug_message .= 'DHL REQUEST:';
		$debug_message .= "\n";
		$debug_message .= $request;
		WC()->session->set('debug_message', $debug_message);
		if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
		$debug_message .= "\n";
		$debug_message .= 'DHL WP ERROR:';
		$debug_message .= "\n";
		$debug_message .= $error_message;
		WC()->session->set('debug_message', $debug_message);
		} elseif (is_array($result) && !empty($result['body'])) {
			$result = $result['body'];
		} else {
			$result = '';
		}

		if (!empty($result) && is_string($result)) {
		$debug_message .= "\n";
		$debug_message .= 'DHL RESPONSE:';
		$debug_message .= "\n";
		$debug_message .= $result;
		WC()->session->set('debug_message', $debug_message);
		   

		}
		
		
		libxml_use_internal_errors(true);

		$xml = '';
		
		if (!empty($result) && is_string($result)) {
			// Check if the string is not already in UTF-8
			if (mb_detect_encoding($result, 'UTF-8', true) === false) {
				// Convert the ISO-8859-1 string to UTF-8
				$result = mb_convert_encoding($result, 'UTF-8', 'ISO-8859-1');
			}
		
			$xml = simplexml_load_string($result);
		}
		
		if ($xml) {
			return $xml;
		} else {
			return null;
		}
	}

	public function wf_get_dhl_base_currency() {
		$base_country       = $this->general_settings['base_country'];
		$currency_countries = array();
		$currency_countries = $this->wf_get_currency_countries();
		$base_currency      = '';
		//Obtaining base country currency code for provided base country code
		foreach ($currency_countries as $currency=>$countries) {
			foreach ($countries as $country) {
				if ($country == $base_country) {
					$base_currency = $currency;
				}
			}
		}

		return $base_currency;
	}

	/**
	* function to get currency based on the country code
	 *
	* @access public
	* @param string country_code
	* @return string currency
	*/
	public function wf_get_currency_based_on_country_code( $country_code) {
		$currency_countries = array();
		$currency_countries = $this->wf_get_currency_countries();
		$currency_code      = '';
		//Obtaining currency code for provided country code
		foreach ($currency_countries as $currency=>$countries) {
			foreach ($countries as $country) {
				if ($country == $country_code) {
					$currency_code = $currency;
				}
			}
		}

		return $currency_code;
	}

	private function wf_get_cost_based_on_currency( $qtdsinadcur, $default_charge, $charge_type) {
		$base_currency = $this->wf_get_dhl_base_currency();
		
		if (!empty($qtdsinadcur)) {
			foreach ($qtdsinadcur as $multiple_currencies) {
				if ($charge_type == 'shipping') {
					if (isset($multiple_currencies['CurrencyCode']) && (string) $multiple_currencies['CurrencyCode'] == $base_currency && !empty($multiple_currencies['TotalAmount']) && ( $multiple_currencies['TotalAmount'] != 0 )) {
						return ( $this->exclude_dhl_tax ? $multiple_currencies['TotalAmount'] - $multiple_currencies['TotalTaxAmount'] : $multiple_currencies['TotalAmount'] );   
					}
				} else {
					if (isset($multiple_currencies['CurrencyCode']) && (string) $multiple_currencies['CurrencyCode'] == $base_currency && !empty($multiple_currencies['WeightCharge']) && ( $multiple_currencies['WeightCharge'] != 0 )) {
						return ( $this->exclude_dhl_tax ? $multiple_currencies['WeightCharge'] - $multiple_currencies['WeightChargeTax'] : $multiple_currencies['WeightCharge'] );   
					}
				}
			}
		}
		return $default_charge;
	}

	private function process_result( $result, $defined_req, $dhl_inurance, $parcel ) {
		
		$processed_ratecode = array();
		$rate_cost_weight   = '';
		$rate_local_code    = '';
		$debug_message = '';
		$base_currency = $this->wf_get_dhl_base_currency();

		$response          = json_decode(json_encode($result), true);
		$response_services = isset($response['GetQuoteResponse']['BkgDetails'])? $response['GetQuoteResponse']['BkgDetails']['QtdShp']: array();

		if (isset($response_services['GlobalProductCode'])) {
			$response_services_temp = $response_services;
			$response_services      = array();
			$response_services[0]   = $response_services_temp;
		}
		
		if ($response && !empty($response_services)) {
			foreach ($response_services as $response_service) {
				$rate_code       = $response_service['GlobalProductCode'];
				$rate_local_code = isset($response_service['LocalProductCode']) ? $response_service['LocalProductCode'] : '';
				$extra_charges   = array();

				if (!in_array($rate_code, $processed_ratecode)) {
					$shipping_rates_source_currency = apply_filters('wf_dhl_shipping_rates_source_currency', get_woocommerce_currency(), $result, $this);
					if (isset($response_service['CurrencyCode']) && (string) $response_service['CurrencyCode'] == $shipping_rates_source_currency) {
						$this->conversion_rate = 1;
						$rate_cost             = $this->exclude_dhl_tax ? floatval((string) ( $response_service['ShippingCharge'] - $response_service['TotalTaxAmount'] )) : floatval((string) $response_service['ShippingCharge']);
						$rate_cost_weight      = $this->exclude_dhl_tax ? floatval((string) ( $response_service['WeightCharge'] - $response_service['WeightChargeTax'] )) : floatval((string) $response_service['WeightCharge']);
						$extra_charges         = $this->get_dhl_extra_charges($response_service);
					} else {
						$charge_type      = 'shipping';
						$rate_cost        = floatval((string) $this->wf_get_cost_based_on_currency($response_service['QtdSInAdCur'], $response_service['ShippingCharge'], $charge_type));
						$charge_type      = 'weight';
						$rate_cost_weight = floatval((string) $this->wf_get_cost_based_on_currency($response_service['QtdSInAdCur'], $response_service['WeightCharge'], $charge_type));
						$extra_charges    = $this->get_dhl_extra_charges($response_service);
					}
					$extra_charges['insurance_charge']      = isset($extra_charges['insurance_charge'])? $extra_charges['insurance_charge']: 0;
					$extra_charges['other_charges']         = isset($extra_charges['other_charges'])? $extra_charges['other_charges']: 0;
					$extra_charges['remote_area_surcharge'] = isset($extra_charges['remote_area_surcharge'])? $extra_charges['remote_area_surcharge']: 0;
					$processed_ratecode[]                   = $rate_code;
					$rate_id                                = $this->id . ':' . $rate_code . '|' . $rate_local_code;
					$mailing_custom_lead_time               = ( isset($this->settings['elex_dhl_custom_lead_time']) && !empty($this->settings['elex_dhl_custom_lead_time']) ) ? sanitize_text_field( $this->settings['elex_dhl_custom_lead_time'] ) : 0;

					$delivery_time = new DateInterval($response_service['DeliveryTime']);
					$delivery_time = $delivery_time->format('%h:%I');
					$delDate       = $response_service['DeliveryDate'];
					$delDate       = date('Y-m-d', strtotime($delDate . '+' . $mailing_custom_lead_time . 'day'));

					$delivery_date_time = $delDate . ' ' . $delivery_time;
					$delivery_date_time = apply_filters('remove_estimated_delivery_time_express_dhl_elex', $delivery_date_time);
					$rate_name          = strval( (string) $response_service['ProductShortName'] );
					if ($rate_cost > 0) {
						$this->prepare_rate($rate_code, $rate_id, $rate_name, $rate_cost, $delivery_date_time, $rate_cost_weight, $extra_charges['insurance_charge'], $extra_charges['remote_area_surcharge'], $shipping_rates_source_currency, $extra_charges['other_charges'], $parcel);
					}
				}
			}
		} elseif ($result && !empty($result->GetQuoteResponse->Note)) {
			foreach ($result->GetQuoteResponse->Note->Condition as $condition) {
				$debug_message .= sprintf(__('DHL Error:  %s', 'wf-shipping-dhl'), htmlspecialchars($condition->ConditionData));
				WC()->session->set('debug_message', $debug_message);
				return;
			}
			$shipping_rates_source_currency = apply_filters('wf_dhl_shipping_rates_source_currency', get_woocommerce_currency(), $result, $this);
			
			if (isset($this->settings['elex_dhl_fall_back']) && sanitize_text_field($this->settings['elex_dhl_fall_back']) > 0) {
				$this->prepare_fallback_rate($shipping_rates_source_currency);
			}

		} elseif (!$result) {
			$shipping_rates_source_currency = apply_filters('wf_dhl_shipping_rates_source_currency', get_woocommerce_currency(), $result, $this);

			if (isset($this->settings['elex_dhl_fall_back']) && sanitize_text_field($this->settings['elex_dhl_fall_back']) > 0) {
				$this->prepare_fallback_rate($shipping_rates_source_currency);
			}
		}
	}
	function prepare_fallback_rate( $shipping_rates_source_currency) {
		$label         = isset($this->settings['title']) && !empty($this->settings['title'])? $this->settings['title'] : 'DHL' ;
		$rate_id       ='wf_dhl_shipping:dhl_fallback';
		$fallback_cost = isset($this->settings['elex_dhl_fall_back']) && !empty($this->settings['elex_dhl_fall_back']) ? sanitize_text_field( intval($this->settings['elex_dhl_fall_back']) ) : 0;

		$this->found_rates[$rate_id] = apply_filters('wf_dhl_shipping_found_rate' , array(
			'id' => $rate_id,
			'label' => $label,
			'cost' => $fallback_cost,
			'sort' => '',
			'packages' => 1,
			'meta_data' => '',
		), $shipping_rates_source_currency, $this);
	}
	private function get_dhl_extra_charges( $response_service) {

		$extra_charges = array();
		if (isset($response_service['QtdShpExChrg'])) {
			$extra_shipping_charges = $response_service['QtdShpExChrg'];
			foreach ($extra_shipping_charges as $extra_shipping_charge) {
				if (isset($extra_shipping_charges['GlobalServiceName'])) {
					$extra_shipping_charge = $extra_shipping_charges;
				}
				if (isset($extra_shipping_charge['GlobalServiceName']) && isset($extra_shipping_charge['ChargeValue'])) {

					if ($extra_shipping_charge['GlobalServiceName'] == 'REMOTE AREA DELIVERY') {
						$extra_charges['remote_area_surcharge'] = $this->exclude_dhl_tax ? $extra_shipping_charge['ChargeValue'] - $extra_shipping_charge['ChargeTaxAmount'] : $extra_shipping_charge['ChargeValue'];
					} elseif ($extra_shipping_charge['GlobalServiceName'] == 'SHIPMENT INSURANCE' || $extra_shipping_charge['GlobalServiceName'] == 'SHIPMENT VALUE PROTECTION') {
						$extra_charges['insurance_charge'] = $this->exclude_dhl_tax ? $extra_shipping_charge['ChargeValue'] - $extra_shipping_charge['ChargeTaxAmount'] : $extra_shipping_charge['ChargeValue'];

					} else {
						if (isset($extra_charges['other_charges'])) {
							$extra_charges['other_charges'] += $this->exclude_dhl_tax ? $extra_shipping_charge['ChargeValue'] - $extra_shipping_charge['ChargeTaxAmount'] : $extra_shipping_charge['ChargeValue'];

						} else {
							$extra_charges['other_charges'] = $this->exclude_dhl_tax ? $extra_shipping_charge['ChargeValue'] - $extra_shipping_charge['ChargeTaxAmount'] : $extra_shipping_charge['ChargeValue'];

						}
					}
				}
				if (isset($extra_shipping_charges['GlobalServiceName'])) {
					break;
				}
			}
		}
		return $extra_charges;
	}

	private function prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost, $delivery_time, $rate_cost_weight, $dhl_insurance, $dhl_remote_area_surcharge, $shipping_rates_source_currency, $other_charges, $pack) {

	

		// Name adjustment
		if (!empty($this->custom_services[$rate_code]['name'])) {

			$rate_name = $this->custom_services[$rate_code]['name'];
		}

		// Cost adjustment %
		if (!empty($this->custom_services[$rate_code]['adjustment_percent'])) {
			$rate_cost = $rate_cost + ( $rate_cost * ( floatval($this->custom_services[$rate_code]['adjustment_percent']) / 100 ) );
		}
		// Cost adjustment
		if (!empty($this->custom_services[$rate_code]['adjustment'])) {
			$rate_cost = $rate_cost + floatval($this->custom_services[$rate_code]['adjustment']);
		}




		if (isset($this->custom_services[$rate_code]) && empty($this->custom_services[$rate_code]['enabled'])) {
			return;
		}

		// Merging
		if (isset($this->found_rates[$rate_id])) {
			$rate_cost = $rate_cost + $this->found_rates[$rate_id]['cost'];
			$packages  = 1 + $this->found_rates[$rate_id]['packages'];
		} else {
			$packages = 1;
		}

		// Sort
		if (isset($this->custom_services[$rate_code]['order'])) {
			$sort = $this->custom_services[$rate_code]['order'];
		} else {
			$sort = 999;
		}
		
		$extra_charge_basic = $dhl_insurance + $dhl_remote_area_surcharge + $other_charges;

		if ($this->conversion_rate) {
			$extra_charge_basic        *= $this->conversion_rate;
			$rate_cost_weight          *= $this->conversion_rate;
			$dhl_insurance             *= $this->conversion_rate;
			$dhl_remote_area_surcharge *= $this->conversion_rate;
		}

		if (isset($this->found_rates[$rate_id])) {
			$extra_charge_basic        +=  $this->found_rates[$rate_id]['meta_data']['Extra Charge'] + ( isset($this->found_rates[$rate_id]['meta_data']['Insurance Charge']) ? $this->found_rates[$rate_id]['meta_data']['Insurance Charge'] : 0 );
			$rate_cost_weight          += $this->found_rates[$rate_id]['meta_data']['Weight Charge'];
			$dhl_insurance             += isset($this->found_rates[$rate_id]['meta_data']['Insurance Charge']) ? $this->found_rates[$rate_id]['meta_data']['Insurance Charge'] : 0;
			$dhl_remote_area_surcharge += isset($this->found_rates[$rate_id]['meta_data']['Remote Area Surcharge']) ? $this->found_rates[$rate_id]['meta_data']['Remote Area Surcharge'] : 0;
		}

		if ($this->aelia_activated) {
			$rate_cost_weight   = apply_filters('wc_aelia_cs_convert', $rate_cost_weight, $this->shop_currency, get_woocommerce_currency());
			$extra_charge_basic = apply_filters('wc_aelia_cs_convert', $extra_charge_basic, $this->shop_currency, get_woocommerce_currency());
		}

		$shipping_service_meta_data = array('DHL Delivery Time'=>$delivery_time,'Weight Charge'=>floatval($rate_cost_weight),'Extra Charge'=>$extra_charge_basic);

		if ($this->general_settings['show_dhl_extra_charges'] == 'yes' && $this->general_settings['insure_contents'] == 'yes' && $this->general_settings['show_dhl_insurance_charges'] == 'yes') {
			$extra_charge               = $extra_charge_basic - $dhl_insurance;
			$shipping_service_meta_data = array('DHL Delivery Time'=>$delivery_time,'Weight Charge'=>floatval($rate_cost_weight),'Insurance Charge'=>$dhl_insurance,'Extra Charge'=>$extra_charge);
		}

		if ($this->general_settings['show_dhl_extra_charges'] == 'yes' && $this->general_settings['show_dhl_remote_area_surcharge'] == 'yes') {
			if ($this->general_settings['insure_contents'] == 'yes' && $this->general_settings['show_dhl_insurance_charges'] == 'yes') {
				$extra_charge               = $extra_charge_basic - $dhl_insurance - $dhl_remote_area_surcharge;
				$shipping_service_meta_data = array('DHL Delivery Time'=>$delivery_time,'Weight Charge'=>floatval($rate_cost_weight),'Insurance Charge'=>$dhl_insurance,'Remote Area Surcharge'=>$dhl_remote_area_surcharge, 'Extra Charge'=>$extra_charge); 
			} else {
				$extra_charge               = $extra_charge_basic - $dhl_remote_area_surcharge;
				$shipping_service_meta_data = array('DHL Delivery Time'=>$delivery_time,'Weight Charge'=>floatval($rate_cost_weight),'Remote Area Surcharge'=>$dhl_remote_area_surcharge, 'Extra Charge'=>$extra_charge); 
			}
		}

		try {
			$this->found_rates[$rate_id] = apply_filters('wf_dhl_shipping_found_rate' , array(
				'id' => $rate_id,
				'label' => $rate_name,
				'cost' => $rate_cost,
				'sort' => $sort,
				'packages' => $packages,
				'meta_data' => apply_filters('elex_dhl_hide_delivery_time', $shipping_service_meta_data)
			), $shipping_rates_source_currency, $this);
		} catch (Error $e) {
			if (is_plugin_active('woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php')) {
				$debug_message .= ('Provide proper exchange rate');
				WC()->session->set('debug_message', $debug_message);
			} else {
				$debug_message .= print_r($e);
				WC()->session->set('debug_message', $debug_message);
			}
		}
	}

	public function add_found_rates() {
		if ($this->found_rates) {

			if ($this->offer_rates == 'all') {

				uasort($this->found_rates, array($this, 'sort_rates'));

				foreach ($this->found_rates as $key => $rate) {
				   
					$this->add_rate($rate);
				}
			} else {
				$cheapest_rate = '';

				foreach ($this->found_rates as $key => $rate) {
					if (!$cheapest_rate || $cheapest_rate['cost'] > $rate['cost']) {
						$cheapest_rate = $rate;
					}
				}
				$cheapest_rate['meta_data']['Service Label'] = $cheapest_rate['label'];
				$cheapest_rate['label']                      = $this->title;
				$this->add_rate($cheapest_rate);
			}
		}
	}

	public function sort_rates( $a, $b) {
		if ($a['sort'] == $b['sort']) {
			return 0;
		}
		return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
	}
	private function wf_get_pack_type( $selected) {
			$pack_type = 'BOX';
		if ($selected == 'FLY') {
			$pack_type = 'FLY';
		} 
		return $pack_type;    
	}

}

