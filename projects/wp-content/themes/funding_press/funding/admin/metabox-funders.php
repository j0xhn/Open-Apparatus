<div id="project-funders">
	<?php foreach($funders as $funder) : ?>
		<?php
			$funder_info = get_post_meta($funder->ID, 'funder', true);
			$amount = get_post_meta($funder->ID, 'funding_amount', true);
			$reward = get_post($funder->post_parent);
			$charged = get_post_meta($funder->ID, 'charged', true);
		?>

		<div class="funder" data-charged="<?php !empty($charged) ? 'true' : 'false' ?>" data-funder-id="<?php print $funder->ID ?>" data-project-id="<?php print $project->ID ?>">
			<div class="avatar">
				<a href="mailto:<?php print $funder_info['email'] ?>" title="<?php printf(__('Email %s', 'funding'), $funder_info['name']) ?>">
					<?php print get_avatar($funder_info['email'], 85) ?>
				</a>
				<div class="loader"></div>
			</div>
			<div class="name"><?php print $funder_info['name'] ?></div>
			<div class="info">
				<span class="amount"><?php print $project_currency_sign.$amount ?></span> -
				<span class="reward"><?php print $reward->post_title ?></span>
			</div>

			<?php if(!empty($charged)) : ?>
				<div class="icon charged"></div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<div class="clear"></div>

	<p>
		<input type="button" class="button" value="Collect Funding" id="collect-funding" <?php disabled(!$ready) ?> />
		<a href="<?php print wp_nonce_url(add_query_arg(array('fa' => 'export_funders', 'project' => $post->ID), site_url()), 'export_funders') ?>" target="_blank"><?php _e("Download", 'funding') ?></a>
		<p class="description"><?php _e("You can collect funding after you've reached your target", 'funding') ?></p>
	</p>
</div>