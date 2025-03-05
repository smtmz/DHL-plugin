<?php 

function wf_validate_crendials( $site_test_mode, $site_id, $site_pwd, $site_country, $mv) {
	if (strlen($site_pwd) < 8 && $mv === 'no') {
		update_option('wf_dhl_validation_error', '<small style="color:red">The Password field should have a minimum of 8 characters.</small>');
		update_option('wf_dhl_shipping_validation_data', 'undone');
		return false;
	}
	global $woocommerce;

	$url = ( $site_test_mode === 'yes' )? 'https://xmlpi-ea.dhl.com/XMLShippingServlet' : 'https://xmlpitest-ea.dhl.com/XMLShippingServlet';

	$mailingDate = date('Y-m-d', time());
$xmlRequest      = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
    <p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
        <GetQuote>
            <Request>
                <ServiceHeader>
                    <SiteID>{$site_id}</SiteID>
                    <Password>{$site_pwd}</Password>
                </ServiceHeader>
            </Request>
            <From>
                <CountryCode>{$site_country}</CountryCode>
            </From>
            <BkgDetails>
                <PaymentCountryCode>{$site_country}</PaymentCountryCode>
                <Date>{$mailingDate}</Date>
                <ReadyTime>PT10H21M</ReadyTime>
                <DimensionUnit>IN</DimensionUnit>
                <WeightUnit>LB</WeightUnit>
                <IsDutiable>N</IsDutiable>
            </BkgDetails>
            <To>
                <CountryCode>{$site_country}</CountryCode>
            </To>
        </GetQuote>
    </p:DCTRequest>
XML;

	$result = wp_remote_post($url, array(
		'method' => 'POST',
		'timeout' => 70,
		'sslverify' => 0,
		'body' => $xmlRequest
		)
	);

	if ('yes' == $mv) {

		if ( is_wp_error( $result ) ) {
			
			return false;
		} elseif (!isset($result['body'])) {
		
			return false;
		}
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string(mb_convert_encoding($result['body'], 'UTF-8', 'ISO-8859-1'));
		if (isset($xml->Response->Status->Condition->ConditionData)) {
		
			return false;

		} else {
			
			return true;
		}

	}
	if ( is_wp_error( $result ) ) {
			$error_message = $result->get_error_message();
			update_option('wf_dhl_validation_error', '<small style="color:red">' . $error_message . '</small>');
			update_option('wf_dhl_shipping_validation_data', 'undone');
			return false;
	} elseif (!isset($result['body'])) {
		update_option('wf_dhl_validation_error', '<small style="color:red">API Informations Invalid</small>');
		update_option('wf_dhl_shipping_validation_data', 'undone');
		return false;
	}
	libxml_use_internal_errors(true);
	$xml = simplexml_load_string(mb_convert_encoding($result['body'], 'UTF-8', 'ISO-8859-1'));
	if (isset($xml->Response->Status->Condition->ConditionData)) {
		update_option('wf_dhl_validation_error', '<small style="color:red">' . $xml->Response->Status->Condition->ConditionData . '</small>');
		update_option('wf_dhl_shipping_validation_data', 'undone');
		return false;

	} else {
		update_option('wf_dhl_shipping_validation_data', 'done');
		update_option('wf_dhl_validation_error', '');
		return true;
	}
	
}