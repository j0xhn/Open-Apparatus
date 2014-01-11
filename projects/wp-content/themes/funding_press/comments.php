<?php /*silence is golden*/ ?>
<?php comment_form(); ?>
<?php wp_link_pages( $args ); ?>
<?php if ( ! isset( $content_width ) ) $content_width = 900; ?>
<?php  posts_nav_link(); previous_posts_link();  ?>
<?php the_tags('Tags: ', ', ', '<br />'); ?>
<?php add_theme_support( 'custom-header', $args );
add_theme_support( 'custom-background', $args );
add_editor_style();  ?>