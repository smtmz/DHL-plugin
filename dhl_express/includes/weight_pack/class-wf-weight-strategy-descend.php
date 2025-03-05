<?php
if (!class_exists('WeightPackDescendExpress')) {
	class WeightPackDescendExpress extends Elex_Weight_Boxpack_Box_DHL {
		public function __construct() {

		}
		
		public function sort_pack_items( $items ) {
			if (is_array($items)) {
				usort($items, array($this, 'sort_items'));
			}
			return $items;
			
		}
		
		private function sort_items( $a, $b) {
			$weight_a =	floatval($a->weight);
			$weight_b =	floatval($b->weight);
			if ($weight_a == $weight_b) {
				return 0;
			}
			return ( $weight_a < $weight_b ) ? +1 : -1;
		}
	}
}
