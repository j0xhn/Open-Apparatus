<?php get_header(); ?>

    <div class="row page-title">
          <div class="container">
            <h1><?php echo get_the_title(); ?></h1>
            <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
          </div>
     </div>
            <div class="container page normal-page sticky" role="main">
                <div class="row">
                    <div class="span12">

                <?php while ( have_posts() ) : the_post(); ?>

                    <?php the_content(); ?>

                    <?php get_template_part( 'content', 'page' ); ?>


                <?php endwhile; // end of the loop. ?>
					<div class="clear"></div>
                    </div><!-- #span12 -->
                </div><!-- #row -->
            </div><!-- #container -->



<?php get_footer(); ?>