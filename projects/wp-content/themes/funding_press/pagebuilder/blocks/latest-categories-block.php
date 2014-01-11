<?php
session_start();
// store session data
/** A latest posts for categories block **/
class Category_Block extends Block {
    //set and create block
    function __construct() {
        $block_options = array(
            'name' => __('Project highlight', 'funding'),
            'size' => 'span12',
        );
        //create the block
        parent::__construct('category_block', $block_options);
    }
    function form($instance) {
        $defaults = array(
            'text' => '',
        );
        $line_options = array(
            'latest' => __('Latest projects', 'funding'),
            'staff' => __('Staff picks', 'funding'),
            'successful' => __('Latest successful projects', 'funding'),
            'ending' => __('First ending projects', 'funding'),
        );
        $instance = wp_parse_args($instance, $defaults);
        extract($instance);
        ?>
        <p class="description">
            <label for="<?php echo $this->get_field_id('categories') ?>">
              <?php _e("Exclude categories (select IDs of categories that you want to exclude, comma separated.)", 'funding'); ?>
                <?php echo field_input('categories', $block_id, $categories, $size = 'full') ?>
            </label>
        </p>
        <p class="description">
            <label for="<?php echo $this->get_field_id('title') ?>">
                <?php _e("Title (optional)", 'funding'); ?>
                <?php echo field_input('title', $block_id, $title, $size = 'full') ?>
            </label>
        </p>
        <p class="description fourth">
            <label for="<?php echo $this->get_field_id('display') ?>">
                <?php _e("Pick display option", 'funding'); ?><br/>
                <?php echo field_select('display', $block_id, $line_options, $display, $block_id); ?>
            </label>
        </p>
        <?php
    }
    function pbblock($instance) {
        extract($instance);
        $idObj = get_category_by_slug('blog');
        $id = $idObj->term_id;
        $id1 = $categories;
        $idt = $id . ',' . $id1;
        if($display == 'latest'){$_SESSION['displ'] = 1; }elseif($display == 'staff'){$_SESSION['displ'] = 2;}elseif($display == 'successful'){$_SESSION['displ'] = 3;}elseif($display == 'ending'){$_SESSION['displ'] = 4;}
       switch($display) {
            case 'latest': //////////////////////////////////////////latest
            /*  list_categories();*/
if($title == ""){}else{?><div class="title"><h4><img width="26" height="27" style="margin-right: 10px; margin-top: -5px;" src="<?php echo get_template_directory_uri(); ?>/img/fire.jpg" /><?php echo $title; ?></h4></div><?php }
            $args=array(
              'orderby' => 'name',
              'order' => 'ASC',
              'exclude' => $idt
              );
            $categories = get_categories($args); ?>
            <ul id="category-menu">
                <?php foreach ( $categories as $cat ) {?>
                <li id="cat-<?php echo $cat->term_id; ?>"><a id="click"  class="<?php echo $cat->slug; ?> ajax" onclick="cat_ajax_get('<?php echo $cat->term_id; ?>');" ><?php echo $cat->name; ?></a></li>
                <?php } ?>
            </ul>
            <div id="loading-animation" style="display: none; position: absolute; width: 700px; height: 325px; background-color: #ffffff;">
                <img src="<?php echo get_template_directory_uri(); ?>/img/loading.gif"/>
            </div>
            <div id="category-post-content"></div>
                <?php
                break;
            case 'staff': //////////////////////////////////staff
             /* list_categories();*/
if($title == ""){}else{?><div class="title"><h4><img width="26" height="27" style="margin-right: 10px; margin-top: -5px;" src="<?php echo get_template_directory_uri(); ?>/img/fire.jpg" /><?php echo $title; ?></h4></div><?php }
            $args=array(
              'type' => 'project',
              'orderby' => 'name',
              'order' => 'ASC',
              'exclude' => $idt,
              'hide_empty' => 1,
              );
            $categories = get_categories($args);  ?>
            <ul id="category-menu">
                <?php foreach ( $categories as $cat ) {
                       global $post;
                       $args = array (
                           'showposts' => -1,
                           'post_type' => 'project',
                           'orderby' => 'post_date',
                           'cat' => $cat->term_id);
                       $posts = get_posts( $args );
                           foreach ( $posts as $post ) {
                                   global $post;
                global $f_currency_signs;
                $project_settings = (array) get_post_meta($post->ID, 'settings', true);
                $project_expired = strtotime($project_settings['date']) < time();
                $project_currency_sign = $f_currency_signs[$project_settings['currency']];
                $target= $project_settings['target'];
                $rewards = get_children(array(
                'post_parent' => $post->ID,
                'post_type' => 'reward',
                'order' => 'ASC',
                'orderby' => 'meta_value_num',
                'meta_key' => 'funding_amount',
            ));
            $funders = array();
            $funded_amount = 0;
            $chosen_reward = null;
            foreach($rewards as $this_reward){
                $these_funders = get_children(array(
                    'post_parent' => $this_reward->ID,
                    'post_type' => 'funder',
                    'post_status' => 'publish'
                ));
                foreach($these_funders as $this_funder){
                    $funding_amount = get_post_meta($this_funder->ID, 'funding_amount', true);
                    $funders[] = $this_funder;
                    $funded_amount += $funding_amount;
                }
            }
              if(get_post_meta($post->ID, '_smartmeta_my-awesome-field2', true) == 'true' and !($funded_amount < $target and $project_expired)){ ?>
                    <li id="cat-<?php echo $cat->term_id; ?>"><a id="click" class="<?php echo $cat->slug; ?> ajax" onclick="cat_ajax_get('<?php echo $cat->term_id; ?>');" ><?php echo $cat->name; ?></a></li>
          <?php   }}}?>
            </ul>
            <div id="loading-animation" style="display: none; position: absolute; width: 700px; height: 325px; background-color: #ffffff;">
                <img src="<?php echo get_template_directory_uri(); ?>/img/loading.gif"/>
            </div>
            <div id="category-post-content"></div>
                <?php
                break;
              case 'successful': ////////////////////////////////////////successful
            /*  list_categories();*/
if($title == ""){}else{?><div class="title"><h4><img width="26" height="27" style="margin-right: 10px; margin-top: -5px;" src="<?php echo get_template_directory_uri(); ?>/img/fire.jpg" /><?php echo $title; ?></h4></div><?php }
            $args=array(
              'type' => 'project',
              'orderby' => 'name',
              'order' => 'ASC',
              'exclude' => $idt,
              'hide_empty' => 1,
              );
            $categories = get_categories($args);  ?>
            <ul id="category-menu">
                <?php foreach ( $categories as $cat ) {
                       global $post;
                       $args = array (
                           'showposts' => -1,
                           'post_type' => 'project',
                           'orderby' => 'post_date',
                           'cat' => $cat->term_id);
                       $posts = get_posts( $args );
                           foreach ( $posts as $post ) {
                                   global $post;
                global $f_currency_signs;
                $project_settings = (array) get_post_meta($post->ID, 'settings', true);
                $project_expired = strtotime($project_settings['date']) < time();
                $project_currency_sign = $f_currency_signs[$project_settings['currency']];
                $target= $project_settings['target'];
                $rewards = get_children(array(
                'post_parent' => $post->ID,
                'post_type' => 'reward',
                'order' => 'ASC',
                'orderby' => 'meta_value_num',
                'meta_key' => 'funding_amount',
            ));
            $funders = array();
            $funded_amount = 0;
            $chosen_reward = null;
            foreach($rewards as $this_reward){
                $these_funders = get_children(array(
                    'post_parent' => $this_reward->ID,
                    'post_type' => 'funder',
                    'post_status' => 'publish'
                ));
                foreach($these_funders as $this_funder){
                    $funding_amount = get_post_meta($this_funder->ID, 'funding_amount', true);
                    $funders[] = $this_funder;
                    $funded_amount += $funding_amount;
                }
            }
               if($funded_amount == $target or $funded_amount > $target){ ?>
                    <li id="cat-<?php echo $cat->term_id; ?>"><a id="click" class="<?php echo $cat->slug; ?> ajax" onclick="cat_ajax_get('<?php echo $cat->term_id; ?>');" ><?php echo $cat->name; ?></a></li>
          <?php   }else{}}}?>
            </ul>
            <div id="loading-animation" style="display: none; position: absolute; width: 700px; height: 325px; background-color: #ffffff;">
                <img src="<?php echo get_template_directory_uri(); ?>/img/loading.gif"/>
            </div>
            <div id="category-post-content"></div>
    <?php   break;
            case 'ending'://///////////////////////////////////ending
         /*  list_categories();*/
if($title == ""){}else{?><div class="title"><h4><img width="26" height="27" style="margin-right: 10px; margin-top: -5px;" src="<?php echo get_template_directory_uri(); ?>/img/fire.jpg" /><?php echo $title; ?></h4></div><?php }
            $args=array(
              'orderby' => 'name',
              'order' => 'ASC',
              'exclude' => $idt
              );
            $categories = get_categories($args); ?>
            <ul id="category-menu">
                <?php foreach ( $categories as $cat ) { ?>
                <li id="cat-<?php echo $cat->term_id; ?>"><a id="click" class="<?php echo $cat->slug; ?> ajax" onclick="cat_ajax_get('<?php echo $cat->term_id; ?>');" ><?php echo $cat->name; ?></a></li>
                <?php } ?>
            </ul>
             <div id="loading-animation" style="display: none; position: absolute; width: 700px; height: 325px; background-color: #ffffff;">
                <img src="<?php echo get_template_directory_uri(); ?>/img/loading.gif"/>
            </div>
            <div id="category-post-content"></div>
                <?php
                break;
        }
    }
}