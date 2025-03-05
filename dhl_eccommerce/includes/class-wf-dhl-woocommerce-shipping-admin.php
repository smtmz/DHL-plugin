<?php
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

if (!class_exists('wf_dhl_ecommerce_shipping_admin')) {
	class wf_dhl_ecommerce_shipping_admin {
	public $settings;
	public $custom_services;
	public $label_enabled;
	public $image_type;
	public $services;
	public $debug;
	public $default_domestic_service;
	public $default_international_service;
	public 	$weight_unit;
	public $dim_unit;

		public function __construct() {
			$this->settings                      = get_option( 'woocommerce_' . WF_DHL_ECOMMERCE_ID . '_settings', null );
			$this->custom_services               = isset( $this->settings['services'] ) ? $this->settings['services'] : '';
			$this->label_enabled                 = isset( $this->settings['label_enabled'] ) ? $this->settings['label_enabled'] : '';
			$this->image_type                    = isset( $this->settings['image_type'] ) ? $this->settings['image_type'] : '';
			$this->services                      = include  'data-wf-service-codes.php' ;
			$this->debug                         = ( $bool = isset($this->settings[ 'debug' ]) ? $this->settings[ 'debug' ] : '' ) && $bool == 'yes' ? true : false;
			$this->default_domestic_service      = isset( $this->settings['default_domestic_service'] ) ? $this->settings['default_domestic_service'] : '';
			$this->default_international_service = isset( $this->settings['default_international_service'] ) ? $this->settings['default_international_service'] : '';
			if ( isset($this->settings['dimension_weight_unit']) && $this->settings['dimension_weight_unit'] == 'KG_CM' ) {
				$this->weight_unit = 'KGS';
				$this->dim_unit    = 'CM';
			} else {
				$this->weight_unit = 'LBS';
				$this->dim_unit    = 'IN';
			}
			if ( isset( $_GET['page']) && 'wc-orders' === $_GET['page']  ) {
				add_action('init', array( $this, 'wf_orders_bulk_action_dhl_ecommerce' ) ); //to handle post id for bulk actions     

			}else{
				   add_action('load-edit.php', array( $this, 'wf_orders_bulk_action_dhl_ecommerce' ) ); //to handle post id for bulk actions     
			}
			
			add_action('admin_notices', array( $this, 'bulk_label_admin_notices_dhl_ecommerce') );

			if (is_admin() && $this->label_enabled === 'yes') {
				add_action('add_meta_boxes', array($this, 'wf_add_dhl_metabox'));
			}

			if ( isset( $_GET['wf_dhl_ecommerce_generate_packages'] ) ) {
				add_action( 'init', array( $this, 'wf_dhl_generate_packages_ec' ), 15 );
			}
			if (isset($_GET['wf_dhl_ecommerce_createshipment'])) {
				add_action('init', array($this, 'wf_dhl_ecommerce_createshipment'));
			}
		
			if (isset($_GET['wf_dhl_viewlabel_ec'])) {
				add_action('init', array($this, 'wf_dhl_viewlabel_ec'));
			}
		
			if (isset($_GET['wf_dhl_ec_view_commercial_invoice'])) {
				add_action('init', array($this, 'wf_dhl_ec_view_commercial_invoice'));
			}

			if (isset($_GET['elex_dhl_eCommerce_delete_label'])) {
				add_action('init', array($this, 'elex_dhl_eCommerce_delete_label'));
			}
		
		}   

		function wf_dhl_generate_packages_ec() {

			if ( !$this->wf_user_permission() ) {
				echo "You don't have admin privileges to view this page.";
				exit;
			}
		
			$wfdhlmsg = '';
			$post_id  =   base64_decode($_GET['wf_dhl_ecommerce_generate_packages']);
			$order    = $this->wf_load_order( $post_id );
			if ( !$order ) {
return;
			}
		
			if ( ! class_exists( 'wf_dhl_ecommerce_shipping_admin_helper' ) ) {
			include_once 'class-wf-dhl-woocommerce-shipping-admin-helper.php';
			}
		
			$woodhlwrapper = new wf_dhl_ecommerce_shipping_admin_helper();
			$packages      =   $woodhlwrapper->wf_get_package_from_order($order);
		
			foreach ($packages as $key => $package) {
				$package_data[] = $woodhlwrapper->get_dhl_packages($package);
			}
			$order->update_meta_data( '_wf_dhl_stored_packages_ec', $package_data );
			$order->save();
		
			wp_redirect( admin_url( '/post.php?post=' . $post_id . '&action=edit') );
			exit;
		}

		function bulk_label_admin_notices_dhl_ecommerce() { 
			global $post_type, $pagenow;
			if( empty($post_type) && isset($_GET) && !empty($_GET['page'])){
				$post_type = $_GET['page'];
			}
			if ( $pagenow == 'edit.php' && $post_type == 'shop_order' && isset($_REQUEST['bulk_label_dhl_ecommerce']) ) {
				if (isset($_REQUEST['ids']) && !empty($_REQUEST['ids'])) {
					$order_ids = explode( ',', $_REQUEST['ids'] );
				}
			
				$faild_ids_str     = '';
				$success_ids_str   = '';
				$already_exist_arr = array();
				if (isset($_REQUEST['already_exist']) && !empty($_REQUEST['already_exist'])) {
					$already_exist_arr = explode( ',', $_REQUEST['already_exist'] );
				}

				if (isset($order_ids) && !empty($order_ids)) {
					foreach ($order_ids as $key => $id) {
						$order = wc_get_order( $id );
						$dhl_shipment_err = $order->get_meta( 'wf_woo_dhl_ecommerceshipmentErrorMessage' );

						if ( !empty($dhl_shipment_err) ) {
							$faild_ids_str .= $id . ', ';
						} elseif ( !in_array( $id, $already_exist_arr ) ) {
							$success_ids_str .= $id . ', '; 
						}
					}
				}

				$faild_ids_str   = rtrim($faild_ids_str, ', ');
				$success_ids_str = rtrim($success_ids_str, ', ');

				if ( $faild_ids_str != '' ) {
					echo '<div class="error"><p>' . __('Create shipment is failed for following order(s) ' . $faild_ids_str, 'wf-shipping-dhl') . '</p></div>';
				}
			
				if ( $success_ids_str != '' ) {
					echo '<div class="updated"><p>' . __('Successfully created shipment for following order(s) ' . $success_ids_str, 'wf-shipping-dhl') . '</p></div>';
				}

				if ( isset( $_REQUEST['already_exist'] ) && $_REQUEST['already_exist'] != '' ) {
					echo '<div class="notice notice-success"><p>' . __('Shipment already exist for following order(s) ' . $_REQUEST['already_exist'] , 'wf-shipping-dhl') . '</p></div>';
				}

			}
		}

		/**
		* function to Delete?Reset the shipment created for the order
		*/
		public function elex_dhl_eCommerce_delete_label() {
			$order_id           = $_GET['elex_dhl_eCommerce_delete_label'];
			$order_data         = wc_get_order( $order_id);
			$order = wc_get_order($this->order_id);
			$order_shipment_ids = $order->get_meta('wf_woo_dhl_ecommerceshipmentId');
			foreach ($order_shipment_ids as $order_shipment_id) {
				
				$order->delete_meta_data('wf_woo_dhl_ecommerceshippingLabel_' . $order_shipment_id);

				$order_data->delete_meta_data('wf_woo_dhl_ecommerceshipping_commercialInvoice_' . $order_shipment_id);
			}
			$order_data->delete_meta_data('wf_woo_dhl_ecommerceservice_code' );
			$order_data->delete_meta_data('wf_woo_dhl_ecommerceshipmentId' );

            $order_data->save();
			$order->save();
			wp_redirect( admin_url( '/post.php?post=' . $order_id . '&action=edit') );
			exit;
		}

		public function wf_orders_bulk_action_dhl_ecommerce() {
			$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ): '';	
			$order_ids = isset( $_REQUEST['post'] ) ? map_deep( wp_unslash( $_REQUEST['post'] ) , 'sanitize_text_field' ) : ( isset( $_REQUEST['order'] ) ? map_deep( wp_unslash( $_REQUEST['order'] ) , 'sanitize_text_field' ) : (isset( $_REQUEST['id'] ) ? map_deep( wp_unslash( $_REQUEST['id'] ) , 'sanitize_text_field' ) : array() ) );
			$page_url = '';
			 if( isset( $_REQUEST['post_type'] ) && 'shop_order' === $_REQUEST['post_type'] ){
			 $page_url = admin_url('edit.php?post_type=shop_order');
			 }elseif( isset( $_REQUEST['page'] ) && 'wc-orders' === $_REQUEST['page'] ){
			 $page_url = admin_url('admin.php?page=wc-orders');
			 }
			
			if ($action == 'create_ecommerce_shipment_dhl') {
				//forcefully turn off debug mode, otherwise it will die and cause to break the loop.
				$this->debug     = false;
				$label_exist_for = '';
				foreach ( $order_ids as $post_id) {
					$order = $this->wf_load_order( $post_id );
					if (!$order) { 
					return;
					}
					$orderid = elex_dhl_get_order_id($order);
				
					$shipmentIds = $order->get_meta('wf_woo_dhl_ecommerceshipmentId');
					if ( !empty($shipmentIds) ) {
						$label_exist_for .= $orderid . ', ';
					} else {
						$this->wf_create_shipment($order);
					}
				}
			
				$sendback = add_query_arg( array(
				'bulk_label_dhl_ecommerce' => 1, 
				'ids' => join(',', $_REQUEST['post'] ),
				'already_exist' =>rtrim( $label_exist_for, ', ' )
				), admin_url( $page_url) );
			
				wp_redirect($sendback);
				exit();
			}
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
	
		public function wf_dhl_ecommerce_createshipment() {
			$user_ok = $this->wf_user_permission();
			if (!$user_ok) {          
			return;
			}
		
			$order = $this->wf_load_order($_GET['wf_dhl_ecommerce_createshipment']);
			if (!$order) { 
			return;
			}
		
		
			$this->wf_create_shipment($order);
		
			if ( $this->debug ) {
				//dont redirect when debug is printed
				die();
			} else {           
				wp_redirect(admin_url('/post.php?post=' . $_GET['wf_dhl_ecommerce_createshipment'] . '&action=edit'));
				exit;
			}
		
		}
	
		public function wf_dhl_viewlabel_ec() {
			$shipmentDetails = explode('|', base64_decode($_GET['wf_dhl_viewlabel_ec']));

			if (count($shipmentDetails) != 2) {
				exit;
			}
			$shipmentId     = $shipmentDetails[0]; 
			$post_id        = $shipmentDetails[1];
			$order = wc_get_order( $post_id );
			$shipping_label = $order->get_meta( 'wf_woo_dhl_ecommerceshippingLabel_' . $shipmentId );
			header('Content-Type: application/' . $this->image_type);
			header('Content-disposition: attachment; filename="ShipmentArtifact-' . $shipmentId . '.' . $this->image_type . '"');
			print( base64_decode($shipping_label) ); 
			exit;
		}
	
		public function wf_dhl_ec_view_commercial_invoice() {
			$invoiceDetails = explode('|', base64_decode($_GET['wf_dhl_ec_view_commercial_invoice']));

			if (count($invoiceDetails) != 2) {
				exit;
			}
			$image_type         =   'pdf'; //commercial invoice generated in pdf only
			$shipmentId         = $invoiceDetails[0]; 
			$post_id            = $invoiceDetails[1]; 
			$order = wc_get_order( $post_id );
			$commercial_invoice = $order->get_meta('wf_woo_dhl_ecommerceshipping_commercialInvoice_' . $shipmentId, true);
			header('Content-Type: application/' . $image_type);
			header('Content-disposition: attachment; filename="CommercialInvoice-' . $shipmentId . '.' . $image_type . '"');
			print( base64_decode($commercial_invoice) ); 
			exit;
		}
	
		private function wf_is_service_valid_for_country( $order, $service_code) {
			return true; 
		}
		private function wf_get_mail_type() {
			if (!empty($_GET['dhl_eccommerce_mail_type'])) {          
				return $_GET['dhl_eccommerce_mail_type'];           
			} else {
				return '2';
			}
		}
		private function wf_get_expacted_delivery() {
			if (!empty($_GET['dhl_eccommerce_delivery_date'])) {          
				return $_GET['dhl_eccommerce_delivery_date'];           
			} else {
				return '1';
			}
		}

	
		private function wf_get_shipping_service( $order, $retrive_from_order = false) {
		
			if ($retrive_from_order == true) {
				$orderid      = elex_dhl_get_order_id($order);
				$service_code = $order->get_meta('wf_woo_dhl_ecommerceservice_code');
				if (!empty($service_code)) { 
				return $service_code;
				}
			}

			if (!empty($_GET['dhl_ecommerce_shipping_service'])) {            
				return $_GET['dhl_ecommerce_shipping_service'];         
			}

			
			$is_international = ( elex_dhl_get_order_shipping_country($order) == WC()->countries->get_base_country() ) ? false : true;
			if ( $is_international ) {
				if (!empty( $this->default_international_service) ) {
				return $this->default_international_service;
				}
			} elseif ( !empty($this->default_domestic_service) ) {
				return $this->default_domestic_service;
			}

			//TODO: Take the first shipping method. It doesnt work if you have item wise shipping method
			$shipping_methods = $order->get_shipping_methods();
		
			if ( ! $shipping_methods ) {
				return '';
			}
	
			$shipping_method = array_shift($shipping_methods);

			return str_replace(WF_DHL_ECOMMERCE_ID . ':', '', $shipping_method['method_id']);
		}
	
		public function wf_create_shipment( $order) {    
			if ( ! class_exists( 'wf_dhl_ecommerce_shipping_admin_helper' ) ) {
			include_once 'class-wf-dhl-woocommerce-shipping-admin-helper.php';
			}
		
			$woodhlwrapper     = new wf_dhl_ecommerce_shipping_admin_helper();
			$serviceCode       = $this->wf_get_shipping_service($order, false);
			$mail_type         = $this->wf_get_mail_type();
			$expected_delivery = $this->wf_get_expacted_delivery();
		
			$orderid = elex_dhl_get_order_id($order);
			$woodhlwrapper->print_label($order, $serviceCode, $orderid, $mail_type, $expected_delivery);

		}
	
		public function wf_add_dhl_metabox() {
			global $theorder;
			
			if (isset($theorder) && $theorder instanceof WC_Abstract_Order) {
					$order_id             = $theorder->get_id();
					$order                = wc_get_order( $order_id );
					if (!$order) {
						return;
					}
					$screen = wc_get_container()->get(CustomOrdersTableController::class)->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id('shop-order') : 'shop_order';

					add_meta_box('wf_dhl_ecommerce_metabox', __('DHL Ecommerce', 'wf-shipping-dhl'), array($this, 'wf_dhl_emetabox_content'), $screen, 'side', 'default');
			}
		}

		public function wf_dhl_emetabox_content() {
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

			if ( ! class_exists( 'wf_dhl_ecommerce_shipping_method' ) ) {
			include_once 'class-wf-dhl-woocommerce-shipping.php';
			}
		
			$ecommerce_dhl_store_obj = new wf_dhl_ecommerce_shipping_method();
		

			$shipmentIds = $order->get_meta('wf_woo_dhl_ecommerceshipmentId');
		
			$shipmentErrorMessage = $order->get_meta('wf_woo_dhl_ecommerceshipmentErrorMessage');

		
			//Only Display error message if the process is not complete. If the Invoice link available then Error Message is unnecessary
			if (!empty($shipmentErrorMessage)) {
				echo '<div class="error"><p>' . sprintf( __( 'DHL Ecommerce Create Shipment Error:%s', 'wf-shipping-dhl' ), $shipmentErrorMessage) . '</p></div>';
			}

			$delete_eCommerce_shipment = admin_url('/post.php?elex_dhl_eCommerce_delete_label=' . $post->ID);

			echo '<ul>';
			$selected_sevice = $this->wf_get_shipping_service($order, true); 
			if (!empty($shipmentIds)) {
				if (!empty($selected_sevice) && !empty($this->services[$selected_sevice]) ) {
				echo '<li>Shipping service: <strong>' . $this->services[$selected_sevice] . '</strong></li>';
				}       
			
				foreach ($shipmentIds as $shipmentId) {
					echo '<li><strong>Shipment #:</strong> ' . $shipmentId;
					echo '<hr>';
					$shipping_label =  $order->get_meta( 'wf_woo_dhl_ecommerceshippingLabel_' . $shipmentId );
					if (!empty($shipping_label)) {
						$download_url = admin_url('/post.php?wf_dhl_viewlabel_ec=' . base64_encode($shipmentId . '|' . $post->ID));?>
					<a class="button tips" href="<?php echo $download_url; ?>" data-tip="<?php _e('Print Label', 'wf-shipping-dhl'); ?>"><?php _e('Print Label', 'wf-shipping-dhl'); ?></a>
						<?php 
					}
					$commercial_invoice = $order->get_meta('wf_woo_dhl_ecommerceshipping_commercialInvoice_' . $shipmentId, true);
					if (!empty($commercial_invoice)) {
						$commercial_invoice_download_url = admin_url('/post.php?wf_dhl_ec_view_commercial_invoice=' . base64_encode($shipmentId . '|' . $post->ID));
						?>
					<a class="button tips" href="<?php echo $commercial_invoice_download_url; ?>" data-tip="<?php _e('Commercial Invoice', 'wf-shipping-dhl'); ?>"><?php _e('Commercial Invoice', 'wf-shipping-dhl'); ?></a>
						<?php 
					}
					echo '<hr style="border-color:#0074a2"></li>';
				} 
				?>
			 
			<a class="button tips" href="<?php echo $delete_eCommerce_shipment; ?>" data-tip="<?php _e('Cancel/Delete Shipment', 'wf-shipping-dhl'); ?>"><?php _e('Reset Shipment', 'wf-shipping-dhl'); ?></a>       
				<?php                               
			} else {
				$stored_packages =   $order->get_meta( '_wf_dhl_stored_packages_ec' );
				$consignee_country            = $order->get_shipping_country();
				$stored_shipment_dhl_packages = array();

				if (isset($stored_packages[0])) {
				$stored_shipment_dhl_packages = $stored_packages[0];
				}

				if (!empty($stored_shipment_dhl_packages)) {
					$access_token = $ecommerce_dhl_store_obj->elex_dhl_ecommerce_get_access_token();
					$ecommerce_dhl_store_obj->elex_dhl_ecommerce_get_shipping_services_rates($stored_shipment_dhl_packages, $access_token, $consignee_country, $post->ID, true);
					$available_shipping_services = $order->get_meta('available_shipment_services_ecommerce_dhl_elex');
				}
				if (empty($stored_packages)) {
					?>
				<a class="button button-primary tips dhl_generate_packages_ec" href="<?php echo admin_url( '/?wf_dhl_ecommerce_generate_packages=' . base64_encode($post->ID) ); ?>" data-tip="<?php _e( 'Generate Packages', 'wf-shipping-dhl' ); ?>"><?php _e( 'Generate Packages', 'wf-shipping-dhl' ); ?></a><hr style="border-color:#0074a2">
				<?php
				} else {
					$generate_url = admin_url('/post.php?wf_dhl_ecommerce_createshipment=' . $post->ID);
					echo '<li>choose service:<select class="select" id="dhl_ecommerce_manual_service">';
					if ($this->custom_services) {
						if (!empty($available_shipping_services)) {
							foreach ($available_shipping_services as $service_code => $service) {
								$service_label = str_replace(' (DHL Ecommerce)', '', $service['label']);
								echo '<option value="' . $service_code . '" >' . $service_label . '</option>';
							}
						} else {
							echo '<option value="" >No Services Found</option>';
						}
					}
					echo '</select></li>';
					// For Selecting Mail Type
					echo '<li>choose Mail Type:<select class="select" id="dhl_ecommerce_manual_mail_type">';
					echo '<option value="2">Irregular Parcel</option>';
					echo '<option value="3">Machinable Parcel</option>';
					echo '<option value="6">BPM Machinable</option>';
					echo '<option value="7">Parcel Select Mach</option>';
					echo '<option value="8">Parcel Select NonMach</option>';
					echo '<option value="9">Media Mail</option>';
					echo '<option value="20">Marketing Parcel < 6oz</option>';
					echo '<option value="30">Marketing Parcel >= 6oz</option>';
				
					echo '</select></li>';

					// For Selecting desired inco-term
					echo '<li>choose Duties Paid type:<select class="select" id="dhl_ecommerce_shipment_inco_terms">';
					echo '<option value="DDU">Delivered Duty Unpaid (DDU)</option>';
					echo '<option value="DAP">Delivered at Place (DAP)</option>';
					echo '<option value="DDP">Delivery Duty Paid (DDP)</option>';
					echo '</select></li>';

					// For Selecting Service Endorsement Type
					echo '<li>choose Service Endorsment Type:<select class="select" id="dhl_ecommerce_shipment_service_endorsement">';
					echo '<option value="1">Address Service Requested</option>';
					echo '<option value="2">Forwarding Service Requested</option>';
					echo '<option value="3">Change Service Requested</option>';
					echo '</select></li>';

					echo '<li> Expected Delivery In Days<input type="number" min="0" style="padding:5px;" id="ma_expected_delivey" value="1"></li>';
					echo '<li>';
					echo '<h4>' . __( 'Package(s)' , 'wf-shipping-dhl') . ': </h4>';
					echo '<table id="wf_dhl_package_list_ec" class="wf-shipment-package-table">';                   
						echo '<tr>';
							echo '<th>' . __('Wt.', 'wf-shipping-dhl') . '</br>(' . $this->weight_unit . ')</th>';
							echo '<th>' . __('L', 'wf-shipping-dhl') . '</br>(' . $this->dim_unit . ')</th>';
							echo '<th>' . __('W', 'wf-shipping-dhl') . '</br>(' . $this->dim_unit . ')</th>';
							echo '<th>' . __('H', 'wf-shipping-dhl') . '</br>(' . $this->dim_unit . ')</th>';
							// echo '<th>'.__('Insur.', 'wf-shipping-dhl').'</th>';
							echo '<th>&nbsp;</th>';
						echo '</tr>';
					if ( empty($stored_packages[0]) ) {
						$stored_packages[0][0] = $this->get_dhl_dummy_package();
					}
					foreach ($stored_packages as $package_group_key  =>  $package_group) {
						if ( !empty($package_group) && is_array($package_group) ) { //package group may empty if boxpacking and product have no dimensions 
							foreach ($package_group as $stored_package_key   =>  $stored_package) {
								$dimensions =   $this->get_dimension_from_package($stored_package);
								if (is_array($dimensions)) {
									?>
										<tr>
											<td><input type="text" id="dhl_manual_weight_ec" name="dhl_manual_weight_ec[]" size="2" value="<?php echo $dimensions['Weight']; ?>" /></td>     
											<td><input type="text" id="dhl_manual_length_ec" name="dhl_manual_length_ec[]" size="2" value="<?php echo $dimensions['Length']; ?>" /></td>
											<td><input type="text" id="dhl_manual_width_ec" name="dhl_manual_width_ec[]" size="2" value="<?php echo $dimensions['Width']; ?>" /></td>
											<td><input type="text" id="dhl_manual_height_ec" name="dhl_manual_height_ec[]" size="2" value="<?php echo $dimensions['Height']; ?>" /></td>
											<td>&nbsp;</td>
										</tr>
										<?php
								}
							}
						}
					}
					echo '</table>';
					echo '<a class="wf-action-button wf-add-button" style="font-size: 12px;" id="wf_dhl_add_package_ec">Add Package</a>';
				
					echo '</li>';
					?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery('#wf_dhl_add_package_ec').on("click", function(){
							var new_row = '<tr>';
								new_row     += '<td><input type="text" id="dhl_manual_weight_ec" name="dhl_manual_weight_ec[]" size="2" value="0"></td>';
								new_row     += '<td><input type="text" id="dhl_manual_length_ec" name="dhl_manual_length_ec[]" size="2" value="0"></td>';                               
								new_row     += '<td><input type="text" id="dhl_manual_width_ec" name="dhl_manual_width_ec[]" size="2" value="0"></td>';
								new_row     += '<td><input type="text" id="dhl_manual_height_ec" name="dhl_manual_height_ec[]" size="2" value="0"></td>';
								// new_row  += '<td><input type="text" id="dhl_manual_insurance" name="dhl_manual_insurance[]" size="2" value="0"></td>';
								new_row     += '<td><a class="wf_dhl_package_line_remove_ec">&#x26D4;</a></td>';
							new_row     += '</tr>';
							
							jQuery('#wf_dhl_package_list_ec tr:last').after(new_row);
						});
						
						jQuery(document).on('click', '.wf_dhl_package_line_remove_ec', function(){
							jQuery(this).closest('tr').remove();
						});
					});
				</script>
				<li style="display:none;">
					<label for="wf_dhl_sat_delivery">
						<input type="checkbox" style="" id="wf_dhl_sat_delivery" name="wf_dhl_sat_delivery" class=""><?php _e('Saturday Delivery', 'wf-shipping-dhl'); ?>
					</label>
				</li>
				<li>
					<a class="button tips onclickdisable dhl_create_shipment_ec" href="<?php echo $generate_url; ?>" data-tip="<?php _e('Create Shipment', 'wf-shipping-dhl'); ?>"><?php _e('Create Shipment', 'wf-shipping-dhl'); ?></a>
				</li>
				<a class="button button-primary tips dhl_generate_packages_ec" href="<?php echo admin_url( '/?wf_dhl_ecommerce_generate_packages=' . base64_encode($post->ID) ); ?>" data-tip="<?php _e( 'Re-Generate Packages', 'wf-shipping-dhl' ); ?>"><?php _e( 'Re-Generate Packages', 'wf-shipping-dhl' ); ?></a><hr style="border-color:#0074a2">
				<?php
				} 
				?>
			<script type="text/javascript">
				jQuery("a.dhl_generate_packages_ec").on("click", function() {
					location.href = this.href;
				});
			</script>
				<?php

			}
			echo '</ul>';
			?>
		<script>
		jQuery("a.dhl_create_shipment_ec").one("click", function() {
			
			jQuery(this).click(function () { return false; });
				var manual_weight_arr   =   jQuery("input[id='dhl_manual_weight_ec']").map(function(){return jQuery(this).val();}).get();
				var manual_weight       =   JSON.stringify(manual_weight_arr);
				
				var manual_height_arr   =   jQuery("input[id='dhl_manual_height_ec']").map(function(){return jQuery(this).val();}).get();
				var manual_height       =   JSON.stringify(manual_height_arr);
				
				var manual_width_arr    =   jQuery("input[id='dhl_manual_width_ec']").map(function(){return jQuery(this).val();}).get();
				var manual_width        =   JSON.stringify(manual_width_arr);
				
				var manual_length_arr   =   jQuery("input[id='dhl_manual_length_ec']").map(function(){return jQuery(this).val();}).get();
				var manual_length       =   JSON.stringify(manual_length_arr);
				
				// var manual_insurance_arr     =   jQuery("input[id='dhl_manual_insurance']").map(function(){return jQuery(this).val();}).get();
				// var manual_insurance         =   JSON.stringify(manual_insurance_arr);
				
				
			   location.href = this.href + '&weight=' + manual_weight +
				'&length=' + manual_length
				+ '&width=' + manual_width
				+ '&height=' + manual_height
				+ '&dhl_ecommerce_shipping_service=' + jQuery('#dhl_ecommerce_manual_service').val()
				+ '&dhl_eccommerce_mail_type=' + jQuery('#dhl_ecommerce_manual_mail_type').val()
				+ '&dhl_ecommerce_shipping_incoterm=' + jQuery('#dhl_ecommerce_shipment_inco_terms').val()
				+ '&dhl_ecommerce_shipment_service_endorsement=' + jQuery('#dhl_ecommerce_shipment_service_endorsement').val()
				+ '&dhl_eccommerce_delivery_date=' + jQuery('#ma_expected_delivey').val();
			return false;           
		});
		</script>       
			<?php
		}

		private function get_dhl_dummy_package() {
			return array(
			'Dimensions' => array(
				'Length' => 0,
				'Width' => 0,
				'Height' => 0,
				'Units' => $this->dim_unit
			),
			'Weight' => array(
				'Value' => 0,
				'Units' => $this->weight_unit
			)
			);
		}

		public function get_dimension_from_package( $package) {
			$dimensions =   array(
			'Length'    =>  0,
			'Width'     =>  0,
			'Height'    =>  0,
			'Weight'    =>  0,
			);
		
			if (!is_array($package)) { // Package is not valid
				return $dimensions;
			}
			if (isset($package['Dimensions'])) {
				$dimensions['Length']   =   $package['Dimensions']['Length'];
				$dimensions['Width']    =   $package['Dimensions']['Width'];
				$dimensions['Height']   =   $package['Dimensions']['Height'];
				$dimensions['dim_unit'] =   $package['Dimensions']['Units'];
			}
		
			$dimensions['Weight']      =   $package['Weight']['Value'];
			$dimensions['weight_unit'] =   $package['Weight']['Units'];
			return $dimensions;
		}   
	}
}
new wf_dhl_ecommerce_shipping_admin();
?>
