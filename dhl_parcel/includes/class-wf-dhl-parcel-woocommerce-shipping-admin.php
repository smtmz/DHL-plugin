<?php
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
class wf_dhl_parcel_woocommerce_shipping_admin {
	public $settings;
	public $enabled;
	public $dimension_unit;
	public $weight_unit;



	public $custom_services;
	public $label_enabled;
	public $image_type;
	public $services;
	public $debug;

	public function __construct() {
		$this->settings        = get_option( 'woocommerce_' . WF_DHL_PARCEL_ID . '_settings', null );
		$this->enabled         = isset($this->settings['enabled']) ? $this->settings['enabled'] : '' ;
		$this->custom_services = isset($this->settings['services']) ? $this->settings['services'] : array();
		$this->image_type      = 'PDF';//$this->settings['image_type'];
		$this->services        = include  'data-wf-service-codes.php' ;
		
		$this->dimension_unit = isset($this->settings['dimension_weight_unit']) && $this->settings['dimension_weight_unit'] == 'LBS_IN' ? 'IN' : 'CM';
		$this->weight_unit    = isset($this->settings['dimension_weight_unit']) && $this->settings['dimension_weight_unit'] == 'LBS_IN' ? 'LBS' : 'KG';        
		$this->debug          = ( $bool = isset( $this->settings[ 'debug' ] ) ? $this->settings[ 'debug' ] : '' ) && $bool == 'yes' ? true : false;
		
		if (is_admin() && $this->enabled === 'yes') {
			$this->init_bulk_printing();
			add_action('admin_notices', array(new wf_admin_notice(), 'throw_notices'), 15); // New notice system
			add_action('add_meta_boxes', array($this, 'wf_add_dhl_parcel_metabox'));
		}

		if (isset($_GET['wf_dhl_parcel_createshipment'])) {
			add_action('init', array($this, 'wf_dhl_parcel_createshipment'));
		}

		if (isset($_GET['wf_dhl_parcel_removeshipment'])) {
			add_action('init', array($this, 'wf_dhl_parcel_removeshipment'));
		}

		if (isset($_GET['wf_dhl_parcel_clearhistory'])) {
			add_action('init', array($this, 'wf_dhl_parcel_clearhistory'));
		}

		if (isset($_GET['wf_dhl_parcel_viewlabel'])) {
			add_action('init', array($this, 'wf_dhl_parcel_viewlabel'));
		}

		if (isset($_GET['wf_dhl_parcel_exportdoc'])) {
			add_action('init', array($this, 'wf_dhl_parcel_exportdoc'));
		}

		if (isset($_GET['wf_dhl_parcel_create_manifest'])) {
			add_action('init', array($this,'wf_dhl_parcel_create_manifest'));
		}
		if (isset($_GET['wf_dhl_parcel_print_label'])) {
			add_action('init', array($this,'wf_dhl_parcel_print_label'));
		}
		
		if (isset($_GET['wf_dhl_parcel_getManifest'])) {
			add_action('init', array($this,'wf_dhl_parcel_getManifest'));
		}
		
		if (isset($_GET['wf_dhl_parcel_generate_packages'])) {
			add_action('init', array($this,'wf_dhl_parcel_generate_packages'));
		}
	}
	
	public function resetErrorMessage( $order) {
		if (!$order) {
			return false;
		}
		$orderid = elex_dhl_get_order_id($order);
		$order->update_meta_data('_wf_woo_dhl_shipmentErrorMessage', '', true);
	}
	
	private function wf_load_order( $orderId) {
		if (!class_exists('WC_Order')) {
			return false;
		}
		return new WC_Order($orderId);      
	}
	
	private function wf_user_permission() {
		// Check if user has rights to generate invoices
		$current_user = wp_get_current_user();
		$user_ok      = false;
		if ($current_user instanceof WP_User) {
			if (in_array('administrator', $current_user->roles) || in_array('shop_manager', $current_user->roles)) {
				$user_ok = true;
			}
		}
		return $user_ok;
	}
	
	public function wf_dhl_parcel_createshipment() {
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) {          
			return;
		}
		
		$order = $this->wf_load_order($_GET['wf_dhl_parcel_createshipment']);
		update_option('order_id', $order->get_order_number());
		if (!$order) { 
			return;
		}
		update_option('create shipment', true);
		$this->resetErrorMessage($order);
		$this->wf_create_shipment($order);
		update_option('create shipment', false);
		update_option('bulk_create_shipment', false);
		if ($this->debug) {
			echo '<a href="' . admin_url('/post.php?post=' . $_GET['wf_dhl_parcel_createshipment'] . '&action=edit') . '">' . __( 'Back to Order', 'wf-shipping-dhl' ) . '</a>'; 
			//For the debug information to display in the page
			die();          
		}
		wp_redirect(admin_url('/post.php?post=' . $_GET['wf_dhl_parcel_createshipment'] . '&action=edit'));
		exit;
	}
	
	public function wf_dhl_parcel_generate_packages() {
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) {          
			return;
		}
		
		$order = $this->wf_load_order($_GET['wf_dhl_parcel_generate_packages']);
		if (!$order) { 
			return;
		}
		
		$this->resetErrorMessage($order);
		
		if ( ! class_exists( 'wf_dhl_parcel_woocommerce_shipping_admin_helper' ) ) {
			include_once 'class-wf-dhl-parcel-woocommerce-shipping-admin-helper.php';
		}
		
		$woodhlwrapper = new wf_dhl_parcel_woocommerce_shipping_admin_helper();
		$serviceCode   = $this->wf_get_shipping_service($order, false);
		
		$woodhlwrapper->generate_packages($order, $serviceCode);
	}
	
	public function wf_dhl_parcel_clearhistory() {
		$order = $this->wf_load_order($_GET['wf_dhl_parcel_clearhistory']);
		$this->wf_clear_history( $order );
		
		wp_redirect(admin_url('/post.php?post=' . $_GET['wf_dhl_parcel_clearhistory'] . '&action=edit'));
	}
	
	public function wf_clear_history( $order) {

		$orderid = elex_dhl_get_order_id($order);
		delete_post_meta($orderid, '_wf_woo_dhl_shipmentErrorMessage');
		$order->delete_meta_data('_wf_woo_dhl_service_code');
		$order->delete_meta_data('_wf_woo_dhl_service_time');

				
		$shipment_ids =  $order->get_meta('_wf_woo_dhl_shipmentId');
		if (is_array($shipment_ids)) {
			foreach ($shipment_ids as $shipment_id) {
				$order->delete_meta_data('_wf_woo_dhl_shippingLabel_' . $shipment_id);
				$order->delete_meta_data('_wf_woo_dhl_export_doc_' . $shipment_id);

				$order->delete_meta_data('_wf_woo_dhl_printed_' . $shipment_id);

			}
		}
		$order->delete_meta_data('_wf_woo_dhl_shipmentId');

		delete_post_meta($orderid, 'wf_woo_dhl_parcel_shipment_void');
		$order->save();
	}

	public function wf_dhl_parcel_removeshipment() {
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) {          
			return;
		}
		
		$order = $this->wf_load_order($_GET['wf_dhl_parcel_removeshipment']);
		if (!$order) { 
			return;
		}
		
		$this->resetErrorMessage($order);
		$this->wf_remove_shipment($order);

		wp_redirect(admin_url('/post.php?post=' . $_GET['wf_dhl_parcel_removeshipment'] . '&action=edit'));
		exit;
	}
	
	public function wf_dhl_parcel_viewlabel() {
		$shipmentDetails = explode('|', base64_decode($_GET['wf_dhl_parcel_viewlabel']));

		if (count($shipmentDetails) != 2) {
			exit;
		}
		
		$shipmentId = $shipmentDetails[0]; 
		$post_id    = $shipmentDetails[1]; 
		$order = wc_get_order($post_id);
		$shipping_label = $order->get_meta('_wf_woo_dhl_shippingLabel_' . $shipmentId);
		
		$order->add_meta_data( '_wf_woo_dhl_printed_', $shipmentId );
		
		header('Content-Type: application/' . $this->image_type);
		header('Content-disposition: attachment; filename="ShipmentArtifact-' . $shipmentId . '.' . $this->image_type . '"');
		print( base64_decode($shipping_label) ); 
		exit;
	}
	
	public function wf_dhl_parcel_getManifest() {
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) {          
			return;
		}
		$order_id  =$_GET['wf_dhl_parcel_getManifest'];
		$mani_date =$_GET['mani_date'];
		$order     = $this->wf_load_order($_GET['wf_dhl_parcel_getManifest']);
		if (!$order) { 
			return;
		} 
		
		$this->wf_get_manifest($order, $mani_date);
		wp_redirect(admin_url('/post.php?post=' . $_GET['wf_dhl_parcel_getManifest'] . '&action=edit'));
		exit;
	}
	
	private function wf_is_service_valid_for_country( $order, $service_code) {
		return true; 
	}

	private function wf_get_shipping_service( $order, $retrive_from_order = false) {
		$orderid = elex_dhl_get_order_id($order);
		if ($retrive_from_order == true) {
			$service_code = $order->get_meta('_wf_woo_dhl_service_code');
			if (!empty($service_code)) {
return $service_code;
			}
		}
		
		if (!empty($_GET['dhl_shipping_service'])) {
			return $_GET['dhl_shipping_service'];           
		}
			
		//TODO: Take the first shipping method. It doesnt work if you have item wise shipping method
		$shipping_methods = $order->get_shipping_methods();
		if ( ! $shipping_methods ) {
			return '';
		}
	
		$shipping_method = array_shift($shipping_methods);

		return str_replace(WF_DHL_PARCEL_ID . ':', '', $shipping_method['method_id']);
	}
	
	public function wf_create_shipment( $order, $auto_label = '') {     
		if ( ! class_exists( 'wf_dhl_parcel_woocommerce_shipping_admin_helper' ) ) {
			include_once 'class-wf-dhl-parcel-woocommerce-shipping-admin-helper.php';
		}
		$woo_dhl_wrapper = new wf_dhl_parcel_woocommerce_shipping_admin_helper();
		$serviceCode     = $this->wf_get_shipping_service($order, false);
		$orderid         = elex_dhl_get_order_id($order);
		if ($auto_label == '') {
			return $woo_dhl_wrapper->print_label($order, $serviceCode, $orderid );
		} else {
			return $woo_dhl_wrapper->print_label($order, $serviceCode, $orderid , $auto_label);
		}
	}
	
	public function wf_remove_shipment( $order) {
		if ( ! class_exists( 'wf_dhl_parcel_woocommerce_shipping_admin_helper' ) ) {
			include_once 'class-wf-dhl-parcel-woocommerce-shipping-admin-helper.php';
		}
		
		$orderid         = elex_dhl_get_order_id($order);
		$woo_dhl_wrapper = new wf_dhl_parcel_woocommerce_shipping_admin_helper();
		//$delete_shipment_result =  $woo_dhl_wrapper->delete_shipment($orderid);    
		if (true) {
			$shipment_ids = $order->get_meta('_wf_woo_dhl_shipmentId');
			$order->update_meta_data('wf_woo_dhl_parcel_shipment_void', $shipment_ids);
		}       
		$order->save();
		$this->wf_clear_history( $order );
		return true;
	}

	public function wf_get_manifest( $order, $mani_date) {
		if ( ! class_exists( 'wf_dhl_parcel_woocommerce_shipping_admin_helper' ) ) {
			include_once 'class-wf-dhl-parcel-woocommerce-shipping-admin-helper.php';
		}
		
		$orderid         = elex_dhl_get_order_id($order);
		$woo_dhl_wrapper = new wf_dhl_parcel_woocommerce_shipping_admin_helper();
		$manifest_data   = $woo_dhl_wrapper->get_manifest($orderid, $mani_date);
		if ($manifest_data) {
			header('Content-Type: application/pdf');
			header('Content-disposition: attachment; filename="exportdoc-' . $orderid . '.pdf"');
			print( $manifest_data ); 
			exit;
		}
	}
	public function wf_add_dhl_parcel_metabox() {
		global $post;
		if (!$post && !$_GET['id']) {
            return;
        }
        if( isset( $_GET['id'] ) ){
            $post = get_post( $_GET['id'] );
        }
		
		if ( in_array( $post->post_type, array('shop_order') )) {
			$order = $this->wf_load_order($post->ID);
			if (!$order) { 
				return;
			}
			$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
			
			add_meta_box('wf_dhl_parcel_metabox', __('DHL Parcel', 'wf-shipping-dhl'), array($this, 'wf_dhl_parcel_metabox_content'), $screen, 'side', 'default');
		}
	}

	public function wf_dhl_parcel_metabox_content() {
		global $post;
		
		if (!$post && !$_GET['id']) {
            return;
        }
        if( isset( $_GET['id'] ) ){
            $post = get_post( $_GET['id'] );
        }


		$order = $this->wf_load_order($post->ID);
		if (!$order) { 
			return;
		}  

		$orderid = elex_dhl_get_order_id($order);

		$shipmentIds          = $order->get_meta('_wf_woo_dhl_shipmentId');
		$shipmentErrorMessage = $order->get_meta('_wf_woo_dhl_shipmentErrorMessage');
		
		//Only Display error message if the process is not complete. If the Invoice link available then Error Message is unnecessary
		if (!empty($shipmentErrorMessage)) {
			echo '<div class="error"><p>' . sprintf( __( 'Error message: %s <br><b>At least one shipment have occurred an error, proceeding might loss some packages. Please Remove shipment and Create again.</b>', 'wf-shipping-dhl' ), $shipmentErrorMessage) . '</p></div>';
			//delete_post_meta($post->ID,'_wf_woo_dhl_shipmentErrorMessage');
		}
		
		$shipment_void_ids = $order->get_meta('wf_woo_dhl_parcel_shipment_void');
		if ( !empty($shipment_void_ids) ) {
			echo '<div class="notice-warning notice"><p>' . sprintf( 'Press Clear History button to clear all the data and make avail create shipment again.' ) . '</p></div>';
		}
		
		echo '<ul>';
		$selected_sevice = $this->wf_get_shipping_service($order, true); 
		if (!empty($shipmentIds)) {
			if (!empty($selected_sevice) && array_key_exists($selected_sevice, $this->services)) {
				$service_time = '';
				if ($selected_sevice == 'DeliveryOnTime') {
					$service_time = '(' .$order->get_meta('_wf_woo_dhl_service_time') . ')';
				}
				echo '<li>Shipping service: <strong>' . $this->services[$selected_sevice] . $service_time . '</strong></li>';     
			}
			foreach ($shipmentIds as $shipmentId) {
				$trackerCode = $order->get_meta('_wf_woo_dhl_trackerId_' . $shipmentId);
				echo '<li><strong>Shipment #:</strong> ' . $shipmentId;
				echo '<li><strong>Tracker Number #:</strong><a href="https://track.dhlparcel.co.uk/' . $trackerCode . '" target="_blank">' . $trackerCode;
				echo '<hr>';
				$packageDetailForTheshipment = $order->get_meta('wf_woo_dhl_packageDetails_' . $shipmentId, true);
				$is_manifest_generated       = false;
				
				if (!empty($packageDetailForTheshipment)) {
					foreach ($packageDetailForTheshipment as $dimentionValue) {
						echo $dimentionValue;
					}
				}
				$shipping_label = $order->get_meta('_wf_woo_dhl_shippingLabel_' . $shipmentId);

		//         if($shipping_label){
		//     header('Content-Type: application/pdf');
		//     header('Content-disposition: attachment; filename="exportdoc-' . $orderid . '.pdf"');
		//     print($shipping_label); 
		//     exit;
		// }

				 $print_url = admin_url('/post.php?wf_dhl_parcel_print_label=' . base64_encode($shipmentId . '|' . $post->ID));
				if (!empty($shipping_label)) {
					$download_url          = $shipping_label;
					$is_manifest_generated = $order->get_meta( '_wf_woo_dhl_manifest_' . $shipmentId );
					$is_printed            = $order->get_meta( '_wf_woo_dhl_printed_' . $shipmentId );
					?>
					<a class="button tips" href="<?php echo $print_url; ?>"data-tip="<?php _e('Print Label', 'wf-shipping-dhl'); ?>" <?php echo $is_manifest_generated?'':'target="_blank"'; ?>><?php _e('Print Label', 'wf-shipping-dhl'); ?></a>
					<?php

					
				}
				echo '<hr style="border-color:#0074a2"></li>';
			}

			$shipment_void_ids = $order->get_meta('wf_woo_dhl_parcel_shipment_void');
			if (is_array($shipmentIds) && is_array($shipment_void_ids)) {
				if ( count($shipmentIds) == count($shipment_void_ids)) {
					$clear_history_link = admin_url( '?wf_dhl_parcel_clearhistory=' . $post->ID );
					?>
								   
					<li><a class="button button-primary tips" href="<?php echo $clear_history_link; ?>" data-tip="<?php _e('Clear History', 'wf-shipping-dhl'); ?>"><?php _e('Clear History', 'wf-shipping-dhl'); ?></a></li>
					<?php 
				}
			} else {
				$generate_url = admin_url('?wf_dhl_parcel_removeshipment=' . $post->ID);
				?>
				<li><a class="button onclickdisable dhl_parcel_remove_shipment" href="<?php echo $generate_url; ?>"><?php _e('Remove Shipment', 'wf-shipping-dhl'); ?></a></li>           
				<?php                               
			}
		} else {           
			$generate_url = admin_url('/post.php?wf_dhl_parcel_createshipment=' . $post->ID);
			
			$stored_packages =  $order->get_meta('_wf_dhl_parcel_stored_packages');
			if (empty($stored_packages)) {
				?>
				<a class="button button-primary tips dhl_parcel_generate_packages" href="<?php echo admin_url( '/?wf_dhl_parcel_generate_packages=' . $post->ID ); ?>" data-tip="<?php _e( 'Generate Packages', 'wf-shipping-dhl' ); ?>"><?php _e( 'Generate Packages', 'wf-shipping-dhl' ); ?></a><hr style="border-color:#0074a2">
			<?php
			} else {
				echo '<strong>' . __( 'Step 2: Initiate your shipment.', 'wf-shipping-dhl' ) . '</strong></br>';
				echo '<ul>';
					// echo '<li><label for="wf_dhl_parcel_return"><input type="checkbox" style="" id="wf_dhl_parcel_return" name="wf_dhl_parcel_return" value="1" class="">' . __('Include Return Label', 'wf-shipping-dhl') . '</label></li>';
					
					//Need to Get clarify from DHL Team
					// echo '<li><label for="wf_dhl_parcel_cod"><input type="checkbox" style="" id="wf_dhl_parcel_cod" name="wf_dhl_parcel_cod" value="1" class="">' . __('Cash On Delivery', 'wf-shipping-dhl') . '</label></li>';
					
					echo '<li>';
					echo '<h4>' . __( 'Package(s)' , 'wf-shipping-dhl') . ': </h4>';
					echo '<table id="wf_dhl_parcel_package_list" class="wf-shipment-package-table">';                    
						echo '<tr>';
							echo '<th>' . __('Package Description', 'wf-shipping-dhl') . '</th>';
							echo '<th>' . __('Wt.', 'wf-shipping-dhl') . '</br>(' . $this->weight_unit . ')</th>';
							echo '<th>' . __('L', 'wf-shipping-dhl') . '</br>(' . $this->dimension_unit . ')</th>';
							echo '<th>' . __('W', 'wf-shipping-dhl') . '</br>(' . $this->dimension_unit . ')</th>';
							echo '<th>' . __('H', 'wf-shipping-dhl') . '</br>(' . $this->dimension_unit . ')</th>';
							echo '<th>&nbsp;</th>';
						echo '</tr>';
				if ( empty($stored_packages[0]) ) {
					$stored_packages[0] = $this->get_dummy_package();
				}
				foreach ($stored_packages as $stored_package_key =>  $stored_package) {
					if (isset($stored_package['package_title'])) {
						$package_title = $stored_package['package_title'];
					} else {
						$package_title = 'Package';
					}
					$dimensions =   $this->get_dimension_from_package($stored_package);
					if (is_array($dimensions)) {
						?>
								<tr>
									<td style="width:25%;padding:5px;border-radius:5px;margin-left:4px;"><small id="dhl_parcel_package_title"><?php echo '<b>' . $package_title; ?></small></td>
									<td><input type="text" id="dhl_parcel_manual_weight" name="dhl_parcel_manual_weight[]" size="2" value="<?php echo $dimensions['Weight']; ?>" /></td> 
									<td><input type="text" id="dhl_parcel_manual_length" name="dhl_parcel_manual_length[]" size="2" value="<?php echo $dimensions['Length']; ?>" /></td>
									<td><input type="text" id="dhl_parcel_manual_width" name="dhl_parcel_manual_width[]" size="2" value="<?php echo $dimensions['Width']; ?>" /></td>
									<td><input type="text" id="dhl_parcel_manual_height" name="dhl_parcel_manual_height[]" size="2" value="<?php echo $dimensions['Height']; ?>" /></td>
									<td>&nbsp;</td>
								</tr>
								<?php
					}
				}
					echo '</table>';
				if ($order->get_shipping_country() == 'DE') {
					echo '<a class="wf-action-button wf-add-button" style="font-size: 12px;" id="wf_dhl_parcel_add_package">' . __('Add Package', 'wf-shipping-dhl') . '</a>';
				}
					echo '</li>';
				echo '</ul>';
				?>
				<li><a class="button tips onclickdisable dhl_parcel_create_shipment" href="#" data-tip="<?php _e('Create Shipment', 'wf-shipping-dhl'); ?>"><?php _e('Create Shipment', 'wf-shipping-dhl'); ?></a></li>
				<a class="button button-primary tips dhl_parcel_generate_packages" href="<?php echo admin_url( '/?wf_dhl_parcel_generate_packages=' . $post->ID ); ?>" data-tip="<?php _e( 'Re-Generate Packages', 'wf-shipping-dhl' ); ?>"><?php _e( 'Re-Generate Packages', 'wf-shipping-dhl' ); ?></a><hr style="border-color:#0074a2">
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery('#wf_dhl_parcel_add_package').on("click", function(){
							var new_row = '<tr>';
								new_row     += '<td style="width:25%;padding:5px;border-radius:5px;margin-left:4px;"><small id="dhl_parcel_package_title">Additional Package</small></td>';
								new_row     += '<td><input type="text" id="dhl_parcel_manual_weight" name="dhl_parcel_manual_weight[]" size="2" value="0"></td>';
								new_row     += '<td><input type="text" id="dhl_parcel_manual_length" name="dhl_parcel_manual_length[]" size="2" value="0"></td>';                             
								new_row     += '<td><input type="text" id="dhl_parcel_manual_width" name="dhl_parcel_manual_width[]" size="2" value="0"></td>';
								new_row     += '<td><input type="text" id="dhl_parcel_manual_height" name="dhl_parcel_manual_height[]" size="2" value="0"></td>';
								new_row     += '<td><a class="wf_dhl_parcel_package_line_remove">&#x26D4;</a></td>';
							new_row     += '</tr>';
							
							jQuery('#wf_dhl_parcel_package_list tr:last').after(new_row);
						});
						
						jQuery(document).on('click', '.wf_dhl_parcel_package_line_remove', function(){
							jQuery(this).closest('tr').remove();
						});
					});
					
					jQuery("a.dhl_parcel_create_shipment").on("click", function() {
						var packages_titles_array   =   jQuery("small[id='dhl_parcel_package_title']").map(function(){return jQuery(this).text();}).get();
						var package_titles       =   JSON.stringify(packages_titles_array);

						var manual_weight_arr   =   jQuery("input[id='dhl_parcel_manual_weight']").map(function(){return jQuery(this).val();}).get();
						var manual_weight       =   JSON.stringify(manual_weight_arr);
						
						var manual_height_arr   =   jQuery("input[id='dhl_parcel_manual_height']").map(function(){return jQuery(this).val();}).get();
						var manual_height       =   JSON.stringify(manual_height_arr);
						
						var manual_width_arr    =   jQuery("input[id='dhl_parcel_manual_width']").map(function(){return jQuery(this).val();}).get();
						var manual_width        =   JSON.stringify(manual_width_arr);
						
						var manual_length_arr   =   jQuery("input[id='dhl_parcel_manual_length']").map(function(){return jQuery(this).val();}).get();
						var manual_length       =   JSON.stringify(manual_length_arr);
						
						var manual_insurance_arr    =   jQuery("input[id='dhl_parcel_manual_insurance']").map(function(){return jQuery(this).val();}).get();
						var manual_insurance        =   JSON.stringify(manual_insurance_arr);
						
						
						location.href = '<?php echo $generate_url; ?>' 
						+ '&package_title=' + package_titles
						+ '&weight=' + manual_weight 
						+ '&length=' + manual_length
						+ '&width=' + manual_width
						+ '&height=' + manual_height
						+ '&insurance=' + manual_insurance
						+ '&return_label=' + jQuery('#wf_dhl_parcel_return').is(':checked')
						+ '&cod=' + jQuery('#wf_dhl_parcel_cod').is(':checked');
					   return false;
					});
				</script>
				<?php
			}           
			?>
			<script type="text/javascript">
				jQuery("a.dhl_parcel_generate_packages").on("click", function() {
					location.href = this.href;
				});
			</script>

			<?php
		}
		echo '</ul>';
		?>
		<script>
		
		jQuery("a.dhl_parcel_remove_shipment").on("click", function() {
			location.href = '<?php echo admin_url('?wf_dhl_parcel_removeshipment=' . $post->ID); ?>';
			return false;           
		});
		jQuery(document).ready(function(){
			toggleServiceTimeBlock();
		});
		function toggleServiceTimeBlock(){
			service=jQuery('#dhl_manual_service').val();
			if(service=='DeliveryOnTime'){
				jQuery('#service_time_block').show('slow');
			}else{
				jQuery('#service_time_block').hide('slow');
			}
		}
		</script>       
		<?php
	}

	private function get_dummy_package() {
		return array(
			'Dimensions' => array(
				'Length' => 0,
				'Width' => 0,
				'Height' => 0,
			),
			'InsuredValue' => array(
				'Amount' => 0
			),
			'Weight' => array(
				'Value' => 0,
			)
		);
	}
	
	public function wf_dhl_parcel_exportdoc() {
		$shipmentDetails = explode('|', base64_decode($_GET['wf_dhl_parcel_exportdoc']));
		if (count($shipmentDetails) != 2) {
			exit;
		}       
		$shipmentId = $shipmentDetails[0]; 
		$post_id    = $shipmentDetails[1];
		$order = wc_get_order($post_id);
		$export_doc = $order->get_meta('_wf_woo_dhl_export_doc_' . $shipmentId);
		header('Content-Type: application/pdf');
		header('Content-disposition: attachment; filename="exportdoc-' . $shipmentId . '.pdf"');
		print( base64_decode($export_doc) ); 
		exit;
	}
	
	public function wf_dhl_parcel_create_manifest() {
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) {          
			return;
		}     
		$shipmentDetails = explode('|', base64_decode($_GET['wf_dhl_parcel_create_manifest']));
		if (count($shipmentDetails) != 2) {
			exit;
		}       
		$shipmentId = $shipmentDetails[0]; 
		$post_id    = $shipmentDetails[1];     
		$this->wf_create_manifest($post_id, $shipmentId);
	}

	public function wf_dhl_parcel_print_label() {
		 $shipmentDetails = explode('|', base64_decode($_GET['wf_dhl_parcel_print_label']));
		if (count($shipmentDetails) != 2) {
			exit;
		}       
		$shipmentId = $shipmentDetails[0]; 
		$post_id    = $shipmentDetails[1]; 
 
		$order = wc_get_order($post_id);
		 $shipping_label = $order->get_meta('_wf_woo_dhl_shippingLabel_' . $shipmentId);
		 $decoded = base64_decode($shipping_label);

		 $file = 'shipment_label -' . $post_id . '.pdf';
		 file_put_contents($file, $decoded);
		if ($shipping_label) {
			header('Content-Type: application/pdf');
			header('Content-disposition: attachment; filename="shipment_label-' . $post_id . '.pdf"');
			readfile($file);
			exit;
		}

	}
	private function wf_create_manifest( $order_id, $shipment_id) {        
		if ( ! class_exists( 'wf_dhl_parcel_woocommerce_shipping_admin_helper' ) ) {
			include_once 'class-wf-dhl-parcel-woocommerce-shipping-admin-helper.php';
		}        
		$woodhlwrapper = new wf_dhl_parcel_woocommerce_shipping_admin_helper();
		$woodhlwrapper->createManifest($order_id, $shipment_id);
		wp_redirect(admin_url('/post.php?post=' . $order_id . '&action=edit'));
		exit;
	}
	public function get_dimension_from_package( $package) {
		
		$dimensions =   array(
			'Length'        =>  0,
			'Width'         =>  0,
			'Height'        =>  0,
			'Weight'        =>  0,
			'InsuredValue'  =>  0,
		);
		
		if ( !is_array($package) || empty($package) ) {
			return $dimensions;
		}
		if (isset($package['Dimensions'])) {
			$dimensions['Length'] =   $package['Dimensions']['Length'];
			$dimensions['Width']  =   $package['Dimensions']['Width'];
			$dimensions['Height'] =   $package['Dimensions']['Height'];
		}
		
		$weight =   $package['Weight']['Value'];
			
		if (isset($package['InsuredValue']['Amount'])) {
			$dimensions['InsuredValue'] =   $package['InsuredValue']['Amount'];
		}
		$dimensions['Weight'] =   $weight;
		return $dimensions;
	}
	
	// Bulk Label Printing
	
	function init_bulk_printing() {
		add_action('admin_footer', array($this, 'add_bulk_print_option'));
		add_action('load-edit.php', array($this, 'perform_bulk_label_actions'));
		add_action('woocommerce_admin_order_actions_end', array($this, 'label_printing_buttons'));
	}
	
	function add_bulk_print_option() {
		global $post_type;
		if( empty($post_type) && isset($_GET['page'])){
			$post_type = $_GET['page'];
		}
		if ($post_type == 'shop_order' ) {
			if (!empty($this->enabled ) && $this->enabled  === 'yes' ) {
				?>

		<script type="text/javascript">
		  jQuery(document).ready(function() {
			jQuery('<option>').val('dhl_parcel_bulk_create_shipment').text('<?php _e('Create DHL Parcel Shipment', 'wf-shipping-dhl'); ?>').appendTo("select[name='action']");
			jQuery('<option>').val('dhl_parcel_bulk_create_shipment').text('<?php _e('Create DHL Parcel Shipment', 'wf-shipping-dhl'); ?>').appendTo("select[name='action2']");
			
			// jQuery('<option>').val('dhl_parcel_bulk_void_shipment').text('<?php _e('Void DHL Parcel Shipment', 'wf-shipping-dhl'); ?>').appendTo("select[name='action']");
			// jQuery('<option>').val('dhl_parcel_bulk_void_shipment').text('<?php _e('Void DHL Parcel Shipment', 'wf-shipping-dhl'); ?>').appendTo("select[name='action2']");
		  });
		</script>
		<?php
			}
		}
	}
	
	function perform_bulk_label_actions( $post_id = '') {
		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ): '';	
		$order_ids = isset( $_REQUEST['post'] ) ? map_deep( wp_unslash( $_REQUEST['post'] ) , 'sanitize_text_field' ) : ( isset( $_REQUEST['order'] ) ? map_deep( wp_unslash( $_REQUEST['order'] ) , 'sanitize_text_field' ) : (isset( $_REQUEST['id'] ) ? map_deep( wp_unslash( $_REQUEST['id'] ) , 'sanitize_text_field' ) : array() ) );
		

		if ($action == 'dhl_parcel_bulk_create_shipment') {
			if (isset($order_ids) && is_array($order_ids)) {
				update_option('bulk_create_shipment', true);
				foreach ($order_ids as $order_id) {
					if (!$this->has_shipment($order_id)) {
						$order = $this->wf_load_order($order_id);
						if ($this->wf_create_shipment($order)) {
							wf_admin_notice::add_notice(sprintf(__('Order #%d: Shipment generated sucessfully.', 'wf-shipping-dhl'), $order_id), 'notice');
						}
					} else {
						wf_admin_notice::add_notice(sprintf(__('Order #%d: Please remove existing shipments before creating new.', 'wf-shipping-dhl'), $order_id), 'warning');
					}           
				}
			} else {
				update_option('bulk_create_shipment', false);
				wf_admin_notice::add_notice(__('Please select atleast one order', 'wf-shipping-dhl'));
			}
		} elseif ($action == 'dhl_parcel_bulk_void_shipment') {
			if (isset($order_ids) && is_array($order_ids)) {
				foreach ($order_ids as $order_id) {
					if ($this->has_shipment($order_id)) {
						$order = $this->wf_load_order($order_id);
						if ($this->wf_remove_shipment($order)) {
							$this->wf_clear_history( $order );
							wf_admin_notice::add_notice(sprintf(__('Order #%d: Shipment voided.', 'wf-shipping-dhl'), $order_id), 'notice');
						}
					} else {
						wf_admin_notice::add_notice(sprintf(__('Order #%d: No shipment is there to void.', 'wf-shipping-dhl'), $order_id), 'warning');
					}
				}
			} else {
				wf_admin_notice::add_notice(__('Please select atleast one order', 'wf-shipping-dhl'));
			}
		}
	}
	public function wf_auto_label_generate_order_dhl_packet( $post_id ) {
		if ( ! class_exists( 'wf_dhl_parcel_woocommerce_shipping_admin_helper' ) ) {
			include_once 'class-wf-dhl-parcel-woocommerce-shipping-admin-helper.php';
		}
		$this->debug   = false;
		$order         = $this->wf_load_order( $post_id );
		$woodhlwrapper = new wf_dhl_parcel_woocommerce_shipping_admin_helper();
		$serviceCode   = $this->wf_get_shipping_service($order, false);
		$woodhlwrapper->generate_packages($order, $serviceCode, 'true');
		$this->wf_create_shipment($order, 'true');
	 
	}
	function has_shipment( $order_id) {
		$order = wc_get_order( $order_id );
		$shipmentIds = $order->get_meta('_wf_woo_dhl_shipmentId');
		if (empty($shipmentIds) || !is_array($shipmentIds) || sizeof($shipmentIds)<=0) {
			return false;
		} else {
			return true;
		}
	}
	
	function get_order_label_links( $order_id) {
		$links       =   array();
		$order = wc_get_order( $order_id );
		$shipmentIds = $order->get_meta('_wf_woo_dhl_shipmentId');
		if (is_array($shipmentIds)) {
			foreach ($shipmentIds as $shipmentId) {
				// Label
				$shipping_label = $order->get_meta('_wf_woo_dhl_shippingLabel_' . $shipmentId);
				if ($shipping_label) {
					$links[] =   $shipping_label;
				}
				
				// Export Doc
				$export_doc =  $order->get_meta('_wf_woo_dhl_export_doc_' . $shipmentId);
				if ($export_doc) {
					$links[] =   $export_doc;
				}
			}
		}
		return $links;
	}
	function label_printing_buttons( $order) {
		$actions =   array();

		$orderid = elex_dhl_get_order_id($order);
		if ($this->has_shipment($orderid)) {
			$labels =   $this->get_order_label_links($orderid);
			
			foreach ($labels as $label_no => $label_link) {
				$actions['print_label' . $label_no] =   array(
					'url'   =>  $label_link,
					'name'  =>  __('Print Label', 'wf-shipping-dhl'),
					'action'=>  'wf-print-label'
				);
			}
		}       
		foreach ( $actions as $action ) {
			printf( '<a class="button tips %s" href="%s" data-tip="%s" target="_blank">%s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
		}
	}
}
new wf_dhl_parcel_woocommerce_shipping_admin();
?>
