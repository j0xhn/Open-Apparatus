<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 */

?>

<?php get_header();?>

<!-- Page content
    ================================================== -->
<!-- Wrap the rest of the page in another container to center all the content. -->
<div class="row page-title">
  <div class="container">
    <h1><?php echo get_the_title(); ?></h1>
    <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="container blog">
  <div class="row">

    <div class="span8">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <div class="blog-post">
        	<div class="blog-post-header">
        		<?php if ( has_post_thumbnail() ) { ?>
				<div class="blog-pdate green-bg"><?php the_time('M'); ?><br /><?php the_time('d'); ?></div>

				<?php the_post_thumbnail(); ?>
				<?php }else{?>

              <div class="blog-pdate-noimg green-bg"><?php the_time('M'); ?><br /><?php the_time('d'); ?></div>

              <?php } ?>


				<h2><?php the_title(); ?></h2>
				    <div class="blog-pinfo-wrapper">
	            	 <div class="post-pinfo">By <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" rel="tooltip" data-original-title="<?php _e("View all posts by ", 'funding'); ?><?php echo get_the_author(); ?>"><?php echo get_the_author(); ?></a> | <a rel="tooltip" title="<?php comments_number( 'No comments', 'One comment', '% comments' ); ?> in this post" href="<?php echo the_permalink(); ?>#comments"> <?php comments_number( 'No comments', 'One comment', '% comments' ); ?></a></div>
	                <div class="clear"></div>
	            </div>
        	</div>


            <?php the_content(); ?>

            <div class="clear"></div>

			<?php if(comments_open()){?>
			<?php comments_template('/short-comments-blog.php'); ?>
            <?php wp_list_comments('type=comment&callback=custom_comments'); ?>
			<?php } ?>

        </div>
        <!-- /.blog-post -->



        <?php endwhile; endif; ?>
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


<?php get_footer(); ?>