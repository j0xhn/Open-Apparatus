<?php
/** A simple text block **/
class Shortcode_Block extends Block {
    //set and create block
    function __construct() {
        $block_options = array(
            'name' => __('Shortcode block', 'funding'),
            'size' => 'span3',
        );
        //create the block
        parent::__construct('shortcode_block', $block_options);
    }
    function form($instance) {
        $defaults = array(
            'text' => '',
        );
        $instance = wp_parse_args($instance, $defaults);
        extract($instance);
        ?>
        <p class="description">
            <label for="<?php echo $this->get_field_id('title') ?>">
               <?php _e("Title (optional)", 'funding'); ?>
                <?php echo field_input('title', $block_id, $title, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('text') ?>">
               <?php _e("Content", 'funding'); ?>
                <?php echo field_textareashortcode('text', $block_id, $text, $size = 'full') ?>
            </label>
        </p>


        <?php
    }
    function pbblock($instance) {
        extract($instance);
        if($title) echo '<h3 class="widget-title">'.strip_tags($title).'</h3>';
        echo '<div class="mcontainer">'.wpautop(do_shortcode(htmlspecialchars_decode($text))).'</div>'; }

}