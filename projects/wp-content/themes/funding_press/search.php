<?php get_header(); ?>
<div class="row page-title">
  <div class="container">
    <h1 ><?php printf( __( 'Search Results for: %s', 'funding' ),   get_search_query()  ); ?></h1>

  </div>
</div>
<div class="container page normal-page">
    <div class="row">
        <div class="span12">
        	 <?php if(get_post_type( $post->ID ) == 'post') { ?>
        	<div class="container blog">
  <div class="row">

    <div class="span8">
<?php } ?>
			<?php if ( have_posts() ) : ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
				    <?php if(get_post_type( $post->ID ) == 'project') { ?>


			<div class="project-card span3">
 <?php if(!has_post_thumbnail()){ ?>
                 <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('projects');  ?></a></div>
            <?php } ?>
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
            <div class="progress progress-striped active bar-green"><div style="width: <?php printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, round($target)) ?>%" class="bar"></div></div>
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
		    </div>
				<?php }else {?>


    <div class="blog-list">

            <?php if ( has_post_thumbnail() ) { ?>
              <div class="blog-pdate green-bg"><?php the_time('M'); ?><br /><?php the_time('d'); ?></div>
              <div class="blog-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('category-thumb');  ?></a></div>

              <?php }else{?>

              <div class="blog-pdate-noimg green-bg"><?php the_time('M'); ?><br /><?php the_time('d'); ?></div>

              <?php } ?>


            <h2><a href="<?php the_permalink(); ?>"> <?php the_title(); ?> </a></h2>

            <p> <?php the_excerpt(); ?></p>

            <div class="clear"></div>
            <div class="blog-pinfo-wrapper">
                <div class="post-pinfo"><?php _e("By",'funding');?> <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" rel="tooltip" data-original-title="<?php _e("View all posts by ",'funding');?><?php echo get_the_author(); ?>"><?php echo get_the_author(); ?></a> | <a rel="tooltip" title="<?php comments_number( 'No comments', 'One comment', '% comments' ); ?> in this post" href="<?php echo the_permalink(); ?>#comments"> <?php comments_number( 'No comments', 'One comment', '% comments' ); ?></a></div>
                <a class="button-green button-small" href="<?php the_permalink(); ?>"><?php _e("Read more",'funding');?></a>
                <div class="clear"></div>
            </div>
        </div>
        <!-- /.blog-post -->

			<?php } ?>

			<?php endwhile; ?>

			    <?php if(get_post_type( $post->ID ) == 'post') { ?>
			    		</div>
			 <div class="span4 ">
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer widgets') ) : ?>
                <?php dynamic_sidebar('two'); ?>
           <?php endif; ?>
    </div>
    <!-- /.span4 -->
    </div>
 <!-- /.row -->
            <ul id="pager">
              <li>
                <?php
            $showposts1 = of_get_option('blognum');
            $additional_loop = new WP_Query('showposts='.$showposts1.'&cat='.$category_id.'&paged='.$paged);
            $page=$additional_loop->max_num_pages;
            echo kriesi_pagination($additional_loop->max_num_pages);
            ?>
            <?php wp_reset_query(); ?>
              </li>
            </ul>
        <div class="clear"></div>
		<?php } ?>
			<?php else : ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'funding' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'funding' ); ?></p>

					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php endif; ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>