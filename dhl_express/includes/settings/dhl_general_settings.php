<?php $this->init_settings(); 
global $woocommerce;
$wc_main_settings = array();
require_once 'validate_credentials.php' ; 

if (isset($_POST['wf_dhl_validate_credentials'])) {
	$site_id        = $_POST['wf_dhl_shipping_site_id'];
	$site_pwd       = $_POST['wf_dhl_shipping_site_pwd'];
	$site_test_mode = $_POST['wf_dhl_shipping_production'];
	$site_country   = $_POST['wf_dhl_shipping_base_country'];
	
	$wc_main_settings                   = get_option('woocommerce_wf_dhl_shipping_settings');   
	$wc_main_settings['production']     = ( isset($_POST['wf_dhl_shipping_production']) && $_POST['wf_dhl_shipping_production'] ==='yes' ) ? 'yes' : '';
	$wc_main_settings['account_number'] = ( isset($_POST['wf_dhl_shipping_ac_num']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_ac_num']) : '130000279';
	$wc_main_settings['site_id']        = ( isset($_POST['wf_dhl_shipping_site_id']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_site_id']) : 'CIMGBTest';
	$wc_main_settings['site_password']  = ( isset($_POST['wf_dhl_shipping_site_pwd']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_site_pwd']) : 'DLUntOcJma';
	update_option('woocommerce_wf_dhl_shipping_settings', $wc_main_settings);



	$validate = wf_validate_crendials( $site_test_mode, $site_id, $site_pwd, $site_country, $mv='no') ;
	//update_option('wf_creadentials_validate','done');
}

if (isset($_POST['wf_dhl_validate_credentials_edit'])) {
	update_option('wf_dhl_shipping_validation_data', '');
}


if (isset($_POST['wf_dhl_genaral_save_changes_button'])) {
	$site_id        = isset($_POST['wf_dhl_shipping_site_id']) ? $_POST['wf_dhl_shipping_site_id'] : false;
	$site_pwd       = isset($_POST['wf_dhl_shipping_site_pwd']) ? $_POST['wf_dhl_shipping_site_pwd'] : false;
	$site_test_mode = isset($_POST['wf_dhl_shipping_production']) ? $_POST['wf_dhl_shipping_production'] : false;
	$site_country   = isset($_POST['wf_dhl_shipping_base_country']) ? $_POST['wf_dhl_shipping_base_country'] : false;
	if ($site_id && $site_pwd && $site_test_mode && $site_country) {
		$validate = wf_validate_crendials($site_test_mode, $site_id, $site_pwd, $site_country, $mv='no');
	} else {
		$validate =true;
	}
	if ($validate) {    

		$wc_main_settings  = get_option('woocommerce_wf_dhl_shipping_settings'); 
		$my_account_number = ( isset($wc_main_settings['account_number']) ) ? $wc_main_settings['account_number'] : '';
		$my_site_id        = ( isset($wc_main_settings['site_id']) ) ? $wc_main_settings['site_id'] : '';
		$my_site_pwd       = ( isset($wc_main_settings['site_password']) ) ? $wc_main_settings['site_password'] : '';
		$my_site_mode      = ( isset($wc_main_settings['production']) ) ? $wc_main_settings['production'] : '';

		$wc_main_settings['production'] = ( isset($_POST['wf_dhl_shipping_production']) && $_POST['wf_dhl_shipping_production'] ==='yes' ) ? 'yes' : $my_site_mode;

		$wc_main_settings['account_number']                   = ( isset($_POST['wf_dhl_shipping_ac_num']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_ac_num']) : $my_account_number;
		$wc_main_settings['site_id']                          = ( isset($_POST['wf_dhl_shipping_site_id']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_site_id']) : $my_site_id;
		$wc_main_settings['site_password']                    = ( isset($_POST['wf_dhl_shipping_site_pwd']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_site_pwd']) : $my_site_pwd;
		$wc_main_settings['enabled']                          = ( isset($_POST['wf_dhl_shipping_rates']) ) ? 'yes' : 'no';
		$wc_main_settings['enabled_label']                    = ( isset($_POST['wf_dhl_shipping_enabled_label']) ) ? 'yes' : 'no';
		$wc_main_settings['insure_contents']                  = ( isset($_POST['wf_dhl_shipping_insure_contents']) ) ? 'yes' : 'no';
		$wc_main_settings['insure_contents_chk']              = ( isset($_POST['wf_dhl_shipping_insure_contents_chk']) && !empty($_POST['wf_dhl_shipping_insure_contents_chk']) ) ? 'yes' : 'no';
		$wc_main_settings['debug']                            = ( isset($_POST['wf_dhl_shipping_debug']) ) ? 'yes' : 'no';
		$wc_main_settings['include_receiver_eori_vat_number'] = isset($_POST['include_receiver_eori_vat_express_dhl_elex_field'])? 'yes': 'no';
		$wc_main_settings['include_ein_number'] = isset($_POST['include_ein_express_dhl_elex_field'])? 'yes': 'no';

		$wc_main_settings['billing_company_format']  = ( isset($_POST['wf_dhl_billing_company_format']) ) ? stripslashes(sanitize_text_field($_POST['wf_dhl_billing_company_format'])) : 'firstName';
		$wc_main_settings['shipper_person_name']     = ( isset($_POST['wf_dhl_shipping_shipper_person_name']) ) ? stripslashes(sanitize_text_field($_POST['wf_dhl_shipping_shipper_person_name'])) : '';
		$wc_main_settings['shipper_company_name']    = ( isset($_POST['wf_dhl_shipping_shipper_company_name']) ) ? stripslashes(sanitize_text_field($_POST['wf_dhl_shipping_shipper_company_name'])) : '';
		$wc_main_settings['shipper_phone_number']    = ( isset($_POST['wf_dhl_shipping_shipper_phone_number']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_shipper_phone_number']) : '';
		$wc_main_settings['shipper_email']           = ( isset($_POST['wf_dhl_shipping_shipper_email']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_shipper_email']) : '';
		$wc_main_settings['freight_shipper_street']  = ( isset($_POST['wf_dhl_shipping_freight_shipper_street']) ) ? stripslashes(sanitize_text_field($_POST['wf_dhl_shipping_freight_shipper_street'])) : '';
		$wc_main_settings['shipper_street_2']        = ( isset($_POST['wf_dhl_shipping_shipper_street_2']) ) ? stripslashes(sanitize_text_field($_POST['wf_dhl_shipping_shipper_street_2'])) : '';
		$wc_main_settings['freight_shipper_city']    = ( isset($_POST['wf_dhl_shipping_freight_shipper_city']) ) ? stripslashes(sanitize_text_field($_POST['wf_dhl_shipping_freight_shipper_city'])) : '';
		$wc_main_settings['freight_shipper_state']   = ( isset($_POST['wf_dhl_shipping_freight_shipper_state']) ) ? stripslashes(sanitize_text_field($_POST['wf_dhl_shipping_freight_shipper_state'])) : '';
		$wc_main_settings['insure_currency']         = ( isset($_POST['wf_dhl_shipping_insure_currency']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_insure_currency']) : '';
		$wc_main_settings['insure_converstion_rate'] = ( isset($_POST['wf_dhl_shipping_insure_converstion_rate']) && !empty($_POST['wf_dhl_shipping_insure_converstion_rate']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_insure_converstion_rate']) : 1;
		$wc_main_settings['origin']                  = ( isset($_POST['wf_dhl_shipping_origin']) ) ? ( $_POST['wf_dhl_shipping_origin'] ) : '';
		$wc_main_settings['base_country']            = $_POST['wf_dhl_shipping_base_country'];
		$wc_main_settings['dutypayment_country']     = ( isset($_POST['wf_dhl_shipping_payment_country']) && !empty($_POST['wf_dhl_shipping_payment_country']) )? $_POST['wf_dhl_shipping_payment_country'] : $wc_main_settings['base_country'];
		$wc_main_settings['conversion_rate']         = isset($_POST['wf_dhl_shipping_conversion_rate']) ? sanitize_text_field($_POST['wf_dhl_shipping_conversion_rate']) : '';
		$country_based_data                          = include_once WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'dhl_express/includes/data-wf-country-details.php';
		$wc_main_settings['dhl_currency_type']       = isset($country_based_data[$wc_main_settings['base_country']]['currency']) ? $country_based_data[$wc_main_settings['base_country']]['currency'] : '';
		$wc_main_settings['region_code']             = isset($country_based_data[$wc_main_settings['base_country']]['region']) ? $country_based_data[$wc_main_settings['base_country']]['region'] : '';
		if (is_plugin_active('multi-vendor-add-on-for-thirdparty-shipping/multi-vendor-add-on-for-thirdparty-shipping.php')) {
			$wc_main_settings['vendor_check'] = ( isset($_POST['wf_dhl_shipping_vendor_check']) ) ? 'yes' : 'no';   
		} else {
			$wc_main_settings['vendor_check'] = 'no';
		}
		if (in_array('elex-dhl-express-for-woocommerce-bulk-printing-labels-addon/elex-dhl-express-for-woocommerce-bulk-printing-labels-addon.php', get_option('active_plugins'))) {
			$wc_main_settings['addon_bulk_printing_project_key'] = ( isset($_POST['addon_bulk_printing_project_key']) ) ? sanitize_text_field($_POST['addon_bulk_printing_project_key']) : '';
			$wc_main_settings['addon_bulk_printing_secret_key']  = ( isset($_POST['addon_bulk_printing_secret_key']) ) ? sanitize_text_field($_POST['addon_bulk_printing_secret_key']) : '';
		}
		update_option('woocommerce_wf_dhl_shipping_settings', $wc_main_settings);
	}
}

$general_settings = get_option('woocommerce_wf_dhl_shipping_settings');
$general_settings = empty($general_settings) ? array() : $general_settings;
$validation       = get_option('wf_dhl_shipping_validation_data');

?>
<img style="float:right;" src="<?php echo WF_DHL_PAKET_PATH . '/dhl_express/resources/images/dhl.png'; ?>" width="180" height="60" />

<table>
	<tr valign="top">
		<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_shipping_production"><?php _e('DHL Account Information', 'wf-shipping-dhl'); ?> </label> <span class="woocommerce-help-tip" data-tip="<?php _e('For getting SiteID and password for countries other than the United States (UK and Rest of the World), the customer should contact DHL account manager. The account manager must request integration with ELEX via DHL Pre-sales Department. For getting SiteID and Password for the United States, you need to write to xmlrequests@dhl.com along with your full Account details like account number, region, address, etc. to get API Access. For getting SiteID and Password for Australia, you are prompted to contact DHL Express by e-mailing onlineshipping.au@dhl.com.', 'wf-shipping-dhl'); ?>"></span>
		</td>
		<?php echo $this->get_option('woocommerce_wf_dhl_shipping_production'); ?>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
				<?php 
				if (isset($general_settings['production']) && $general_settings['production'] ==='yes') { 
					?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_production" <?php echo ( $validation === 'done' ) ? 'disabled="true"' : ''; ?> value="no" placeholder=""> <?php _e('Test Mode', 'wf-shipping-dhl'); ?>
				<input class="input-text regular-input " type="radio"  name="wf_dhl_shipping_production" checked="true" id="wf_dhl_shipping_production" <?php echo ( $validation === 'done' ) ? 'disabled="true"' : ''; ?> value="yes" placeholder=""> <?php _e('Live Mode', 'wf-shipping-dhl'); ?>
				<?php } else { ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_production" checked="true" <?php echo ( $validation === 'done' ) ? 'disabled="true"' : ''; ?> value="no" placeholder=""> <?php _e('Test Mode', 'wf-shipping-dhl'); ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_production" <?php echo ( $validation === 'done' ) ? 'disabled="true"' : ''; ?> value="yes" placeholder=""> <?php _e('Live Mode', 'wf-shipping-dhl'); ?>
				<?php } ?>
				<br>
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="number" name="wf_dhl_shipping_ac_num" id="wf_dhl_shipping_ac_num" <?php echo ( $validation === 'done' ) ? 'disabled="true"' : ''; ?>  value="<?php echo ( isset($general_settings['account_number']) ) ? $general_settings['account_number'] : '130000279'; ?>" placeholder="130000279"> <label for="wf_dhl_shipping_ac_num"><?php _e('Account Number', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Enter your DHL online account number as obtained from DHL. You can contact your DHL sales representative for this.', 'wf-shipping-dhl'); ?>"></span>

			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " required type="text" name="wf_dhl_shipping_site_id" id="wf_dhl_shipping_site_id" <?php echo ( $validation === 'done' ) ? 'disabled="true"' : ''; ?> value="<?php echo ( isset($general_settings['site_id']) ) ? $general_settings['site_id'] : 'CIMGBTest'; ?>" placeholder="CIMGBTest"><?php echo ( $validation === 'done' ) ? '<span style="vertical-align: bottom;color:green" class="dashicons dashicons-yes"></span>' : ''; ?> 
				<label for="wf_dhl_shipping_"><?php _e('API Key', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('You can get the SITE ID from the DHL integration team.', 'wf-shipping-dhl'); ?>"></span>   
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " required type="Password" name="wf_dhl_shipping_site_pwd" id="wf_dhl_shipping_site_pwd" <?php echo ( $validation === 'done' ) ? 'disabled="true"' : ''; ?> value="<?php echo ( isset($general_settings['site_password']) ) ? $general_settings['site_password'] : 'DLUntOcJma'; ?>" placeholder="**************"><?php echo ( $validation === 'done' ) ? '<span style="vertical-align: bottom;color:green" class="dashicons dashicons-yes"></span>' : ''; ?> 
				<label for="wf_dhl_shipping_site_pwd"><?php _e('API Secret', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label>
				<span class="woocommerce-help-tip" data-tip="<?php _e('You can get the PASSWORD from the DHL integration team.', 'wf-shipping-dhl'); ?>"></span>              

			</fieldset>
			<?php echo get_option('wf_dhl_validation_error'); ?>
			<fieldset style="padding:3px;">
				<?php
				if ($validation === 'done') {
					echo '<input type="submit" value="Edit Credentials" class="button button-secondary" name="wf_dhl_validate_credentials_edit" >';
				} else {
					echo '<input type="submit" value=" Validate Credentials" class="button button-secondary" name="wf_dhl_validate_credentials" >';
				}
				?>
			</fieldset>
		</td>
	</tr>
	<tr valign="top">
		<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_shipping_rates"><?php _e('Enable/Disable', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">

				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_rates" id="wf_dhl_shipping_rates" style="" value="yes" <?php echo ( !isset($general_settings['enabled']) || isset($general_settings['enabled']) && $general_settings['enabled'] ==='yes' ) ? 'checked' : ''; ?> placeholder=""> <?php _e('Enable Real time Rates', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to fetch the rates from DHL in cart/checkout page.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_enabled_label" id="wf_dhl_shipping_enabled_label" style="" value="yes" <?php echo ( !isset($general_settings['enabled_label']) || isset($general_settings['enabled_label']) && $general_settings['enabled_label'] ==='yes' ) ? 'checked' : ''; ?> placeholder=""> <?php _e('Enable Shipping Label', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows the user to create a label in the order admin page. Disabling it will hide the label creation option.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_insure_contents" id="wf_dhl_shipping_insure_contents" style="" <?php echo ( !isset($general_settings['insure_contents']) || isset($general_settings['insure_contents']) && $general_settings['insure_contents'] ==='yes' ) ? 'checked' : ''; ?> value="yes" placeholder=""> <?php _e('Enable Insurance', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to insure your products. The insured value will be the total cart value.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			<?php $default_currency = get_woocommerce_currency(); ?>
			<fieldset style="padding:3px;"id="wf_dhl_insurance_related">
				Select Insurance Currency </br> <select name="wf_dhl_shipping_insure_currency" class="wc-enhanced-select">
				<?php 
					$selected_currency = isset($general_settings['insure_currency']) ? $general_settings['insure_currency'] : get_woocommerce_currency();
					$currency_arr      = get_woocommerce_currencies();
				foreach ($currency_arr as $key => $value) {
					echo '<option value="' . $key . '" ' . ( ( $key === $selected_currency ) ? 'selected' : '' ) . '>' . $value . ' (' . $key . ') </option>';
				}
				?>
				</select><br/>
				<label for="wf_dhl_shipping_conversion_rate"><?php _e('Converstion Rate', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="Use this field to set the conversion rate of the Store currency <?php echo $default_currency; ?> to the Insurance currency choosen from the dropdown above."></span> <br/>   
				<input type="number" min="0" step="0.00001" name="wf_dhl_shipping_insure_converstion_rate" value="<?php echo isset($general_settings['insure_converstion_rate']) ? $general_settings['insure_converstion_rate'] : ''; ?>"/><br/>
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_insure_contents_chk" id="wf_dhl_shipping_insure_contents_chk" style="" <?php echo ( !isset($general_settings['insure_contents_chk']) || isset($general_settings['insure_contents_chk']) && $general_settings['insure_contents_chk'] ==='yes' ) ? 'checked' : ''; ?> value="yes" placeholder=""> <?php _e('DHL Insurance Checkout Field', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to insure your products. The insured value will be the total cart value.', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			<fieldset style="padding:3px;" id="include_receiver_eori_vat_express_dhl_elex_field">
				<input class="input-text regular-input " type="checkbox" name="include_receiver_eori_vat_express_dhl_elex_field" id="include_receiver_eori_vat_express_dhl_elex_field"  value="yes" <?php echo ( isset($general_settings['include_receiver_eori_vat_number']) && $general_settings['include_receiver_eori_vat_number'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e("Enable Reciever's EORI and VAT Number", 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e("Adding Receiver's VAT and EORI fields on the Checkout page and accordingly displaying the Vat and EORI information on the commercial Invoice.", 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;" id="include_ein_express_dhl_elex_field">
				<input class="input-text regular-input " type="checkbox" name="include_ein_express_dhl_elex_field" id="include_ein_express_dhl_elex_field"  value="yes" <?php echo ( isset($general_settings['include_ein_number']) && $general_settings['include_ein_number'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e("Enable EIN Number", 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e("Adding EIN field on the Checkout page and accordingly displaying the Vat and EORI information on the commercial Invoice.", 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_debug" id="wf_dhl_shipping_debug" style="" value="yes" <?php echo ( isset($general_settings['debug']) && $general_settings['debug'] ==='yes' ) ? 'checked' : ''; ?> placeholder=""> <?php _e('Enable Developer Mode', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e("Enabling Developer's mode will let you troubleshoot the plugin, Request/Response information would be available in the browser console.", 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			<?php
			if (class_exists('wf_vendor_addon_setup')) {
				?>
			<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_vendor_check" id="wf_dhl_shipping_vendor_check" style="" value="yes" <?php echo ( isset($general_settings['vendor_check']) && $general_settings['vendor_check'] ==='yes' ) ? 'checked' : ''; ?> placeholder=""> <?php _e('Enable Multi-Vendor Shipper Address', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('You are seeing this option becuase XA Multi-Vendor Shipping Add-On is installed. By enabling this option, Shipper Adress set in multi-vendor plugin settings will be overriden by the below Shipper Address settings..', 'wf-shipping-dhl'); ?>"></span>
			</fieldset>
			<?php
			}
			?>
		</td>
	</tr>
	<tr valign="top">
		<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Default Currency', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
				<?php $selected_currency = isset($general_settings['dhl_currency_type']) ? $general_settings['dhl_currency_type'] : get_woocommerce_currency(); ?>
				<label for="wf_dhl_shipping_"><?php echo '<b>' . $selected_currency . ' (' . get_woocommerce_currency_symbol($selected_currency) . ')</b>'; ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('This field picks the default currency of the country provided in Shipper Address Section.', 'wf-shipping-dhl'); ?>"></span><br/>
			</fieldset>
			<?php
			$selected_currency = isset($general_settings['dhl_currency_type']) ? $general_settings['dhl_currency_type'] : get_woocommerce_currency();
			if ($selected_currency != $default_currency) {
				?>
				<fieldset style="padding:3px;">
				<label for="wf_dhl_shipping_conversion_rate"><?php _e('Conversion Rate', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="Use this field to set the conversion rate of the  DHL currency <?php echo $selected_currency; ?> to the Store’s currency <?php echo $default_currency; ?>. "></span> <br/>   
				<input class="input-text regular-input " type="number" min="0" step="0.00001" name="wf_dhl_shipping_conversion_rate" id="wf_dhl_shipping_conversion_rate" style="" value="<?php echo ( isset($general_settings['conversion_rate']) ) ? $general_settings['conversion_rate'] : ''; ?>" placeholder=""><b> <?php echo $default_currency; ?></b>
				</fieldset>
				<?php
			}
			?>
			
			
		</td>
	</tr>

	<tr valign="top">
		<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_billing_company_format"><?php _e('Billing Company Name Format', 'wf-shipping-dhl'); ?></label>
		</td>
		<td>
		<fieldset style="padding:3px;" id="">
					<label for="wf_dhl_billing_company_format"><?php _e('Format Type', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('As per the DHL Express API version 10, Receiver Company name is a required field. You may make the Company name field as mandatory on the checkout page by selecting the first option.  Or the plugin will pass the First name option to add the Company name as the First name of the receiver as Company Name to DHL.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_billing_company_format" id="wf_dhl_billing_company_format" style="width:40%;">
					<?php
						$billing_company_format_type = array( 'mandatory' => __('Mandatory', 'wf-shipping-dhl'), 'firstName' => __('Choose First Name', 'wf-shipping-dhl'));

						$selected_format_type = isset($general_settings['billing_company_format']) ? $general_settings['billing_company_format'] : 'firstName';
					foreach ($billing_company_format_type as $key => $value) {
						if ($selected_format_type === $key) {
							echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
						} else {
							echo '<option value="' . $key . '">' . $value . '</option>';
						}
					}

					?>
					</select><br>
				</fieldset>
		</td>
	</tr>  

	<tr valign="top">
		<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Shipper Address', 'wf-shipping-dhl'); ?></label>
		</td>
		<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">

			<table>
				<tr>
					<td>
						<fieldset style="padding-left:3px;">
							<label for="wf_dhl_shipping_"><?php _e('Shipper Name', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Name of the person responsible for shipping.', 'wf-shipping-dhl'); ?>"></span>    <br/>
							<input class="input-text regular-input " type="text" name="wf_dhl_shipping_shipper_person_name" id="wf_dhl_shipping_shipper_person_name" style="" autocomplete="off" value="<?php echo ( isset($general_settings['shipper_person_name']) ) ? $general_settings['shipper_person_name'] : ''; ?>" placeholder="">  
						</fieldset>
					</td>
					<td>
						<fieldset style="padding-left:3px;">
							<label for="wf_dhl_shipping_"><?php _e('Company Name', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Company name of the shipper.', 'wf-shipping-dhl'); ?>"></span>     <br/>
							<input class="input-text regular-input " type="text" name="wf_dhl_shipping_shipper_company_name" id="wf_dhl_shipping_shipper_company_name" style="" autocomplete="off" value="<?php echo ( isset($general_settings['shipper_company_name']) ) ? $general_settings['shipper_company_name'] : ''; ?>" placeholder="">  
						</fieldset>

					</td>
				</tr>
				<tr>
					<td>

						<fieldset style="padding-left:3px;">
							<label for="wf_dhl_shipping_"><?php _e('Phone Number', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Phone number of the shipper.', 'wf-shipping-dhl'); ?>"></span>    <br/>
							<input class="input-text regular-input " type="text" name="wf_dhl_shipping_shipper_phone_number" id="wf_dhl_shipping_shipper_phone_number" style="" autocomplete="off" value="<?php echo ( isset($general_settings['shipper_phone_number']) ) ? $general_settings['shipper_phone_number'] : ''; ?>" placeholder="">  
						</fieldset>
					</td>
					<td>

						<fieldset style="padding-left:3px;">
							<label for="wf_dhl_shipping_"><?php _e('Email Address', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Email address of the shipper.', 'wf-shipping-dhl'); ?>"></span>   <br/>
							<input class="input-text regular-input " type="text" name="wf_dhl_shipping_shipper_email" id="wf_dhl_shipping_shipper_email" style="" autocomplete="off" value="<?php echo ( isset($general_settings['shipper_email']) ) ? $general_settings['shipper_email'] : ''; ?>" placeholder="">  
						</fieldset>

					</td>
				</tr>
				<tr>
					<td>

						<fieldset style="padding-left:3px;">
							<label for="wf_dhl_shipping_"><?php _e('Address Line 1', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Official address line 1 of the shipper.', 'wf-shipping-dhl'); ?>"></span>   <br> 
							<input class="input-text regular-input " type="text" name="wf_dhl_shipping_freight_shipper_street" id="wf_dhl_shipping_freight_shipper_street" style="" autocomplete="off" value="<?php echo ( isset($general_settings['freight_shipper_street']) ) ? $general_settings['freight_shipper_street'] : ''; ?>" placeholder="">  
						</fieldset>

					</td>
					<td>

						<fieldset style="padding-left:3px;">
							<label for="wf_dhl_shipping_"><?php _e('Address Line 2', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Official address line 2 of the shipper.', 'wf-shipping-dhl'); ?>"></span>    <br/> 
							<input class="input-text regular-input " type="text" name="wf_dhl_shipping_shipper_street_2" id="wf_dhl_shipping_shipper_street_2" style="" autocomplete="off" value="<?php echo ( isset($general_settings['shipper_street_2']) ) ? $general_settings['shipper_street_2'] : ''; ?>" placeholder="">  
						</fieldset>

					</td>
				</tr>
				<tr>
					<td>
						<fieldset style="padding-left:3px;">
							<label for="wf_dhl_shipping_freight_shipper_city"><?php _e('City', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('City of the shipper.', 'wf-shipping-dhl'); ?>"></span>     <br/>

							<input class="input-text regular-input " type="text" name="wf_dhl_shipping_freight_shipper_city" id="wf_dhl_shipping_freight_shipper_city" style="" autocomplete="off" value="<?php echo ( isset($general_settings['freight_shipper_city']) ) ? $general_settings['freight_shipper_city'] : ''; ?>" placeholder="">
						</fieldset>
					</td>
					<td>
						<fieldset style="padding-left:3px;">

							<label for="wf_dhl_shipping_freight_shipper_state"><?php _e('State', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('State of the shipper.', 'wf-shipping-dhl'); ?>"></span> <br/>
							<input class="input-text regular-input " type="text" name="wf_dhl_shipping_freight_shipper_state" id="wf_dhl_shipping_freight_shipper_state" style="" autocomplete="off" value="<?php echo ( isset($general_settings['freight_shipper_state']) ) ? $general_settings['freight_shipper_state'] : ''; ?>" placeholder="">
						</fieldset>
					</td>
				</tr>
				<tr>
					<td>

					<fieldset style="padding-left:3px;">

					<label for="wf_dhl_shipping_base_country"><?php _e('Country', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Country of the shipper(Used for fetching rates and label generation).', 'wf-shipping-dhl'); ?>"></span><br/>

						<select style="width:75%;" name="wf_dhl_shipping_base_country" >
							<?php 
							$woocommerce_countries = $woocommerce->countries->get_countries();
							$selected_country      =  ( isset($general_settings['base_country']) && $general_settings['base_country'] !='' ) ? $general_settings['base_country'] : $woocommerce->countries->get_base_country();

							foreach ($woocommerce_countries as $key => $value) {
								if ($key === $selected_country) {
									echo '<option value="' . $key . '" selected>' . $value . '</option>';
								}
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
							?>
						</select>

					</fieldset>
				</td>
				<td>

					<fieldset style="padding-left:3px;">

					<label for="wf_dhl_shipping_payment_country"><?php _e('Account Payment Country', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the Country for DHL Account Payment. By default, the Shipper Country is considered as the Account Payment Country. But, if your DHL Account Payment Country is different from the Shipper Country, you can set it up here.', 'wf-shipping-dhl'); ?>"></span><br/>

						<select style="width:75%;" name="wf_dhl_shipping_payment_country" >
							<?php 
							$woocommerce_countries = $woocommerce->countries->get_countries();
							$selected_country      =  ( isset($general_settings['dutypayment_country']) && $general_settings['dutypayment_country'] !='' ) ? $general_settings['dutypayment_country'] : $general_settings['base_country'];

							foreach ($woocommerce_countries as $key => $value) {
								if ($key === $selected_country) {
									echo '<option value="' . $key . '" selected>' . $value . '</option>';
								}
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
							?>
						</select>

					</fieldset>
				</td>
			</tr>
			<tr>
				<td>    
					<fieldset style="padding-left:3px;">

						<label for="wf_dhl_shipping_origin"><?php _e('Postal Code', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Postal code of the shipper(Used for fetching rates and label generation).', 'wf-shipping-dhl'); ?>"></span><br/>
						<input class="input-text regular-input " type="text" name="wf_dhl_shipping_origin" id="wf_dhl_shipping_origin" style="" value="<?php echo ( isset($general_settings['origin']) ) ? $general_settings['origin'] : ''; ?>" placeholder="">
					</fieldset>
				</td>
			</tr>
		</table>

	</td>
</tr>
<?php
if (in_array('elex-dhl-express-for-woocommerce-bulk-printing-labels-addon/elex-dhl-express-for-woocommerce-bulk-printing-labels-addon.php', get_option('active_plugins'))) {
	?>
	<tr>
		<td colspan="2">
			<h3><?php _e( 'ELEX DHL Express Bulk Label Printing Add-On Settings', 'wf-shipping-dhl' ); ?></h3>
			<p><?php _e( 'Get your credentials by logging in to <a href="https://developer.ilovepdf.com/login" target="_blank">iLovePDF site</a>. Free subscription allows 250 files to be downloaded per month. For higher plans checkout their <a href="https://developer.ilovepdf.com/pricing" target="_blank">pricing page</a>.', 'wf-shipping-dhl' ); ?></p>
		</td>
	</tr>
	<tr>
		<td style="width:40%;font-weight:700;">
				<label for="wf_australia_post_"><?php _e('Project Key', 'wf-shipping-dhl'); ?></label>
		</td>
		<td>
			<fieldset style="padding-left:3px;">
				<input class="input-text regular-input " type="text" name="addon_bulk_printing_project_key" id="addon_bulk_printing_project_key" style="" value="<?php echo ( isset($general_settings['addon_bulk_printing_project_key']) ) ? $general_settings['addon_bulk_printing_project_key'] : ''; ?>" placeholder="" size="40">  
			</fieldset>
		</td>
	</tr>
	<tr>
		<td style="width:40%;font-weight:700;">
				<label for="wf_australia_post_"><?php _e('Secret Key', 'wf-shipping-dhl'); ?></label>
		</td>
		<td>
			<fieldset style="padding-left:3px;">
				<input class="input-text regular-input " type="text" name="addon_bulk_printing_secret_key" id="addon_bulk_printing_secret_key" style="" value="<?php echo ( isset($general_settings['addon_bulk_printing_secret_key']) ) ? $general_settings['addon_bulk_printing_secret_key'] : ''; ?>" placeholder="" size="40">  
			</fieldset>
		</td>
	</tr>
<?php
}
?>
<tr>
	<td colspan="2" style="text-align:center;">

		<button type="submit" class="button button-primary" name="wf_dhl_genaral_save_changes_button"> <?php _e('Save Changes', 'wf-shipping-dhl'); ?> </button>
		
	</td>
</tr>
</table>

<script type="text/javascript">

		jQuery(window).load(function(){
			jQuery('#wf_dhl_shipping_insure_contents').change(function(){
				if(jQuery('#wf_dhl_shipping_insure_contents').is(':checked')) {
					jQuery('#wf_dhl_insurance_related').show();
				}else
				{
					jQuery('#wf_dhl_insurance_related').hide();
				}
			}).change();
		});

</script>
