<?php
if ( !class_exists('WF_DHLP_Admin_Options') ) {
	class WF_DHLP_Admin_Options {
		function __construct() {
			$this->init();
		}

		function init() {
			//add a custome field in product page
			add_action( 'woocommerce_product_options_shipping', array($this,'wf_add_customer_age_field')  );

			//Saving the values
			add_action( 'woocommerce_process_product_meta', array( $this, 'wf_save_customer_age_field' ) );
		}

		function wf_add_customer_age_field() {
			// Print a custom text field
			woocommerce_wp_checkbox( array(
				'id' => '_wf_dhlp_age_check',
				'label' => __('Visual check of age'),
				'description' => __('Order recipient\'s age must be over 18'),
				'desc_tip' => false,
			) );
		}

		function wf_save_customer_age_field( $post_id ) {
			$product = wc_get_product( $post_id );

			if (empty($_POST['_wf_dhlp_age_check'])) {
				$product->update_meta_data('_wf_dhlp_age_check', '');
			} else {
				$product->update_meta_data( '_wf_dhlp_age_check', esc_attr( $_POST['_wf_dhlp_age_check'] ) );
			}
			$product->save();
		}
	}
	new WF_DHLP_Admin_Options();
}
