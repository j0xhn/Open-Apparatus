<?php
/*
* Template name: All projects page
*/
?>
<?php get_header();?>
<div class="row page-title">
  <div class="container">
    <h1><?php _e("Projects", 'funding');?></h1>
    <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="container blog all-projects">
  <div class="row">

    <div class="span12">
     <?php
        $_SESSION['displ'] = 5;
        $idObj = get_category_by_slug('blog');
        $id = $idObj->term_id;
        $args=array(
              'hide_empty' => 1,
              'orderby' => 'name',
              'order' => 'ASC',
              'exclude' => $id);
            $categories = get_categories($args); ?>

            <ul id="category-menu">
                <?php foreach ( $categories as $cat ) { ?>
                <li id="cat-<?php echo $cat->term_id; ?>"><a id="click" class="<?php echo $cat->slug; ?> ajax" onclick="cat_ajax_get('<?php echo $cat->term_id; ?>');" ><?php echo $cat->name; ?></a></li>

                <?php } ?>
            </ul>
             <div id="loading-animation" style="display: none; position: absolute; width: 700px; background-color: #ffffff;">
                <img src="<?php echo get_template_directory_uri(); ?>/img/loading.gif"/>
            </div>
            <div id="category-post-content"></div>
    </div>
    <!-- /.span12 -->
  </div>
  <!-- /.row -->
</div>
<!-- /.container -->
<?php get_footer(); ?>