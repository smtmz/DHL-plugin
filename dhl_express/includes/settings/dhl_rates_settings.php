<?php $this->init_settings(); 
global $woocommerce;
$wc_main_settings = array();
if (isset($_POST['wf_dhl_rates_save_changes_button'])) {

	$wc_main_settings = get_option('woocommerce_wf_dhl_shipping_settings');

	$wc_main_settings['delivery_time']                  = ( isset($_POST['wf_dhl_shipping_delivery_time']) ) ? 'yes' : '';
	$wc_main_settings['request_type']                   = ( isset($_POST['wf_dhl_shipping_request_type']) ) ? 'ACCOUNT' : 'LIST';
	$wc_main_settings['show_dhl_extra_charges']         = ( isset($_POST['wf_dhl_shipping_show_dhl_extra_charges']) ) ? 'yes' : '';
	$wc_main_settings['show_dhl_insurance_charges']     = ( isset($_POST['show_dhl_insurance_charges_express_dhl_elex']) ) ? 'yes' : '';
	$wc_main_settings['show_dhl_remote_area_surcharge'] = ( isset($_POST['show_dhl_remote_area_surcharge_express_dhl_elex']) ) ? 'yes' : '';
	$wc_main_settings['offer_rates']                    = ( isset($_POST['wf_dhl_shipping_offer_rates']) ) ? 'cheapest' : 'all';
	$wc_main_settings['exclude_dhl_tax']                = ( isset($_POST['wf_dhl_shipping_exclude_dhl_tax']) ) ? 'yes' : '';
	$wc_main_settings['title']                          = ( isset($_POST['wf_dhl_shipping_title']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_title']) :  __( 'DHL', 'wf-shipping-dhl' );
	$wc_main_settings['availability']                   = ( isset($_POST['wf_dhl_shipping_availability']) && sanitize_text_field( $_POST['wf_dhl_shipping_availability'] ) ==='all' ) ? 'all' : 'specific';
	$wc_main_settings['latin_encoding']                 = ( isset($_POST['wf_dhl_shipping_latin_encoding']) ) ? 'yes' : '';
	$wc_main_settings['rate_is_dutiable']               = ( isset($_POST['wf_dhl_shipping_rate_is_dutiable']) ) ? 'Y' : 'N';
	$temp_array = array();
	foreach ( $_POST['dhl_service'] as $key => $value ) {
		$temp_array[$key] = array_map( 'sanitize_text_field', wp_unslash( $value ) );
	}
	$wc_main_settings['services'] = $temp_array;

	if ($wc_main_settings['availability'] === 'specific') {
		$wc_main_settings['countries'] = isset($_POST['wf_dhl_shipping_countries']) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['wf_dhl_shipping_countries'] ) ) : '';
	}

	$wc_main_settings['working_day_monday']                        = isset($_POST['working_day_monday'])? 'yes': 'no';
	$wc_main_settings['working_day_tuesday']                       = isset($_POST['working_day_tuesday'])? 'yes': 'no';
	$wc_main_settings['working_day_wednesday']                     = isset($_POST['working_day_wednesday'])? 'yes': 'no';
	$wc_main_settings['working_day_thursday']                      = isset($_POST['working_day_thursday'])? 'yes': 'no';
	$wc_main_settings['working_day_friday']                        = isset($_POST['working_day_friday'])? 'yes': 'no';
	$wc_main_settings['working_day_saturday']                      = isset($_POST['working_day_saturday'])? 'yes': 'no';
	$wc_main_settings['working_day_sunday']                        = isset($_POST['working_day_sunday'])? 'yes': 'no';
	$wc_main_settings['elex_dhl_cutoff_time']                      = isset($_POST['elex_dhl_cutoff_time'])? sanitize_text_field( $_POST['elex_dhl_cutoff_time'] ): '23:59';
	$wc_main_settings['elex_dhl_insurance_for_specific_countries'] = isset($_POST['elex_dhl_insurance_for_specific_countries']) && !empty($_POST['elex_dhl_insurance_for_specific_countries'])? array_map( 'sanitize_text_field', wp_unslash( $_POST['elex_dhl_insurance_for_specific_countries'] ) ): array();
	$wc_main_settings['elex_dhl_fall_back']                        = isset($_POST['elex_dhl_fall_back']) ? sanitize_text_field( $_POST['elex_dhl_fall_back'] ) : '';
	$wc_main_settings['elex_dhl_custom_lead_time']                 = isset($_POST['elex_dhl_custom_lead_time']) ? sanitize_text_field( $_POST['elex_dhl_custom_lead_time'] ) : '00:00';
	$wc_main_settings['countries_to_hide_selected']                = isset($_POST['wf_dhl_shipping_hide_services'])? 'yes': 'no';
	$wc_main_settings['countries_to_hide_services']                = isset($_POST['wf_dhl_shipping_hide_for_countries'])? array_map( 'sanitize_text_field', wp_unslash( $_POST['wf_dhl_shipping_hide_for_countries'] ) ): ''; 
	update_option('woocommerce_wf_dhl_shipping_settings', $wc_main_settings);
}

$general_settings      = get_option('woocommerce_wf_dhl_shipping_settings');
$this->custom_services = isset($general_settings['services']) ? $general_settings['services'] : array();

function elex_dhl_return_wpml_string( $string_to_translate, $name) {
	   do_action( 'wpml_register_single_string', 'wf-shipping-dhl', $name, $string_to_translate );
	   $ret_string = apply_filters('wpml_translate_single_string', $string_to_translate, 'wf-shipping-dhl', $name );
	   return $ret_string;
}
if (isset($general_settings['title'])) {
	$wf_dhl_method_title = elex_dhl_return_wpml_string($general_settings['title'], 'DHL Method Title');
} else {
	$wf_dhl_method_title = __( 'DHL', 'wf-shipping-dhl' );
}

$shipping_rates_source_currency = apply_filters('wf_dhl_shipping_rates_source_currency', get_woocommerce_currency(), array(), $this);

?>
<table>
	<tr valign="top">
		<td style="width:30%;font-weight:800;">
			<label for="wf_dhl_shipping_delivery_time"><?php _e('Show/Hide', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">

				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_delivery_time" id="wf_dhl_shipping_delivery_time" style="" value="yes" <?php echo ( isset($general_settings['delivery_time']) && $general_settings['delivery_time'] ==='yes' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('Show Delivery Time', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('on enabling this, estimated delivery date will shown for each service of DHL.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_request_type" id="wf_dhl_shipping_request_type" style="" value="yes" <?php echo ( isset($general_settings['request_type']) && $general_settings['request_type'] ==='ACCOUNT' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('Show DHL Account Rates', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('On enabling this, the plugin will fetch the account specific rates of the shipper.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>

			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_show_dhl_extra_charges" id="wf_dhl_shipping_show_dhl_extra_charges" style="" value="yes" <?php echo ( isset($general_settings['show_dhl_extra_charges']) && $general_settings['show_dhl_extra_charges'] ==='yes' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('Show Break Down Charges', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to display the breakdown of shipping charges on the cart/checkout pages. This includes weight charge and DHL extra charges. DHL extra charges include handling charge, insurance charge, and remote area surcharge.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>

			<fieldset style="padding:3px;" id="show_dhl_insurance_charges_fieldset_express_dhl_elex">
				<input class="input-text regular-input " type="checkbox" name="show_dhl_insurance_charges_express_dhl_elex" id="show_dhl_insurance_charges_express_dhl_elex" style="" value="yes" <?php echo ( isset($general_settings['show_dhl_insurance_charges']) && $general_settings['show_dhl_insurance_charges'] ==='yes' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('Show Insurance Charges', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to display Insurance charge (if enabled) in the breakdown.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>

			<fieldset style="padding:3px;" id="show_dhl_remote_area_surcharge_fieldset_express_dhl_elex">
				<input class="input-text regular-input " type="checkbox" name="show_dhl_remote_area_surcharge_express_dhl_elex" id="show_dhl_remote_area_surcharge_charge_express_dhl_elex" style="" value="yes" <?php echo ( isset($general_settings['show_dhl_remote_area_surcharge']) && $general_settings['show_dhl_remote_area_surcharge'] ==='yes' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('Show Remote Area Surcharge', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to display Remote area surcharge in the breakdown.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>

			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_offer_rates" id="wf_dhl_shipping_offer_rates" style="" value="yes" <?php echo ( isset($general_settings['offer_rates']) && $general_settings['offer_rates'] ==='cheapest' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('Show Cheapest Rates Only', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('On enabling this, the cheapest rate will be shown in the cart/checkout page.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top">
		<td style="width:30%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Enable/Disable', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_rate_is_dutiable" id="wf_dhl_shipping_rate_is_dutiable" style="" value="N" <?php echo ( isset($general_settings['rate_is_dutiable']) && $general_settings['rate_is_dutiable'] ==='Y' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('Is Dutiable?', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e("By default, Dutiable will be 'No' for domestic shipment and between EU regions. We recommend enabling this option for international non-EU shipment. Availability of certain DHL shipping services will be based on this.", 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
				 <fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_exclude_dhl_tax" id="wf_dhl_shipping_exclude_dhl_tax" style="" value="yes" <?php echo ( isset($general_settings['exclude_dhl_tax']) && $general_settings['exclude_dhl_tax'] ==='yes' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('Exclude DHL Tax', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this option to deduct Taxes (like VAT) from the rates returned by DHL. This option will be useful if you have set up shipping taxes using WooCommerce tax settings.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_latin_encoding" id="wf_dhl_shipping_latin_encoding" style="" value="yes" <?php echo ( isset($general_settings['latin_encoding']) && $general_settings['latin_encoding'] === 'yes' ) ? 'checked' : ''; ?> placeholder="">  <?php _e('UTF-8 Support', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enables UTF-8 character set support. This settings will be useful while getting rates for UTF-8 characters from languages like Chinese, Japanese, etc.', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			
		</td>
	</tr>
	<tr valign="top" >
		<td style="width:30%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Method Config', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<label for="wf_dhl_shipping_title"><?php _e('Method Title / Availability', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Provide the Method title which will be reflected as the service name if Show cheapest rates only is enabled.', 'wf-shipping-dhl'); ?>"></span>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="text" name="wf_dhl_shipping_title" id="wf_dhl_shipping_title" style="" value="<?php echo $wf_dhl_method_title; ?>" placeholder=""> 
			</fieldset>
			
			<fieldset style="padding:3px;">
				<?php 
				if (isset($general_settings['availability']) && $general_settings['availability'] ==='specific') { 
					?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_availability"  id="wf_dhl_shipping_availability1" value="all" placeholder=""> <?php _e('Supports All Countries', 'wf-shipping-dhl'); ?>
				<input class="input-text regular-input " type="radio"  name="wf_dhl_shipping_availability" checked="true" id="wf_dhl_shipping_availability2"  value="specific" placeholder=""> <?php _e(' Supports Specific Countries', 'wf-shipping-dhl'); ?>
				<?php } else { ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_availability" checked=true id="wf_dhl_shipping_availability1"  value="all" placeholder=""> <?php _e('Supports All Countries', 'wf-shipping-dhl'); ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_availability" id="wf_dhl_shipping_availability2"  value="specific" placeholder=""> <?php _e('Supports Specific Countries', 'wf-shipping-dhl'); ?>
				<?php } ?>
			</fieldset>
			<fieldset style="padding:3px;" id="dhl_specific">
				<label for="wf_dhl_shipping_countries"><?php _e('Specific Countries', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('You can select the shipping method to be available for all countries or selective countries.', 'wf-shipping-dhl'); ?>"></span><br/>

				<select class="chosen_select" multiple="true" name="wf_dhl_shipping_countries[]" >
					<?php 
					$woocommerce_countries = $woocommerce->countries->get_countries();
					$selected_country      =  ( isset($general_settings['countries']) && !empty($general_settings['countries']) ) ? $general_settings['countries'] : array($woocommerce->countries->get_base_country());

					foreach ($woocommerce_countries as $key => $value) {
						if (in_array($key, $selected_country)) {
							echo '<option value="' . $key . '" selected>' . $value . '</option>';
						}
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</fieldset>
			<fieldset style="padding:3px;" id="field_hide_for_specific">
				<input class="input-text regular-input " type="checkbox"  name="wf_dhl_shipping_hide_services" id="wf_dhl_shipping_hide_services"  value="hide_specific" <?php echo ( isset($general_settings['countries_to_hide_selected']) && $general_settings['countries_to_hide_selected'] ==='yes' ) ? 'checked' : ''; ?>> <?php _e('Block for Specific Countries', 'wf-shipping-dhl'); ?>
			</fieldset>
			<fieldset style="padding:3px;" id="hide_for_specific">
				<label for="wf_dhl_shipping_hide_services"><?php _e('Specific Countries', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('You can select the counties to block DHL shipping method for selective countries.', 'wf-shipping-dhl'); ?>"></span><br/>

				<select class="chosen_select" multiple="true" name="wf_dhl_shipping_hide_for_countries[]" >
					<?php 
					$woocommerce_countries = $woocommerce->countries->get_countries();
					$selected_country      =  ( isset($general_settings['countries_to_hide_services']) && !empty($general_settings['countries_to_hide_services']) ) ? $general_settings['countries_to_hide_services'] : array();

					
					foreach ($woocommerce_countries as $key => $value) {
						if (in_array($key, $selected_country)) {
							echo '<option value="' . $key . '" selected>' . $value . '</option>';
						}
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<td style="width:30%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Insurance for Specific Countries', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;" id="elex_dhl_insurance_specific_countries">
				<select class="chosen_select" multiple="true" name="elex_dhl_insurance_for_specific_countries[]" >
					<?php 
					$woocommerce_countries               = $woocommerce->countries->get_countries();
					$selected_specific_insurance_country =  ( isset($general_settings['elex_dhl_insurance_for_specific_countries']) && !empty($general_settings['elex_dhl_insurance_for_specific_countries']) ) ? $general_settings['elex_dhl_insurance_for_specific_countries'] : array();

					
					foreach ($woocommerce_countries as $key => $value) {
						if (in_array($key, $selected_specific_insurance_country)) {
							echo '<option value="' . $key . '" selected>' . $value . '</option>';
						}
						echo '<option value="' . $key . '">' . $value . '</option>';
					}
					?>
				</select>
				<span class="woocommerce-help-tip" data-tip="<?php _e('Choose countries for which you want to provide an option to enable insurance on the checkout page.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<td style="width:30%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Working Days', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<input type="checkbox" name="working_day_monday" <?php echo isset($general_settings['working_day_monday']) && ( $general_settings['working_day_monday'] == 'yes' )? 'checked': ''; ?>><?php _e('Monday', 'wf-shipping-dhl'); ?>
			<input type="checkbox" name="working_day_tuesday" <?php echo isset($general_settings['working_day_tuesday']) && ( $general_settings['working_day_tuesday'] == 'yes' )? 'checked': ''; ?>><?php _e('Tuesday', 'wf-shipping-dhl'); ?>
			<input type="checkbox" name="working_day_wednesday" <?php echo isset($general_settings['working_day_wednesday']) && ( $general_settings['working_day_wednesday'] == 'yes' )? 'checked': ''; ?>><?php _e('Wednesday', 'wf-shipping-dhl'); ?>
			<input type="checkbox" name="working_day_thursday" <?php echo isset($general_settings['working_day_thursday']) && ( $general_settings['working_day_thursday'] == 'yes' )? 'checked': ''; ?>><?php _e('Thursday', 'wf-shipping-dhl'); ?>
			<input type="checkbox" name="working_day_friday" <?php echo isset($general_settings['working_day_friday']) && ( $general_settings['working_day_friday'] == 'yes' )? 'checked': ''; ?>><?php _e('Friday', 'wf-shipping-dhl'); ?>
			<input type="checkbox" name="working_day_saturday" <?php echo isset($general_settings['working_day_saturday']) && ( $general_settings['working_day_saturday'] == 'yes' )? 'checked': ''; ?>><?php _e('Saturday', 'wf-shipping-dhl'); ?>
			<input type="checkbox" name="working_day_sunday" <?php echo isset($general_settings['working_day_sunday']) && ( $general_settings['working_day_sunday'] == 'yes' )? 'checked': ''; ?>><?php _e('Sunday', 'wf-shipping-dhl'); ?>
			<span class="woocommerce-help-tip" data-tip="<?php _e('Choose your working days. If an order is placed on a non-working day, the next working day will be automatically chosen as the shipping date.', 'wf-shipping-dhl'); ?>"></span>
		</td>
	</tr>
	<tr valign="top">
		<td style="width:30%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Cut-off Time', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<input type="time" name="elex_dhl_cutoff_time" value="<?php echo isset($general_settings['elex_dhl_cutoff_time'])? $general_settings['elex_dhl_cutoff_time']: '00:00'; ?>">
			<span class="woocommerce-help-tip" data-tip="<?php _e('Choose your shipping cut-off time. If an order is placed after the specified cut-off time, the next working day will be chosen as the shipping date in the request to DHL API. The Estimated Delivery Date will be displayed based on this shipping date. Please note the time format will be based on the date and time format of your system.', 'wf-shipping-dhl'); ?>"></span>
		</td>
	</tr>

	<tr valign="top">
		<td style="width:30%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Lead Time (Days)', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<input type="text" name="elex_dhl_custom_lead_time" value="<?php echo isset($general_settings['elex_dhl_custom_lead_time'])? $general_settings['elex_dhl_custom_lead_time']: ''; ?>">
			<span class="woocommerce-help-tip" data-tip="<?php _e('By default, the Estimated Delivery Time is calculated considering that the shipping happens on the next business day after the order is placed. The Lead Time option will be useful if your business needs a few days time to start shipping, once an order is placed. You can enter the number of days that you require as Lead Time, which will be added to the Estimated Delivery Time during the checkout process. This will help to present a more accurate Estimated Delivery Time to the Customer.', 'wf-shipping-dhl'); ?>"></span>
		</td>
	</tr>

	<tr valign="top" id="elex_dhl_fall_back_tr">
		<td style="width:30%;font-weight:800;">
			<label for="wf_dhl_shipping_">
			<?php 
			_e('Fallback Rate', 'wf-shipping-dhl') ;
_e(' [' . $shipping_rates_source_currency . ']'); 
			?>
			</label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<input type="text" name="elex_dhl_fall_back" value="<?php echo isset($general_settings['elex_dhl_fall_back'])? $general_settings['elex_dhl_fall_back']: ''; ?>">
			<span class="woocommerce-help-tip" data-tip="<?php _e('If DHL API fails to return the rate (for example, a server issue), the plugin will display the Fallback Rate to the Customer as a Shipping Charge. Leaving the Fallback rate empty will allow your Customers to proceed with checkout even though no shipping rates are available. API failure scenario is a rare case. Still, we recommend setting a fallback flat rate to avoid a checkout with no shipping charges.', 'wf-shipping-dhl'); ?>"></span>
		</td>
	</tr>
	<tr valign="top">
		<td colspan="2">
			<?php
			require  WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'dhl_express/includes/html-wf-services.php' ;
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center;">
			<br/>
			<input type="submit" value="<?php _e('Save Changes', 'wf-shipping-dhl'); ?>" class="button button-primary" name="wf_dhl_rates_save_changes_button">  
		</td>
	</tr>

	</table>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			showHideDHLInsuranceRemoteAreaSurcharge();
			showHideCountriesToHideForServices();
			jQuery('#wf_dhl_shipping_availability1').change(function(){
				jQuery('#dhl_specific').hide();
				showHideCountriesToHideForServices();
			}).change();

			jQuery('#wf_dhl_shipping_availability2').change(function(){
				if(jQuery('#wf_dhl_shipping_availability2').is(':checked')) {
					jQuery('#dhl_specific').show();
				}else{
					jQuery('#dhl_specific').hide();
				}
			}).change();

			jQuery('#wf_dhl_shipping_show_dhl_extra_charges').change(function(){
				showHideDHLInsuranceRemoteAreaSurcharge();                                
			});

			jQuery('#wf_dhl_shipping_hide_services').change(function(){
				showHideCountriesToHideForServices();
			});

			jQuery('#wf_dhl_shipping_availability2').change(function(){
				showHideCountriesToHideForServices();
			});
		  
			function showHideDHLInsuranceRemoteAreaSurcharge(){
				if(jQuery('#wf_dhl_shipping_show_dhl_extra_charges').is(':checked')){
					jQuery('#show_dhl_insurance_charges_fieldset_express_dhl_elex').show();
					jQuery('#show_dhl_remote_area_surcharge_fieldset_express_dhl_elex').show();
				}else{
					jQuery('#show_dhl_insurance_charges_fieldset_express_dhl_elex').hide();
					jQuery('#show_dhl_remote_area_surcharge_fieldset_express_dhl_elex').hide();
				}
			};

			function showHideCountriesToHideForServices(){
				if(jQuery('#wf_dhl_shipping_availability1').is(':checked')){
					jQuery('#field_hide_for_specific').show();
					if(jQuery('#wf_dhl_shipping_hide_services').is(':checked')){
						jQuery('#hide_for_specific').show();
					}else{
						jQuery('#hide_for_specific').hide();
					}   
				}else{
					jQuery('#field_hide_for_specific').hide();
					jQuery('#hide_for_specific').hide();
				}

				if(jQuery('#wf_dhl_shipping_availability2').is(':checked')){
					jQuery('#field_hide_for_specific').hide();
					jQuery('#hide_for_specific').hide();
				}
			};
		});

	</script>
