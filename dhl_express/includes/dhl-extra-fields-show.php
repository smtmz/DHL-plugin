<?php 
if (!class_exists('WF_Dhl_Extra_Meta_Fields_Class')) {
	class WF_Dhl_Extra_Meta_Fields_Class {
	    public $settings;
		public $insurance_content;
		public $insurance_content_chk;
    	public $destination_country;

		public function __construct() {
			$this->settings         = get_option( 'woocommerce_' . WF_DHL_ID . '_settings', null );
			$delivery_time          = false;
			$show_dhl_extra_charges = '';
			if (!empty($this->settings) && isset($this->settings)) {
				$show_dhl_extra_charges = isset($this->settings['show_dhl_extra_charges']) ? $this->settings['show_dhl_extra_charges'] : '' ;
				$del_bool               =  isset($this->settings['delivery_time']) ? $this->settings['delivery_time'] : 'no' ;
				$delivery_time          = ( $del_bool == 'yes' ) ? true : false;

			}

			if (!empty($show_dhl_extra_charges) && $show_dhl_extra_charges === 'yes') {
				add_filter( 'woocommerce_cart_shipping_method_full_label', array($this, 'wf_add_extra_charges'), 10, 2 );
			}
				
			// Disply estimate delivery time
			if ($delivery_time) {
				add_filter( 'woocommerce_cart_shipping_method_full_label', array($this, 'wf_add_delivery_time'), 10, 2 );
			}
				
			$this->insurance_content     = isset($this->settings['insure_contents']) ? $this->settings['insure_contents'] : '' ;
			$this->insurance_content_chk = isset($this->settings['insure_contents_chk']) ? $this->settings['insure_contents_chk'] : '' ;

			add_filter( 'woocommerce_checkout_fields' , array($this, 'wf_dhl_custom_override_checkout_fields') );
				
			add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'wf_dhl_woocommerce_cart_shipping_packages' ));
		}
			
		function wf_dhl_woocommerce_cart_shipping_packages( $shipping = array()) {
			$this->destination_country = $shipping[0]['destination']['country'];
			foreach ($shipping as $key=>$val) {
				$str = '';
				if (isset($_POST['post_data'])) {
					parse_str($_POST['post_data'], $str);
				}

				if (isset($str['wf_dhl_insurance'])) {
					$shipping[$key]['wf_dhl_insurance'] = true;
				} elseif (!empty($this->insurance_content) && $this->insurance_content === 'yes' && !empty($this->insurance_content_chk) && $this->insurance_content_chk === 'yes') {
					$shipping[$key]['wf_dhl_insurance'] = false;
				} elseif (!empty($this->insurance_content) && $this->insurance_content == 'yes') {
					$shipping[$key]['wf_dhl_insurance'] = true;
				} else {
					$shipping[$key]['wf_dhl_insurance'] = false;
				}
					
				if (isset($_POST['wf_dhl_insurance'])) {
					$shipping[$key]['wf_dhl_insurance'] = true;
				}

				$shipping[$key]['wf_dhl_delivery_signature'] = isset($str['wf_dhl_delivery_signature'])? true: false;
			}
			return $shipping;
		}
			
		function wf_dhl_custom_override_checkout_fields( $fields ) {

			$insurance_specific_countries = isset($this->settings['elex_dhl_insurance_for_specific_countries'])? $this->settings['elex_dhl_insurance_for_specific_countries']: array();
			$show_insurance_checkbox      = false;

			if (!empty($insurance_specific_countries)) {
				if ( isset( WC()->customer ) && in_array(WC()->customer->get_shipping_country(), $insurance_specific_countries)) {
					$show_insurance_checkbox = true;
				} else {
					$show_insurance_checkbox = false;
				}                    
			} else {
				$show_insurance_checkbox = true;
			}
			$restrict_insurance_country = apply_filters( 'elex_dhl_restrict_insurance_countries', array() );
			if ( isset( WC()->customer ) && in_array(WC()->customer->get_shipping_country(), $restrict_insurance_country)) {
					$show_insurance_checkbox = false;
			} 
				

			if (!empty($this->insurance_content) && $this->insurance_content === 'yes' && !empty($this->insurance_content_chk) && $this->insurance_content_chk === 'yes' && $show_insurance_checkbox) {
				// Adding custom checkout field for DHL Insurance
				$fields['billing']['wf_dhl_insurance'] = array(
					'label' => __('Enable DHL Shipping Insurance', 'wf-shipping-dhl'),
					'type'  => 'checkbox',
					'required' => 0,
					'default'   => false,
					'class' => array ( 'update_totals_on_change' )
				);
			}

			if (isset($this->settings['delivery_signature']) && $this->settings['delivery_signature'] == 'yes') {
				// Adding custom checkout field for DHL Signature on Delivery
				$fields['billing']['wf_dhl_delivery_signature'] = array(
					'label' => __('Enable DHL Signature on Delivery', 'wf-shipping-dhl'),
					'type'  => 'checkbox',
					'required' => 0,
					'default'   => true,
					'class' => array ( 'update_totals_on_change' )
				);
			}

			return $fields;
		}
			
		public function wf_add_delivery_time( $label, $method ) {
			if ( !is_object($method) ) {
				return $label;
			}
			$est_delivery = $method->get_meta_data();
			if ( isset($est_delivery['DHL Delivery Time']) ) {
				$est_delivery_html = '<br /><small>' . __('Est delivery: ', 'wf-shipping-dhl') . $est_delivery['DHL Delivery Time'] . '</small>';
				$est_delivery_html = apply_filters( 'wf_dhl_estimated_delivery', $est_delivery_html, $est_delivery );
				$label            .= $est_delivery_html;
			}
			return $label;
		}

		public function wf_add_extra_charges( $label, $method ) {
			if ( !is_object($method) ) {
				return $label;
			}

			$extra_charges          = $method->get_meta_data();
			$tax_calculation_amount = 0;
			foreach ($method->taxes as $value) {
			   $tax_calculation_amount += $value;
			}

			if ( isset($extra_charges['Weight Charge']) ) {
				$tax_calculation_html = '';
				if ($tax_calculation_amount > 0) {
					$check_tax_type = get_option('woocommerce_tax_display_cart');
					if ($check_tax_type != 'excl') {
						$tax_calculation_html = '<small>+ ' . __('Taxes: ', 'wf-shipping-dhl') . wc_price($tax_calculation_amount) . '</small>';
					}
				}
				$obj                = new wf_dhl_woocommerce_shipping_method();
				$string_append      = $obj->exclude_dhl_tax ? '' : ' (Inc Tax,Ship,etc.)';
				$extra_charges_html = '<br /><small>' . __('Weight Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Weight Charge']) . ' + ' . __('DHL Handling Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Extra Charge']) . $string_append . ' </small>' . $tax_calculation_html;
				//$est_delivery_html = apply_filters( 'wf_dhl_estimated_delivery', $est_delivery_html, $est_delivery ); for future reference

				if (isset($extra_charges['Insurance Charge']) && $this->settings['show_dhl_insurance_charges'] == 'yes') {
					$extra_charges_html = '<br /><small>' . __('Weight Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Weight Charge']) . ' + ' . __('DHL Handling Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Extra Charge']) . $string_append . ' </small>' . $tax_calculation_html . ' + <small>' . __('DHL Insurance Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Insurance Charge']) . '</small>';
				}

				if (isset($extra_charges['Remote Area Surcharge']) && $this->settings['show_dhl_remote_area_surcharge'] == 'yes') {
					if ($this->settings['show_dhl_insurance_charges'] == 'yes' && isset($extra_charges['Insurance Charge'])) {
						$extra_charges_html = '<br /><small>' . __('Weight Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Weight Charge']) . ' + ' . __('DHL Handling Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Extra Charge']) . $string_append . ' </small>' . $tax_calculation_html . ' + <small>' . __('DHL Insurance Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Insurance Charge']) . '</small> + <small>' . __('DHL Remote Area Surcharge: ', 'wf-shipping-dhl') . wc_price($extra_charges['Remote Area Surcharge']) . '</small>';
					} else {
						$extra_charges_html = '<br /><small>' . __('Weight Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Weight Charge']) . ' + ' . __('DHL Handling Charges: ', 'wf-shipping-dhl') . wc_price($extra_charges['Extra Charge']) . $string_append . ' </small>' . $tax_calculation_html . ' +  <small>' . __('DHL Remote Area Surcharge: ', 'wf-shipping-dhl') . wc_price($extra_charges['Remote Area Surcharge']) . '</small>';
					}

				}

				$label .= $extra_charges_html;
			}

			return $label;
		}
	}
}
	new WF_Dhl_Extra_Meta_Fields_Class();
