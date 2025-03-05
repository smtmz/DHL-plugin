<tr valign="top" id="ec_packing_options">
	<td class="titledesc" colspan="2" style="padding-left:0px">
	<strong id="box_packing_label"><?php _e( 'Box Sizes', 'wf-shipping-dhl' ); ?></strong><br><br>
		<style type="text/css">
			.dhl_boxes td, .dhl_services td {
				vertical-align: middle;
				padding: 4px 7px;
			}
			.dhl_services th, .dhl_boxes th {
				padding: 9px 7px;
			}
			.dhl_boxes td input {
				margin-right: 4px;
			}
			.dhl_boxes .check-column {
				vertical-align: middle;
				text-align: left;
				padding: 0 7px;
			}
			.dhl_services th.sort {
				width: 16px;
				padding: 0 16px;
			}
			.dhl_services td.sort {
				cursor: move;
				width: 16px;
				padding: 0 16px;
				cursor: move;
				background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;
			}
		</style>
		<table class="dhl_boxes widefat" id="dhl_packet_box_packing">
			<thead>
				<tr>
					<th class="check-column"><input type="checkbox" /></th>
					<th><?php _e( 'Name', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Length', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Width', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Height', 'wf-shipping-dhl' ); ?></th>
					<th><?php _e( 'Box Weight', 'wf-shipping-dhl' ); ?></th>
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
					<th colspan="6">
						<small class="description"><?php _e( 'Preloaded the Dimension and Weight in unit Inches and Pound. If you have selected unit as Centimetre and Kilogram please convert it accordingly.', 'wf-shipping-dhl' ); ?></small>
					</th>
				</tr>
			</tfoot>
			<tbody id="rates">
				<?php
				if ( $this->boxes ) {
					foreach ( $this->boxes as $key => $box ) {
						?>
							<tr>
								<td class="check-column"><input type="checkbox" /></td>
								<input type="hidden" size="5" name="boxes_id[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['id'] ); ?>" />
								<td><input type="text" size="20" name="boxes_name[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['name'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_length[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['length'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_width[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['width'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_height[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['height'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_box_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['box_weight'] ); ?>" /></td>
								<td><input type="text" size="5" name="boxes_max_weight[<?php echo $key; ?>]" value="<?php echo esc_attr( $box['max_weight'] ); ?>" /></td>
								<td><input type="checkbox" name="boxes_enabled[<?php echo $key; ?>]" <?php checked( $box['enabled'], true ); ?> /></td>
							</tr>
							<?php
					}
				}
				?>
			</tbody>
		</table>
		<script type="text/javascript">

			jQuery(window).load(function(){

				jQuery('#woocommerce_dhl_packing_method').change(function(){

					if ( jQuery(this).val() == 'box_packing' )
						jQuery('#packing_options').show();
					else
						jQuery('#packing_options').hide();

				}).change();

				jQuery('.dhl_boxes .insert').click( function() {
					var $tbody = jQuery('.dhl_boxes').find('tbody');
					var size = $tbody.find('tr').size();
					var code = '<tr class="new">\
							<td class="check-column"><input type="checkbox" /></td>\
							<input type="hidden" size="5" name="boxes_id[' + size + ']" />\
							<td><input type="text" size="20" name="boxes_name[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_length[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_width[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_height[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_box_weight[' + size + ']" /></td>\
							<td><input type="text" size="5" name="boxes_max_weight[' + size + ']" /></td>\
							<td><input type="checkbox" name="boxes_enabled[' + size + ']" /></td>\
						</tr>';

					$tbody.append( code );

					return false;
				} );

				jQuery('.dhl_boxes .remove').click(function() {
					var $tbody = jQuery('.dhl_boxes').find('tbody');

					$tbody.find('.check-column input:checked').each(function() {
						jQuery(this).closest('tr').hide().find('input').val('');
					});

					return false;
				});

				// Ordering
				jQuery('.dhl_services tbody').sortable({
					items:'tr',
					cursor:'move',
					axis:'y',
					handle: '.sort',
					scrollSensitivity:40,
					forcePlaceholderSize: true,
					helper: 'clone',
					opacity: 0.65,
					placeholder: 'wc-metabox-sortable-placeholder',
					start:function(event,ui){
						ui.item.css('baclbsround-color','#f6f6f6');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
						dhl_services_row_indexes();
					}
				});

				function dhl_services_row_indexes() {
					jQuery('.dhl_services tbody tr').each(function(index, el){
						jQuery('input.order', el).val( parseInt( jQuery(el).index('.dhl_services tr') ) );
					});
				};

			});

		</script>
	</td>
</tr>
