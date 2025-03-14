<tr valign="top" id="service_options">
	<td class="titledesc" colspan="2" style="padding-left:0px">
	<strong><u><?php _e( 'Shipping Services & Price Adjustments', 'wf-shipping-dhl' ); ?></u></strong><br><br>
		<table class="dhl_services widefat">
			<thead>
				<th class="sort">&nbsp;</th>
				<th><?php _e( 'Service Code', 'wf-shipping-dhl' ); ?></th>
				<th><?php _e( 'Name', 'wf-shipping-dhl' ); ?></th>
				<th><?php _e( 'Enabled', 'wf-shipping-dhl' ); ?></th>
				<th><?php echo sprintf( __( 'Price Adjustment (%s)', 'wf-shipping-dhl' ), get_woocommerce_currency_symbol() ); ?></th>
				<th><?php _e( 'Price Adjustment (%)', 'wf-shipping-dhl' ); ?></th>
			</thead>
			<tbody>
				<?php
					$sort                   = 0;
					$this->ordered_services = array();

				foreach ( $this->services as $code => $name ) {

					if ( isset( $this->custom_services[ $code ]['order'] ) ) {
						$sort = $this->custom_services[ $code ]['order'];
					}

					while ( isset( $this->ordered_services[ $sort ] ) ) {
						$sort++;
					}

					$this->ordered_services[ $sort ] = array( $code, $name );

					$sort++;
				}

					ksort( $this->ordered_services );

				foreach ( $this->ordered_services as $value ) {
					$code = $value[0];
					$name = $value[1];
					$this->custom_services[$code]['default_name'] = $name;
					?>
						<tr>
							<td class="sort"><input type="hidden" class="order" name="dhl_service[<?php echo $code; ?>][order]" value="<?php echo isset( $this->custom_services[ $code ]['order'] ) ? $this->custom_services[ $code ]['order'] : ''; ?>" /></td>
							<td><strong><?php echo $code; ?></strong></td>
							<td><input type="text" name="dhl_service[<?php echo $code; ?>][name]" placeholder="<?php echo $name; ?>" value="<?php echo isset( $this->custom_services[ $code ]['name'] ) ? $this->custom_services[ $code ]['name'] : ''; ?>" size="50" /></td>
							<td><input type="checkbox" name="dhl_service[<?php echo $code; ?>][enabled]" <?php checked( ( ( isset( $this->custom_services[ $code ]['enabled'] ) && $this->custom_services[ $code ]['enabled'] == true ) || ( !isset($this->custom_services[ $code ]['name']) && in_array($this->custom_services[ $code ], array('P','N')) ) ), true ); ?> /></td>
							<td><input type="text" name="dhl_service[<?php echo $code; ?>][adjustment]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ]['adjustment'] ) ? $this->custom_services[ $code ]['adjustment'] : ''; ?>" size="4" /></td>
							<td><input type="text" name="dhl_service[<?php echo $code; ?>][adjustment_percent]" placeholder="N/A" value="<?php echo isset( $this->custom_services[ $code ]['adjustment_percent'] ) ? $this->custom_services[ $code ]['adjustment_percent'] : ''; ?>" size="4" /></td>
						</tr>
						<?php
				}

					update_option('custom_services', $this->custom_services);
				?>
			</tbody>
		</table>
	</td>
</tr>
