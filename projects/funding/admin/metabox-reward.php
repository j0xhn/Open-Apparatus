<ul id="current-rewards">
	<?php foreach($rewards_keyed as $id => $reward) : ?>
		<li class="reward" id="reward-<?php esc_attr_e($id); ?>">
			<strong><?php print $reward['title'] ?></strong>
			<span class="availability">
				<?php printf(__('%s available @ %s%s each', 'funding'), $reward['available'], $project_currency_sign, $reward['amount']) ?>
			</span>
			<p><?php print $reward['description'] ?></p>
		</li>
	<?php endforeach; ?>
</ul>

<div id="reward-inputs">
	<ul>
		<li>
			<label><?php _e("Title", 'funding'); ?></label>
			<input type="text" class="widefat" name="reward_title" />
		</li>
		<li>
			<label><?php _e("Description", 'funding'); ?></label>
			<textarea class="widefat"  name="reward_description"></textarea>
		</li>
		<li>
			<label><?php _e("Minimum Amount", 'funding'); ?></label>
			<input type="text" name="reward_amount" />
			<span class="description">Amount a user has to fund to get this reward.</span>
		</li>
		<li>
			<label><?php _e("Number Available", 'funding'); ?></label>
			<input type="text" name="reward_available" />
		</li>
	</ul>

	<input type="button" id="add-reward-save" value="Save" class="button-secondary" /> or <a href="#" id="add-reward-cancel"><?php _e('cancel', 'funding') ?></a> | <a href="#" class="delete" id="add-reward-delete"><?php _e('delete', 'funding') ?></a>
</div>
<input type="button" value="Add Reward" id="add-reward" class="button-secondary" />

<input type="hidden" name="rewards" id="rewards-field" />
<input type="hidden" name="rewards_deleted" id="rewards-deleted-field" />
