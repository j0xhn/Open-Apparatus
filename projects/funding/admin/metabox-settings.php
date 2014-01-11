<ul>
	<li>
		<label for="f_target_currency"><?php _e("Currency", 'funding'); ?></label>
		<select name="f_target_currency" id="f_target_currency" <?php disabled(!empty($funders)) ?>>
			<?php global $f_currencies; foreach($f_currencies as $key => $name) : ?>
				<option value=<?php print $key ?> <?php selected($settings['currency'], $key) ?>><?php print $name ?></option>
			<?php endforeach; ?>
		</select>
</li>
	<li>
		<label for="f_target_amount"><?php _e("Amount", 'funding'); ?></label>
		<input type="text" name="f_target_amount" id="f_target_amount" class="widefat" value="<?php esc_attr_e($settings['target']) ?>" />
		<div class="description"><?php _e('The minimum amount you need.', 'funding') ?></div>
	</li>
	<li>
		<label for="f_target_date"><?php _e("Date", 'funding'); ?></label>
		<input type="text" name="f_target_date" id="f_target_date" class="widefat" value="<?php esc_attr_e($settings['date']) ?>" />
		<div class="description"><?php _e('Date that funding ends.', 'funding') ?></div>
	</li>
</ul>