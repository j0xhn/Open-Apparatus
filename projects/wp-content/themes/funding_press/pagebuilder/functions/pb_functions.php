<?php
/**
 * Page Builder functions
 *
 * This holds the external functions which can be used by the theme
 * Requires the Page_Builder class
 *
 * @todo - multicheck, image checkbox, better colorpicker
**/
if(class_exists('Page_Builder')) {
	/**
	 * Core functions
	*******************/
	/* Register a block */
	function register_block($block_class) {
		global $registered_blocks;
		$registered_blocks[strtolower($block_class)] = new $block_class;
	}
	/** Un-register a block **/
	function unregister_block($block_class) {
		global $registered_blocks;
		$block_class = strtolower($block_class);
		foreach($registered_blocks as $block) {
			if($block->id_base == $block_class) unset($registered_blocks[$block_class]);
		}
	}
	/** Get list of all blocks **/
	function get_blocks($template_id) {
		//global $page_builder;
		$page_builder = new Page_Builder();
		$blocks = $page_builder->get_blocks($template_id);
		return $blocks;
	}
	/**
	 * Form Field Helper functions
	 *
	 * Provides some default fields for use in the blocks
	 *
	 * @todo build this into a separate class instead!
	********************************************************/
	/* Input field - Options: $size = min, small, full */
	function field_input($field_id, $block_id, $input, $size = 'full', $type = 'text') {
		$output = '<input type="'.$type.'" id="'. $block_id .'_'.$field_id.'" class="input-'.$size.'" value="'.$input.'" name="blocks['.$block_id.']['.$field_id.']">';
		return $output;
	}
    /* Input field - Options: $size = min, small, full */
    function field_input_skill($field_id, $block_id, $input, $size = 'full', $type = 'text') {
        $output = '<input data-slider="true" data-slider-highlight="true" type="'.$type.'" id="'. $block_id .'_'.$field_id.'" class="input-'.$size.'" value="'.$input.'" name="blocks['.$block_id.']['.$field_id.']">';
        return $output;
    }
	/* Textarea field */
	function field_textarea($field_id, $block_id, $text, $size = 'full', $ckeditor = true) {

		$ckeditor_class = 'ckeditor';

		if (!$ckeditor) $ckeditor_class = 'no_ckeditor';

		$output = '<textarea id="'. $block_id .'_'.$field_id.'" class="'.$ckeditor_class.' textarea-'.$size.'" name="blocks['.$block_id.']['.$field_id.']" rows="5">'.$text.'</textarea>';
		return $output;
	}
    function field_textareashortcode($field_id, $block_id, $text, $size = 'full') {
        $output = '<textarea id="'. $block_id .'_'.$field_id.'" class="textarea-'.$size.'" name="blocks['.$block_id.']['.$field_id.']" rows="5">'.$text.'</textarea>';
        return $output;
    }
	/* Select field */
	function field_select($field_id, $block_id, $options, $selected) {
		$options = is_array($options) ? $options : array();
		$output = '<select id="'. $block_id .'_'.$field_id.'" name="blocks['.$block_id.']['.$field_id.']">';
		foreach($options as $key=>$value) {
			$output .= '<option value="'.$key.'" '.selected( $selected, $key, false ).'>'.htmlspecialchars($value).'</option>';
		}
		$output .= '</select>';
		return $output;
	}
	/* Multiselect field */
	function field_multiselect($field_id, $block_id, $options, $selected_keys = array()) {
		$output = '<select id="'. $block_id .'_'.$field_id.'" multiple="multiple" class="select of-input" name="blocks['.$block_id.']['.$field_id.'][]">';
		foreach ($options as $key => $option) {
			$selected = (is_array($selected_keys) && in_array($key, $selected_keys)) ? $selected = 'selected="selected"' : '';
			$output .= '<option id="'. $block_id .'_'.$field_id.'_'. $key .'" value="'.$key.'" '. $selected .' />'.$option.'</option>';
		}
		$output .= '</select>';
		return $output;
	}
	/* Color picker field */
	function field_color_picker($field_id, $block_id, $color, $default = '') {
		$output = '<div class="pb-color-picker">';
			$output .= '<input type="text" id="'. $block_id .'_'.$field_id.'" class="input-color-picker" value="'. $color .'" name="blocks['.$block_id.']['.$field_id.']" data-default-color="'. $default .'"/>';
		$output .= '</div>';
		return $output;
	}
	/* Single Checkbox */
	function field_checkbox($field_id, $block_id, $check) {
		$output = '<input type="hidden" name="blocks['.$block_id.']['.$field_id.']" value="0" />';
		$output .= '<input type="checkbox" id="'. $block_id .'_'.$field_id.'" class="input-checkbox" name="blocks['.$block_id.']['.$field_id.']" '. checked( 1, $check, false ) .' value="1"/>';
		return $output;
	}
    /* Single Checkbox */
	/* Multi Checkbox */
	function field_multicheck($field_id, $block_id, $fields = array(), $selected = array()) {
	}
	/* Media Uploader */
	function field_upload($field_id, $block_id, $media, $media_type = 'image') {
		$output = '<input type="text" id="'. $block_id .'_'.$field_id.'" class="input-full input-upload" value="'.$media.'" name="blocks['.$block_id.']['.$field_id.']">';
		$output .= '<a href="#" class="upload_button button" rel="'.$media_type.'">Upload</a><p></p>';
		return $output;
	}
	/**
	 * Misc Helper Functions
	**************************/
	/** Get column width
	 * @parameters - $size (column size), $grid (grid size e.g 940), $margin
	 */
	function get_column_width($size, $grid = 940, $margin = 20) {
		$columns = range(1,12);
		$widths = array();
		foreach($columns as $column) {
			$width = (( $grid + $margin ) / 12 * $column) - $margin;
			$width = round($width);
			$widths[$column] = $width;
		}
		$column_id = absint(preg_replace("/[^0-9]/", '', $size));
		$column_width = $widths[$column_id];
		return $column_width;
	}
	/** Recursive sanitize
	 * For those complex multidim arrays
	 * Has impact on server load on template save, so use only where necessary
	 */
	function recursive_sanitize($value) {
		if(is_array($value)) {
			$value = array_map('recursive_sanitize', $value);
		} else {
			$value = htmlspecialchars(stripslashes($value));
		}
		return $value;
	}
}
?>