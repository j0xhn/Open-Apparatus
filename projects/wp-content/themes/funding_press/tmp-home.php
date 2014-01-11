<?php
/*
 * Template name: Home page
 */
?>
<?php get_header(); ?>


            <div class="container page" role="main">
                <div class="row">
                    <div class="span12">

                <?php while ( have_posts() ) : the_post(); ?>

                    <?php the_content(); ?>

                    <?php get_template_part( 'content', 'page' ); ?>


                <?php endwhile; // end of the loop. ?>

                    </div><!-- #span12 -->
                </div><!-- #row -->
            </div><!-- #container -->



<?php get_footer(); ?>