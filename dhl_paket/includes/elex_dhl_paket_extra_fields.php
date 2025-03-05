<?php
	
if (!class_exists('elex_dhl_paket_custom_checkout_fields')) {
	class elex_dhl_paket_custom_checkout_fields {
		public function __construct() {
			add_filter( 'woocommerce_checkout_fields' , array($this, 'elex_dhl_paket_add_custom_checkout_fields') );
			add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'elex_dhl_paket_cart_shipping_packages' ));
		}

		public function elex_dhl_paket_add_custom_checkout_fields( $checkout_fields) {
			$checkout_fields['billing']['billing_house_number_paket_dhl_elex'] = array(
				'label' => __('House Number', 'wf-shipping-dhl'),
				'type' => 'text',
				'required' => 0,
				'placeholder' => __('House Number', 'wf-shipping-dhl'),
				'class' => array( 'form-row-wide', 'address-field'),
				'priority' => 50
			);

			$checkout_fields['shipping']['shipping_house_number_paket_dhl_elex'] = array(
				'label' => __('House Number', 'wf-shipping-dhl'),
				'required' => 0,
				'placeholder' => __('House Number', 'wf-shipping-dhl'),
				'class' => array( 'form-row-wide', 'address-field'),
				'priority' => 50
			);

			return $checkout_fields;
		}

		public function elex_dhl_paket_cart_shipping_packages( $shipping_packages) {

			foreach ($shipping_packages as $package_key => $package_data) {
				$str = '';
				if (isset($_POST['post_data'])) {
					parse_str($_POST['post_data'], $str);
				}

				if ($str != '') {
					if (!empty($str['shipping_house_number_paket_dhl_elex'])) {
						$shipping_packages[$package_key]['destination']['house_number'] = $str['shipping_house_number_paket_dhl_elex'];
					} else {
						$shipping_packages[$package_key]['destination']['house_number'] = $str['billing_house_number_paket_dhl_elex'];
					}
				}
			}

			return $shipping_packages;
		}
	}
}
	new elex_dhl_paket_custom_checkout_fields();
