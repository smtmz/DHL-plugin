<?php

if (!class_exists('WeightPack')) {
	include_once 'class-wf-weight-packing-util.php';
	class WeightPack {
		private $package_requests;
		private $pack_obj;
		function __construct( $strategy, $options = array() ) {
			
			switch ($strategy) {
				case 'pack_ascending':
					include_once 'class-wf-weight-strategy-ascend.php';
					$this->pack_obj	=	new WeightPackAscend();
					break;
				//case 'pack_descending':
				case 'pack_simple':
					include_once 'class-wf-weight-strategy-simple.php';
					$this->pack_obj	=	new WeightPackSimple();
					break;
				default:
					include_once 'class-wf-weight-strategy-descend.php';
					$this->pack_obj	=	new WeightPackDescend();
					break;
			}
		}
		
		function set_max_weight( $max_weight) {
			$this->pack_obj->set_max_weight($max_weight);
		}
		
		function add_item( $item_weight, $item_data, $quantity) {
			$this->pack_obj->add_item($item_weight,	$item_data,	$quantity);
		}
		
		function pack_items() {
			$this->pack_obj->pack_items();
			return $this->get_result();
		}
		
		function get_packable_items() {
			return $this->pack_obj->get_packable_items();
		}
		
		function get_result() {
			return $this->pack_obj->get_result();
		}
	}
	
	abstract class WeightPackStrategy {
		private $packable_items	=	array();
		private $max_weight;
		public 	$pack_util;
		private $result;
		
		public function __construct() {
			$this->pack_util =	new WeightPacketUtil();				
		}
		
		public function set_max_weight( $max_weight) {
			$this->max_weight =	$max_weight;
		}
		
		public function get_max_weight() {
			return $this->max_weight;
		}
		
		public function set_result( $result) {
			$this->result =	$result;
		}
		
		public function get_result() {
			return $this->result;
		}
		
		public function get_packable_items() {
			return $this->packable_items;
		}	
		
		public function add_item( $item_weight, $item_data, $quantity = 1) {
			for ($i=0;$i<$quantity;$i++) {
				$this->packable_items[] =array(
					'weight'	=>	$item_weight,
					'data'		=>	$item_data
				);
			}
		}
		
		abstract function pack_items();
	}	
	
	class WeightPackResult {
		private	$errors   =	array();
		private $packed   =	array();
		private	$unpacked =	array();
		
		public function set_error( $errors) {
			$this->errors[] =	$errors;
		}
		
		public function set_packed_boxes( $packages) {
			$this->packed =	$packages;
		}
		
		public function set_unpacked_items( $packages) {
			$this->unpacked =	$packages;
		}
		
		public function get_errors() {
			return $this->errors;
		}
		
		public function get_packed_boxes() {
			return $this->packed;
		}
		
		public function get_unpacked_items() {
			return $this->unpacked;
		}
	}
}
