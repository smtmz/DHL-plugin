<?php

if (!function_exists('wf_get_settings_url')) {
	function wf_get_settings_url() {
		return version_compare(WC()->version, '2.1', '>=') ? 'wc-settings' : 'woocommerce_settings';
	}
}

if (!function_exists('wf_plugin_override')) {
	add_action( 'plugins_loaded', 'wf_plugin_override' );
	function wf_plugin_override() {
		if (!function_exists('WC')) {
			function WC() {
				return $GLOBALS['woocommerce'];
			}
		}
	}
}

if (!function_exists('wf_get_shipping_countries')) {
	function wf_get_shipping_countries() {
		$woocommerce        = WC();
		$shipping_countries = method_exists($woocommerce->countries, 'get_shipping_countries')
		? $woocommerce->countries->get_shipping_countries()
		: $woocommerce->countries->countries;
		return $shipping_countries;
	}
}


/***************ORDER FUNCTION *************/
if (!function_exists('elex_dhl_get_order_id')) {
	function elex_dhl_get_order_id( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->id : $order->get_id();
	}
}
if (!function_exists('elex_dhl_get_order_currency')) {
	function elex_dhl_get_order_currency( $order) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->get_order_currency() : $order->get_currency();
	}
}
if (!function_exists('elex_dhl_get_order_shipping_country')) {
	function elex_dhl_get_order_shipping_country( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_country : $order->get_shipping_country();
	}
}

if (!function_exists('elex_dhl_get_order_shipping_first_name')) {
	function elex_dhl_get_order_shipping_first_name( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_first_name : $order->get_shipping_first_name();
	}
}

if (!function_exists('elex_dhl_get_order_shipping_last_name')) {
	function elex_dhl_get_order_shipping_last_name( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_last_name : $order->get_shipping_last_name();
	}
}
if (!function_exists('elex_dhl_get_order_shipping_company')) {
	function elex_dhl_get_order_shipping_company( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_company : $order->get_shipping_company();
	}
}

if (!function_exists('elex_dhl_get_order_shipping_address_1')) {
	function elex_dhl_get_order_shipping_address_1( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_address_1 : $order->get_shipping_address_1();
	}
}
if (!function_exists('elex_dhl_get_order_shipping_address_2')) {
	function elex_dhl_get_order_shipping_address_2( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_address_2 : $order->get_shipping_address_2();
	}
}
if (!function_exists('elex_dhl_get_order_shipping_city')) {
	function elex_dhl_get_order_shipping_city( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_city : $order->get_shipping_city();
	}
}
if (!function_exists('elex_dhl_get_order_shipping_state')) {
	function elex_dhl_get_order_shipping_state( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_state : $order->get_shipping_state();
	}
}

if (!function_exists('elex_dhl_get_order_shipping_postcode')) {
	function elex_dhl_get_order_shipping_postcode( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->shipping_postcode : $order->get_shipping_postcode();
	}
}

if (!function_exists('elex_dhl_get_order_billing_first_name')) {
	function elex_dhl_get_order_billing_first_name( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_first_name : $order->get_billing_first_name();
	}
}

if (!function_exists('elex_dhl_get_order_billing_last_name')) {
	function elex_dhl_get_order_billing_last_name( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_last_name : $order->get_billing_last_name();
	}
}
if (!function_exists('elex_dhl_get_order_billing_company')) {
	function elex_dhl_get_order_billing_company( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_company : $order->get_billing_company();
	}
}

if (!function_exists('elex_dhl_get_order_billing_address_1')) {
	function elex_dhl_get_order_billing_address_1( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_address_1 : $order->get_billing_address_1();
	}
}
if (!function_exists('elex_dhl_get_order_billing_address_2')) {
	function elex_dhl_get_order_billing_address_2( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_address_2 : $order->get_billing_address_2();
	}
}
if (!function_exists('elex_dhl_get_order_billing_city')) {
	function elex_dhl_get_order_billing_city( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_city : $order->get_billing_city();
	}
}
if (!function_exists('elex_dhl_get_order_billing_state')) {
	function elex_dhl_get_order_billing_state( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_state : $order->get_billing_state();
	}
}

if (!function_exists('elex_dhl_get_order_billing_postcode')) {
	function elex_dhl_get_order_billing_postcode( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_postcode : $order->get_billing_postcode();
	}
}
if (!function_exists('elex_dhl_get_order_billing_country')) {
	function elex_dhl_get_order_billing_country( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_country : $order->get_billing_country();
	}
}

if (!function_exists('elex_dhl_get_order_billing_email')) {
	function elex_dhl_get_order_billing_email( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_email : $order->get_billing_email();
	}
}

if (!function_exists('elex_dhl_get_order_billing_phone')) {
	function elex_dhl_get_order_billing_phone( $order ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order->billing_phone : $order->get_billing_phone();
	}
}

/*To obtain the meta-data of the shipment service selected*/
if (!function_exists('wf_get_order_shipping_method_meta_data')) {
	function wf_get_order_shipping_method_meta_data( $shipping_method_data ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $shipping_method_data : $shipping_method_data->get_meta_data();
	}
}

if (!function_exists('wf_get_total_tax_on_order')) {
	function wf_get_total_tax_on_order( $order ) {
		global $woocommerce;
		$total_tax_on_order = 0;

		if (WC()->version < '2.7.0' ) {
			$total_tax_on_order = $order->get_total_tax();
		} else {
			$order_data         = $order->get_data();
			$total_tax_on_order = $order_data['total_tax'];
		}
		return $total_tax_on_order;
	}
}

if (!function_exists('elex_dhl_get_order_item_meta_data')) {
	function elex_dhl_get_order_item_meta_data( $order_item ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $order_item : $order_item->get_meta_data();
	}
}

if (!function_exists('elex_dhl_get_order_shipping_service')) {
	function elex_dhl_get_order_shipping_service( $shipping_method ) {
		$meta_data = $shipping_method->get_meta_data();
		$service_label = '';
		foreach ($meta_data as $meta) {
			if ($meta->key === 'Service Label') {
				$service_label = $meta->value;
				break;
			}
		}
		if( isset( $service_label ) && !empty( $service_label )){
			return $service_label;
		}
		global $woocommerce;
		$shipping_output = array();
		if (WC()->version < '2.7.0') {
			if (isset($shipping_method['method_id'])) {
				$shipping_output = explode('|', $shipping_method['method_id']);
			}
		} elseif (isset($shipping_method['method_title'])) {
			$shipping_output = explode('|', $shipping_method['method_title']);
		}

		return $shipping_output;
	}
}

/****************PRODUCT FUNCTIONS ***************/
if (!function_exists('elex_dhl_get_product_id')) {
	function elex_dhl_get_product_id( $item ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $item->id : $item->get_id();
	}
}

if (!function_exists('elex_dhl_get_product_name')) {
	function elex_dhl_get_product_name( $item ) {
		global $woocommerce;
		$is_woocommerce_composite_products_installed = ( in_array('woocommerce-composite-products/woocommerce-composite-products.php', get_option('active_plugins')) )? true: false;
		$item_name                                   = '';

		if (WC()->version < '2.7.0') {
			$item_post_data = $item->post;
			$item_name      = isset($item->variation_id)? 'Variation #' . $item->variation_id . ' of ' . $item_post_data->post_title: $item_post_data->post_title;
		} else {
			$item_data            = $item->get_data();
			$product = wc_get_product($item_data['id']);
			$item_composite_title = $product->get_meta('_composite_title_express_dhl_elex');
			if ($is_woocommerce_composite_products_installed && !empty($item_composite_title)) {
				$item_name = $item_composite_title;
			} else {
				$item_name = $item_data['name'];
			}
		}

		return $item_name;
	}
}

if (!function_exists('elex_dhl_get_product_length')) {
	function elex_dhl_get_product_length( $item ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $item->length : $item->get_length();
	}
}

if (!function_exists('elex_dhl_get_product_width')) {
	function elex_dhl_get_product_width( $item ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $item->width : $item->get_width();
	}
}

if (!function_exists('elex_dhl_get_product_height')) {
	function elex_dhl_get_product_height( $item ) {
		global $woocommerce;
		return ( WC()->version < '2.7.0' ) ? $item->height : $item->get_height();
	}
}

if (!function_exists('elex_dhl_get_product_weight')) {
	function elex_dhl_get_product_weight( $item ) {
		global $woocommerce;
	
		if ($item->is_type( 'bundle')) {

			$order_item_metadata                 = $item->get_meta_data();

			$order_item_meta = $order_item_metadata[2]->get_data();

			$id_req          = $item->get_id() ;
			$bundled_product = new WC_Product_Bundle($id_req);
			
			$aggregate_wt =  get_post_meta( $id_req, '_wc_pb_aggregate_weight', true);
			if ( 'no' == $aggregate_wt ) {
				$measured_weight = $bundled_product->get_weight();   
			} else {
				$bundled_items = $bundled_product->get_bundled_items();
				$bundle_weight = 0; 
				foreach ($bundled_items as $item) {
					$meta_val = $order_item_meta['value'][$item->get_id()];


					$sub_product_qty = isset($meta_val['quantity']) ? $meta_val['quantity'] : 0;
					$item_id            = $item->get_product_id();
					$sub_product        =  wc_get_product( $item_id );
					$sub_product_weight = $sub_product->get_weight();
					$bundle_weight     += $sub_product_weight * $sub_product_qty;
				}
				$measured_weight = $bundle_weight;   
			}
		
			return  $measured_weight;

		}
		return ( WC()->version < '2.7.0' ) ? $item->weight : $item->get_weight();
	}
}

if (!function_exists('elex_dhl_get_product_total_price')) {
	function elex_dhl_get_product_total_price( $order, $values = array(), $items_in_the_order = array()) {
		global $woocommerce;
		$item_value       = 0;
		$package_item_id  = 0;
		$order_product_id = 0;

		$current_order_items  = $order->get_items();
		$discount_price_items = $order->get_discount_total();
		$get_subtotal         = $order->get_subtotal();
		$discount_percentage  = $order->get_subtotal() !==0 ? $discount_price_items/$get_subtotal : 0 ;
		
		foreach ($items_in_the_order as $item) {

		

			if (WC()->version < '2.7.0') {
				if (isset($item['line_total']) && !empty($item['line_total'])) {
					if (is_array($values)) {
						if (( $values['data']->get_id() == $item['product_id'] ) || ( $values['data']->get_id() == $item['variation_id'] )) {
							$item_value = $item['line_total'];
						}
					} else {
						if (( $values->get_id() == $item['product_id'] ) || ( $values->get_id() == $item['variation_id'] )) {
							$item_value = $item['line_total'];
						}
					}
				}
			} else {
				$order_product    = $item->get_product();
				$order_product_id = $order_product->get_id();

				if (is_array($values)) {
					$package_item_id = $values['data']->get_id();
				} else {
					$package_item_id = $values->get_id();
				}

				if ($order_product_id == $package_item_id) {
					$item_value = $item->get_total() / $item->get_quantity();
					if (empty($item_value)) {
						$item_value = $order_product->get_sale_price() == ''? $order_product->get_price(): $order_product->get_sale_price();
					}
				
					if ($order_product->is_type('bundle')) {
						if ( 0 == $item_value ) { 
							$dhl_shipping_obj     = new wf_dhl_woocommerce_shipping_method();
							$current_bundled_items = $item->get_product()->get_bundled_items();
							$total_bundle_price    = 0;
							foreach ($current_bundled_items as $bundle_item) { 
								$bundle_item_price               = $bundle_item->get_price() ;
								$bundle_item_price_with_discount = $bundle_item_price - ( $bundle_item_price * $discount_percentage );
								$total_bundle_price             += $bundle_item_price_with_discount  * $bundle_item->get_quantity();                                    
							}
							$item_value =$total_bundle_price; 
						}
						if ($dhl_shipping_obj->aelia_activated) {
							$item_value = apply_filters('wc_aelia_cs_convert', $item_value, $dhl_shipping_obj->shop_currency, $order->get_currency());
						}
					}
				}
			}
		}


		return $item_value;
	}
	
	if (!function_exists('elex_dhl_generate_random_message_reference')) {
		function elex_dhl_generate_random_message_reference() {
			$number = '';
			for ($i=0; $i<20; $i++) {
			  $min     = ( $i == 0 ) ? 1:0;
			  $number .= mt_rand($min, 9);
			}
			$number .= current_time('timestamp');
			return $number;
		}
	}
}
