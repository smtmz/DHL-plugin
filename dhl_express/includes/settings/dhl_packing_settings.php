<?php $this->init_settings(); 
global $woocommerce;
$wc_main_settings = array();
$package_type     = array('BOX'=>__('DHL Box', 'wf-shipping-dhl'),'FLY'=>__('Flyer', 'wf-shipping-dhl'),'YP'=>__('Your Pack', 'wf-shipping-dhl'));
$weight_type      =  array('pack_descending'=>__('Pack heavier items first', 'wf-shipping-dhl'),'pack_ascending'=>__('Pack lighter items first', 'wf-shipping-dhl'));
if (isset($_POST['wf_dhl_packing_save_changes_button'])) {
	$wc_main_settings                          = get_option('woocommerce_wf_dhl_shipping_settings'); 
	$wc_main_settings['packing_method']        = sanitize_text_field($_POST['wf_dhl_shipping_packing_method']);
	$wc_main_settings['dimension_weight_unit'] = ( isset($_POST['wf_dhl_shipping_dimension_weight_unit']) && $_POST['wf_dhl_shipping_dimension_weight_unit'] ==='KG_CM' ) ? 'KG_CM' : 'LBS_IN';
	if ($wc_main_settings['packing_method'] === 'per_item') {
		$wc_main_settings['shp_pack_type'] = sanitize_text_field($_POST['wf_dhl_shipping_shp_pack_type']);
	}
	if ($wc_main_settings['packing_method'] === 'box_packing') {

		$box_data = isset($_POST['boxes_name']) ? array_values($_POST['boxes_name']) : '';	 
		$boxes_name = isset($_POST['boxes_name']) ? array_values($_POST['boxes_name']) : '';
		$boxes_id = isset($_POST['boxes_id']) ? array_values($_POST['boxes_id']) : '';
		$boxes_length = isset($_POST['boxes_length']) ? array_values($_POST['boxes_length']) : '';
		$boxes_width = isset($_POST['boxes_width']) ? array_values($_POST['boxes_width']) : '' ;
		$boxes_height = isset($_POST['boxes_height']) ? array_values($_POST['boxes_height']) : ''  ;
		$boxes_inner_length = isset($_POST['boxes_inner_length']) ? array_values($_POST['boxes_inner_length']) : '';
		$boxes_inner_width = isset($_POST['boxes_inner_width']) ? array_values($_POST['boxes_inner_width']) : '';
		$boxes_inner_height = isset($_POST['boxes_inner_height']) ? array_values($_POST['boxes_inner_height']) : '';
		$boxes_box_weight = isset($_POST['boxes_box_weight']) ? array_values($_POST['boxes_box_weight']) : '';
		$boxes_max_weight = isset($_POST['boxes_max_weight']) ? array_values($_POST['boxes_max_weight']) : '';
		$boxes_enabled = isset($_POST['boxes_enabled']) ? array_values($_POST['boxes_enabled']) : '';
		$boxes_pack_type = isset($_POST['boxes_pack_type']) ? array_values($_POST['boxes_pack_type']) : '';

		$box = array();
		foreach ( $box_data as $key => $value) {
		  $box_id = isset( $boxes_id[$key] ) ? $boxes_id[$key] : '';
			if (!empty($boxes_name[$key])) {

				$box_name           = empty($boxes_name[$key]) ? 'New Box' : sanitize_text_field($boxes_name[$key]);
				$box_length         = empty($boxes_length[$key]) ? 0 : sanitize_text_field($boxes_length[$key]); 
				$box_width        = empty($boxes_width[$key]) ? 0 : sanitize_text_field($boxes_width[$key]); 
				$box_height       = empty($boxes_height[$key]) ? 0 : sanitize_text_field($boxes_height[$key]); 
				$box_inner_length = empty($boxes_inner_length[$key]) ? 0 : sanitize_text_field($boxes_inner_length[$key]); 
				$box_inner_width  = empty($boxes_inner_width[$key]) ? 0 : sanitize_text_field($boxes_inner_width[$key]); 
				$box_inner_height = empty($boxes_inner_height[$key]) ? 0 : sanitize_text_field($boxes_inner_height[$key]); 
				$box_box_weight   = empty($boxes_box_weight[$key]) ? 0 : sanitize_text_field($boxes_box_weight[$key]); 
				$box_max_weight   = empty($boxes_max_weight[$key]) ? 0 : sanitize_text_field($boxes_max_weight[$key]);
				$box_enabled        = isset($boxes_enabled[$key]) ? true : false; 
			 			  
			  $box[$key]          = array(
				  'id' => $box_id,
				  'name' => $box_name,
				  'length' => $box_length,
				  'width' => $box_width,
				  'height' => $box_height,
				  'inner_length' => $box_inner_length,
				  'inner_width' => $box_inner_width,
				  'inner_height' => $box_inner_height,
				  'box_weight' => $box_box_weight,
				  'max_weight' => $box_max_weight,
				  'enabled' => $box_enabled,
				  'pack_type' => $boxes_pack_type[$key]
			  );
			}
			   
		}

		$wc_main_settings['boxes'] = $box;
	}
	if ($wc_main_settings['packing_method'] === 'weight_based') {
		$weight_box_data                            = isset($_POST['weight_boxes_name']) && !empty( $_POST['weight_boxes_name'] ) ? $_POST['weight_boxes_name'] : array();
		$wc_main_settings['box_max_weight']         = !empty($_POST['wf_dhl_shipping_box_max_weight']) ?sanitize_text_field($_POST['wf_dhl_shipping_box_max_weight']) : '';
		$wc_main_settings['weight_packing_process'] = sanitize_text_field($_POST['wf_dhl_shipping_weight_packing_process']);
		$weight_box                                 = array();
		if ( isset($_POST['weight_boxes_name']) && !empty( $_POST['weight_boxes_name'] ) ) {

			$wh_box_names =  array_values($_POST['weight_boxes_name']) ;

			$wh_box_ids = isset($_POST['weight_boxes_id']) ? array_values($_POST['weight_boxes_id']) : '';
			$wh_box_lengths = isset($_POST['weight_boxes_length']) ? array_values($_POST['weight_boxes_length']) : '';
			$wh_box_widths = isset($_POST['weight_boxes_width']) ? array_values($_POST['weight_boxes_width']) : '';
			$wh_box_heights = isset($_POST['weight_boxes_height']) ? array_values($_POST['weight_boxes_height']) : '';
			$wh_boxes_box_weights = isset($_POST['weight_boxes_min_weight']) ? array_values($_POST['weight_boxes_min_weight']) : '';
			$wh_boxes_max_weights = isset($_POST['weight_boxes_max_weight']) ? array_values($_POST['weight_boxes_max_weight']) : '';
			$wh_box_enableds = isset($_POST['weight_boxes_enabled']) ? array_values($_POST['weight_boxes_enabled']) : '';


			foreach ($wh_box_names as $box_key => $size ) {
				$wh_box_id = $_POST['weight_boxes_id'][$box_key];

				$wh_box_name         = empty($size) ? 'New Box' : sanitize_text_field($size);
				$wh_box_length       = empty($wh_box_lengths[$box_key]) ? 0 : sanitize_text_field($wh_box_lengths[$box_key]); 
				$wh_boxes_width      = empty($wh_box_widths[$box_key]) ? 0 : sanitize_text_field($wh_box_widths[$box_key]); 
				$wh_boxes_height     = empty($wh_box_heights[$box_key]) ? 0 : sanitize_text_field($wh_box_heights[$box_key]); 
				$wh_boxes_box_weight = empty($wh_boxes_box_weights[$box_key]) ? 0 : sanitize_text_field($wh_boxes_box_weights[$box_key]); 
				$wh_boxes_max_weight = empty($wh_boxes_max_weights[$box_key]) ? 0 : sanitize_text_field($wh_boxes_max_weights[$box_key]);
				$wh_box_enabled      = isset($wh_box_enableds[$box_key]) ? true : false; 

				$weight_box[$box_key] = array(
						'id' => $wh_box_id,
						'name' => $wh_box_name,
						'length' => $wh_box_length,
						'width' => $wh_boxes_width,
						'height' => $wh_boxes_height,
						'min_weight' => $wh_boxes_box_weight,
						'max_weight' => $wh_boxes_max_weight,
						'enabled' => $wh_box_enabled,
						);
			}
			$wc_main_settings['weight_boxes'] = $weight_box;

		} else {
			$wc_main_settings['weight_boxes'] = array();
		}
		

	}

	update_option('woocommerce_wf_dhl_shipping_settings', $wc_main_settings);
	
	
}

$general_settings   = get_option('woocommerce_wf_dhl_shipping_settings');
$this->boxes        = isset($general_settings['boxes']) ? $general_settings['boxes'] : require   WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'dhl_express/includes/data-wf-box-sizes.php' ;
$this->weight_boxes = isset($general_settings['weight_boxes']) ? $general_settings['weight_boxes'] : array();
?>
<table>
	<tr valign="top">
		<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_shipping_"><?php _e('Packing Options', 'wf-shipping-dhl'); ?>
		</td><td scope="row" class="titledesc" style="display: block;width:100%;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
			 <label for="wf_dhl_shipping_"><?php _e('Parcel Packing Method', 'wf-shipping-dhl'); ?></label> <span class="woocommerce-help-tip" data-tip="<?php _e('Select the Packing method using which you want to pack your products.  Pack items individually - This option allows you to pack each item separately in a box. Hence, multiple items will go in multiple boxes. Pack into boxes with weights and dimensions - This option allows you to pack items into boxes of various sizes. Weight based packing - This option allows you to pack your products based on weight of the package.', 'wf-shipping-dhl'); ?>"></span>    <br>
				<select name="wf_dhl_shipping_packing_method" id="wf_dhl_shipping_packing_method" default="per_item">
					<?php 
						$selected_packing_method = isset($general_settings['packing_method']) ? $general_settings['packing_method'] : 'per_item';
					?>
					<option value="per_item" <?php echo ( $selected_packing_method === 'per_item' ) ? 'selected="true"': ''; ?> ><?php _e('Default: Pack items individually', 'wf-shipping-dhl'); ?></option>
					<option value="box_packing" <?php echo ( $selected_packing_method === 'box_packing' ) ? 'selected="true"': ''; ?> ><?php _e('Recommended: Pack into boxes with weights and dimensions', 'wf-shipping-dhl'); ?></option>
					<option value="weight_based" <?php echo ( $selected_packing_method === 'weight_based' ) ? 'selected="true"': ''; ?> ><?php _e('Weight based: Calculate shipping on the basis of order total weight', 'wf-shipping-dhl'); ?></option>
				</select>
			</fieldset>
			<fieldset style="padding:3px;">
				<?php 
				if (isset($general_settings['dimension_weight_unit']) && $general_settings['dimension_weight_unit'] ==='KG_CM') { 
					?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_dimension_weight_unit"  id="wf_dhl_shipping_dimension_weight_unit"  value="LBS_IN" placeholder=""> <?php _e('Use Pounds,Inches (lbs,in) ', 'wf-shipping-dhl'); ?>
				<input class="input-text regular-input " type="radio"  name="wf_dhl_shipping_dimension_weight_unit" checked="true" id="wf_dhl_shipping_dimension_weight_unit"  value="KG_CM" placeholder=""> Use <?php _e('Kilograms,Centimeters (Kg,cm)', 'wf-shipping-dhl'); ?>
				<?php } else { ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_dimension_weight_unit" checked="true" id="wf_dhl_shipping_dimension_weight_unit"  value="LBS_IN" placeholder=""> <?php _e('Use Pounds,Inches (lbs,in) ', 'wf-shipping-dhl'); ?>
				<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_dimension_weight_unit" id="wf_dhl_shipping_dimension_weight_unit"  value="KG_CM" placeholder=""> <?php _e('Use Kilograms,Centimeters (Kg,cm)', 'wf-shipping-dhl'); ?> <br>
				<?php 
				} 
				if ($selected_packing_method === 'weight_based') {
					?>
					<span style=" float:right; display:block; font-size:10px; color: black; border: 1px solid blue ; border-radius:5px;">As per the DHL Express API version 10, package dimensions are now mandatory to get the shipment price and generate the label. The Shop owner can still use the weight-based packing algorithm by defining the dimensions of the box in which items are packed. The box dimensions and weight will be passed to DHL API, to receive the real-time shipment rates.</span>    <br>
				<?php 
				}
				?>
			</fieldset>
		</td>
	</tr>
	<tr>
		
		<tr id="packing_options">
			<td colspan="2">
			<?php require  WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'dhl_express/includes/html-wf-box-packing.php'; ?>
			</td>
		</tr>
		<tr id="packing_options_shp_pack_type">
			<td style="width:35%;font-weight:800;">
			<label for="wf_dhl_shipping_shp_pack_type"><?php _e('Pack items individually <br/>(Package Type)', 'wf-shipping-dhl'); ?></label> 
			<span class="woocommerce-help-tip" data-tip="DHL Box: There are the most commonly used boxes for packing. These are the boxes which get populated when you install the plugin.<br/>Flyer: This option is suitable for Binded documents and Flat materials.<br/> Your Box: With this option, your item gets packed into customized box.<br/> For example, the shipping cost of Item X is £10. If the customer adds two quantities of Item X to the Cart, then the total shipping cost is £10 x 2, which is £20."></span>
			
		</td><td scope="row" class="titledesc" style="display: block;width:100%;margin-bottom: 20px;margin-top: 3px;">
			<fieldset style="padding:3px;">
			
				<?php 
					$slected_pack_type = isset($general_settings['shp_pack_type']) ? $general_settings['shp_pack_type'] : 'BOX';
				foreach ($package_type as $key => $value) {
					if ($key === $slected_pack_type) {
						echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_shp_pack_type" id="wf_dhl_shipping_shp_pack_type" style="" value="' . $key . '" checked="true" placeholder=""> ' . $value . ' ';
					} else {
						echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_shp_pack_type" id="wf_dhl_shipping_shp_pack_type" style="" value="' . $key . '"  placeholder=""> ' . $value . ' ';
					}
				}
				?>
			</td>

			<table class="dhl_weight_boxes widefat" id="packing_options_weight_packing_process">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" /></th>
					<th><?php _e( 'Name', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Length', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Width', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Height', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Min Weight', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Max Weight', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Enabled', 'wf-shipping-dhl' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="3">
						<a href="#" class="button plus insert"><?php _e( 'Add Box', 'wf-shipping-dhl' ); ?></a>
						<a href="#" class="button minus remove"><?php _e( 'Remove selected box(es)', 'wf-shipping-dhl' ); ?></a>
					</th>
				</tr>
			</tfoot>
			<tbody id="rates">
				<?php
				if ( $this->weight_boxes ) {
					foreach ( $this->weight_boxes as $key => $box ) {
						?>
							<tr>
								<td class="check-column"><input type="checkbox" /></td>
								<input type="hidden" size="5" name="weight_boxes_id[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['id'] ); ?>" />
								<td><input type="text" size="20" name="weight_boxes_name[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['name'] ); ?>" /></td>
								<td><input type="text" size="5" name="weight_boxes_length[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['length'] ); ?>" /></td>
								<td><input type="text" size="5" name="weight_boxes_width[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['width'] ); ?>" /></td>
								<td><input type="text" size="5" name="weight_boxes_height[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['height'] ); ?>" /></td>
								<td><input type="text" size="5" name="weight_boxes_min_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['min_weight'] ); ?>" /></td>
								<td><input type="text" size="5" name="weight_boxes_max_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['max_weight'] ); ?>" /></td>
								<td><input type="checkbox" name="weight_boxes_enabled[<?php echo $key; ?>]" <?php checked( $box['enabled'], true ); ?> /></td>
							</tr>
							<?php
					}
				}
				?>
			</tbody>
		
		</table> 
		
	<tr>
		<fieldset id="packing_options_weight_packing_process_type" style="padding:3px;">
			<?php 
				$slected_weight_type = isset($general_settings['weight_packing_process']) ? $general_settings['weight_packing_process'] : 'pack_descending';
			foreach ($weight_type as $key => $value) {
				if ($key === $slected_weight_type) {
					echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_weight_packing_process" id="wf_dhl_shipping_weight_packing_process" style="" value="' . $key . '" checked="true" placeholder=""> ' . $value . ' ';
				} else {
					echo '<input class="input-text regular-input " type="radio" name="wf_dhl_shipping_weight_packing_process" id="wf_dhl_shipping_weight_packing_process" style="" value="' . $key . '"  placeholder=""> ' . $value . ' ';
				}
			}
			?>
			</fieldset>
	</tr>
	<tr>  
		<td colspan="2" style="text-align:right;padding-right: 10%;">
			<br/>
			<input type="submit" value="<?php _e('Save Changes', 'wf-shipping-dhl'); ?>" class="button button-primary" name="wf_dhl_packing_save_changes_button">
		</td>
	</tr>
</table>

<script>
jQuery('.dhl_weight_boxes .insert').click( function() {
					var $tbody = jQuery('.dhl_weight_boxes').find('tbody');
					var size = $tbody.find('tr').size();
					var code = '<tr class="new">\
							<td class="check-column"><input type="checkbox" /></td>\
							<input type="hidden" size="1" name="weight_boxes_id[' + size + ']" value ="'+size+'" />\
							<td><input type="text" size="20" name="weight_boxes_name[' + size + ']" /></td>\
							<td><input type="text" size="5"" name="weight_boxes_length[' + size + ']" /></td>\
							<td><input type="text" size="5"" name="weight_boxes_width[' + size + ']" /></td>\
							<td><input type="text" size="5"" name="weight_boxes_height[' + size + ']" /></td>\
							<td><input type="text" size="5"" name="weight_boxes_min_weight[' + size + ']" /></td>\
							<td><input type="text" size="5"" name="weight_boxes_max_weight[' + size + ']" /></td>\
							<td><input type="checkbox" name="weight_boxes_enabled[' + size + ']" /></td>\
													 </tr>';
					$tbody.append( code );
					return false;
				} );


	

</script>
