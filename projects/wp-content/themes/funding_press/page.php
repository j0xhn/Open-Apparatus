<?php get_header(); ?>

<div class="row page-title">
  <div class="container">
	<h1><?php echo get_the_title(); ?></h1>
	<div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="container page normal-page">
	<div class="row">
		<div class="span12">
			<?php while ( have_posts() ) : the_post(); ?>
			<?php the_content(); ?>
			<?php endwhile; // end of the loop. ?>

		<div class="clear"></div>
		</div>
	</div>
</div>


<?php get_footer(); ?>