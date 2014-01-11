<?php
/** Notifications block **/

if(!class_exists('Alert_Block')) {
	class Alert_Block extends Block {

		//set and create block
		function __construct() {
			$block_options = array(
				'name' => __('Alerts', 'funding'),
				'size' => 'span6',
			);

			//create the block
			parent::__construct('alert_block', $block_options);
		}

		function form($instance) {

			$defaults = array(
				'content' => '',
				'type' => __('note', 'funding'),
				'style' => ''
			);
			$instance = wp_parse_args($instance, $defaults);
			extract($instance);

			$type_options = array(
				'default' => __('Standard', 'funding'),
				'info' => __('Info', 'funding'),
				'note' => __('Notification', 'funding'),
				'warn' => __('Warning', 'funding'),
				'tips' => __('Tips', 'funding')
			);

			?>

			<p class="description">
				<label for="<?php echo $this->get_field_id('title') ?>">
					<?php _e("Title (optional)", 'funding'); ?><br/>
					<?php echo field_input('title', $block_id, $title) ?>
				</label>
			</p>
			<p class="description">
				<label for="<?php echo $this->get_field_id('content') ?>">
					<?php _e("Alert Text (required)", 'funding'); ?><br/>
					<?php echo field_textarea('content', $block_id, $content) ?>
				</label>
			</p>
			<p class="description half">
				<label for="<?php echo $this->get_field_id('type') ?>">
					<?php _e("Alert Type", 'funding'); ?><br/>
					<?php echo field_select('type', $block_id, $type_options, $type) ?>
				</label>
			</p>
			<p class="description half last">
				<label for="<?php echo $this->get_field_id('style') ?>">
					<?php _e("Additional inline css styling (optional)", 'funding'); ?><br/>
					<?php echo field_input('style', $block_id, $style) ?>
				</label>
			</p>
			<?php

		}

		function pbblock($instance) {
			extract($instance);

			echo '<div class="alert '.$type.' cf" style="'. $style .'">' . do_shortcode(htmlspecialchars_decode($content)) . '</div>';

		}

	}
}