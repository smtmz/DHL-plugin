<?php

/**
 * The is the factory which creates shipment tracking objects
 */
class WfTrackingFactory {
	public static function init() {
		self::wf_include_once( 'WfTrackingAbstract', 'class-wf-tracking-common.php' );
		self::wf_include_once( 'WfTrackingAbstract', 'class-wf-tracking-abstract.php' );
	}

	public static function create( $shipment_source_obj ) {
		switch ( $shipment_source_obj->shipping_service ) {
			case '':
				$tracking_obj = null;
				break;
			case 'united-states-postal-service-usps':
				self::wf_include_once( 'WfTrackingUSPS', 'class-wf-tracking-usps.php' );
				$tracking_obj = new WfTrackingUSPS();
				break;
			case 'canada-post':
				self::wf_include_once( 'WfTrackingCanadaPost', 'class-wf-tracking-canadapost.php' );
				$tracking_obj = new WfTrackingCanadaPost();
				break;
			default:
				self::wf_include_once( 'WfTrackingDefault', 'class-wf-tracking-default.php' );
				$tracking_obj = new WfTrackingDefault();
				break;
		}

		if ( null != $tracking_obj ) {
			$tracking_obj->init ( $shipment_source_obj );
		}

		return $tracking_obj;
	}

	private static function wf_include_once( $class_name, $file_name ) {
		if ( ! class_exists( $class_name ) ) {
			include_once  $file_name ;
		}
	}
}


