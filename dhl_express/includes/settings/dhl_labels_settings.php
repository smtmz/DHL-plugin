<?php 
	$this->init_settings();
	global $woocommerce;
	$woo_countries         = new WC_Countries();
	$wc_main_settings      = array();
	$print_size            = array('8X4_A4_PDF' => '8X4_A4_PDF', '8X4_thermal' => '8X4_thermal', '8X4_A4_TC_PDF' => '8X4_A4_TC_PDF', '8X4_CI_PDF' => '8X4_CI_PDF', '8X4_CI_thermal' => '8X4_CI_thermal', '8X4_RU_A4_PDF' => '8X4_RU_A4_PDF', '8X4_PDF' => '8X4_PDF', '8X4_CustBarCode_PDF' => '8X4_CustBarCode_PDF', '8X4_CustBarCode_thermal' => '8X4_CustBarCode_thermal', '6X4_A4_PDF' => '6X4_A4_PDF', '6X4_thermal' => '6X4_thermal', '6X4_PDF' => '6X4_PDF');
	$printer_doc_type      = array('PDF' => 'PDF Output', 'ZPL2' => 'ZPL2 Output', 'EPL2' => 'EPL2 Output');
	$duty_payment_type     = array('' => 'None', 'S' => __('Shipper', 'wf-shipping-dhl'), 'R' => __('Recipient', 'wf-shipping-dhl'), 'T' => __('Third Party/Other', 'wf-shipping-dhl'));
	$domestic_service      =  array(
	'1'                    => 'EXPRESS DOMESTIC 12:00',
	'2'                    => 'B2C',
	'3'                    => 'B2C',
	'4'                    => 'JETLINE',
	'5'                    => 'SPRINTLINE',
	'9'                    => 'EUROPACK',
	'E'                    => 'EXPRESS 9:00',
	'G'                    => 'DOMESTIC ECONOMY SELECT',
	'H'                    => 'ECONOMY SELECT',
	'I'                    => 'EXPRESS DOMESTIC 9:00',
	'J'                    => 'JUMBO BOX',
	'K'                    => 'EXPRESS 9:00',
	'N'                    => 'EXPRESS DOMESTIC',
	'O'                    => 'EXPRESS DOMESTIC 10:30',
	'S'                    => 'SAME DAY',
	'T'                    => 'EXPRESS 12:00',
	'V'                    => 'EUROPACK',
	'W'                    => 'ECONOMY SELECT',
	'X'                    => 'EXPRESS ENVELOPE',
	'Y'                    => 'EXPRESS 12:00'   
	);
	$international_service = array(
	'B'                    => 'BREAKBULK EXPRESS',
	'C'                    => 'MEDICAL EXPRESS',
	'D'                    => 'EXPRESS WORLDWIDE',
	'E'                    => 'EXPRESS 9:00',
	'F'                    => 'FREIGHT WORLDWIDE',
	'J'                    => 'JUMBO BOX',
	'K'                    => 'EXPRESS 9:00',
	'L'                    => 'EXPRESS 10:30',
	'M'                    => 'EXPRESS 10:30',
	'P'                    => 'EXPRESS WORLDWIDE',
	'Q'                    => 'MEDICAL EXPRESS',
	'R'                    => 'GLOBALMAIL BUSINESS',
	'S'                    => 'SAME DAY',
	'T'                    => 'EXPRESS 12:00',
	'U'                    => 'EXPRESS WORLDWIDE',
	'V'                    => 'EUROPACK',
	'W'                    => 'ECONOMY SELECT',
	'X'                    => 'EXPRESS ENVELOPE',
	'Y'                    => 'EXPRESS 12:00'   
	);
	$special_services      = array(
		'N' => __('NONE', 'wf-shipping-dhl'),
		'HECAOI1A' => __('DANGEROUS GOODS (HE) PI965 1A', 'wf-shipping-dhl'),
		'HECAOI1B' => __('DANGEROUS GOODS (HE) PI965 1B', 'wf-shipping-dhl'),
		'HEDGDI966' => __('DANGEROUS GOODS (HE) PI966', 'wf-shipping-dhl'),
		'HEDGDI967' => __('DANGEROUS GOODS (HE) PI967', 'wf-shipping-dhl'),
		'HB' => __('LITHIUM ION PI965 SECTION II (HB)', 'wf-shipping-dhl'),
		'HD' => __('LITHIUM ION PI966 SECTION II (HD)', 'wf-shipping-dhl'),
		'HV' => __('LITHIUM ION PI967 SECTION II (HV)', 'wf-shipping-dhl'),
		'HECAOM1A' => __('DANGEROUS GOODS (HE) PI968 1A', 'wf-shipping-dhl'),
		'HECAOM1B' => __('DANGEROUS GOODS (HE) PI968 1B', 'wf-shipping-dhl'),
		'HEDGDM969' => __('DANGEROUS GOODS (HE) PI969', 'wf-shipping-dhl'),
		'HEDGDM970' => __('DANGEROUS GOODS (HE) PI970', 'wf-shipping-dhl'),
		'HM' => __('LITHIUM METAL PI969 SECTION II (HM)', 'wf-shipping-dhl'),
		'HW' => __('LITHIUM METAL PI970 SECTION II (HW)', 'wf-shipping-dhl'),
		'HVHW' => __('LITHIUM ION PI967 SECTION II (HV) LITHIUM METAL PI970 SECTION II (HW)', 'wf-shipping-dhl'),
		'HH' => __('DANGEROUS GOODS IN EXCEPTED QUANTITIES (HH)', 'wf-shipping-dhl'),
		'HK' => __('CONSUMER GOODS ID8000 (HK)', 'wf-shipping-dhl'),
		'HY' => __('BIOLOGICAL UN3373 (HY)', 'wf-shipping-dhl'),
		'HEFG' => __('DANGEROUS GOODS (HE) FLAMMABLE GAS', 'wf-shipping-dhl'),
		'HENFG' => __('DANGEROUS GOODS (HE) NON-FLAMMABLE, NON-TOXIC GAS', 'wf-shipping-dhl'),
		'HEFL' => __('DANGEROUS GOODS (HE) FLAMMABLE LIQUID', 'wf-shipping-dhl'),
		'HEFS' => __('DANGEROUS GOODS (HE) FLAMMABLE SOLIDS', 'wf-shipping-dhl'),
		'HESCS' => __('DANGEROUS GOODS (HE) SPONTANEOUS COMBUSTION SUBSTANCES', 'wf-shipping-dhl'),
		'HESDWW' => __('DANGEROUS GOODS (HE) SUBSTANCES DANGEROUS WHEN WET', 'wf-shipping-dhl'),
		'HEO' => __('DANGEROUS GOODS (HE) OXIDIZER', 'wf-shipping-dhl'),
		'HEOPO' => __('DANGEROUS GOODS (HE) Organic Peroxides', 'wf-shipping-dhl'),
		'HETS' => __('DANGEROUS GOODS (HE) TOXIC SUBSTANCES', 'wf-shipping-dhl'),
		'HEC' => __('DANGEROUS GOODS (HE) CORROSIVES', 'wf-shipping-dhl'),
		'HEM' => __('DANGEROUS GOODS (HE) MISCELLANEOUS', 'wf-shipping-dhl'),
		'IUP' => __('LITHIUM ION PI967 Section II (LiBa in equipment) UNDER PROVISO', 'wf-shipping-dhl'),
		'MUP' => __('LITHIUM METAL PI970 Section II (LiBa in equipment) UNDER PROVISO', 'wf-shipping-dhl'),
	);

	$invoice_generator = array(
		'classic' => __('Classic Invoice Generator', 'wf-shipping-dhl'),
		'default' => __('DHL Invoice Generator', 'wf-shipping-dhl')
	);
	$invoice_type      = array(
		'CMI' => __('Commercial Invoice', 'wf-shipping-dhl'),
		'PFI' => __('Proforma Invoice', 'wf-shipping-dhl')
	);

	$invoice_quantity_unit = array(
			'UOM' => __('Description (UOM)', 'wf-shipping-dhl'),
			'BOX' => __('Boxes (BOX)', 'wf-shipping-dhl'),
			'2GM' => __('Centigram (2GM)', 'wf-shipping-dhl'),
			'2M' => __('Centimeters (2M)', 'wf-shipping-dhl'),
			'2M3' => __('Cubic Centimeters (2M3)', 'wf-shipping-dhl'),
			'3M3' => __('Cubic Feet (3M3)', 'wf-shipping-dhl'),
			'M3' => __('Cubic Meters (M3)', 'wf-shipping-dhl'),
			'DPR' => __('Dozen Pairs (DPR)', 'wf-shipping-dhl'),
			'DOZ' => __('Dozen (DOZ)', 'wf-shipping-dhl'),
			'2NO' => __('Each (2NO)', 'wf-shipping-dhl'),
			'PCS' => __('Pieces (PCS)', 'wf-shipping-dhl'),
			'GM' => __('Grams (GM)', 'wf-shipping-dhl'),
			'GRS' => __('Gross (GRS)', 'wf-shipping-dhl'),
			'KG' => __('Kilograms (KG)', 'wf-shipping-dhl'),
			'L' => __('Liters (L)', 'wf-shipping-dhl'),
			'M' => __('Meters (M)', 'wf-shipping-dhl'),
			'3GM' => __('Milligrams (3GM)', 'wf-shipping-dhl'),
			'3L' => __('Milliliters (3L)', 'wf-shipping-dhl'),
			'X' => __('No Unit Required (X)', 'wf-shipping-dhl'),
			'NO' => __('Number (NO)', 'wf-shipping-dhl'),
			'2KG' => __('Ounces (2KG)', 'wf-shipping-dhl'),
			'PRS' => __('Pairs (PRS)', 'wf-shipping-dhl'),
			'2L' => __('Gallons (2L)', 'wf-shipping-dhl'),
			'3KG' => __('Pounds (3KG)', 'wf-shipping-dhl'),
			'CM2' => __('Square Centimeters (CM2)', 'wf-shipping-dhl'),
			'2M2' => __('Square Feet (2M2)', 'wf-shipping-dhl'),
			'3M2' => __('Square Inches (3M2)', 'wf-shipping-dhl'),
			'M2' => __('Square Meters (M2)', 'wf-shipping-dhl'),
			'4M2' => __('Square Yards (4M2)', 'wf-shipping-dhl'),
			'3M' => __('Yards (3M)', 'wf-shipping-dhl')
	);

	$invoice_languages = array(
			'bg' => __('Bugalrian (BG)', 'wf-shipping-dhl'),
			'br' => __('Portuguse Brazil (BR)', 'wf-shipping-dhl'),
			'bs' => __('Bosnian (BS)', 'wf-shipping-dhl'),
			'cs' => __('Czech (CS)', 'wf-shipping-dhl'),
			'da' => __('Danish (DA)', 'wf-shipping-dhl'),
			'de' => __('German (DE)', 'wf-shipping-dhl'),
			'el' => __('Greek (EL)', 'wf-shipping-dhl'),
			'en' => __('English (EN)', 'wf-shipping-dhl'),
			'ep' => __('English with SVP (EP)', 'wf-shipping-dhl'),
			'et' => __('Estonian (ET)', 'wf-shipping-dhl'),
			'fi' => __('Finnish (FI)', 'wf-shipping-dhl'),
			'fr' => __('French (FR)', 'wf-shipping-dhl'),
			'he' => __('Hebrew (HE)', 'wf-shipping-dhl'),
			'hr' => __('Croatian (HR)', 'wf-shipping-dhl'),
			'hu' => __('Hungaria (HU)', 'wf-shipping-dhl'),
			'is' => __('Icelandic (IS)', 'wf-shipping-dhl'),
			'it' => __('Italian (IT)', 'wf-shipping-dhl'),
			'lt' => __('Lithuanian (LT)', 'wf-shipping-dhl'),
			'lv' => __('Latvian (LV)', 'wf-shipping-dhl'),
			'mk' => __('Macedon (MK)', 'wf-shipping-dhl'),
			'nl' => __('Dutch (NL)', 'wf-shipping-dhl'),
			'no' => __('Norwegian (NO)', 'wf-shipping-dhl'),
			'pl' => __('Polish (PL)', 'wf-shipping-dhl'),
			'pt' => __('Portuguse (PT)', 'wf-shipping-dhl'),
			'ro' => __('Romanian (RO)', 'wf-shipping-dhl'),
			'ru' => __('Russian (RU)', 'wf-shipping-dhl'),
			'si' => __('Slovenian (SI)', 'wf-shipping-dhl'),
			'sk' => __('Slovak (SK)', 'wf-shipping-dhl'),
			'sp' => __('Spanish Latam SVP (SP)', 'wf-shipping-dhl'),
			'sq' => __('Albanian (SQ)', 'wf-shipping-dhl'),
			'sr'=> __('Serbian (SR)', 'wf-shipping-dhl'),
			'sv' => __('Swedish (SV)', 'wf-shipping-dhl'),
			'tr' => __('Turkish (TR)', 'wf-shipping-dhl'),
			'uk' => __('Ukranian (UK)', 'wf-shipping-dhl'),
	);

	$type_of_export = array(
		''  => __('None', 'wf-shipping-dhl'),
		'P' => __('P (Permanent)', 'wf-shipping-dhl'),
		'T' => __('T (Temporary)', 'wf-shipping-dhl'),
		'R' => __('R (Re-Export)', 'wf-shipping-dhl')
	);

	$receiver_duty_payment_types = array('DAP' => __('Delivered At Place (DAP)', 'wf-shipping-dhl'), 'DDU' => __('Delivered Duty Unpaid (DDU)', 'wf-shipping-dhl'),'DDP' => __('Delivered Duty Paid (DDP)', 'wf-shipping-dhl'), 'EXW' => __('Ex Works (EXW)', 'wf-shipping-dhl'), 'FCA' => __('Free Carrier (FCA)', 'wf-shipping-dhl'), 'CPT' => __('Carriage Paid To (CPT)', 'wf-shipping-dhl'), 'CIP' => __('Carriage and Insurance Paid to (CIP)', 'wf-shipping-dhl'), 'DAT' => __('Delivered At Terminal (DAT)', 'wf-shipping-dhl'),'DAP' => __('Delivered At Place (DAP)', 'wf-shipping-dhl'), 'FAS' => __('Free Alongside Ship (FAS)', 'wf-shipping-dhl'), 'FOB' => __('Free on Board (FOB)', 'wf-shipping-dhl'), 'CFR' => __('Cost and Freight (CFR)', 'wf-shipping-dhl'), 'CIF' => __('Cost, Insurance & Freight (CIF)', 'wf-shipping-dhl'));

	if (isset($_POST['wf_dhl_label_save_changes_button'])) {
		$wc_main_settings                                      = get_option('woocommerce_wf_dhl_shipping_settings');
		$wc_main_settings['plt']                               = ( isset($_POST['wf_dhl_shipping_plt']) ) ? 'yes' : '';
		$wc_main_settings['enable_saturday_delivery']          = ( isset($_POST['wf_dhl_shipping_enable_saturday_delivery']) ) ? 'yes' : '';
		$wc_main_settings['services_select']                   = ( isset($_POST['wf_dhl_shipping_services_select']) ) ? 'yes' : '';
		$wc_main_settings['show_front_end_shipping_method']    = ( isset($_POST['wf_dhl_shipping_show_front_end_shipping_method']) ) ? 'yes' : '';
		$wc_main_settings['output_format']                     = $_POST['wf_dhl_shipping_output_format'];
		$wc_main_settings['image_type']                        = $_POST['wf_dhl_shipping_image_type'];
		$wc_main_settings['option_generate_proforma_invoice']  = isset($_POST['option_generate_proforma_invoice_dhl_elex'])? 'yes': '';
		$wc_main_settings['return_label_key']                  = ( isset($_POST['wf_dhl_shipping_return_label_key']) ) ? 'yes' : '';
		$wc_main_settings['return_label_acc_number']           = ( isset($_POST['wf_dhl_shipping_return_label_acc_number']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_return_label_acc_number']) : '';
		$wc_main_settings['default_domestic_service']          = isset($_POST['wf_dhl_shipping_default_domestic_service']) ? $_POST['wf_dhl_shipping_default_domestic_service'] : 'none';
		$wc_main_settings['default_international_service']     = isset($_POST['wf_dhl_shipping_default_international_service']) ? $_POST['wf_dhl_shipping_default_international_service'] : 'none';
		$wc_main_settings['add_trackingpin_shipmentid']        = ( isset($_POST['wf_dhl_shipping_add_trackingpin_shipmentid']) ) ? 'yes' : '';
		$wc_main_settings['custom_message']                    = '';
		$wc_main_settings['customer_logo_url']                 = sanitize_text_field($_POST['wf_dhl_shipping_customer_logo_url']);
		$wc_main_settings['request_archive_airway_label']      = ( isset($_POST['wf_dhl_shipping_request_archive_airway_label']) ) ? 'yes' : '';
		$wc_main_settings['customer_signature_url']            = sanitize_text_field($_POST['wf_dhl_shipping_customer_signature_url']); 
		$wc_main_settings['option_print_label_by_customers']   = ( isset($_POST['wf_dhl_shipping_option_print_label_by_customers']) ) ? 'yes' : '';
		$wc_main_settings['no_of_archive_bills']               = ( isset($_POST['wf_dhl_shipping_no_of_archive_bills']) ) ? $_POST['wf_dhl_shipping_no_of_archive_bills'] : '1';
		$wc_main_settings['dhl_email_notification_service']    = ( isset($_POST['wf_dhl_shipping_dhl_email_notification_service']) ) ? 'yes' : '';
		$wc_main_settings['dhl_email_notification_message']    = ( isset($_POST['wf_dhl_shipping_dhl_email_notification_message']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_dhl_email_notification_message']) : '';
		$wc_main_settings['dir_download']                      = ( isset($_POST['wf_dhl_shipping_dir_download']) ) ? 'yes' : '';
		$wc_main_settings['dutypayment_type']                  = $_POST['wf_dhl_shipping_dutypayment_type'];
		$wc_main_settings['receiver_duty_payment_type']        = isset($_POST['receiver_duty_payment_type_dhl_elex'])? $_POST['receiver_duty_payment_type_dhl_elex']: 'DAP';
		$wc_main_settings['dutyaccount_number']                = isset($_POST['wf_dhl_shipping_dutyaccount_number']) ? $_POST['wf_dhl_shipping_dutyaccount_number'] : '';
		$wc_main_settings['label_contents_text']               = ( isset($_POST['wf_dhl_shipping_label_contents_text']) && !empty($_POST['wf_dhl_shipping_label_contents_text']) )? sanitize_text_field($_POST['wf_dhl_shipping_label_contents_text']) : 'NA';
		$wc_main_settings['label_comment_text']                = ( isset($_POST['wf_dhl_shipping_label_comment_text']) && !empty($_POST['wf_dhl_shipping_label_comment_text']) )? sanitize_text_field($_POST['wf_dhl_shipping_label_comment_text']) : 'NA';
		$wc_main_settings['default_special_service']           = ( isset($_POST['wf_dhl_shipping_default_special_service']) && !empty($_POST['wf_dhl_shipping_default_special_service']) ) ? $_POST['wf_dhl_shipping_default_special_service'] : 'NONE';
		$wc_main_settings['default_special_service_un_number'] = ( isset($_POST['wf_dhl_shipping_default_special_service_un_number']) && !empty($_POST['wf_dhl_shipping_default_special_service_un_number']) ) ? $_POST['wf_dhl_shipping_default_special_service_un_number'] : '';
		$wc_main_settings['add_pickup']                        = ( isset($_POST['wf_dhl_shipping_add_pickup']) ) ? 'yes' : '';
		$wc_main_settings['pickup_date']                       = ( isset($_POST['wf_dhl_shipping_pickup_date']) ) ? $_POST['wf_dhl_shipping_pickup_date'] : '0';
		$wc_main_settings['pickup_time_from']                  = ( isset($_POST['wf_dhl_shipping_pickup_time_from']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_pickup_time_from']) : '';
		$wc_main_settings['pickup_time_to']                    = ( isset($_POST['wf_dhl_shipping_pickup_time_to']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_pickup_time_to']) : '';
		$wc_main_settings['pickup_person']                     = ( isset($_POST['wf_dhl_shipping_pickup_person']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_pickup_person']) : '';
		$wc_main_settings['pickup_contact']                    = ( isset($_POST['wf_dhl_shipping_pickup_contact']) ) ? sanitize_text_field($_POST['wf_dhl_shipping_pickup_contact']) : '';
		$wc_main_settings['include_woocommerce_tax']           = ( isset($_POST['wf_dhl_shipping_include_woocommerce_tax']) ) ? 'yes' : '';
		$wc_main_settings['include_shipping_service_type']     = ( isset($_POST['option_commercial_invoice_shipping_service_type']) )? 'yes': '';
		$wc_main_settings['include_shipper_vat_number']        = isset($_POST['include_vat_number_express_dhl_elex'])? 'yes': 'no';
		$wc_main_settings['shipper_vat_number']                = isset($_POST['shipper_vat_number_express_dhl_elex']) && !empty($_POST['shipper_vat_number_express_dhl_elex'])? $_POST['shipper_vat_number_express_dhl_elex']: '';

		$wc_main_settings['classic_commercial_invoice'] = $_POST['wf_dhl_shipping_classic_commercial_invoice'];
		$wc_main_settings['invoice_type']               = $_POST['wf_dhl_shipping_invoice_type'];
		$wc_main_settings['invoice_quantity_unit']      = $_POST['wf_dhl_shipping_invoice_quantity_unit'];
		$wc_main_settings['invoice_language_code']      = $_POST['wf_dhl_shipping_invoice_language_code'];
		$wc_main_settings['type_of_export']             = $_POST['wf_dhl_shipping_type_of_export'];
		$wc_main_settings['export_reason']              = $_POST['wf_dhl_shipping_export_reason'];
		$wc_main_settings['eori_no']                    = $_POST['wf_dhl_shipping_eori_no'];
		$wc_main_settings['exporter_id']                = $_POST['wf_dhl_shipping_exporter_id'];
		$wc_main_settings['exporter_code']              = $_POST['wf_dhl_shipping_exporter_code'];
		$wc_main_settings['include_freight_cost']       = isset($_POST['wf_dhl_shipping_include_freight_cost']) ? 'yes' : '';
		$wc_main_settings['include_insurance_cost']     = isset($_POST['wf_dhl_shipping_include_insurance_cost']) ? 'yes' : '';


		$wc_main_settings['return_address_different']                = isset($_POST['elex_dhl_return_address_different'])? 'yes': 'no';
		$wc_main_settings['return_shipment_address']['person_name']  = ( isset($_POST['elex_dhl_return_receiver_person_name']) ) ? sanitize_text_field($_POST['elex_dhl_return_receiver_person_name']) : '';
		$wc_main_settings['return_shipment_address']['company_name'] = ( isset($_POST['elex_dhl_return_receiver_company_name']) ) ? sanitize_text_field($_POST['elex_dhl_return_receiver_company_name']) : '';
		$wc_main_settings['return_shipment_address']['phone']        = ( isset($_POST['elex_dhl_return_receiver_phone_number']) ) ? sanitize_text_field($_POST['elex_dhl_return_receiver_phone_number']) : '';
		$wc_main_settings['return_shipment_address']['email']        = ( isset($_POST['elex_dhl_return_receiver_email']) ) ? sanitize_text_field($_POST['elex_dhl_return_receiver_email']) : '';
		$wc_main_settings['return_shipment_address']['address_1']    = ( isset($_POST['elex_dhl_return_receiver_street']) ) ? sanitize_text_field($_POST['elex_dhl_return_receiver_street']) : '';
		$wc_main_settings['return_shipment_address']['address_2']    = ( isset($_POST['elex_dhl_return_receiver_street_2']) ) ? sanitize_text_field($_POST['elex_dhl_return_receiver_street_2']) : '';
		$wc_main_settings['return_shipment_address']['city']         = ( isset($_POST['elex_dhl_return_receiver_city']) ) ? sanitize_text_field($_POST['elex_dhl_return_receiver_city']) : '';
		$wc_main_settings['return_shipment_address']['state']        = ( isset($_POST['elex_dhl_return_receiver_state']) ) ? sanitize_text_field($_POST['elex_dhl_return_receiver_state']) : '';
		$woocommerce_countries                                       = $woocommerce->countries->get_countries();
		$wc_main_settings['return_shipment_address']['country_name'] = $woocommerce_countries[$_POST['elex_dhl_return_receiver_country']];
		$wc_main_settings['return_shipment_address']['country_code'] = $_POST['elex_dhl_return_receiver_country'];
		$wc_main_settings['return_shipment_address']['postcode']     = $_POST['elex_dhl_return_receiver_postcode'];
		$wc_main_settings['eh_country_filter_type']                  = isset($_POST['eh_country_filter_type'])? $_POST['eh_country_filter_type'] : array();
		$wc_main_settings['ioss_number']                             = isset($_POST['ioss_number'])? $_POST['ioss_number'] : '';
		$wc_main_settings['ioss_country_code']                       = isset($_POST['ioss_country_code'])? $_POST['ioss_country_code'] : array();
		 $wc_main_settings['include_ioss_number_check']              = isset($_POST['include_ioss_dhl_elex_field'])? $_POST['include_ioss_dhl_elex_field'] : '';
		update_option('woocommerce_wf_dhl_shipping_settings', $wc_main_settings);
	}
	$general_settings = get_option('woocommerce_wf_dhl_shipping_settings');
	?>

	<table>
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_"><?php _e('Enable/Disable', 'wf-shipping-dhl'); ?></label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
			<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_plt" id="wf_dhl_shipping_plt"  value="yes" <?php echo ( isset($general_settings['plt']) && $general_settings['plt'] === 'yes' ) ? 'checked' : ''; ?> > <?php _e('Enable PaperLess Trade (PLT)', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e("DHL’s Paperless Trade service allows you to electronically transmit Commercial and Proforma Invoices, eliminating the need to print and physically attach them to your shipments. With Paperless Trade, you have the option to generate Commercial or Proforma invoices in the DHL shipping solutions or to upload invoices created separately. On enabling this, DHL's paperless trade feature will be activated and a receipt will be generated as a commercial invoice.", 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;">
			<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_enable_saturday_delivery" id="wf_dhl_shipping_enable_saturday_delivery"  value="yes" <?php echo ( isset($general_settings['enable_saturday_delivery']) && $general_settings['enable_saturday_delivery'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Enable Saturday Delivery (SD)', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Special service. On activating this feature, the shipment can be delivered on Saturdays.', 'wf-shipping-dhl'); ?> " ></span>
			</fieldset>
			<fieldset style="padding:3px;">
			  <input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_show_front_end_shipping_method" id="wf_dhl_shipping_show_front_end_shipping_method"  value="yes" <?php echo ( isset($general_settings['show_front_end_shipping_method']) && $general_settings['show_front_end_shipping_method'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Enable Default Service for Label Generation', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('On enabling this option, the service selected in the cart/checkout page will only be reflected while creating shipment.', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;">
			  <input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_services_select" id="wf_dhl_shipping_services_select"  value="yes" <?php echo ( isset($general_settings['services_select']) && $general_settings['services_select'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Show only chosen services on <a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&section=wf_dhl_shipping&subtab=rates') . '">Rates & Services</a> section.', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enabling this option will display only those selected services from Rates & Services section while printing the label from Order Admin page.', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;">
			  <input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_dir_download" id="wf_dhl_shipping_dir_download"  value="yes" <?php echo ( isset($general_settings['dir_download']) && $general_settings['dir_download'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Enable Direct Download', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('By choosing this option, label and invoice will be downloaded instead of opening in a new browser window.', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;">
			  <input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_option_print_label_by_customers" id="wf_dhl_shipping_option_print_label_by_customers"  value="yes" <?php echo ( isset($general_settings['option_print_label_by_customers']) && $general_settings['option_print_label_by_customers'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Enable Customers to Print label', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e("Choose this option to allow your customers to view and print labels from their 'My Accounts' page upon order completion.", 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			
			</td>
		</tr>

		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_">Shipping Label</label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">

				<fieldset style="padding:3px;">
					 <label for="wf_dhl_shipping_"><?php _e('Printing Size', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows you to choose the size of the label among various options. Three file formats are supported for the labels - PDF, ZPL2, EPL2.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_shipping_output_format">
					<?php
						$selected_value = isset($general_settings['output_format']) ? $general_settings['output_format'] : '6X4_A4_PDF';
					foreach ($print_size as $key => $value) {
						if ($key == $selected_value) {
							echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
						} else {
							echo '<option value="' . $key . '">' . $value . '</option>';
						}
					}
					?>
					</select>
				</fieldset>
				<fieldset style="padding:3px;">
					<?php
						$selected_doc_type = isset($general_settings['image_type']) ? $general_settings['image_type'] : 'PDF';
					foreach ($printer_doc_type as $key => $value) {
						if ($key === $selected_doc_type) {
							echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_image_type" id="wf_dhl_shipping_image_type"  value="' . $key . '" checked=true > ' . $value . ' ';
						} else {
							echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_image_type" id="wf_dhl_shipping_image_type"  value="' . $key . '"  > ' . $value . ' ';
						}
					}
					?>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_label_contents_text"><?php _e('Shipping Contents Description', 'wf-shipping-dhl'); ?></label>
			</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;">
				<label for="wf_dhl_shipping_label_contents_text"><?php _e('Default Content', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="Enter a default description for the contents of the shipment. You can change the description from individual order page as well." ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_label_contents_text" id="wf_dhl_shipping_label_contents_text" value="<?php echo ( isset($general_settings['label_contents_text']) ) ? $general_settings['label_contents_text'] : ''; ?>" placeholder="NA">
				</fieldset>

			</td>
		</tr>
		
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_label_comment_text"><?php _e('Shipper Comment', 'wf-shipping-dhl'); ?></label>
			</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;">
				<label for="wf_dhl_shipping_label_comment_text"><?php _e('Default Comment', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="Enter a default shipper comment. You can change the description from individual order page as well." ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_label_comment_text" id="wf_dhl_shipping_label_contents_text" value="<?php echo ( isset($general_settings['label_comment_text']) ) ? $general_settings['label_comment_text'] : ''; ?>" placeholder="NA">
				</fieldset>

			</td>
		</tr>
		
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_customer_logo_url"><?php _e('Company Logo', 'wf-shipping-dhl'); ?></label>
			</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;" id="">
					 <label for="wf_dhl_shipping_customer_logo_url"><?php _e('Select Company Logo', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows you to upload your own company logo which will be visible in shipping labels, return labels and commercial invoices. Recommended image format: PNG. Recommended Dimensions: 250 x 200.', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_customer_logo_url" id="wf_dhl_shipping_customer_logo_url"  value="<?php echo ( isset($general_settings['customer_logo_url']) ) ? $general_settings['customer_logo_url'] : ''; ?>" ><br><a href="#" id="dhl_media_upload_image_button" class="button-secondary"><?php _e('Choose Image', 'wf-shipping-dhl'); ?></a>
				</fieldset>

			</td>
		</tr>
	   
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_dutypayment_type"><?php _e('Duty Payment', 'wf-shipping-dhl'); ?></label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;" id="">
					<label for="wf_dhl_shipping_dutypayment_type"><?php _e('Payment on', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the payment type for duty and tax charges. This is a mandatory requirement for non-doc and dutiable products. If you select duty payment type as Shipper, Delivered Duty Paid (DDP) will be automatically applied as Terms of Trade.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_shipping_dutypayment_type" id="wf_dhl_shipping_dutypayment_type" style="width:65%;">
					<?php
						$selected_pay_type = isset($general_settings['dutypayment_type']) ? $general_settings['dutypayment_type'] : '';
					foreach ($duty_payment_type as $key => $value) {
						if ($selected_pay_type === $key) {
							echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
						} else {
							echo '<option value="' . $key . '">' . $value . '</option>';
						}
					}
					?>
					</select><br>
				</fieldset>
				<fieldset style="padding:3px;" id="wf_t_acc_number">
					<label for="wf_dhl_shipping_dutyaccount_number"><?php _e('Duty Account Number', 'wf-shipping-dhl'); ?></label>
					<span class="woocommerce-help-tip" data-tip="<?php _e('Duty Billing account number. Required if the DutyPaymentType is Third Party.', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_dutyaccount_number" id="wf_dhl_shipping_dutyaccount_number"  value="<?php echo ( isset($general_settings['dutyaccount_number']) ) ? $general_settings['dutyaccount_number'] : ''; ?>" >
				</fieldset>
				<fieldset style="padding:3px;" id="fieldset_receiver_duty_payment_type_dhl_elex">
					<label for="receiver_duty_payment_type_dhl_elex"><?php _e('Duty Payment Type', 'wf-shipping-dhl'); ?></label>
					<span class="woocommerce-help-tip" data-tip="<?php _e('Select the Term of Trade you want to apply when customer is paying duty.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select id="receiver_duty_payment_type_dhl_elex" name="receiver_duty_payment_type_dhl_elex" style="width:65%;">
						<?php
						foreach ($receiver_duty_payment_types as $receiver_duty_payment_type_key => $receiver_duty_payment_type_value) {
							if ($general_settings['receiver_duty_payment_type'] == $receiver_duty_payment_type_key) {
								echo '<option value="' . $receiver_duty_payment_type_key . '" selected>' . $receiver_duty_payment_type_value . '</option>';    
							} else {
								echo '<option value="' . $receiver_duty_payment_type_key . '">' . $receiver_duty_payment_type_value . '</option>';
							}
						}
						?>
					</select>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_request_archive_airway_label"><?php _e('Archive Air Waybill', 'wf-shipping-dhl'); ?></label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;">
					<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_request_archive_airway_label" id="wf_dhl_shipping_request_archive_airway_label"  value="yes" <?php echo ( isset($general_settings['request_archive_airway_label']) && $general_settings['request_archive_airway_label'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Request Archive Air Waybill', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('For downloading archive airway bill Documents.', 'wf-shipping-dhl'); ?>" ></span>
				</fieldset>
				<fieldset style="padding:3px;" id="wf_no_of_archive_bills">
					<?php if (isset($general_settings['no_of_archive_bills']) && $general_settings['no_of_archive_bills'] === '2') { ?>
					<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_no_of_archive_bills"  id="wf_dhl_shipping_no_of_archive_bills"  value="1" > <?php _e('One Document', 'wf-shipping-dhl'); ?>
					<input class="input-text regular-input " type="radio"  name="wf_dhl_shipping_no_of_archive_bills" checked=true id="wf_dhl_shipping_no_of_archive_bills"  value="2" > <?php _e('Two Documents', 'wf-shipping-dhl'); ?>
					<?php } else { ?>
					<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_no_of_archive_bills" checked=true id="wf_dhl_shipping_no_of_archive_bills"  value="1" > <?php _e('One Document', 'wf-shipping-dhl'); ?>
					<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_no_of_archive_bills" id="wf_dhl_shipping_no_of_archive_bills"  value="2" > <?php _e('Two Documents', 'wf-shipping-dhl'); ?>
					<?php } ?>
				</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_default_domestic_service"><?php _e('Bulk Shipment', 'wf-shipping-dhl'); ?></label>
			</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;" id="">
					 <label for="wf_dhl_shipping_default_domestic_service"><?php _e('Default Domestic Service', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose the default service for domestic shipment which will be set while generating bulk shipment label from order admin page. The default service will be applicable if there is no DHL service chosen during the checkout process. ', 'wf-shipping-dhl'); ?>" ></span><br>


					<select name="wf_dhl_shipping_default_domestic_service" id="wf_dhl_shipping_default_domestic_service" style="width:65%;">
						<?php
							$selected_pay_type = isset($general_settings['default_domestic_service']) ? $general_settings['default_domestic_service'] : '';
							echo '<option value="none" >None</option>';
						foreach ($domestic_service as $key => $value) {

							if ($selected_pay_type == $key) {
								echo '<option value="' . $key . '" selected="true">[' . $key . '] ' . $value . '</option>';
							} else {
								echo '<option value="' . $key . '">[' . $key . '] ' . $value . '</option>';
							}
						}
						?>

					</select><br>
					</fieldset>
					<fieldset style="padding:3px;" id="">
					<label for="wf_dhl_shipping_default_international_service"><?php _e('Default International Service', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose the default service for international shipment which will be set while generating bulk shipment label from order admin page. The default service will be applicable if there is no DHL service chosen during the checkout process. ', 'wf-shipping-dhl'); ?>" ></span><br>


					<select name="wf_dhl_shipping_default_international_service" id="wf_dhl_shipping_default_international_service" style="width:65%;">
						<?php
							$selected_pay_type = isset($general_settings['default_international_service']) ? $general_settings['default_international_service'] : '';
							echo '<option value="none" >None</option>';
						foreach ($international_service as $key => $value) {

							if ($selected_pay_type == $key) {
								echo '<option value="' . $key . '" selected="true">[' . $key . '] ' . $value . '</option>';
							} else {
								echo '<option value="' . $key . '">[' . $key . '] ' . $value . '</option>';
							}
						}
						?>

					</select><br>
					</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_default_special_service"><?php _e('Special Service', 'wf-shipping-dhl'); ?></label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px; padding-right: -50% !important;">
				<fieldset style="padding:3px;" id="">
					 <label for="wf_dhl_shipping_default_special_service"><?php _e('Default Special Service', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose the default special service type if you are products are restricted commodities or dangerous goods. You can even configure it for individual products by going to the corresponding products admin settings page -> shipping -> special service. By enabling it, a compliance warning will be displayed on DHL labels.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_shipping_default_special_service" id="wf_dhl_shipping_default_special_service" style="width:65%;">
						<?php
							$default_service_status = '';
						if (isset($general_settings['default_special_service']) && !empty($general_settings['default_special_service'])) {
							$default_service_status = $general_settings['default_special_service'];
						}

							$selected_default_special_service = ( isset($special_services[$default_service_status]) && !empty($special_services[$default_service_status]) ) ? $special_services[$default_service_status] : 'NONE';

						foreach ($special_services as $key => $value) {
							if ($selected_default_special_service == $value) {
								echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
							} else {
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
						}

						?>
						}

					</select><br>
				</fieldset>
				<fieldset style="padding:3px;" id="wf_dhl_express_default_un_number_field">
					 <label for="wf_dhl_shipping_default_special_service"><?php _e('Default UN Number', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" id="woocommerce_data_tip_for_default_un_number" data-tip="<?php _e('You have selected default Special Service for Restricted commodities and Dangerous goods. Enter default UN number', 'wf-shipping-dhl'); ?>" ></span><br>
					 <input type="text" name="wf_dhl_shipping_default_special_service_un_number" id="wf_dhl_shipping_default_special_service_un_number" value="<?php echo ( isset($general_settings['default_special_service_un_number']) && !empty($general_settings['default_special_service_un_number']) )? $general_settings['default_special_service_un_number']: ''; ?>">
					<br>
				</fieldset>
			</td>
		</tr>

		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_return_label_key"><?php _e('Return Label', 'wf-shipping-dhl'); ?></label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;">
					<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_return_label_key" id="wf_dhl_shipping_return_label_key" value="yes" <?php echo ( isset($general_settings['return_label_key']) && $general_settings['return_label_key'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Enable Return Label', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows the plugin to provide the return label feature in the order page.', 'wf-shipping-dhl'); ?>" ></span>
				</fieldset>

				<fieldset style="padding:3px;" id="wf_return_label_acc_number">
					 <label for="wf_dhl_shipping_return_label_acc_number"><?php _e('Return Label Account Number', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Fill in the import account number provided by DHL for return labels, if your customer not belongs to your shop country.', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_return_label_acc_number" id="wf_dhl_shipping_return_label_acc_number" value="<?php echo ( isset($general_settings['return_label_acc_number']) ) ? $general_settings['return_label_acc_number'] : ''; ?>">
				</fieldset>

				<fieldset style="padding:3px;" id="elex_dhl_field_return_address_different">
					<input class="input-text regular-input " type="checkbox" name="elex_dhl_return_address_different" id="elex_dhl_return_address_different" value="yes" <?php echo ( isset($general_settings['return_address_different']) && $general_settings['return_address_different'] === 'yes' ) ? 'checked' : ''; ?>>  <?php _e('Return Address not same as Shipper Address', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows to provide alternate return receiver address if the return shipment receiver address is not same as the Shipper address provided in the General section.', 'wf-shipping-dhl'); ?>" ></span>
				</fieldset>
			</td>
		</tr>
		<tr valign="top" id="elex_dhl_row_return_address_different">
			<td style="width:35%;font-weight:800;">
				<label for="wf_dhl_shipping_"><?php _e('Return Shipment Address', 'wf-shipping-dhl'); ?></label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<table>
					<tr>
						<td>
							<fieldset style="padding-left:3px;">
								<label for="wf_dhl_shipping_"><?php _e('Name', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Name of the person responsible for shipping.', 'wf-shipping-dhl'); ?>"></span>    <br/>
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_person_name" id="elex_dhl_return_receiver_person_name" value="<?php echo ( isset($general_settings['return_shipment_address']['person_name']) ) ? $general_settings['return_shipment_address']['person_name'] : ''; ?>" required>  
							</fieldset>
						</td>
						<td>
							<fieldset style="padding-left:3px;">
								<label for="wf_dhl_shipping_"><?php _e('Company Name', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Company name of the shipper.', 'wf-shipping-dhl'); ?>"></span>     <br/>
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_company_name" id="elex_dhl_return_receiver_company_name" value="<?php echo ( isset($general_settings['return_shipment_address']['company_name']) ) ? $general_settings['return_shipment_address']['company_name'] : ''; ?>" required>  
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>
							<fieldset style="padding-left:3px;">
								<label for="wf_dhl_shipping_"><?php _e('Phone Number', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Phone number of the shipper.', 'wf-shipping-dhl'); ?>"></span>    <br/>
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_phone_number" id="elex_dhl_return_receiver_phone_number" value="<?php echo ( isset($general_settings['return_shipment_address']['phone']) ) ? $general_settings['return_shipment_address']['phone'] : ''; ?>" required> 
							</fieldset>
						</td>
						<td>
							<fieldset style="padding-left:3px;">
								<label for="wf_dhl_shipping_"><?php _e('Email Address', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Email address of the shipper.', 'wf-shipping-dhl'); ?>"></span>   <br/>
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_email" id="elex_dhl_return_receiver_email"  value="<?php echo ( isset($general_settings['return_shipment_address']['email']) ) ? $general_settings['return_shipment_address']['email'] : ''; ?>" required>  
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>
							<fieldset style="padding-left:3px;">
								<label for="wf_dhl_shipping_"><?php _e('Address Line 1', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Official address line 1 of the shipper.', 'wf-shipping-dhl'); ?>"></span>   <br> 
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_street" id="elex_dhl_return_receiver_street"  value="<?php echo ( isset($general_settings['return_shipment_address']['address_1']) ) ? $general_settings['return_shipment_address']['address_1'] : ''; ?>" required>  
							</fieldset>
						</td>
						<td>
							<fieldset style="padding-left:3px;">
								<label for="wf_dhl_shipping_"><?php _e('Address Line 2', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Official address line 2 of the shipper.', 'wf-shipping-dhl'); ?>"></span>    <br/> 
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_street_2" id="elex_dhl_return_receiver_street_2" value="<?php echo ( isset($general_settings['return_shipment_address']['address_2']) ) ? $general_settings['return_shipment_address']['address_2'] : ''; ?>">  
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>
							<fieldset style="padding-left:3px;">
								<label for="wf_dhl_shipping_"><?php _e('City', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('City of the shipper.', 'wf-shipping-dhl'); ?>"></span>     <br/>
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_city" id="elex_dhl_return_receiver_city" value="<?php echo ( isset($general_settings['return_shipment_address']['city']) ) ? $general_settings['return_shipment_address']['city'] : ''; ?>" required>
							</fieldset>
						</td>
						<td>
							<fieldset style="padding-left:3px;">
								<label for="wf_dhl_shipping_"><?php _e('State', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('State of the shipper.', 'wf-shipping-dhl'); ?>"></span> <br/>
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_state" id="elex_dhl_return_receiver_state" value="<?php echo ( isset($general_settings['return_shipment_address']['state']) ) ? $general_settings['return_shipment_address']['state'] : ''; ?>" required>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td>
							<fieldset style="padding-left:3px;">

							<label for="wf_dhl_shipping_base_country"><?php _e('Country', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Country of the shipper.', 'wf-shipping-dhl'); ?>"></span><br/>
								<select style="width:75%;" name="elex_dhl_return_receiver_country" >
									<?php 
									$woocommerce_countries = $woocommerce->countries->get_countries();
									$selected_country      =  ( isset($general_settings['return_shipment_address']['country_name']) && $general_settings['return_shipment_address']['country_name'] !='' ) ? $general_settings['return_shipment_address']['country_name'] : $woocommerce->countries->get_base_country();

									foreach ($woocommerce_countries as $key => $value) {
										if ($value === $selected_country) {
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
								<label for="wf_dhl_shipping_"><?php _e('Postal Code', 'wf-shipping-dhl'); ?><font style="color:red;">*</font></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Postal code of the receiving address.', 'wf-shipping-dhl'); ?>"></span><br/>
								<input class="input-text regular-input " type="text" name="elex_dhl_return_receiver_postcode" id="elex_dhl_return_receiver_postcode" value="<?php echo ( isset($general_settings['return_shipment_address']['postcode']) ) ? $general_settings['return_shipment_address']['postcode'] : ''; ?>" required>
							</fieldset>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_add_trackingpin_shipmentid"><?php _e('Tracking', 'wf-shipping-dhl'); ?></label>

			</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
			<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_add_trackingpin_shipmentid" id="wf_dhl_shipping_add_trackingpin_shipmentid"  value="yes" <?php echo ( isset($general_settings['add_trackingpin_shipmentid']) && $general_settings['add_trackingpin_shipmentid'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Enable Tracking', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to activate the tracking feature of the plugin. Custom tracking message - Provide your own tracking message which will be displayed in the order completion email. ', 'wf-shipping-dhl'); ?>" required></span>
			</fieldset>
			</td>
		</tr>
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_include_woocommerce_tax"><?php _e('Commercial Invoice', 'wf-shipping-dhl'); ?></label>

			</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">

				<fieldset style="padding:3px;" id="">
					 <label for="wf_dhl_shipping_classic_commercial_invoice"><?php _e('Method to generate the Invoice', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the Invoice generator option. DHL Commercial Invoice option uses DHL APIs to generate the commercial invoice. Where as Classic Commercial Invoice option uses FPDF libraries to generate the invoice without using any external API call.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_shipping_classic_commercial_invoice" id="wf_dhl_shipping_classic_commercial_invoice" style="width:65%;">
					   <?php
						$selected_value = isset($general_settings['classic_commercial_invoice']) ? $general_settings['classic_commercial_invoice'] : 'classic';
						foreach ($invoice_generator as $key => $value) {
							if ($key == $selected_value) {
								echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
							} else {
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
						}
						?>
					</select><br>
				</fieldset>

				<fieldset style="padding:3px;" id="wf_dhl_shipping_invoice_type_field">
					 <label for="wf_dhl_shipping_invoice_type"><?php _e('Invoice Type', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the type of invoice you want to generate.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_shipping_invoice_type" id="wf_dhl_shipping_invoice_type" style="width:65%;">
					   <?php
						$selected_value = isset($general_settings['invoice_type']) ? $general_settings['invoice_type'] : 'CMI';
						foreach ($invoice_type as $key => $value) {
							if ($key == $selected_value) {
								echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
							} else {
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
						}
						?>
					</select><br>
				</fieldset>

				<fieldset style="padding:3px;" id="wf_dhl_shipping_invoice_quantity_unit_field">
					 <label for="wf_dhl_shipping_invoice_quantity_unit"><?php _e('Quantity Unit', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the quantity unit to display in the invoice.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_shipping_invoice_quantity_unit" id="wf_dhl_shipping_invoice_quantity_unit" style="width:65%;">
						<?php
						$selected_value = isset($general_settings['invoice_quantity_unit']) ? $general_settings['invoice_quantity_unit'] : 'PCS';
						foreach ($invoice_quantity_unit as $key => $value) {
							if ($key == $selected_value) {
								echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
							} else {
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
						}
						?>
				</select>
					  <br>
				</fieldset>

				<fieldset style="padding:3px;" id="wf_dhl_shipping_invoice_language_code_field">
					 <label for="wf_dhl_shipping_invoice_language_code"><?php _e('Language', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the language for the invoice.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_shipping_invoice_language_code" id="wf_dhl_shipping_invoice_language_code" style="width:65%;">

						<?php
						$selected_value = isset($general_settings['invoice_language_code']) ? $general_settings['invoice_language_code'] : 'en';
						foreach ($invoice_languages as $key => $value) {
							if ($key == $selected_value) {
								echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
							} else {
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
						}
						?>
				</select>
					   <br>
				</fieldset>

				<fieldset style="padding:3px;" id="wf_dhl_shipping_exporter_id_field">
					 <label for="wf_dhl_shipping_exporter_id"><?php _e('Exporter ID', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Enter the Exporter ID.', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_exporter_id" id="wf_dhl_shipping_exporter_id"  value="<?php echo ( isset($general_settings['exporter_id']) ) ? $general_settings['exporter_id'] : ''; ?>" >
				</fieldset>

				<fieldset style="padding:3px;" id="wf_dhl_shipping_exporter_code_field">
					 <label for="wf_dhl_shipping_exporter_code"><?php _e('Exporter Code', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Enter the Exporter Code.', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_exporter_code" id="wf_dhl_shipping_exporter_code"  value="<?php echo ( isset($general_settings['exporter_code']) ) ? $general_settings['exporter_code'] : ''; ?>" >
				</fieldset>

				<fieldset style="padding:3px;" id="wf_dhl_shipping_type_of_export_field">
					 <label for="wf_dhl_shipping_type_of_export"><?php _e('Type of Export', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the type of export.', 'wf-shipping-dhl'); ?>" ></span><br>
					<select name="wf_dhl_shipping_type_of_export" id="wf_dhl_shipping_type_of_export" style="width:65%;">

						<?php
						$selected_value = isset($general_settings['type_of_export']) ? $general_settings['type_of_export'] : '';
						foreach ($type_of_export as $key => $value) {
							if ($key == $selected_value) {
								echo '<option value="' . $key . '" selected="true">' . $value . '</option>';
							} else {
								echo '<option value="' . $key . '">' . $value . '</option>';
							}
						}
						?>
				</select>
					   <br>
				</fieldset>

			<fieldset style="padding:3px;" id="wf_dhl_shipping_export_reason_field">
				 <label for="wf_dhl_shipping_export_reason"><?php _e('Reason for Export', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Enter the reason for export.', 'wf-shipping-dhl'); ?>" ></span><br>
				<input class="input-text regular-input " type="text" name="wf_dhl_shipping_export_reason" id="wf_dhl_shipping_export_reason"  value="<?php echo ( isset($general_settings['export_reason']) ) ? $general_settings['export_reason'] : ''; ?>" >
			</fieldset>

			<fieldset style="padding:3px;" id="wf_dhl_shipping_eori_no_field">
				 <label for="wf_dhl_shipping_eori_no"><?php _e('EORI Number', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('The EORI Number contains the information about the shipper’s unique idenetificaiton number of the company.', 'wf-shipping-dhl'); ?>" ></span><br>
				<input class="input-text regular-input " type="text" name="wf_dhl_shipping_eori_no" id="wf_dhl_shipping_eori_no"  value="<?php echo ( isset($general_settings['eori_no']) ) ? $general_settings['eori_no'] : ''; ?>" >
			</fieldset>

			<fieldset style="padding:3px;" id="wf_dhl_shipping_include_freight_cost_field">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_include_freight_cost" id="wf_dhl_shipping_include_freight_cost"  value="yes" <?php echo ( isset($general_settings['include_freight_cost']) && $general_settings['include_freight_cost'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Include Freight Cost', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this option if you want to include freight cost in the invoice.', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;" id="wf_dhl_shipping_include_insurance_cost_field">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_include_insurance_cost" id="wf_dhl_shipping_include_insurance_cost"  value="yes" <?php echo ( isset($general_settings['include_insurance_cost']) && $general_settings['include_insurance_cost'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Include Insurance Cost', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this option if you want to include insurance cost in the invoice.', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>

			<fieldset style="padding:3px;" id="wf_dhl_shipping_include_woocommerce_tax_field">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_include_woocommerce_tax" id="wf_dhl_shipping_include_woocommerce_tax"  value="yes" <?php echo ( isset($general_settings['include_woocommerce_tax']) && $general_settings['include_woocommerce_tax'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Display WooCommerce Tax Details', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enabling this option will include WooCommerce Tax on DHL Commercial Invoice. ', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;" id="include_vat_number_express_dhl_elex_field">
				<input class="input-text regular-input " type="checkbox" name="include_vat_number_express_dhl_elex" id="include_vat_number_express_dhl_elex"  value="yes" <?php echo ( isset($general_settings['include_shipper_vat_number']) && $general_settings['include_shipper_vat_number'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Include VAT number', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enabling this option will include the VAT Number on DHL Commercial Invoice. ', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;" id="shipper_vat_number_express_dhl_elex">
				 <label for="shipper_vat_number_express_dhl_elex"><?php _e('VAT Number', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Fill in your VAT number to display on the Commercial Invoice.', 'wf-shipping-dhl'); ?>" ></span><br>
				<input class="input-text regular-input " type="text" name="shipper_vat_number_express_dhl_elex" id="shipper_vat_number_express_dhl_elex"  value="<?php echo ( isset($general_settings['shipper_vat_number']) ) ? $general_settings['shipper_vat_number'] : ''; ?>" >
			</fieldset>
			<fieldset style="padding:3px;" id="option_commercial_invoice_shipping_service_type_field">
				<input class="input-text regular-input " type="checkbox" name="option_commercial_invoice_shipping_service_type" id="option_commercial_invoice_shipping_service_type"  value="yes" <?php echo ( isset($general_settings['include_shipping_service_type']) && $general_settings['include_shipping_service_type'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Display shipping Service Type', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enabling this option will include Shipping Service type on the DHL Commercial Invoice. ', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			<fieldset style="padding:3px;" id="option_generate_proforma_invoice_dhl_elex_field">
			  <input class="input-text regular-input " type="checkbox" name="option_generate_proforma_invoice_dhl_elex" id="option_generate_proforma_invoice_dhl_elex"  value="yes" <?php echo ( isset($general_settings['option_generate_proforma_invoice']) && $general_settings['option_generate_proforma_invoice'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Enable Proforma Invoice', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose this option to generate Proforma Invoice. The Proforma Invoice should be generated and downloaded before creating a shipment.', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>

			</td>
		</tr>
		<tr id="wf_dhl_shipping_customer_signature_url_tr" valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_customer_signature_url"><?php _e('Signature for Classic Invoice', 'wf-shipping-dhl'); ?></label>
			</td><td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;" id="">
					 <label for="wf_dhl_shipping_customer_signature_url"><?php _e('Select Image of the Signature', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('This option allows you to upload your Signature, which will be part of the Invoices generated. Recommended image format: PNG (Transparent). Recommended Dimensions: 160 x 70. ', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_customer_signature_url" id="wf_dhl_shipping_customer_signature_url"  value="<?php echo ( isset($general_settings['customer_signature_url']) ) ? $general_settings['customer_signature_url'] : ''; ?>" ><br><a href="#" id="dhl_media_upload_image_signature_button" class="button-secondary"><?php _e('Choose Image', 'wf-shipping-dhl'); ?></a>
				</fieldset>

			</td>
		</tr>
		<tr valign="top" id="dhl_email_service">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_dhl_email_notification_service"><?php _e('DHL Email Service', 'wf-shipping-dhl'); ?></label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;">
			<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_dhl_email_notification_service" id="wf_dhl_shipping_dhl_email_notification_service"  value="yes" <?php echo ( isset($general_settings['dhl_email_notification_service']) && $general_settings['dhl_email_notification_service'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('DHL Tracking Message to Customers', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('With due permission from your customers (In order to be GDPR compliant), you can enable this option which would send your customer’s email id to DHL, using which DHL would be able to send shipment tracking related information to them.', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>

				<fieldset style="padding:3px;" id="wf_dhl_email_notification_message">
					 <label for="wf_dhl_shipping_dhl_email_notification_message"><?php _e('Shipper Message', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Message to customers.', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input " type="text" name="wf_dhl_shipping_dhl_email_notification_message" id="wf_dhl_shipping_dhl_email_notification_message"  value="<?php echo ( isset($general_settings['dhl_email_notification_message']) ) ? $general_settings['dhl_email_notification_message'] : ''; ?>" >
				</fieldset>

			</td>
		</tr>
		<tr valign="top">
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_add_pickup"><?php _e('Pickup', 'wf-shipping-dhl'); ?></label>
			</td>
			<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
				<fieldset style="padding:3px;">
				<input class="input-text regular-input " type="checkbox" name="wf_dhl_shipping_add_pickup" id="wf_dhl_shipping_add_pickup"  value="yes" <?php echo ( isset($general_settings['add_pickup']) && $general_settings['add_pickup'] === 'yes' ) ? 'checked' : ''; ?> >  <?php _e('Enable Pickup', 'wf-shipping-dhl'); ?> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this if you want DHL to be able to pickup the shipment from your store. ', 'wf-shipping-dhl'); ?>" ></span>
				</fieldset>

				<fieldset style="padding:3px;" id="wf_pickup_date">
					 <label for="wf_dhl_shipping_pickup_date"><?php _e('Schedule Pickup After', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('How many days after the order has been placed, do you want the pickup to arrive at your store.', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input " min="0" max="7" type="number" name="wf_dhl_shipping_pickup_date" id="wf_dhl_shipping_pickup_date"  value="<?php echo ( isset($general_settings['pickup_date']) ) ? $general_settings['pickup_date'] : ''; ?>" placeholder="0"> <?php _e('Day(s).', 'wf-shipping-dhl'); ?>
				</fieldset>

				<fieldset style="padding:3px;" id="wf_pickup_from_to">
					 <label for="wf_dhl_shipping_pickup_time_from"><?php _e('Pickup Availbility Time (24 hours Format)', 'wf-shipping-dhl'); ?></label> <span style="color:red;"> *</span> <span class="woocommerce-help-tip" data-tip="<?php _e('Give a definite range of time within which you can allow pickup in order to avoid conflict.', 'wf-shipping-dhl'); ?>" ></span><br>
					<b><?php _e('From', 'wf-shipping-dhl'); ?>:</b> <input class="input-text regular-input " size="7"  type="text" name="wf_dhl_shipping_pickup_time_from" id="wf_dhl_shipping_pickup_time_from"  value="<?php echo ( isset($general_settings['pickup_time_from']) ) ? $general_settings['pickup_time_from'] : ''; ?>" placeholder="From">
					<b><?php _e('To', 'wf-shipping-dhl'); ?>:</b> <input class="input-text regular-input "  size="7" type="text" name="wf_dhl_shipping_pickup_time_to" id="wf_dhl_shipping_pickup_time_to"  value="<?php echo ( isset($general_settings['pickup_time_to']) ) ? $general_settings['pickup_time_to'] : ''; ?>" placeholder="To">
				</fieldset>

				<fieldset style="padding:3px; " id="wf_pickup_details" >
					 <label for="wf_dhl_shipping_pickup_person"><?php _e('Pickup Person Name', 'wf-shipping-dhl'); ?></label> <span style="color:red;"> *</span> <span class="woocommerce-help-tip" data-tip="<?php _e('Give a contact person’s name and contact no. who can be contacted in case of any convenience..', 'wf-shipping-dhl'); ?>" ></span><br>
					<input class="input-text regular-input "  type="text" name="wf_dhl_shipping_pickup_person" id="wf_dhl_shipping_pickup_person"  value="<?php echo ( isset($general_settings['pickup_person']) ) ? $general_settings['pickup_person'] : ''; ?>" placeholder="Person Name">
					<input class="input-text regular-input "  type="text" name="wf_dhl_shipping_pickup_contact" id="wf_dhl_shipping_pickup_contact"  value="<?php echo ( isset($general_settings['pickup_contact']) ) ? $general_settings['pickup_contact'] : ''; ?>" placeholder="Contact Number">
				</fieldset>
			</td>
		</tr>
		<tr>
			<td style="width:40%;font-weight:800;">
				<label for="wf_dhl_shipping_ioss"><?php _e('Shipper Registration Details', 'wf-shipping-dhl'); ?></label>
			</td>
			<td>
			<fieldset style="padding:3px;" id="include_ioss_dhl_elex_fields">
				<input class="input-text regular-input " type="checkbox" name="include_ioss_dhl_elex_field" id="include_ioss_dhl_elex_field"  value= "yes"<?php echo ( isset($general_settings['include_ioss_number_check']) && $general_settings['include_ioss_number_check'] === 'yes' ) ? 'checked' : ''; ?> > <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this option to include registration details in the DHL commercial invoice. ', 'wf-shipping-dhl'); ?>" ></span>
			</fieldset>
			</td>
		</tr>
		<tr>

			<td>
				<label for="wf_dhl_shipping_number_reg"><?php _e('By enabling this option, you will receive the DHL commercial invoice in the updated format.', 'wf-shipping-dhl'); ?></label>
			</td>
		</tr>
		<tr>

			<table class="dhl_ioss widefat" id="dhl_ioss_country">
				<thead>
					<tr>
						<th style="text-align:center;"><?php _e( 'Country', 'wf-shipping-dhl' ); ?></th>
						<th style="text-align:center;"><?php _e( 'Registration Number', 'wf-shipping-dhl' ); ?></th>
						<th style="text-align:center;"><?php _e( 'Issuer Country Code', 'wf-shipping-dhl' ); ?></th>
					</tr>
				</thead>
				<tbody class="dhl_ioss_body">
					<?php
					if ( !empty( $general_settings['eh_country_filter_type'] ) ) {

						foreach ($general_settings['eh_country_filter_type'] as $key => $value) {
							$newzland  = '';
							$norway    = '';
							$australia = '';
							$europe    = '';
							$none      = '';

							$ioss_number       = !empty( $general_settings['ioss_number'][$key]) ? $general_settings['ioss_number'][$key] : '' ;
							$ioss_country_code = !empty( $general_settings['ioss_country_code'][$key]) ? $general_settings['ioss_country_code'][$key] : '';

							switch ($value) {
								case 'NZ':
									$newzland = 'selected';
									break;
								case 'NW':
									$norway = 'selected';
									break;
								case 'AU':
									$australia = 'selected';
									break;
								case 'EU':
									  $europe = 'selected';
									break;
								case '':
									$none = 'selected';
									break;
							}
							?>
							<tr class="new">
								<td>
									<select class = "eh_country_filter_type"  id="eh_country_filter_type[<?php echo( $key ); ?>]" name="eh_country_filter_type[<?php echo( $key ); ?>]" data-value = '<?php echo( $key ); ?>' style="width:100%;">
									<option value=" " <?php echo $none; ?> >None</option>
									<option value="NZ" <?php echo $newzland; ?> >New Zealand</option>
									<option value="NW" <?php echo $norway; ?>>Norway</option>
									<option value="AU" <?php echo $australia; ?>>Australia</option>
									<option value="EU" <?php echo $europe; ?>>Europe27</option>
								</select>
								</td>
								<td><input type="text" style="width:100%;" name="ioss_number[<?php echo( $key ); ?>]" value ="<?php echo( $ioss_number ); ?>" placeholder="Enter Registeration Number"/></td>
								<td><input type="text" style="width:100%;" id= "eh_country_code<?php echo( $key ); ?>" name="ioss_country_code[<?php echo( $key ); ?>]" value ="<?php echo( $ioss_country_code ); ?>" placeholder="XX Country Code"/></td>
								<td class="remove_icons"></td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
						 <button class="btn btn-info btn-sm" id="eh_add_ioss"> Add Details</button>
					 </td>
					</tr>
				</tfoot>
			</table>
			 
		</tr>
		
		<tr>

			<td colspan="2" style="text-align:center;margin-left: -10%; margin-bottom:10px;">
				<input type="submit" style="margin-bottom:10px;"align="center" value="<?php _e('Save Changes', 'wf-shipping-dhl'); ?>" class="button button-primary" name="wf_dhl_label_save_changes_button">
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			
			showHideVATNumberField();
			showHideReturnAddress();
			showHideReceiverDutyPaymentType();
			elex_show_hide_invoice_fields();
			addIossSettings();
			Iosscheck();

			jQuery('#include_ioss_dhl_elex_field').change(function(){
				Iosscheck();
			});


			jQuery('#wf_dhl_shipping_classic_commercial_invoice').change(function(){
				elex_show_hide_invoice_fields();
			});
			jQuery('#wf_dhl_shipping_add_pickup').change(function(){
				if(jQuery('#wf_dhl_shipping_add_pickup').is(':checked')) {
					jQuery('#wf_pickup_date').show();
					jQuery('#wf_pickup_from_to').show();
					jQuery('#wf_pickup_details').show();
				}else
				{
					jQuery('#wf_pickup_date').hide();
					jQuery('#wf_pickup_from_to').hide();
					jQuery('#wf_pickup_details').hide();
				}
			}).change();

			jQuery('#wf_dhl_shipping_add_trackingpin_shipmentid').change(function(){
				if(jQuery(wf_dhl_shipping_add_trackingpin_shipmentid).is(':checked')) {
					jQuery('#dhl_email_service').show();
				}
			}).change();

			jQuery('#wf_dhl_shipping_return_label_key').change(function(){
				if(jQuery('#wf_dhl_shipping_return_label_key').is(':checked')) {
					jQuery('#wf_return_label_acc_number').show();
					jQuery('#elex_dhl_field_return_address_different').show();
				}else
				{
					jQuery('#wf_return_label_acc_number').hide();
					jQuery('#elex_dhl_field_return_address_different').hide();
				}
			}).change();

			jQuery('#wf_dhl_shipping_request_archive_airway_label').change(function(){
				if(jQuery('#wf_dhl_shipping_request_archive_airway_label').is(':checked')) {
					jQuery('#wf_no_of_archive_bills').show();
				}else
				{
					jQuery('#wf_no_of_archive_bills').hide();
				}
			}).change();
			jQuery('#wf_dhl_shipping_dhl_email_notification_service').change(function(){
				if(jQuery('#wf_dhl_shipping_dhl_email_notification_service').is(':checked')) {
					jQuery('#wf_dhl_email_notification_message').show();
				}else
				{
					jQuery('#wf_dhl_email_notification_message').hide();
				}
			}).change();
			jQuery('#wf_dhl_shipping_dutypayment_type').change(function(){
				if(jQuery(this).val() == 'T') {
					jQuery('#wf_t_acc_number').show();
				}else
				{
					jQuery('#wf_t_acc_number').hide();
				}
			}).change();

			jQuery('#wf_dhl_express_default_un_number_field').hide();
			var default_special_service_value = jQuery('#wf_dhl_shipping_default_special_service').val();

			if(default_special_service_value != 'N'){
				jQuery('#wf_dhl_express_default_un_number_field').show();
			}

			jQuery('#wf_dhl_shipping_default_special_service').change(function(){
				if(jQuery('#wf_dhl_shipping_default_special_service').val() != 'N'){
					jQuery('#wf_dhl_express_default_un_number_field').show();
				}else{
					jQuery('#wf_dhl_express_default_un_number_field').hide();
				}
			});

			jQuery('#include_vat_number_express_dhl_elex').change(function(){
				showHideVATNumberField();
			});

			function showHideVATNumberField(){
				if(jQuery('#include_vat_number_express_dhl_elex').is(':checked')){
					jQuery('#shipper_vat_number_express_dhl_elex').show();
				}else{
					jQuery('#shipper_vat_number_express_dhl_elex').hide();
				}
			}

			function elex_show_hide_invoice_fields() {
				if(jQuery('#wf_dhl_shipping_classic_commercial_invoice').val() == 'classic'){
					jQuery('#wf_dhl_shipping_invoice_quantity_unit_field').hide();
					jQuery('#wf_dhl_shipping_invoice_language_code_field').hide();
					jQuery('#wf_dhl_shipping_type_of_export_field').hide();
					jQuery('#wf_dhl_shipping_export_reason_field').hide();
					jQuery('#wf_dhl_shipping_eori_no_field').hide();
					jQuery('#wf_dhl_shipping_exporter_id_field').hide();
					jQuery('#wf_dhl_shipping_exporter_code_field').hide();
					jQuery('#wf_dhl_shipping_include_freight_cost_field').hide();
					jQuery('#wf_dhl_shipping_include_insurance_cost_field').hide();
					jQuery('#wf_dhl_shipping_invoice_type_field').hide();
					jQuery('#wf_dhl_shipping_include_woocommerce_tax_field').show();
					jQuery('#option_commercial_invoice_shipping_service_type_field').show();
					jQuery('#option_generate_proforma_invoice_dhl_elex_field').show();
					jQuery('#wf_dhl_shipping_customer_signature_url_tr').show();
				}
				else {
					jQuery('#wf_dhl_shipping_invoice_quantity_unit_field').show();
					jQuery('#wf_dhl_shipping_invoice_language_code_field').show();
					jQuery('#wf_dhl_shipping_type_of_export_field').show();
					jQuery('#wf_dhl_shipping_export_reason_field').show();
					jQuery('#wf_dhl_shipping_eori_no_field').show();
					jQuery('#wf_dhl_shipping_exporter_id_field').show();
					jQuery('#wf_dhl_shipping_exporter_code_field').show();
					jQuery('#wf_dhl_shipping_include_freight_cost_field').show();
					jQuery('#wf_dhl_shipping_include_insurance_cost_field').show();
					jQuery('#wf_dhl_shipping_invoice_type_field').show();
					jQuery('#wf_dhl_shipping_include_woocommerce_tax_field').hide();
					jQuery('#option_commercial_invoice_shipping_service_type_field').hide();
					jQuery('#option_generate_proforma_invoice_dhl_elex_field').hide();
					jQuery('#wf_dhl_shipping_customer_signature_url_tr').hide();

				}
			}

			function showHideReturnAddress(){
				if(jQuery('#elex_dhl_return_address_different').is(':checked')){
					jQuery('#elex_dhl_row_return_address_different').show();
					jQuery('#elex_dhl_return_receiver_person_name').prop("required", true);
					jQuery('#elex_dhl_return_receiver_company_name').prop("required", true);
					jQuery('#elex_dhl_return_receiver_phone_number').prop("required", true);
					jQuery('#elex_dhl_return_receiver_email').prop("required", true);
					jQuery('#elex_dhl_return_receiver_street').prop("required", true);
					jQuery('#elex_dhl_return_receiver_city').prop("required", true);
					jQuery('#elex_dhl_return_receiver_state').prop("required", true);
					jQuery('#elex_dhl_return_receiver_postcode').prop("required", true);
				}else{
					jQuery('#elex_dhl_return_receiver_person_name').removeAttr('required');
					jQuery('#elex_dhl_return_receiver_company_name').removeAttr('required');
					jQuery('#elex_dhl_return_receiver_phone_number').removeAttr('required');
					jQuery('#elex_dhl_return_receiver_email').removeAttr('required');
					jQuery('#elex_dhl_return_receiver_street').removeAttr('required');
					jQuery('#elex_dhl_return_receiver_city').removeAttr('required');
					jQuery('#elex_dhl_return_receiver_state').removeAttr('required');
					jQuery('#elex_dhl_return_receiver_postcode').removeAttr('required');
					jQuery('#elex_dhl_row_return_address_different').hide();
				}
			};

			jQuery('#elex_dhl_return_address_different').change(function(){
				showHideReturnAddress();
			});

			jQuery('#wf_dhl_shipping_dutypayment_type').change(function(){
				showHideReceiverDutyPaymentType();
			});

			

			function showHideReceiverDutyPaymentType(){
				if(jQuery('#wf_dhl_shipping_dutypayment_type').val() == 'R'){
					jQuery('#fieldset_receiver_duty_payment_type_dhl_elex').show();
				}else{
					jQuery('#fieldset_receiver_duty_payment_type_dhl_elex').hide();
				}
			}
		function Iosscheck(){
			if(document.getElementById('include_ioss_dhl_elex_field').checked){
				jQuery('#dhl_ioss_country').show();
			}else{
				jQuery('#dhl_ioss_country').hide();
			}
		} 
		function addIossSettings(){
			
			jQuery('#eh_add_ioss').click( function(e){
				e.preventDefault();
				var $tbody = jQuery('.dhl_ioss').find('tbody');
					var size = $tbody.find('tr').size();
					var code = '<tr class="new">\
						<td>\
							<select class = "eh_country_filter_type"  id="eh_country_filter_type'+size+'" name="eh_country_filter_type['+size+']" data-value = '+size+' style="width:100%;">\
							<option value=" " >None</option>\
							<option value="NZ" >New Zealand</option>\
							<option value="NW">Norway</option>\
							<option value="AU">Australia</option>\
							<option value="EU">Europe27</option>\
						</select>\
						</td>\
							<td><input type="text" style="width:100%;" name="ioss_number[' + size + ']" placeholder="Enter Registeration Number" /></td>\
							<td><input type="text" style="width:100%;" id= "eh_country_code'+size+'" name="ioss_country_code[' + size + ']" value=" " placeholder="XX Issuer Country Code" /></td>\
							<td class="remove_icons"></td>\
						</tr>';

					$tbody.append( code );
			});
		}
		jQuery(".dhl_ioss_body").on('click', '.remove_icons', function () {
			jQuery(this).closest("tr").remove();
		});
		
	});
		

	</script>
