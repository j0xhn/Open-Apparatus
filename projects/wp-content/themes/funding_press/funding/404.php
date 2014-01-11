<?php get_header(); ?>

<div class="row page-title">
  <div class="container">
    <h1><?php _e('Page not found!', 'funding');?></h1>
    <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="container page">
    <div class="row">
        <div class="span12">
           <div class="four0four">
    <p class="huge"> OOPS! 404</p>
    <?php _e('Page not found, sorry', 'funding');?> :(

</div>
        </div>
    </div>
</div>


<?php get_footer(); ?>