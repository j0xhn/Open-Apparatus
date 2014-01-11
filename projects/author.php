
<?php get_header();?>

<?php
if(isset($post->post_author)){
$author_id=$post->post_author;
$user_info = get_userdata( $author_id );
$level = $user_info->user_level;
}else{
  $author = get_user_by( 'slug', get_query_var( 'author_name' ) );
  $author_id = $author->ID;
  $level = 1;
}

if($level == 1){ ?>

<!-- Page content
    ================================================== -->
<!-- Wrap the rest of the page in another container to center all the content. -->
<div class="row page-title">
  <div class="container">
    <h1>
        <?php $author = get_user_by( 'slug', get_query_var( 'author_name' ) );
        $author_id = $author->ID;
         _e('Member: ','funding');echo get_the_author_meta('display_name',$author_id); ?>
   </h1>
    <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="row profile">
  <div class="container">
    <div class="profile-info row">
        <div class="span3"><?php echo get_avatar( $author_id, 250 ); ?></div>
        <div class="span7">

            <h1><?php if ( get_the_author_meta('display_name', $author_id) ) {echo get_the_author_meta('display_name',$author_id); }?></h1>

                <dl>
                  <?php
                    if (get_the_author_meta('first_name', $author_id)){ ?>
                  <dt><small><?php _e("Name:", 'funding'); ?></small></dt>
                  <dd> <?php echo get_the_author_meta('first_name', $author_id); if (get_the_author_meta('last_name', $author_id)){
                      echo ' ';echo get_the_author_meta('last_name', $author_id); }?>
                  </dd>
                    <?php } ?>
                    <?php if (usercountry_name_display($author_id) != ""){ ?>
                  <dt><small><?php _e("Country:", 'funding'); ?></small></dt>
                  <dd><?php echo usercountry_name_display($author_id); ?></dd>
                   <?php } ?>

                  <?php if (get_the_author_meta('user_registered', $author_id)) { ?>
                <dt><small><?php _e("Member Since:", 'funding'); ?></small></dt>
                <dd><?php echo date("F Y", strtotime(get_userdata($author_id) -> user_registered));?>
               </dd>
                <?php } ?>

                 <?php   if (get_the_author_meta('user_url', $author_id)) { ?>
                  <dt><small><?php _e("Website:", 'funding'); ?></small></dt>
                  <dd><a target="_blank" href="<?php
                    if (get_the_author_meta('user_url', $author_id)) {echo get_the_author_meta('user_url', $author_id);}?>">
                    <?php echo get_the_author_meta('user_url', $author_id);?></a></dd>
                <?php } ?>
              </dl>
       </div>
      </div>

    <div class="biography"><p><?php
    if ( get_the_author_meta('description', $author_id) ) {
        echo get_the_author_meta('description',$author_id);
    }
 ?></p></div>
  </div>
</div>
<div class="profile-projects">
<div class="container blog">
  <div class="row">
<h2><?php _e("Projects", 'funding'); ?></h2>

    <div class="span12">
        <?php

        $args = array(
            'post_type'=> 'project',
            'areas'    => 'painting',
            'order'    => 'ASC',
            'author' => $author_id,
            'posts_per_page' => -1
         );

        $wp_query = new WP_Query( $args);
         if ( $wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

    <div class="project-card span3">

            <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('projects');  ?></a></div>
              <h5 class="bbcard_name"><a href="<?php the_permalink(); ?>"><?php $title = get_the_title(); echo mb_substr($title, 0,20); if(strlen($title) > 23){echo '...';}?></a></h5>

            <p> <?php
                $excerpt = get_the_excerpt();
                echo mb_substr($excerpt, 0,80);echo '...';
             ?></p>

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
            }?>
            <?php if($funded_amount == $target or $funded_amount > $target){ ?>
                <div class="project-successful">
                    <strong><?php _e('Successful!', 'funding') ?></strong>
                </div>
            <?php }elseif($project_expired){ ?>

                        <div class="project-unsuccessful">
                            <strong><?php _e('Unsuccessful!', 'funding') ?></strong>
                        </div>

                <?php }else{ ?>
            <div class="progress progress-striped active bar-green"><div style="width: <?php printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?>%" class="bar"></div></div>
            <?php } ?>
            <ul class="project-stats">
                <li class="first funded">
                     <strong><?php printf(__('%u%%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?></strong><?php _e("funded", 'funding'); ?>
                </li>
                <li class="pledged">
                    <strong>
                        <?php print $project_currency_sign; print number_format(round((int)$target), 0, '.', ',');?></strong><?php _e("target", 'funding'); ?>
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


        <?php endwhile; endif; ?>

        <div class="clear"></div>
    </div>
    <!-- /.span12 -->




  </div>
  <!-- /.row -->
</div>
<!-- /.container -->
</div> <!-- /.profile -->




<?php }else{ ?>





<!-- Page content
    ================================================== -->
<!-- Wrap the rest of the page in another container to center all the content. -->
<div class="row page-title">
  <div class="container">
    <h1><?php _e("Author: ", 'funding'); ?><?php echo get_the_author_meta('display_name',$author);?></h1>
    <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="container blog">
  <div class="row">

    <div class="span8">
        <?php
        $showposts = of_get_option('blognum');
        $author = get_user_by( 'slug', get_query_var( 'author_name' ) );
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        query_posts( array( 'post_status' => 'publish','showposts' => $showposts, 'author' => $author->ID, 'paged' => $paged ) );
      ?>

        <?php if (  have_posts() ) : while (  have_posts() ) :  the_post(); ?>

    <div class="blog-list">

             <?php if ( has_post_thumbnail() ) { ?>
              <div class="blog-pdate green-bg"><?php the_time('M'); ?><br /><?php the_time('d'); ?></div>
              <div class="blog-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('category-thumb');  ?></a></div>

              <?php }else{?>

              <div class="blog-pdate-noimg green-bg"><?php the_time('M'); ?><br /><?php the_time('d'); ?></div>

              <?php } ?>
            <h2><?php the_title(); ?></h2>

            <p> <?php the_excerpt(); ?></p>

            <div class="clear"></div>
            <div class="blog-pinfo-wrapper">
                <div class="post-pinfo">By <a href="<?php echo get_author_posts_url($author_id); ?>" rel="tooltip" data-original-title="<?php _e("View all posts by ", 'funding'); ?><?php echo get_the_author(); ?>"><?php echo get_the_author(); ?></a> | <a rel="tooltip" title="<?php comments_number( 'No comments', 'One comment', '% comments' ); ?> in this post" href="<?php echo the_permalink(); ?>#comments"> <?php comments_number( 'No comments', 'One comment', '% comments' ); ?></a></div>
                <a class="button-green button-small" href="<?php the_permalink(); ?>"><?php _e("Read more", 'funding'); ?></a>
                <div class="clear"></div>
            </div>
        </div>
        <!-- /.blog-post -->


        <?php endwhile; endif; ?>
            <ul id="pager">
              <li>
                <?php
            $showposts1 = of_get_option('blognum');
            $additional_loop = new WP_Query('showposts='.$showposts1.'&author=' .$author->ID.'&paged='.$paged );
            $page=$additional_loop->max_num_pages;
            echo kriesi_pagination($additional_loop->max_num_pages);
            ?>
            <?php wp_reset_query(); ?>
              </li>
            </ul>
        <div class="clear"></div>
    </div>
    <!-- /.span8 -->


    <div class="span4 ">
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer widgets') ) : ?>
                <?php dynamic_sidebar('two'); ?>
           <?php endif; ?>
    </div>
    <!-- /.span4 -->

  </div>
  <!-- /.row -->
</div>
<!-- /.container -->
<?php } ?>

<?php get_footer(); ?>
</body>
</html>