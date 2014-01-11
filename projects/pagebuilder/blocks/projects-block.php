<?php

/** A latest posts for projects block **/
class Projects_Block extends Block {


    //set and create block
    function __construct() {
        $block_options = array(
            'name' => __('Projects', 'funding'),
            'size' => 'span12',
        );

        //create the block
        parent::__construct('projects_block', $block_options);
    }

    function form($instance) {

        $defaults = array(
            'text' => '',
        );

        $line_options = array(
            'static' => __('Static projects', 'funding'),
            'slide' => __('Slider projects', 'funding'),
            'all' => __('Show both', 'funding')
         );
        $instance = wp_parse_args($instance, $defaults);
        extract($instance);

        $line_color = isset($line_color) ? $line_color : '#353535';
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
            <label for="<?php echo $this->get_field_id('projectsld') ?>">
                <?php _e("Pick display option", 'funding'); ?><br/>
                <?php echo field_select('projectsld', $block_id, $line_options, $projectsld, $block_id); ?>
            </label>
        </p>
        <p class="description fourth">
            <label for="<?php echo $this->get_field_id('slidenum') ?>">
                <?php _e("Insert number of slide posts", 'funding'); ?>
                <?php echo field_input('slidenum', $block_id, $slidenum, $size = 'full') ?>
            </label>
        </p>
        <p class="description fourth">
            <label for="<?php echo $this->get_field_id('staticnum') ?>">
                <?php _e("Insert number of static posts", 'funding'); ?>
                <?php echo field_input('staticnum', $block_id, $staticnum, $size = 'full') ?>
            </label>
        </p>

        <?php
    }


    function pbblock($instance) {
        extract($instance);
        $_SESSION['slidenum'] = $slidenum;
        $_SESSION['cat'] = $categories;
            switch($projectsld) {
            case 'static': /////////////////////////////////////static
if($title == ""){}else{?><div class="title"><h4><img width="26" height="27" style="margin-right: 8px; margin-top: -5px;" src="<?php echo get_template_directory_uri(); ?>/img/star.jpg" /><?php echo $title; ?></h4></div><?php }

            $idObj = get_category_by_slug('blog');
            $id = $idObj->term_id;
            $numpost = $staticnum;
            $id1 = $categories;
            $idt = '-'.$id . ',' . $id1; ?>


<div class="container blog">
  <div class="row">

    <div class="span12">
          <?php
             $posts = new WP_Query(array(
               'term' => 'project',
                'showposts' => $numpost,
                 'post_type' => 'project',
                 'orderby' => 'post_date',
                'cat' => $idt
            )); ?>

<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
    <?php      global $post;
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
            }?>


    <div class="project-card span3">
            <?php if(!has_post_thumbnail()){ ?>
                 <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('projects');  ?></a></div>
            <?php } ?>
            <h5 class="bbcard_name"><a href="<?php the_permalink(); ?>"><?php $title = get_the_title(); echo mb_substr($title, 0,23); if(strlen($title) > 23){echo '...';}?></a></h5>

            <p> <?php
                $excerpt = get_the_excerpt();
                echo mb_substr($excerpt, 0,80);echo '...';
             ?></p>


            <?php if($funded_amount == $target or $funded_amount > $target){ ?>
                <div class="project-successful">
                    <strong><?php _e('Successful!', 'funding') ?></strong>
                </div>
            <?php }elseif($project_expired){ ?>

                        <div class="project-unsuccessful">
                            <strong><?php _e('Unsuccessful!', 'funding') ?></strong>
                        </div>

            <?php }else{ ?>
            <div class="progress progress-striped active bar-green"><div style="width: <?php printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, round($target)) ?>%" class="bar"></div></div>
            <?php } ?>
            <ul class="project-stats">
                <li class="first funded">
                     <strong><?php printf(__('%u%%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?></strong>funded
                </li>
                <li class="pledged">
                    <strong>
                        <?php print $project_currency_sign; print number_format(round((int)$target), 0, '.', ',')?></strong>target
                </li>
                <li data-end_time="2013-02-24T08:41:18Z" class="last ksr_page_timer">
                    <?php

                    if(!$project_expired) : ?>
                        <strong><?php print F_Controller::timesince(time(), strtotime($project_settings['date']), 1, ''); ?></strong>
                        <?php _e('days to go', 'funding') ?>

                    <?php endif; ?>
                </li>
            </ul>

           <div class="clear"></div>
        </div>

        <!-- /.blog-post -->
    <?php endwhile; ?>

<?php wp_reset_query(); ?>
  <div class="clear"></div>
        <a class="edit-button button-small button-green" href="<?php $all_pr = get_ID_by_slug('all-projects'); echo get_page_link($all_pr); ?>"><?php _e("View all projects", 'funding'); ?></a>
          <div class="clear"></div>
    </div>
    <!-- /.span12 -->

 </div>
  <!-- /.row -->
</div>
<!-- /.container -->



<?php

break;
 case 'slide': /////////////////////////////////////////slide

if($title == ""){}else{?><div class="title"><h4><img width="26" height="27" style="margin-right: 8px; margin-top: -5px;" src="<?php echo get_template_directory_uri(); ?>/img/star.jpg" /><?php echo $title; ?></h4></div><?php }
global $wpdb, $wp_version;
//add shortcode

//page function
function TCHPCSCarousel()
{
    echo Carousel_shortcode();
}
function Carousel_shortcode()
{

    $idObj = get_category_by_slug('blog');
    $id = $idObj->term_id;
    $id1 = $categories;
    $idt = '-'.$id . ',' . $id1;
    $numposts = $_SESSION['slidenum'];


    global $wpdb;
    $word_imit = 80;

   //Image slider
    global $post;
    global $slider_gallery;

    $slider_gallery.= '<div class="image_carousel">';
    $slider_gallery.='<div id="foo1">';

    $args = array( 'numberposts' => $numposts,  'category' => $idt, 'order'=> 'ASC', 'orderby' => 'rand', 'post_type' => 'project' );
    $myposts = get_posts( $args );
    foreach( $myposts as $post ){
setup_postdata($post);
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
        $postbar = round($funded_amount/$target*100);
        $post_title = $post->post_title;
        $post_link =  get_permalink($post->ID);
        $post_content = $post->post_content;
        $displaydesc= $word_imit;

        $slider_gallery.= '<div class="project-card span3">';
        if(!has_post_thumbnail()){
        $slider_gallery.='<div class="project-thumb-wrapper"><a href="'.post_permalink().'"><img src="'.get_template_directory_uri().'/img/default-image.jpg" /></a></div>';
        }else{
        $slider_gallery.='<div class="project-thumb-wrapper"><a href="'.post_permalink().'"><img src="'.return_thumb_url().'" /></a></div>';}

        $title = get_the_title();
        $slider_gallery.= '<h5 class="bbcard_name" ><a href="'.post_permalink().'">'.mb_substr($title, 0,20).'</a></h5>';
        $slider_gallery.= '<p><span class="foo_con">'.tchpcs_clean($post_content, $displaydesc).'</span></p>';

          if($funded_amount == $target or $funded_amount > $target){
                $slider_gallery.= '<div class="project-successful">';
                $slider_gallery.= '<strong>'.__("Successful!", "funding").'</strong>';
                $slider_gallery.= '</div>';
             }elseif($project_expired){

              $slider_gallery.='<div class="project-unsuccessful">';
              $slider_gallery.='<strong>'.__("Unsuccessful!", "funding").'</strong>';
              $slider_gallery.='</div>';

           }else{
             $slider_gallery.='<div class="progress progress-striped active bar-green"><div style="width:';
             $slider_gallery.= $postbar;
             $slider_gallery.='%" class="bar"></div></div>';
             }
             $slider_gallery.='<ul class="project-stats">';
             $slider_gallery.='<li class="first funded">';
             $slider_gallery.='<strong>'.$postbar.'%</strong>'.__("funded", "funding");
             $slider_gallery.= ' </li>';
             $slider_gallery.='<li class="pledged">';
             $slider_gallery.='<strong>'.$project_currency_sign.number_format(round((int)$target), 0, '.', ',').'</strong>'.__("target", "funding");
             $slider_gallery.='</li><li " class="last ksr_page_timer">';
             if(!$project_expired){
             $slider_gallery.='<strong>'.F_Controller::timesince(time(), strtotime($project_settings['date']), 1, '').'</strong>'.__("days to go", "funding");

             }
              $slider_gallery.='</li></ul><div class="clear"></div>';

        $slider_gallery.= '</div>';

    }

    $all_pr = get_ID_by_slug('all-projects');
    $slider_gallery.='</div>';
    $slider_gallery.='<div class="clearfix"></div>';
    $slider_gallery.='<a class="prev" id="foo1_prev" href="#"><span>'.__("prev", "funding").'</span></a>';
    $slider_gallery.='<a class="next" id="foo1_next" href="#"><span>'.__("next", "funding").'</span></a>';
    $slider_gallery.='<a class="edit-button button-small button-green" href="'.get_page_link($all_pr).'">'.__("View all projects", "funding").'</a>';

    $slider_gallery.='</div>';


    return $slider_gallery;

}

//limit words
function tchpcs_clean($excerpt, $substr) {
    $string = $excerpt;
    $string = substr($string, 0, $substr);

    return $string;
}
if(function_exists('TCHPCSCarousel')){ echo TCHPCSCarousel(); }
break;

case 'all': //////////////////////////////////all
if($title == ""){}else{?><div class="title"><h4><img width="26" height="27" style="margin-right: 8px; margin-top: -5px;" src="<?php echo get_template_directory_uri(); ?>/img/star.jpg" /><?php echo $title; ?></h4></div><?php }

global $wpdb, $wp_version;
//add shortcode

//page function
function TCHPCSCarousel()
{
    echo Carousel_shortcode();
}
function Carousel_shortcode()
{

    $idObj = get_category_by_slug('blog');
    $id = $idObj->term_id;
    $id1 =  $_SESSION['cat'];
    $idt = '-'.$id . ',' . $id1;
    $numposts = $_SESSION['slidenum'];
    global $wpdb;
    $word_imit = 80;

   //Image slider
    global $post;
    global $slider_gallery;

    $slider_gallery.= '<div class="image_carousel">';
    $slider_gallery.='<div id="foo1">';

    $args = array( 'numberposts' => $numposts,  'category' => $idt, 'order'=> 'ASC', 'orderby' => 'rand', 'post_type' => 'project' );
    $myposts = get_posts( $args );
    foreach( $myposts as $post ){
setup_postdata($post);
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
        $postbar = round($funded_amount/$target*100);
        $post_title = $post->post_title;
        $post_link =  get_permalink($post->ID);
        $post_content = $post->post_content;
        $displaydesc= $word_imit;

        $slider_gallery.= '<div class="project-card span3">';
        if(!has_post_thumbnail()){
        $slider_gallery.='<div class="project-thumb-wrapper"><a href="'.post_permalink().'"><img src="'.get_template_directory_uri().'/img/default-image.jpg" /></a></div>';
        }else{
        $slider_gallery.='<div class="project-thumb-wrapper"><a href="'.post_permalink().'"><img src="'.return_thumb_url().'" /></a></div>';}

        $title = get_the_title();

        $slider_gallery.= '<h5 class="bbcard_name" ><a href="'.post_permalink().'">'.mb_substr($title, 0,20).'</a></h5>';
        $slider_gallery.= '<p><span class="foo_con">'.tchpcs_clean($post_content, $displaydesc).'</span></p>';

          if($funded_amount == $target or $funded_amount > $target){
                $slider_gallery.= '<div class="project-successful">';
                $slider_gallery.= '<strong>'.__("Successful!", "funding").'</strong>';
                $slider_gallery.= '</div>';
             }elseif($project_expired){

              $slider_gallery.='<div class="project-unsuccessful">';
                $slider_gallery.= '<strong>'.__("Unsuccessful!", "funding").'</strong>';
              $slider_gallery.='</div>';

           }else{
             $slider_gallery.='<div class="progress progress-striped active bar-green"><div style="width:';
             $slider_gallery.= $postbar;
             $slider_gallery.='%" class="bar"></div></div>';
             }
             $slider_gallery.='<ul class="project-stats">';
             $slider_gallery.='<li class="first funded">';
             $slider_gallery.='<strong>'.$postbar.'%</strong>'.__("funded", "funding");
             $slider_gallery.= ' </li>';
             $slider_gallery.='<li class="pledged">';
             $slider_gallery.='<strong>'.$project_currency_sign.number_format(round((int)$target), 0, '.', ',').'</strong>'.__("target", "funding");
             $slider_gallery.='</li><li " class="last ksr_page_timer">';
             if(!$project_expired){
             $slider_gallery.='<strong>'.F_Controller::timesince(time(), strtotime($project_settings['date']), 1, '').'</strong>'.__("days to go", "funding");

             }
              $slider_gallery.='</li></ul><div class="clear"></div>';

        $slider_gallery.= '</div>';

    }


    $slider_gallery.='</div>';
    $slider_gallery.='<div class="clearfix"></div>';
    $slider_gallery.='<a class="prev" id="foo1_prev" href="#"><span>'.__("prev", "funding").'</span></a>';
    $slider_gallery.='<a class="next" id="foo1_next" href="#"><span>'.__("next", "funding").'</span></a>';
    $slider_gallery.='</div>';


    return $slider_gallery;

}

//limit words
function tchpcs_clean($excerpt, $substr) {
    $string = $excerpt;
    $string = substr($string, 0, $substr);

    return $string;
}
if(function_exists('TCHPCSCarousel')){ echo TCHPCSCarousel(); }


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $idObj = get_category_by_slug('blog');
            $id = $idObj->term_id;
            $id1 = $categories;
            $idt = '-'.$id . ',' . $id1;
            $numpost = $staticnum; ?>

<div class="container blog">
  <div class="row">
    <div class="span12">
        <?php
             $posts = new WP_Query(array(
               'term' => 'project',
                'showposts' => $numpost,
                 'post_type' => 'project',
                 'orderby' => 'post_date',
                'cat' => $idt
            )); ?>

<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
         <?php
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
            } ?>
    <div class="project-card span3">
            <?php if(!has_post_thumbnail()){ ?>
                 <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('projects');  ?></a></div>
            <?php } ?>
            <h5 class="bbcard_name"><a href="<?php the_permalink(); ?>"><?php $title = get_the_title(); echo mb_substr($title, 0,23); if(strlen($title) > 23){echo '...';}?></a></h5>

            <p> <?php
                $excerpt = get_the_excerpt();
                echo mb_substr($excerpt, 0,80);echo '...';
             ?></p>



            <?php if($funded_amount == $target or $funded_amount > $target){ ?>
                <div class="project-successful">
                    <strong><?php _e('Successful!', 'funding') ?></strong>
                </div>
            <?php }elseif($project_expired){ ?>

                        <div class="project-unsuccessful">
                            <strong><?php _e('Unsuccessful!', 'funding') ?></strong>
                        </div>

            <?php }else{ ?>
            <div class="progress progress-striped active bar-green"><div style="width: <?php printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, round($target)) ?>%" class="bar"></div></div>
            <?php } ?>

            <ul class="project-stats">
                <li class="first funded">
                     <strong><?php printf(__('%u%%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?></strong>funded
                </li>
                <li class="pledged">
                    <strong>
                        <?php print $project_currency_sign; print number_format(round((int)$target), 0, '.', ',');?></strong>target
                </li>
                <li data-end_time="2013-02-24T08:41:18Z" class="last ksr_page_timer">
                    <?php

                    if(!$project_expired) : ?>
                        <strong><?php print F_Controller::timesince(time(), strtotime($project_settings['date']), 1, ''); ?></strong>
                        <?php _e('days to go', 'funding') ?>

                    <?php endif; ?>
                </li>
            </ul>

           <div class="clear"></div>
        </div>

        <!-- /.blog-post -->
   <?php endwhile; ?>
<?php wp_reset_query(); ?>
       <div class="clear"></div>
        <a class="edit-button button-small button-green" href="<?php $all_pr = get_ID_by_slug('all-projects'); echo get_page_link($all_pr); ?>"><?php _e("View all projects", 'funding'); ?></a>
          <div class="clear"></div>
    </div>
    <!-- /.span12 -->
 </div>
  <!-- /.row -->
</div>
<!-- /.container -->

<?php


break;
}
  }
}