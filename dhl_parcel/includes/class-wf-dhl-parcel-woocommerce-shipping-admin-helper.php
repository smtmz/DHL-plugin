<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wf_dhl_parcel_woocommerce_shipping_admin_helper {
	private $service_code;
	private $id;
	private $settings;
	private $add_trackingpin_shipmentid;
	private $origin;
	private $origin_country;
	private $account_number;
	private $production;
	private $site_id;
	private $site_password;
	private $refershToken;
	private $service_url;
	private $label_url;
	private $label_creater_url;
	private $debug;
	private $insure_contents;
	private $request_type;
	private $packing_method;
	private $box_max_weight;
	private $weight_packing_process;
	private $custom_services;
	private $boxes;
	private $offer_rates;
	private $freight_shipper_person_name;
	private $freight_shipper_company_name;
	private $freight_shipper_phone_number;
	private $shipper_email;
	private $freight_shipper_street;
	private $freight_shipper_street_2;
	private $freight_shipper_city;
	private $freight_shipper_state;
	private $output_format;
	private $image_type;
	private $dutypayment_type;
	private $export_doc_desc;
	private $dutyaccount_number;
	private $dimension_unit;
	private $weight_unit;
	private $labelapi_dimension_unit;
	private $labelapi_weight_unit;
	private $europarcel_enabled;
	private $product_code;
	private $export_doc_terms_of_trade;
	private $dhl_connect_enabled;
	private $order;
	private $order_id;
	private $shipmentErrorMessage;
	private $master_tracking_id;
	private $total_weight;
	private $destination_country;
	private $Parcelboxes;

	public function __construct() {
		$this->id = WF_DHL_PARCEL_ID;
		$this->init();      
	}

	private function init() {       
		$this->settings                   = get_option( 'woocommerce_' . WF_DHL_PARCEL_ID . '_settings', null );
		$this->add_trackingpin_shipmentid = @$this->settings['add_trackingpin_shipmentid'];
		
		
		$this->origin         = str_replace( ' ', '', strtoupper( $this->settings[ 'origin' ] ) );
		$this->origin_country = 'NL';//WC()->countries->get_base_country();
		$this->account_number = $this->settings[ 'account_number' ];
		
		$this->production    = ( $bool = $this->settings[ 'production' ] ) && $bool == 'yes' ? true : false;
		$this->site_id       = $this->settings['site_id'] ? $this->settings['site_id'] : '40c8ce21-a140-48aa-8043-e774cd8a80a8';   // Site ID and Pass static for live mode 
		$this->site_password = $this->settings['site_password'] ? $this->settings['site_password'] :'437d710a-06c1-4332-a850-404cfc41b449';
		//$this->region_code    = $this->settings[ 'region_code' ];
		
		$this->refershToken = 'https://api-gw.dhlparcel.nl/authenticate/refresh-token';

		$this->service_url            = 'https://api-gw.dhlparcel.nl/authenticate/api-key' ;
		$this->label_url              = 'https://api-gw.dhlparcel.nl/shipments';
		$this->label_creater_url      = 'https://api-gw.dhlparcel.nl/labels';
		$this->debug                  = ( $bool = $this->settings[ 'debug' ] ) && $bool == 'yes' ? true : false;
		$this->insure_contents        = ( $bool = @$this->settings[ 'insure_contents' ] ) && $bool == 'yes' ? true : false;
		$this->request_type           = @$this->settings[ 'request_type'];
		$this->packing_method         = $this->settings[ 'packing_method'];
		$this->box_max_weight         =   $this->settings['box_max_weight'];
		$this->weight_packing_process =   $this->settings['weight_packing_process'];
		$this->boxes                  = $this->settings[ 'boxes'];
		$this->custom_services        = isset($this->settings[ 'services']) ? $this->settings[ 'services']: array();
		$this->offer_rates            = @$this->settings[ 'offer_rates'];
				
		$this->freight_shipper_person_name  = htmlspecialchars_decode( $this->settings[ 'shipper_person_name' ] );
		$this->freight_shipper_company_name = htmlspecialchars_decode( $this->settings[ 'shipper_company_name' ] );
		$this->freight_shipper_phone_number = $this->settings[ 'shipper_phone_number' ];
		$this->shipper_email                =  $this->settings[ 'shipper_email' ];
		
		$this->freight_shipper_street   = htmlspecialchars_decode( $this->settings[ 'freight_shipper_street' ] );
		$this->freight_shipper_street_2 = htmlspecialchars_decode( $this->settings[ 'shipper_street_2'] );
		$this->freight_shipper_city     = $this->settings[ 'freight_shipper_city' ];
		$this->freight_shipper_state    = $this->settings[ 'freight_shipper_state' ];
		
		$this->output_format = @$this->settings['output_format'];
		$this->image_type    = @$this->settings['image_type'];     
		
		$this->dutypayment_type   = isset($this->settings['dutypayment_type']) ? $this->settings['dutypayment_type'] : '';
		$this->dutyaccount_number = isset($this->settings['dutyaccount_number']) ? $this->settings['dutyaccount_number'] : '';
	
		$this->dimension_unit = isset($this->settings['dimension_weight_unit']) && $this->settings['dimension_weight_unit'] == 'LBS_IN' ? 'IN' : 'CM';
		$this->weight_unit    = isset($this->settings['dimension_weight_unit']) && $this->settings['dimension_weight_unit'] == 'LBS_IN' ? 'lbs' : 'kg';
		
		$this->labelapi_dimension_unit = $this->dimension_unit == 'IN' ? 'I' : 'C';
		$this->labelapi_weight_unit    = $this->weight_unit == 'lbs' ? 'L' : 'K';
		
		$this->product_code              =   'V01PAK';
		$this->europarcel_enabled        =   ( isset( $this->settings['europarcel_enabled'] ) && $this->settings['europarcel_enabled'] == 'yes' ) ? true : false;
		$this->dhl_connect_enabled       =   ( isset( $this->settings['dhl_connect'] ) && $this->settings['dhl_connect'] == 'yes' ) ? true : false;
		$this->export_doc_terms_of_trade =    $this->settings[ 'export_doc_terms_of_trade' ];
		$this->export_doc_desc           =    $this->settings[ 'export_doc_desc' ];
		
	}

	public function debug( $message, $type = 'notice' ) {
		if ( $this->debug ) {
			echo( $message );
		}
	}

	public function get_dhl_packages( $package ) {
		switch ( $this->packing_method ) {
			case 'box_packing':
				return $this->box_shipping( $package );
			break;
			case 'weight_based':
				return $this->weight_based_shipping($package);
			break;
			case 'per_item':
			default:
				return $this->per_item_shipping( $package );
			break;
		}
	}

	

	private function per_item_shipping( $package ) {
		$to_ship  = array();
		$group_id = 1;

		// Get weight of order
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				$this->debug( sprintf( __( 'Product # is virtual. Skipping.', 'wf-shipping-dhl' ), $item_id ), 'error' );
				continue;
			}

			$skip_product = apply_filters('wf_shipping_skip_product_from_dhl_label', false, $values, $package['contents']);
			if ($skip_product) {
				continue;
			}
			if (isset($values['mesured_weight']) && $values['mesured_weight'] !=0 ) {
				$weight = $values['mesured_weight'];
			} else {
				$weight = wc_get_weight( ( !$values['data']->get_weight() ? 0 :$values['data']->get_weight() ), $this->weight_unit );
			}
			if ( ! $weight ) {
				$this->debug( sprintf( __( 'Product # is missing weight.', 'wf-shipping-dhl' ), $item_id ), 'error' );
				wf_admin_notice::add_notice(sprintf( __( 'Product is missing weight.', 'wf-shipping-dhl' )), 'error');
				return;
			}

			$group = array();

			if ( elex_dhl_get_product_length( $values['data'] ) && elex_dhl_get_product_height($values['data']) && elex_dhl_get_product_width( $values['data'] ) ) {

				$group = array(
					'package_title'     => $values['data']->get_title(),
					'GroupNumber'       => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => max( '0.5', round( $weight, 2 ) ),
						'Units' => $this->weight_unit
					),
					'packed_products' => array( $values['data'] )
				);

				$dimensions = array( elex_dhl_get_product_length( $values['data'] ), elex_dhl_get_product_width( $values['data'] ), elex_dhl_get_product_height( $values['data'] ) );

				sort( $dimensions );

				$group['Dimensions'] = array(
					'Length' => max( 1, round( wc_get_dimension( $dimensions[2], $this->dimension_unit ), 0 ) ),
					'Width'  => max( 1, round( wc_get_dimension( $dimensions[1], $this->dimension_unit ), 0 ) ),
					'Height' => max( 1, round( wc_get_dimension( $dimensions[0], $this->dimension_unit ), 0 ) ),
					'Units'  => $this->dimension_unit
				);

				$group['InsuredValue'] = array(
					'Amount'   => round( $values['data']->get_price() ),
					'Currency' => get_woocommerce_currency()
				);
			}

			for ($loop = 0; $loop < $values['quantity'];$loop++) {
				if (!empty($group)) {
				$to_ship[] = $group;
				}
			}
			$group_id++;
		}
		return $to_ship;
	}

	private function box_shipping( $main_packages ) {
		if ( ! class_exists( 'WF_Boxpack' ) ) {
			include_once 'class-wf-packing.php';
		}

		$boxpack = new WF_Boxpack();

		
		// Define boxes
		foreach ( $this->boxes as $key => $box ) {
			
			if ( ! $box['enabled'] ) {
				continue;
			}

			$newbox = $boxpack->add_box( $box['length'], $box['width'], $box['height'], $box['box_weight'] );

			if ( !empty( $box['id'] ) ) {
				$newbox->set_id( current( explode( ':', $box['id'] ) ) );
			}

			if ( $box['max_weight'] ) {
				$newbox->set_max_weight( $box['max_weight'] );
			}
		}

		// Add items
		foreach ( $main_packages['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				$this->debug( sprintf( __( 'Product # is virtual. Skipping.', 'wf-shipping-dhl' ), $item_id ), 'error' );
				continue;
			}
				
			$skip_product = apply_filters('wf_shipping_skip_product_from_dhl_label', false, $values, $main_packages['contents']);
			if ($skip_product) {
				continue;
			}
			if ( $values['data']->get_length() && $values['data']->get_height() && $values['data']->get_width() && $values['data']->get_weight() ) {

				$dimensions = array( $values['data']->get_length(), $values['data']->get_height(), $values['data']->get_width() );

				for ( $i = 0; $i < $values['quantity']; $i ++ ) {
					
					if (isset($values['mesured_weight']) && $values['mesured_weight'] !=0 ) {
						$weight = $values['mesured_weight'];
					} else {
						$weight = wc_get_weight( ( !$values['data']->get_weight() ? 0 :$values['data']->get_weight() ), $this->weight_unit );
					}       
					$boxpack->add_item(
						wc_get_dimension( $dimensions[2], $this->dimension_unit ),
						wc_get_dimension( $dimensions[1], $this->dimension_unit ),
						wc_get_dimension( $dimensions[0], $this->dimension_unit ),
						$weight,
						$values['data']->get_price(),
						array(
							'data' => $values['data']
						)
					);
				}

			} else {
				$this->debug( sprintf( __( 'Product #%s is missing dimensions. Aborting.', 'wf-shipping-dhl' ), $item_id ), 'error' );
				wf_admin_notice::add_notice(sprintf( __( 'Product is missing dimensions. Aborting.', 'wf-shipping-dhl' ) ), 'error');
				return;
			}
		}

		// Pack it
		$boxpack->pack();
		$packages = $boxpack->get_packages();
		$to_ship  = array();
		$group_id = 1;

		foreach ( $packages as $package ) {
			if ( $package->unpacked === true ) {
				$this->debug( '<b><font color="red">Unpacked Item</font></b>' );
				return $this->per_item_shipping($main_packages);
			} else {
				$this->debug( '<b><font color="green">Packed </font></b>' . $package->id . '<br>');
			}

			$dimensions = array( $package->length, $package->width, $package->height );

			sort( $dimensions );

			$group = array(
				'package_title'     => 'BOX ' . $group_id,
				'GroupNumber'       => $group_id,
				'GroupPackageCount' => 1,
				'Weight' => array(
					'Value' => max( '0.5', round( $package->weight, 2 ) ),
					'Units' => $this->weight_unit
				),
				'Dimensions'        => array(
					'Length' => max( 1, round( $dimensions[2], 0 ) ),
					'Width'  => max( 1, round( $dimensions[1], 0 ) ),
					'Height' => max( 1, round( $dimensions[0], 0 ) ),
					'Units'  => $this->dimension_unit
				),
				'InsuredValue'      => array(
					'Amount'   => round( $package->value ),
					'Currency' => get_woocommerce_currency()
				),
				'packed_products' => array(),
				'package_id'      => $package->id
			);

			if ( ! empty( $package->packed ) && is_array( $package->packed ) ) {
				foreach ( $package->packed as $packed ) {
					$group['packed_products'][] = $packed->get_meta( 'data' );
				}
			}

			

			$to_ship[] = $group;

			$group_id++;
		}

		return $to_ship;
	}
	
	/**
	 * weight_based_shipping function.
	 *
	 * @access private
	 * @param mixed $package
	 * @return void
	 */
	private function weight_based_shipping( $package) {
		global $woocommerce;
		if ( ! class_exists( 'WeightPack' ) ) {
			include_once 'weight_pack/class-wf-weight-packing.php';
		}
		$weight_pack =new WeightPack($this->weight_packing_process);
		$weight_pack->set_max_weight($this->box_max_weight);
		
		$package_total_weight = 0;
		$insured_value        = 0;
		
		
		$ctr = 0;
		foreach ($package['contents'] as $item_id => $values) {
			$ctr++;
			
			$skip_product = apply_filters('wf_shipping_skip_product_from_dhl_label', false, $values, $package['contents']);
			if ($skip_product) {
				continue;
			}

			//for backward compatibility
			$skip_product = apply_filters('wf_shipping_skip_product', false, $values, $package['contents']);
			if ($skip_product) {
				continue;
			}
			
			if (!( $values['quantity'] > 0 && $values['data']->needs_shipping() )) {
				$this->debug(sprintf(__('Product #%d is virtual. Skipping.', 'wf-shipping-dhl'), $ctr));
				continue;
			}

			if (isset($values['mesured_weight']) && $values['mesured_weight'] !=0 ) {
				$weight = $values['mesured_weight'];
			} else {
				$weight = wc_get_weight( ( !$values['data']->get_weight() ? 0 :$values['data']->get_weight() ), $this->weight_unit );
			}
			
			if (!$weight) {
				$this->debug(sprintf(__('Product #%d is missing weight.', 'wf-shipping-dhl'), $ctr), 'error');
				wf_admin_notice::add_notice(sprintf( __( 'Product is missing weight.', 'wf-shipping-dhl' )), 'error');
				return;
			}
			$weight_pack->add_item( $weight, $values['data'], $values['quantity'] );
		}
		
		$pack   =   $weight_pack->pack_items();     
		$errors =   $pack->get_errors();
		if ( !empty($errors) ) {
			//do nothing
			return;
		} else {
			$boxes          =   $pack->get_packed_boxes();
			$unpacked_items =   $pack->get_unpacked_items();
			
			$insured_value =   0;
			
			$packages      =   array_merge( $boxes, $unpacked_items ); // merge items if unpacked are allowed
			$package_count =   sizeof($packages);
			
			// get all items to pass if item info in box is not distinguished
			$packable_items =   $weight_pack->get_packable_items();
			$all_items      =   array();
			if (is_array($packable_items)) {
				foreach ($packable_items as $packable_item) {
					$all_items[] =   $packable_item['data'];
				}
			}
			//pre($packable_items);
			
			if (isset($this->order)) {
				$order_total =   $this->order->get_total();
			}
			
			$to_ship  = array();
			$group_id = 1;
			foreach ($packages as $package) {//pre($package);
			
				$packed_products = array();
				if (( $package_count  ==  1 ) && isset($order_total)) {
					$insured_value =   $order_total;
				} else {
					$insured_value =   0;
					if (!empty($package['items'])) {
						foreach ($package['items'] as $item) {                        
							$insured_value =   $insured_value+$item->get_price();
							
						}
					} else {
						if ( isset($order_total) && $package_count) {
							$insured_value =   $order_total/$package_count;
						}
					}
				}
				$packed_products =   isset($package['items']) ? $package['items'] : $all_items;
				// Creating package request
				$package_total_weight =   $package['weight'];
				
				$group = array(
					'package_title'     => 'WEIGHT BOX ' . $group_id,
					'GroupNumber'       => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => max( '0.5', round( $package['weight'], 2 ) ),
						'Units' => $this->weight_unit
					),
					'InsuredValue'      => array(
						'Amount'   => round( $insured_value ),
						'Currency' => get_woocommerce_currency()
					),
					'packed_products' => $packed_products,
				);
				
				$to_ship[] = $group;
				$group_id++;
			}
		}
		return $to_ship;
	}
	
	private function wf_get_receiver_details( $package) {
		$destination_city         = strtoupper( $package['destination']['city'] );
		$destination_postcode     = strtoupper( $package['destination']['postcode'] );
		$destination_country_name = isset( WC()->countries->countries[ $package['destination']['country'] ] ) ? WC()->countries->countries[ $package['destination']['country'] ] : $package['destination']['country']; 
		
		$receiver = array();
		
		$receiver['name']['firstName'] = elex_dhl_get_order_shipping_first_name($this->order);
		$receiver['name']['lastName']  = elex_dhl_get_order_shipping_last_name($this->order); 

		 $shipping_company = elex_dhl_get_order_shipping_company( $this->order );
		if ( isset($shipping_company) && !empty($shipping_company) ) {
			$receiver['name']['companyName'] = $shipping_company;
		}


		//$packstation_data  =   wf_packstation::get_order_packstation($this->order);
		
		// if($packstation_data){
		//         $receiver['Packstation']['postNumber']          =   wf_packstation::get_order_postnumber($this->order);
		//         $receiver['Packstation']['packstationNumber']   =   $packstation_data['packstationId'];
		//         $receiver['Packstation']['zip']                 =   $packstation_data['address']->zip;
		//         $receiver['Packstation']['city']                =   $packstation_data['address']->city;
		// }else{
			
			 $receiver['address']['countryCode'] = $package['destination']['country'];
			 $receiver['address']['postalCode']  = $destination_postcode;
			$address_line1                       = elex_dhl_get_order_shipping_address_1($this->order);
			$address_line2                       = elex_dhl_get_order_shipping_address_2($this->order);
		if (!empty($address_line2)) {
			$receiver['address']['street'] = $address_line1 . ' ' . $address_line2;
		} else {
			$receiver['address']['street'] = $address_line1;
		}

			$receiver['address']['streetNumber'] = '20';// Used invisible character Alt + 255 to provide space, if address line 2 is empty because, normal whitespace is accepted by DHL Parcel API
			
		   
			$receiver['address']['isBusiness'] = true;
			$receiver['address']['city']       = $destination_city;
		

		if ($this->settings['dhl_email_service'] == 'yes') {
			$receiver['email'] = elex_dhl_get_order_billing_email($this->order);
		}
		$receiver['email']       = elex_dhl_get_order_billing_email($this->order);
		$receiver['phoneNumber'] = elex_dhl_get_order_billing_phone($this->order);        
		return $receiver;
	}
	
	private function wf_get_shipper_details( $package) {
		$destination_city         = strtoupper( $package['destination']['city'] );
		$destination_postcode     = str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ));
		$destination_country_name = isset( WC()->countries->countries[ $package['destination']['country'] ] ) ? WC()->countries->countries[ $package['destination']['country'] ] : $package['destination']['country']; 
		$consignee_name           = elex_dhl_get_order_shipping_first_name($this->order) . ' ' . elex_dhl_get_order_shipping_last_name($this->order);
		
		$origin_country_name = isset( WC()->countries->countries[ $this->origin_country ] ) ? WC()->countries->countries[ $this->origin_country ] : $this->origin_country;
		
		$shipper =array();
		
		//if person name is empty, take company name as name1
		if ( empty($this->freight_shipper_person_name) ) {
			$name1 = $this->freight_shipper_company_name;
			$name2 = '';
		} else {
			$name1 = $this->freight_shipper_person_name;
			$name2 = $this->freight_shipper_company_name;
		}

		$shipper['name']['firstName'] = $name1;
		
		if ( !empty($name2) ) {
			$shipper['name']['lastName'] = $name2;
		}
		
		$shipper['address']['street']      = $this->freight_shipper_street;
		$shipper['address']['number']      = $this->freight_shipper_street_2;
		$shipper['address']['isBusiness']  = true;
		$shipper['address']['postalCode']  =   $this->origin;      
		$shipper['address']['city']        =$this->freight_shipper_city;
		$shipper['address']['countryCode'] =$this->origin_country; 
		// if($this->freight_shipper_state){
		//     $shipper['address']['Origin']['state']=$this->freight_shipper_state; 
		// }
		
		$shipper['email'] =$this->shipper_email;
		//$shipper['contactPerson']= $this->freight_shipper_person_name;
		$shipper['phoneNumber'] =$this->freight_shipper_phone_number;
		
		return $shipper;
	}
	
	private function wf_get_return_receiver_details( $package) {
		//if person name is emplty, take company name as name1
		if ( empty($this->freight_shipper_person_name) ) {
			$name1 = $this->freight_shipper_company_name;
			$name2 = '';
		} else {
			$name1 = $this->freight_shipper_person_name;
			$name2 = $this->freight_shipper_company_name;
		}
		
		$return['Name'] =   array(
			'name1' =>  $name1,
		);
		if ( !empty($name2) ) {
			$return['Name']['name2'] =   $name2;
		}

		$return['Address']       =   array(
			'streetName'    =>  $this->freight_shipper_street,
			'streetNumber'  =>  $this->freight_shipper_street_2,
			'zip'           =>  $this->origin,
			'city'          =>  $this->freight_shipper_city,
			'Origin'        =>  array(
				'countryISOCode'    =>  $this->origin_country,
				'state'             =>  $this->freight_shipper_state,
			),
		);
		$return['Communication'] =   array(
			'phone'         =>  $this->freight_shipper_phone_number,
			'email'         =>  $this->shipper_email,
			'contactPerson' =>  $this->freight_shipper_person_name,
		);
		return $return;
	}
	
	private function wf_get_export_doc( $order_id, $dhl_packages) {
		$order = wc_get_order( $order_id );
		if (!$order) {
			return;
		}

		if ($order->get_shipping_country() == 'DE') {
			return;   
		}

		$order_total                         = $order->get_total();
		$oder_currency                       =   elex_dhl_get_order_currency($order);
		$order_weight                        = $this->total_weight;
		$export_doc                          = array();
		$export_doc['exportType']            = 'OTHER';
		$export_doc['exportTypeDescription'] = 'goods';
		$export_doc['termsOfTrade']          = $this->export_doc_terms_of_trade?$this->export_doc_terms_of_trade:'DDU';
		$export_doc['placeOfCommital']       = $this->origin_country;       
		$export_doc['additionalFee']         = 0;      
		
		//Take HST of the first product in the package.
		$packed_products = !empty($dhl_packages['packed_products']) ? $dhl_packages['packed_products'] : '';

		foreach ($packed_products as $packed_product) {
			$product_data   = $packed_product->get_data();
			$product_weight = 0;
			if (isset($product_data['parent_id']) && !empty($product_data['parent_id'])) {
				$product_parent_data = $packed_product->get_parent_data();
				$product_weight      = !empty($product_data['weight'])? $product_data['weight']: $product_parent_data['weight']; 
			} else {
				$product_weight = $product_data['weight'];
			}
			$par_id  = wp_get_post_parent_id( elex_dhl_get_product_id($packed_product) );
			$post_id = $par_id ? $par_id : elex_dhl_get_product_id( $packed_product );
			
			$product = wc_get_product( $post_id );
			$wf_hs_code = $product->get_meta( '_wf_hs_code', 1);
			
			$country_of_origin = $product->get_meta( '_wf_manufacture_country', 1);

			$export_doc['ExportDocPosition'][] = array(
				'description'=>$this->export_doc_desc,
				'countryCodeOrigin'=>$country_of_origin,
				'amount' => 1,
				'netWeightInKG'=>round(wc_get_weight($product_weight, 'kg', $this->weight_unit), 2),
				'customsValue'=>!empty($product_data['sale_price'])? round($product_data['sale_price'], 2) : round($product_data['regular_price'], 2),
				'customsTariffNumber'=> !empty($wf_hs_code) ? $wf_hs_code : $this->order_id
			);
		}

		return $export_doc;
	}
	
	private function get_dhl_requests( $dhl_packages, $package) {
		$product = !empty($dhl_packages['packed_products']) ? current($dhl_packages['packed_products']) : '';
		if (empty($product) && !isset($dhl_packages['Weight'])) {
			return;
		}


		$return_label_required =   false;
		if (isset($_GET['return_label']) &&  $_GET['return_label']=='true') {
			$return_label_required =   true;
		}
		
		$cod_required =   false;
		$cod_amount   =   0;
		if (isset($_GET['cod'])  &&  $_GET['cod'] == 'true') {
			$cod_required =   true;
			$cod_amount   =   $this->order->get_total();
		}
		
		
		$shipment_details_params =   array(
			'return_label_required' =>  $return_label_required,
			'cod_required'          =>  $cod_required,
			'cod_amount'            =>  $cod_amount,
			'visual_check_of_age'   =>  $this->get_package_visual_check_of_age($package),
		);
		$shipment_details        = $this->wf_get_shipment_details($dhl_packages, $shipment_details_params);
		$shipper_details         = $this->wf_get_shipper_details($package);
		$receiver_details        = $this->wf_get_receiver_details($package);
		
		$export_doc   = $this->wf_get_export_doc($this->order_id, $dhl_packages);
		$option_setup = array(
			'key' => 'DOOR',
		); 
		// if( $cod_required ){
		//     $option_setup = array(
		//         'key' => 'COD',
		//         "inputType"=> $cod_amount
		//     );
			
		// }     
		$request['labelId']        = $this->elexGeneratedUUID();
		$request['labelFormat']    = 'pdf';
		$request['orderReference'] = "$this->order_id";
		$request['parcelTypeKey']  = $shipment_details['ShipmentItem']['packageType'];
		$request['receiver']       = $receiver_details;
		$request['shipper']        = $shipper_details;
		$request['accountId']      = $shipment_details['accountNumber']; 
		$request['options']        = [$option_setup];
		$request['returnLabel']    = $return_label_required;
		
		return json_encode($request);  
	}
	private function elexGeneratedUUID() {
		$data = random_bytes(16);
		assert(strlen($data) == 16);

		// Set version to 0100
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		// Set bits 6-7 to 10
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		// Output the 36 character UUID.
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}
	private function wf_get_shipment_details( $dhl_packages, $params = array()) {
		$pieces       = '';
		$total_weight = 0;
		$total_value  = 0;
		$currency     = get_woocommerce_currency();

		if ($this->origin_country == 'DE' && $this->destination_country !='DE') {
			$this->product_code = 'V53WPAK';//default
			if ($this->is_european_country($this->destination_country)) {
				if ($this->europarcel_enabled) { // Europarcel
					$this->product_code = 'V54EPAK';
				} elseif ($this->dhl_connect_enabled ) {//Parcel Connect
					$this->product_code = 'V55PAK';
				}
			}      
		}
		
		// Replace product code of account number
		$this->account_number =   $this->account_number_with_product_code($this->account_number, $this->product_code);
		
		$shipping_date = date('Y-m-d', current_time('timestamp'));
				
		$items = array();
		if ( $dhl_packages ) {
			
			$items['weightInKG'] =   $dhl_packages['Weight']['Value'];
			
			if (isset($dhl_packages['Dimensions'])) {
				$items['lengthInCM']   = $dhl_packages['Dimensions']['Length'];
				$items['widthInCM']    = $dhl_packages['Dimensions']['Width'];
				$items['heightInCM']   = $dhl_packages['Dimensions']['Height'];
				 $items['packageType'] = $this->elexParcelGetPackageType( $dhl_packages );
			}
		}
	   
		$this->total_weight = $total_weight;
		
		$shipment_details                  = array();      
		$shipment_details['product']       = $this->product_code;
		$shipment_details['accountNumber'] = str_replace(' ', '', $this->account_number);
		
		$shipment_details['shipmentDate'] =   date('Y-m-d', current_time('timestamp'));
		$shipment_details['ShipmentItem'] = $items;
		
		if ($params['return_label_required']) {
			$shipment_details['returnShipmentAccountNumber'] =str_replace(' ', '', $this->return_account_number);
			$shipment_details['Service']['ReturnReceipt']    =array(
				'_'=>'',
				'active'=>1,
			);
		}
		
		if ($params['cod_required']) {
			$shipment_details['Service']['CashOnDelivery'] =   array(
				'_'         =>  '',
				'active'    =>  1,
				'codAmount' =>  $params['cod_amount']
			);
		}       
		
		if ($params['visual_check_of_age']) {
			$shipment_details['Service']['VisualCheckOfAge'] =array(
				'_'=>'',
				'active'=>1,
				'type'=>'A18',
			);
		}
				
		
		if ($this->product_code=='V53WPAK') {
			$shipment_details['Service']['Premium'] =true;
		}
		
		$shipment_details = apply_filters('woocommerce_dhl_parcel_request', $shipment_details, $this->order_id);
		return  $shipment_details;
	}
	private function elexParcelGetPackageType( $dhl_packages ) {
		$this->Parcelboxes = require 'data-parcel-boxes.php';

		if ( empty( $dhl_packages['Weight']['Value'] ) ) {
			return 'SMALL';
		}

		foreach ($this->Parcelboxes as $Boxkey => $value) {

			//Check Weight comparision 
			if ( $value['min_weight'] <= $dhl_packages['Weight']['Value'] && $value['max_weight'] >= $dhl_packages['Weight']['Value'] ) {
				
				return $Boxkey;
			}           
		}

	}
	private function wf_get_package_from_order( $order) {
		$orderItems = $order->get_items();
		
		foreach ($orderItems as $orderItem) {
			$product_data = wc_get_product( $orderItem['variation_id'] ? $orderItem['variation_id'] : $orderItem['product_id'] );
			if (WC()->version >'2.7') {
				$data = $orderItem->get_meta_data();
			} else {
				$data = $orderItem;
			}
			$mesured_weight = 0;
			if (isset($data[1]->value['weight']['value'])) {
				$mesured_weight = ( wc_get_weight($data[1]->value['weight']['value'], $this->weight_unit, $data[1]->value['weight']['unit']) );
			}
			
			$items[] = array( 'data' => $product_data , 'quantity' => $orderItem['qty'],'mesured_weight' => $mesured_weight );
			
		}
		$package['contents']                  = $items;
		$package['destination']['country']    =    elex_dhl_get_order_shipping_country($order);
		$package['destination']['first_name'] = elex_dhl_get_order_shipping_first_name($order);
		$package['destination']['last_name']  =  elex_dhl_get_order_shipping_last_name($order);
		$package['destination']['company']    =    elex_dhl_get_order_shipping_company($order);
		$package['destination']['address_1']  =  elex_dhl_get_order_shipping_address_1($order);
		$package['destination']['address_2']  =  elex_dhl_get_order_shipping_address_2($order);
		$package['destination']['city']       =       elex_dhl_get_order_shipping_city($order);
		$package['destination']['state']      =      elex_dhl_get_order_shipping_state($order);
		$package['destination']['postcode']   =   elex_dhl_get_order_shipping_postcode($order);
		return $package;
	}
	
	public function print_label( $order, $service_code, $order_id, $auto_label = '') {
		$this->order        = $order; 
		$this->order_id     = $order_id;
		$this->service_code = $service_code;

		$pack = $this->wf_get_package_from_order( $order );

		if ( is_array( $pack ) ) {
		   return $this->print_label_processor( $pack, $auto_label );
		} else {
			if ($auto_label != '') {
			 wf_admin_notice::add_notice(__( 'Unexpected error while get package.', 'wf-shipping-dhl' ));
			}
			return false;
		}
	}
	
	public function print_label_processor( $package, $auto_label = '' ) {
		if (!isset($_GET['weight']) && $auto_label == '') {
			return $package;
		}
		

		$this->shipmentErrorMessage = '';
		$this->master_tracking_id   = ''; 
		if ($auto_label == '') {
			$length_arr = json_decode(stripslashes(html_entity_decode($_GET['length'])));
			$width_arr  = json_decode(stripslashes(html_entity_decode($_GET['width'])));
			$height_arr = json_decode(stripslashes(html_entity_decode($_GET['height'])));
			$weight_arr = json_decode(stripslashes(html_entity_decode($_GET['weight'])));    
		}

		// Debugging
		$this->debug( __( 'dhl debug mode is on - to hide these messages, turn debug mode off in the settings.<br>', 'wf-shipping-dhl' ) );

		$this->destination_country = $package['destination']['country'];
		
		$ctr =0;
		if ($auto_label == 'true') {
			$length_arr    = array();
			$width_arr     = array();
			$height_arr    = array();
			$weight_arr    = array();
			$package_title = array();
		}
		if ($auto_label == '') {
			foreach ( $package['contents'] as $item_id => $values ) {
				$ctr++;
			
				if ( !( $values['quantity'] > 0 && $values['data']->needs_shipping() ) ) {
					$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-dhl' ), $ctr ) );
					continue;
				}

				if ( ! $values['data']->get_weight() ) {
					$this->debug( sprintf( __( 'Product #%d is missing weight.', 'wf-shipping-dhl' ), $ctr ), 'error' );
					// return;
				}
			
			}
		} else {
			foreach ($package['contents'] as $key => $data) {
				$length_arr[]    = $data['data']->get_length();
				$width_arr[]     = $data['data']->get_width();
				$height_arr[]    = $data['data']->get_height();
				$weight_arr[]    = $data['data']->get_weight(); 
				$package_title[] = $data['data']->get_name();
			}
		}
		// Get requests
		$dhl_packages = $this->get_dhl_packages( $package );
		if ($auto_label == '') {          
		$dhl_packages = $this->manual_packages($dhl_packages);
		}
		if ($auto_label == '') {
		$package_title_array = json_decode(stripslashes(html_entity_decode($_GET['package_title'])));
		} else {
			$package_title_array = $package_title;
		}

		$extra_packages = array();
		for ($i = 0; $i < count($package_title_array); $i++) {
			if ($package_title_array[$i] == 'Additional Package') {
				$extra_packages[] = array(
					'Weight' => array(
						'Value' => $weight_arr[$i],
						'Units' => $this->weight_unit
					),
					'Dimensions' => array(
						'Length' => $length_arr[$i],
						'Width'  => $width_arr[$i],
						'Height' => $height_arr[$i],
						'Units'  => $this->dimension_unit
					),
				);
			}
		}
		if (!empty($extra_packages)) {
			foreach ($extra_packages as $extra_package) {
				$items['weightInKG'] = $extra_package['Weight']['Value'];
				$items['lengthInCM'] = $extra_package['Dimensions']['Length'];
				$items['widthInCM']  = $extra_package['Dimensions']['Width'];   
				$items['heightInCM'] = $extra_package['Dimensions']['Height'];
			}
		}

		if (is_array($dhl_packages)) {    
			foreach ($dhl_packages as $key => $dhl_pack) {
				$dhl_requests = $this->get_dhl_requests( $dhl_pack, $package);
				if ( $dhl_requests ) {
					$dhl_requests =   apply_filters('wf_dhl_parcel_create_shipment_request', $dhl_requests, $this->order);
					$this->run_package_request( $dhl_requests, $dhl_packages, $key, $auto_label );
				}
			}
		}

		if ($this->shipmentErrorMessage) {
			if ($auto_label == '') {
			wf_admin_notice::add_notice(sprintf(__('Order #%1$d: %2$s', 'wf-shipping-dhl'), elex_dhl_get_order_id($this->order), $this->shipmentErrorMessage));
			}
			return false;
		} else {
			return true;
		}
	}
	
	public function generate_packages( $order, $service_code, $auto_label = '') {
		$package = $this->wf_get_package_from_order( $order );
		if ( is_array( $package ) ) {
			$dhl_packages = $this->get_dhl_packages( $package );
			if ( empty($dhl_packages) ) {
				$dhl_packages = array();
			}
			if ($auto_label == 'true') {
				$orderid = elex_dhl_get_order_id($order);
				$order->update_meta_data('_wf_dhl_parcel_stored_packages', $dhl_packages );
				$order->save();
				//wp_redirect( admin_url( '/post.php?post='.$orderid.'&action=edit') );
			} else {
			$orderid = elex_dhl_get_order_id($order);
			$order->update_meta_data('_wf_dhl_parcel_stored_packages', $dhl_packages );
			$order->save();
			wp_redirect( admin_url( '/post.php?post=' . $orderid . '&action=edit') );
			exit;
			}
		} else {
			$error_msg = __( 'Unexpected error while get package.', 'wf-shipping-dhl' );
			$return    = array(
				'ErrorMessage' => $error_msg
			);
			return $return;
		}
	}

	public function run_package_request( $request, $dhl_packages, $package_number, $auto_label ) {
		try {          
			   $result  = $this->get_result( $request, $package_number );
			   $results = !empty($result) ? $result : '';
			$this->process_result( $results , $request, $dhl_packages, $auto_label);
			
		} catch ( Exception $e ) {
			$this->debug( print_r( $e, true ), 'error' );
			return false;
		}  
	}

	private function  get_result( $request, $package_number ) {
		$this->debug( 'DHL REQUEST for package ' . ++$package_number . ': <pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r($request, true ) . '</pre>' );
		$client = $this->loginSoapClient();
		if ( $client ) {
			//$result = $client->createShipmentOrder($request);
		}
		$token          = get_option('dhlparcel_token');
		$requestHeaders = array(
					'Authorization'   => 'Bearer ' . $token,
					'Content-Type' => 'application/json',
					'Accept'=>'application/json'
				);
		// Create body
		  $result = wp_safe_remote_post($this->label_creater_url, array(
					'method'=>'POST',
					'headers'=>$requestHeaders,
					'timeout'=>70,
					'body'=>$request
					)
				);

		$this->debug( 'DHL RESPONSE for package ' . $package_number . ': <pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . htmlspecialchars(print_r( $result, true ), ENT_SUBSTITUTE) . '</pre>' );
		
		// if($result->Status->statusCode > 0 || !isset($result->CreationState->LabelData->shipmentNumber)){
		//     //handle the error
		//     if(isset($result->CreationState->LabelData->Status->statusMessage) && is_array($result->CreationState->LabelData->Status->statusMessage)){
		//         $error_msg = '<ul>';
		//             foreach($result->CreationState->LabelData->Status->statusMessage as $msg){
		//                 $error_msg.='<li>'.$msg.'</li>';
		//             }
		//         $error_msg .= '</ul>';
		//     }
		//     else{
		//         $error_msg = $result->Status->statusMessage;
		//     }

		//     $return = array(
		//         'ErrorMessage' => $error_msg
		//     );
		// }
		// else{
		//     // response is ok
		//     $return = array('ShipmentID' => $result->CreationState->LabelData->shipmentNumber,
		//         'LabelImage' => $result->CreationState->LabelData->labelUrl,
		//         'PieceInformation' => '',//json_encode($result->CreationState->PieceInformation) // Deprecated in 2.0
		//     );

		//     if( in_array($this->product_code,   array('V53WPAK','V54EPAK')) )
		//     {
		//         $exportDoc=$this->getExportDoc($result->CreationState->LabelData->shipmentNumber);
		//         if($exportDoc->Status->statusCode === 0)
		//         {
		//             if($exportDoc->ExportDocData->exportDocURL)
		//                 $return['ExportDoc']=$exportDoc->ExportDocData->exportDocURL;
		//         }
		//     }
		// }
		return $result;
	}

	private function process_result( $result, $request, $dhl_packages, $auto_label ) {
		$order = wc_get_order( $this->order_id );
		if ( 201 === $result['response']['code'] ) {
			$resultBody    = json_decode($result['body']);
			$shipmentIde   = $resultBody->shipmentId;
			$parcelType    = $resultBody->parcelType;
			$labelType     = $resultBody->labelType;
			$trackerCode   = $resultBody->trackerCode;
			$shippingLabel = $resultBody->pdf;
			if ( $order->get_meta('_wf_woo_dhl_shipmentId')) {
				$shipmentId = $order->get_meta('_wf_woo_dhl_shipmentId');
				array_push($shipmentId, $shipmentIde);    
			} else {
			   $shipmentId[] = $shipmentIde;
			}
			$order->update_meta_data('_wf_woo_dhl_shipmentId', $shipmentId);
			$order->update_meta_data('_wf_woo_dhl_trackerId_' . $shipmentIde, $trackerCode, false);
			$order->update_meta_data('_wf_woo_dhl_shippingLabel_' . $shipmentIde, $shippingLabel, true);

			$order->update_meta_data( '_wf_woo_dhl_piece_information', $parcelType, false);

		}
		if (isset( $result['ShipmentID'] ) && !empty($result['ShipmentID']) && !empty($result['LabelImage'])) {            
			$shipmentId       = $result['ShipmentID'];
			$shippingLabel    = $result['LabelImage'];
			$pieceInformation = $result['PieceInformation'];
			
			if (isset($request['ShipmentOrder']['Shipment']['ShipmentDetails']['Service'])) {
				$shipment_services = $request['ShipmentOrder']['Shipment']['ShipmentDetails']['Service'];
				if (is_array($shipment_services)) {
					foreach ($shipment_services as $service_key=>$service_name) {
						if (is_array($this->custom_services)) {
							if (array_key_exists($service_key, $this->custom_services)) {
								$order->update_meta_data( '_wf_woo_dhl_service_code', $service_key, false);
								if ($service_key == 'DeliveryOnTime') {
								$order->update_meta_data( '_wf_woo_dhl_service_time', $shipment_services[$service_key]['time'], false);

								}
							}
						}
						
					}
				}
			}

			$stored_shipments_ids = $order->get_meta('_wf_woo_dhl_shipmentId');
			if (!empty($stored_shipments_ids)) {
				$stored_shipments_ids[] = $shipmentId;
			} else {
				$stored_shipments_ids   = array();
				$stored_shipments_ids[] = $shipmentId;
			}
			$order->update_meta_data('_wf_woo_dhl_shipmentId', $stored_shipments_ids);

			$order->update_meta_data('_wf_woo_dhl_shippingLabel_' . $shipmentIde, $shippingLabel, true);
			$order->update_meta_data( '_wf_woo_dhl_piece_information', $pieceInformation, false);


			if ( !empty($result['ExportDoc']) ) {
				$order->update_meta_data( '_wf_woo_dhl_export_doc_' . $shipmentId, $result['ExportDoc'] );
			}
			// Shipment Tracking (Auto)
			try {
				$shipment_id_cs = $shipmentId;
				$admin_notice   = WfTrackingUtil::update_tracking_data( $this->order_id, $shipment_id_cs, 'deutsche-post-dhl', WF_Tracking_Admin_DHLParcel::SHIPMENT_SOURCE_KEY, WF_Tracking_Admin_DHLParcel::SHIPMENT_RESULT_KEY );
			} catch ( Exception $e ) {
				$admin_notice = '';
				// Do nothing.
			}
			
			// Shipment Tracking (Auto)
			if ( $admin_notice != '' && $auto_label == '') {
				WF_Tracking_Admin_DHLParcel::display_admin_notification_message( $this->order_id, $admin_notice );
			} else {
				//Do your plugin's desired redirect.
				//exit;
			}
			
		}
		$order->save();
		if (!empty($result['ErrorMessage'])) {
			$this->shipmentErrorMessage .=  $result['ErrorMessage'];        
		}               
	}
	
	private function wf_get_parcel_details( $dhl_packages) {
		$complete_box = array();
		if ( $dhl_packages ) {
			foreach ( $dhl_packages as $key => $parcel ) {
				$box_details = '';                  
				if (!empty($parcel['package_id'])) {
					$box_details .=  '<strong>BOX:  </strong>' . $parcel['package_id'] . '<br />';                  
				}               
				if (isset($parcel['Weight'])) {
					$box_details .=  '<strong>Weight:  </strong>' . $parcel['Weight']['Value'] . ' ' . $parcel['Weight']['Units'] . '<br />';                   
				}       
				if (isset($parcel['Dimensions'])) {
					$box_details .=  '<strong>Height:  </strong>' . $parcel['Dimensions']['Height'] . ' ' . $parcel['Dimensions']['Units'] . '<br />';
					$box_details .=  '<strong>Width:  </strong>' . $parcel['Dimensions']['Width'] . ' ' . $parcel['Dimensions']['Units'] . '<br />';
					$box_details .=  '<strong>Length:  </strong>' . $parcel['Dimensions']['Length'] . ' ' . $parcel['Dimensions']['Units'] . '<br />';                  
				}
				$box_details   .= '<hr>';
				$complete_box[] = $box_details;             
			}           
		}           
		return $complete_box;
	}
	
	private function getExportDoc( $shipment_id) {
		
		$client                    = $this->loginSoapClient();     
		$request                   =array();       
		$request['Version']        =array(
			'majorRelease'=>'2',
			'minorRelease'=>'0',
		);      
		$request['shipmentNumber'] =$shipment_id;
		try {
			$result = $client->getExportDoc($request);
			return $result;
		} catch (Exception $e) {
			$order = wc_get_order($this->order_id);
			$order->update_meta_data('_wf_woo_dhl_shipmentErrorMessage', $e->faultstring);
			$order->save();
		}   
	}
	
	public function delete_shipment( $order_id) {
		$order = wc_get_order($order_id);
		$shipment_ids = $order->get_meta('_wf_woo_dhl_shipmentId');
		$client       = $this->loginSoapClient();     
		$request      = array();
		
		$request['Version'] = array(
			'majorRelease' => '1',
			'minorRelease' => '0',
		);
		$shipments_ids[]    = $shipment_ids;
		foreach ($shipment_ids as $key => $shipment_id) {
			$request['shipmentNumber'] = $shipment_id;
			try {
				$result = $client->deleteShipmentOrder($request);
			} catch (Exception $e) {
				wf_admin_notice::add_notice($e->faultstring);
				//update_post_meta($order_id, '_wf_woo_dhl_shipmentErrorMessage', $e->faultstring);
				return false;
			}       
		}

		return true;
	}
	
	public function createManifest( $order_id, $shipment_id) {
		$order = wc_get_order( $order_id );
		$client  = $this->loginSoapClient();     
		$request =array();
		
		$request['Version'] =array(
			'majorRelease'=>'2',
			'minorRelease'=>'0',
		);
		
		$request['shipmentNumber'] =$shipment_id;
		try {
			$result = $client->doManifest($request);
			if ($result->Status->statusCode>0) {
				//error occured
				$order->update_meta_data('_wf_woo_dhl_shipmentErrorMessage', $result->Status->statusMessage);       
			} else {
				// successful manifest
				$order->add_meta_data( '_wf_woo_dhl_manifest_' . $shipment_id, $shipment_id, true);
			}
		} catch (Exception $e) {
			$order->update_meta_data('_wf_woo_dhl_shipmentErrorMessage', $e->faultstring);       

		}
		$order->save();
	}
	
	public function get_manifest( $order_id, $mani_date = false) {
		$order = wc_get_order($order_id);
		$client  = $this->loginSoapClient();     
		$request =array();
		
		$request['Version'] =array(
			'majorRelease'=>'2',
			'minorRelease'=>'0',
		);
		
		$request['manifestDate'] =$mani_date?$mani_date:date('Y-m-d', current_time('timestamp'));
		try {
			$result = $client->getManifest($request);
			if ($result->Status->statusCode>0) {
				//error occured
				$order->update_meta_data( '_wf_woo_dhl_shipmentErrorMessage', $result->Status->statusMessage);       
			} else {
				// successful manifest
				return $result->manifestData;
			}
		} catch (Exception $e) {
			$order->update_meta_data( '_wf_woo_dhl_shipmentErrorMessage', $e->faultstring);  
		}
		$order->save();
		return false;
	}
	
	public function validate_label_data() {
		
		$ship_pack_length =   (float) $_REQUEST['ship_pack_length'];
		$ship_pack_width  =   (float) $_REQUEST['ship_pack_width'];
		$ship_pack_height =   (float) $_REQUEST['ship_pack_height'];
		$ship_pack_weight =   (float) $_REQUEST['ship_pack_weight'];
		if (( $ship_pack_length||$ship_pack_width||$ship_pack_height||$ship_pack_weight )&&!( $ship_pack_length&&$ship_pack_width&&$ship_pack_height&&$ship_pack_weight )) {
			$order = wc_get_order( $this->order_id);
			$order->update_meta_data('_wf_woo_dhl_shipmentErrorMessage', 'You might have missed one or more dimensions');
			$order->save();
			return false;
		} else {
			return true;
		}
		
	}
	
	public function manual_packages( $packages) {
		if (!isset($_GET['weight'])) {
			return $packages;
		}
		
		$length_arr    =   json_decode(stripslashes(html_entity_decode($_GET['length'])));
		$width_arr     =   json_decode(stripslashes(html_entity_decode($_GET['width'])));
		$height_arr    =   json_decode(stripslashes(html_entity_decode($_GET['height'])));
		$weight_arr    =   json_decode(stripslashes(html_entity_decode($_GET['weight'])));     
		$insurance_arr =   json_decode(stripslashes(html_entity_decode($_GET['insurance'])));

		$no_of_package_entered =   count($weight_arr);
		$no_of_packages        =   count($packages);
		
		// Populate extra packages, if entered manual values
		if ($no_of_package_entered > $no_of_packages) { 
			$package_clone =   is_array($packages) ? current($packages) : $this->get_dummy_dhl_parcel_package(); //get first package to clone default data
			for ($i=$no_of_packages; $i<$no_of_package_entered; $i++) {
				
				$packages[$i] =   $package_clone;
				
				$packages[$i]['GroupNumber']       =   $i+1;
				$packages[$i]['GroupPackageCount'] =   1;
				unset($packages[$i]['packed_products']);
			}
		}
		
		// Overridding package values
		foreach ($packages as $key => $package) {
			
			if (isset($weight_arr[$key])) {// If not available in GET then don't overwrite.
				$packages[$key]['Weight']['Value'] =   $weight_arr[$key];
			}
			
			if (isset($length_arr[$key])) {// If not available in GET then don't overwrite.
				$packages[$key]['Dimensions']['Length'] =   $length_arr[$key];
			}
			
			if (isset($width_arr[$key])) {// If not available in GET then don't overwrite.
				$packages[$key]['Dimensions']['Width'] =   $width_arr[$key];
			}
			
			if (isset($height_arr[$key])) {// If not available in GET then don't overwrite.
				$packages[$key]['Dimensions']['Height'] =   $height_arr[$key];
			}
			
			if ( isset($insurance_arr[$key]) ) {// If not available in GET then don't overwrite.
				$packages[$key]['InsuredValue']['Amount'] =   $insurance_arr[$key];
			}
		}
		
		return $packages;
	}
	private function get_dummy_dhl_parcel_package() {
		return array (
			'GroupNumber' => 1,
			'GroupPackageCount' => 1,
			'Weight' => array (
				'Value' => 0,
				'Units' => $this->weight_unit,
			),
			'Dimensions' => array (
				'Length' => 0,
				'Width' => 0,
				'Height' => 0,
				'Units' => $this->dimension_unit,
			),
			'InsuredValue' => array (
				'Amount' => '0',
				'Currency' => get_woocommerce_currency(),
			),
			'packed_products' => array(),
		);
	}
	
	private function loginSoapClient() {
		try {

			$refreshToken    = array();
			$response_result = '';
			$requestHeaders  = array(
					'Content-Type' => 'application/json',
					'Accept'=>'application/json'
				);
			$clientreq       = array(
				'userId'=> $this->site_id,
				'key'   => $this->site_password
			);
			 $response       = wp_safe_remote_post($this->service_url, array(
					'method'=>'POST',
					'headers'=>$requestHeaders,
					'timeout'=>70,
					'body'=>json_encode($clientreq)
					)
				);
			if ( isset( $response['body'] ) ) {
				$response_result = json_decode($response['body']);
			}

			if ( $response_result ) {
				if (isset($response_result->accessToken)) {

					update_option('dhlparcel_token', $response_result->accessToken);
				}
				if (isset($response_result->refreshToken)) {
					$refreshToken = array(
					   'refreshToken'=> $response_result->refreshToken
					);
				}
			}
			if ( !empty( $refreshToken) ) {
				$refershResponse = wp_safe_remote_post($this->refershToken, array(
				   'method'=>'POST',
				   'headers'=>$requestHeaders,
				   'timeout'=>70,
				   'body'=>json_encode($refreshToken)
				   )
			   );
			} else {
				return false;
			}
			if ( isset($refershResponse) && isset($refershResponse['response']) ) {
				if ( $refershResponse['response']['code'] == 200 ) {
					return true;
				}
			}
			
		} catch (Exception $e) {
			$this->debug($e->getMessage());
		}    
	}   
	
	public function get_visual_check_of_age( $item) {
		$product = wc_get_product( elex_dhl_get_product_id($item) );
		return $product->get_meta( '_wf_dhlp_age_check' );
	}
	
	public function get_package_visual_check_of_age( $package) {
		foreach ( $package['contents'] as $item_id => $values ) {
			if ($this->get_visual_check_of_age($values['data'])) {
				return true;
			}
		}
		return false;
	}
	
	public function account_number_with_product_code( $account_number, $product_code) {
		
		$prd_codes =   array(
			'V01PAK'    =>  '01',//DHL PARCEL
			'V53WPAK'   =>  '53',//DHL PARCEL International
			'V54EPAK'   =>  '54',//DHL Europarcel
			'V06TG'     =>  '01',//DHL PARCEL Taggleich
			'V06WZ'     =>  '01',//Kurier Wunschzeit
			'V86PARCEL' =>  '86',//DHL PARCEL Austria
			'V87PARCEL' =>  '87',//DHL PARCEL Connect
			'V82PARCEL' =>  '82',//DHL PARCEL International
			'V55PAK'    =>  '55'
		);
		
		$account_number =   str_replace(' ', '', $account_number);
		if (strlen($account_number) != 14) { // Account number is not well formatted, 10 digit ekp, 2 digit product code, 2 digit partner id
			return $account_number;
		}
		
		if (!array_key_exists($product_code, $prd_codes)) { // Invalid product code
			return $account_number;
		}
		
		$ekp     =   substr($account_number, 0, 10);
		$prtn_id =   substr($account_number, -2);
		
		$account_number =   $ekp . $prd_codes[$product_code] . $prtn_id;
		
		return $account_number;
	}
	
	public static function is_european_country( $country_code) {
		if (!$country_code) {
			return false;
		}
		$euro_countries =   array(
			'RU','UA','FR','ES','SE','NO','DE','FI','PL','IT',
			'UK','RO','BY','EL','BG','IS','HU','PT','AZ','AT',
			'CZ','RS','IE','GE','LT','LV','HR','BA','SK','EE',
			'DK','CH','NL','MD','BE','AL','MK','TR','SI','ME',
			'XK','LU','MT','LI' 
		);
		
		return in_array($country_code, $euro_countries)?true:false;
	}
	
	public function wf_array_to_xml( $tags, $full_xml = false) {
//$full_xml true will contain <?xml version
		$xml_str =   '';
		foreach ($tags as $tag_name  => $tag) {
			$out =   '';
			try {
				$xml = new SimpleXMLElement('<' . $tag_name . '/>');
				
				if (is_array($tag)) {
					$this->array2XML($xml, $tag);
					
					if (!$full_xml) {
						$dom  =   dom_import_simplexml($xml);
						$out .=$dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
					} else {
						$out .=$xml->saveXML();
					}
				} else {
					$out .=$tag;
				}
				
			} catch (Exception $e) {
				// Do nothing
			}
			$xml_str .=$out;
		}
		return $xml_str;
	}
	
	public function array2XML( $obj, $array) {
		foreach ($array as $key => $value) {
			if (is_numeric($key)) {
				$key = 'item' . $key;
			}

			if (is_array($value)) {
				$node = $obj->addChild($key);
				$this->array2XML($node, $value);
			} else {
				$obj->addChild($key, htmlspecialchars($value));
			}
		}
	}
}
