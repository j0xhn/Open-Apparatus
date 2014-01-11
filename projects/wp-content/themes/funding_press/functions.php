<?php
//translatable theme
ob_start();
load_theme_textdomain( 'funding', get_template_directory() . '/langs');
?>
<?php
require_once (get_template_directory() . '/themeOptions/functions.php');
require_once ('pagebuilder/page-builder.php');
require_once ('smartmetabox/SmartMetaBox.php');
require_once ('functions/adminbar.php');
wp_insert_term( 'blog', 'category');
$sql = mysql_query("SELECT paypal_email FROM ".$wpdb->prefix."users");if (!$sql){
mysql_query("ALTER TABLE `".$wpdb->prefix."users` ADD `paypal_email` VARCHAR( 100 )");}
$metas = $wpdb->get_results( "SELECT meta_key FROM ".$wpdb->prefix."postmeta where meta_key='update'" );
if (($wpdb->num_rows)>0) {}else{
$wpdb->insert(
   $wpdb->prefix.'postmeta',
    array(
        'meta_id' => -1,
        'post_id' => -1,
        'meta_key' => 'update',
        'meta_value' => 'test'
    )
);}
if(get_ID_by_slug('my-projects') == ""){
//create my-projects page
$post = array(
  'post_name' => 'my-projects',
  'post_status' => 'publish',
  'post_title' => __('My projects', 'funding'),
  'post_type' => 'page'
);
// Insert my projects page into the database
wp_insert_post( $post );}
if(get_ID_by_slug('all-projects') == ""){
//create my-projects page
$post = array(
  'post_name' => 'all-projects',
  'post_status' => 'publish',
  'post_title' => _('All projects', 'funding'),
  'post_type' => 'page'
);
// Insert my projects page into the database
wp_insert_post( $post );}
function get_ID_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}
//set my projects page template
$id_page = get_ID_by_slug('my-projects');
update_post_meta($id_page, "_wp_page_template", "tmp-projects.php");
$id_page = get_ID_by_slug('all-projects');
update_post_meta($id_page, "_wp_page_template", "tmp-all-projects.php");
//restrict access
 add_action('admin_head','my_restrict_access');
    function my_restrict_access(){
        $Path=$_SERVER['REQUEST_URI'];
        $basepath= site_url().'/wp-admin';
        if(substr($basepath, 0, 5) == 'https'){
            if(substr($basepath, 8, 3) == 'www'){
              $URI='http://www.'.$_SERVER['SERVER_NAME'].$Path;
            }else{
              $URI='http://'.$_SERVER['SERVER_NAME'].$Path;
            }
       }else{
            if(substr($basepath, 7, 3) == 'www'){
                if(substr($_SERVER['SERVER_NAME'], 0, 3) == 'www'){
                    $URI='http://'.$_SERVER['SERVER_NAME'].$Path;
                }
                else{
              $URI='http://www.'.$_SERVER['SERVER_NAME'].$Path;
                }
            }else{
              $URI='http://'.$_SERVER['SERVER_NAME'].$Path;
            }
        }
        $current_user= wp_get_current_user();
        $level = $current_user->user_level;

        if (($URI ==($basepath.'/post-new.php?post_type=project')) or ($URI ==($basepath.'/edit.php?post_type=project&page=funding-settings')) or $level == 10 or preg_match('#wp-admin/post.php?#',$_SERVER['REQUEST_URI'])){}else{
        get_header();
        echo '<div class="container page"><div class="row"><div class="alert alert-error"><strong>'.__("You dont have the right permissions to access this page!", "funding").'</strong></div></div></div>';
        get_footer();exit();}}
//custom columns
add_filter( 'manage_edit-project_columns', 'my_edit_project_columns' ) ;
//allow contributor to upload media
if ( current_user_can('contributor') && !current_user_can('upload_files') )
    add_action('admin_init', 'allow_contributor_uploads');
function allow_contributor_uploads() {
    $contributor = get_role('contributor');
    $contributor->add_cap('upload_files');
    }
function my_edit_project_columns( $columns ) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => __( "Project Title", 'funding' ),
        "status" => __( 'Project status', 'funding' ),
        "funding-progress" => __( "Progress", 'funding' ),
        "funding-time" => __( "Time Remaining", 'funding' ),
        "ppal" => __( "Paypal", 'funding' ),
        "author" => __( "Creator", 'funding' ),
        "comments" => '<img src="'.site_url().'/wp-admin/images/comment-grey-bubble.png" alt="Comments" />',
        'date' => __( 'Date', 'funding' ),
    );
    return $columns;
}
add_action( 'manage_project_posts_custom_column', 'my_manage_project_columns', 10, 2 );
function my_manage_project_columns( $column, $post_id ) {
    global $post;
    switch( $column ) {
        /* If displaying the 'duration' column. */
        case 'funding-time' :
            $project_settings = (array) get_post_meta($post_id, 'settings', true);
            $target = $project_settings['target'];
            $project_expired = strtotime($project_settings['date']) < time();
            $funded_amount = 0;
            $rewards = get_children(array('post_parent' => $post -> ID, 'post_type' => 'reward', 'order' => 'ASC', 'orderby' => 'meta_value_num', 'meta_key' => 'funding_amount', ));
            $funders = array();
            $funded_amount = 0;
            $chosen_reward = null;
            foreach ($rewards as $this_reward) {
                $these_funders = get_children(array('post_parent' => $this_reward -> ID, 'post_type' => 'funder', 'post_status' => 'publish'));
                foreach ($these_funders as $this_funder) {
                    $funding_amount = get_post_meta($this_funder -> ID, 'funding_amount', true);
                    $funders[] = $this_funder;
                    $funded_amount += $funding_amount;
                }
            }
             if(!$project_expired){ ?>
             <strong><?php print F_Controller::timesince(time(), strtotime($project_settings['date']), 1, ''); ?></strong>
             <?php _e('days to go', 'funding') ?>
            <?php }else{ _e('Project expired!', 'funding');}
            break;
        /* If displaying the 'genre' column. */
        case 'status' :
            $project_settings = (array) get_post_meta($post_id, 'settings', true);
            $target = $project_settings['target'];
            $project_expired = strtotime($project_settings['date']) < time();
            $funded_amount = 0;
            $rewards = get_children(array('post_parent' => $post -> ID, 'post_type' => 'reward', 'order' => 'ASC', 'orderby' => 'meta_value_num', 'meta_key' => 'funding_amount', ));
            $funders = array();
            $funded_amount = 0;
            $chosen_reward = null;
            foreach ($rewards as $this_reward) {
                $these_funders = get_children(array('post_parent' => $this_reward -> ID, 'post_type' => 'funder', 'post_status' => 'publish'));
                foreach ($these_funders as $this_funder) {
                    $funding_amount = get_post_meta($this_funder -> ID, 'funding_amount', true);
                    $funders[] = $this_funder;
                    $funded_amount += $funding_amount;
                }
            }
            if(get_post_status( $post_id ) == 'pending') { global $a; $a =1; ?>
            <strong><?php _e('Pending!', 'funding') ?></strong>
             <?php }elseif(get_post_status( $post_id ) == 'draft'){ global $a; $a =2;?>
            <strong><?php _e('Draft!', 'funding') ?></strong>
            <?php }elseif( $funded_amount > $target){  global $a; $a =3;?>
            <strong><?php _e('Successful!', 'funding') ?></strong>
            <?php }elseif($project_expired){  global $a; $a =4;?>
            <strong><?php _e('Unsuccessful!', 'funding') ?></strong>
            <?php }else{ global $a; $a =5;?>
            <strong><?php _e('Active!', 'funding') ?></strong>
            <?php }
            break;
            case 'ppal':
            $reward = get_post($funder->post_parent);
            $project = get_post($reward->post_parent);
            $admin_info = get_userdata($project->post_author);
            echo $admin_info->paypal_email;
            break;
            case 'funding-progress':
            $project_settings = (array) get_post_meta($post_id, 'settings', true);
            $target = $project_settings['target'];
            $project_currency_sign = $f_currency_signs[$project_settings['currency']];
            $project_expired = strtotime($project_settings['date']) < time();
            $funded_amount = 0;
            $rewards = get_children(array('post_parent' => $post -> ID, 'post_type' => 'reward', 'order' => 'ASC', 'orderby' => 'meta_value_num', 'meta_key' => 'funding_amount', ));
            $funders = array();
            $funded_amount = 0;
            $chosen_reward = null;
            foreach ($rewards as $this_reward) {
                $these_funders = get_children(array('post_parent' => $this_reward -> ID, 'post_type' => 'funder', 'post_status' => 'publish'));
                foreach ($these_funders as $this_funder) {
                    $funding_amount = get_post_meta($this_funder -> ID, 'funding_amount', true);
                    $funders[] = $this_funder;
                    $funded_amount += $funding_amount;
                }
            }
            if($funded_amount == 0)
            {echo '0%';}else{
            printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, round($target));echo '%';}
            break;
        /* Just break out of the switch statement for everything else. */
        default :
            break;
    }
}
function fix_category_pagination($qs){
    if(isset($qs['category_name']) && isset($qs['paged'])){
        $qs['post_type'] = get_post_types($args = array(
            'public'   => true,
            '_builtin' => false
        ));
        array_push($qs['post_type'],'post');
    }
    return $qs;
}
add_filter('request', 'fix_category_pagination');
//jquery bootstrap
function andrew_unregister_widgets() {
unregister_widget( 'WP_Widget_Categories' );}
add_action( 'widgets_init', 'andrew_unregister_widgets');
function wpbootstrap_scripts_with_jquery() {
     // Register the script like this for a theme:
     wp_register_script( 'custom-script', get_template_directory_uri() . '/js/bootstrap.js', array( 'jquery' ) );
    // For either a plugin or a theme, you can then enqueue the script:
     wp_enqueue_script( 'custom-script' ); }
     add_action( 'wp_enqueue_scripts', 'wpbootstrap_scripts_with_jquery' );
//create sidebars
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => __( 'Footer area widgets', 'funding' ),
'id' => 'one',
'description' => __( 'Widgets in this area will be shown in the footer.' , 'funding'),
'before_widget' => '<div class="footer_widget span3">',
'after_widget' => '</div>',
'before_title' => '<h3>',
'after_title' => '</h3>', ));
if ( function_exists('register_sidebar') )
register_sidebar(array(
'name' => __( 'Blog sidebar', 'funding' ),
'id' => 'two',
'description' => __( 'Widgets in this area will be shown in the blog sidebar.' , 'funding'),
'before_widget' => '<div class="blog_widget span3">',
'after_widget' => '</div>',
'before_title' => '<h3>',
'after_title' => '</h3>', ));
//add featured image support
add_theme_support( 'post-thumbnails' );
//add custom menus support
add_theme_support( 'menus' );
// Add Theme option menu in admin
function fundingpress_create_menu(){
$themeicon1 = get_template_directory_uri()."/img/fundingpress.png";
add_menu_page("Theme Options", "Theme Options", 'edit_theme_options', 'options-framework', 'optionsframework_page',$themeicon1,1800 );
}add_action( 'admin_menu', 'fundingpress_create_menu' );
// When this theme is activated send the user to the theme options
if (is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
// Call action that sets
add_action('admin_head','gp_setup');
// Do redirect
header( 'Location: '.admin_url().'themes.php?page=options-framework' ) ;
}function register_my_menus() {
  register_nav_menus(
    array(
      'header-menu' => __( 'Header Menu' , 'funding'),
      )
  );
}add_action( 'init', 'register_my_menus' );
//get categories
function list_categories( $args = '' ) {
    $defaults = array(
        'show_option_all' => '', 'show_option_none' => __('No categories', 'funding'),
        'orderby' => 'name', 'order' => 'ASC',
        'style' => 'list',
        'show_count' => 0, 'hide_empty' => 1,
        'use_desc_for_title' => 1, 'child_of' => 0,
        'feed' => '', 'feed_type' => '',
        'feed_image' => '', 'exclude' => '',
        'exclude_tree' => '', 'current_category' => 0,
        'hierarchical' => true,
        'echo' => 1, 'depth' => 0,
        'taxonomy' => 'category'
    );
    $r = wp_parse_args( $args, $defaults );
    if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] )
        $r['pad_counts'] = true;
    if ( true == $r['hierarchical'] ) {
        $r['exclude_tree'] = $r['exclude'];
        $r['exclude'] = '';
    }
    if ( !isset( $r['class'] ) )
        $r['class'] = ( 'category' == $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];
    extract( $r );
    if ( !taxonomy_exists($taxonomy) )
        return false;
    $categories = get_categories( $r );
    $output = '';
    if ( $title_li && 'list' == $style )
            $output = '<li class="' . esc_attr( $class ) . '">' . $title_li . '<ul>';
    if ( empty( $categories ) ) {
        if ( ! empty( $show_option_none ) ) {
            if ( 'list' == $style )
                $output .= '<li>' . $show_option_none . '</li>';
            else
                $output .= $show_option_none;
        }
    } else {
        if ( ! empty( $show_option_all ) ) {
            $posts_page = ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
            $posts_page = esc_url( $posts_page );
            if ( 'list' == $style )
                $output .= "<li><a href='$posts_page'>$show_option_all</a></li>";
            else
                $output .= "<a href='$posts_page'>$show_option_all</a>";
        }
        if ( empty( $r['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
            $current_term_object = get_queried_object();
            if ( $r['taxonomy'] == $current_term_object->taxonomy )
                $r['current_category'] = get_queried_object_id();
        }
        if ( $hierarchical )
            $depth = $r['depth'];
        else
            $depth = -1; // Flat.
        $output .= walk_category_tree( $categories, $depth, $r );
    }
    if ( $title_li && 'list' == $style )
        $output .= '</ul></li>';
    $output = apply_filters( 'wp_list_categories', $output, $args );
    if ( $echo )
        echo $output;
    else
        return $output;
}
//get post for ajax category
add_action( 'wp_ajax_nopriv_load-filter', 'prefix_load_cat_posts' );
add_action( 'wp_ajax_load-filter', 'prefix_load_cat_posts' );
function prefix_load_cat_posts () {
        global $post;
        $cat_id = $_POST[ 'cat' ];
            $args = array (
            'showposts' => -1,
            'post_type' => 'project',
            'orderby' => 'post_date',
            'cat' => $cat_id);
        $posts = get_posts( $args );
        ob_start ();
if( $_SESSION['displ'] == 1){ //////////////////////////latest
        foreach ( $posts as $post ) {
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
            }
                   setup_postdata( $post );?>
            <div id="post-<?php echo $post -> ID; ?> <?php post_class(); ?>>
                <?php if(!has_post_thumbnail()){ ?>
                 <div id="post-image" class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div id="post-image" class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large');  ?></a></div>
            <?php } ?>
               <div class="category-container">
            <h3 class="posttitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="post-author"> <span class="icon-user" ></span><b>&nbsp;by</b>
                  <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php  if ( get_the_author_meta('first_name', get_the_author_meta( 'ID' )) ) {echo get_the_author_meta('first_name',get_the_author_meta( 'ID' )); }?>
                      <?php  if ( get_the_author_meta('last_name', get_the_author_meta( 'ID' )) ) {echo get_the_author_meta('last_name',get_the_author_meta( 'ID' )); }?></a>
                  <p><span class="icon-globe" ></span><b> <?php echo usercountry_name_display(get_the_author_meta( 'ID' ));?></b></p>
           </div>
            <div id="post-content">
              <?php $excerpt = get_the_excerpt();
                echo mb_substr($excerpt, 0,400);echo '...'; ?>
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
           <?php $category = get_the_category(); $catid = $category[0]->cat_ID; ?>
            <a class="edit-button button-small button-green" href="<?php echo get_category_link($catid); ?>"><?php _e("View all projects", 'funding'); ?></a>
          </div> <!--post-content -->
           </div> <!-- category-container -->
 <?php
             break;  } wp_reset_postdata();
    $response = ob_get_contents();
    ob_end_clean();
    echo $response;
    die(1);
 unset($_SESSION['displ']);
}elseif($_SESSION['displ'] == 2){ ///////////////////////////////////staff
        foreach ( $posts as $post ) {
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
            }
           if(get_post_meta($post->ID, '_smartmeta_my-awesome-field2', true) == 'true'){
                   setup_postdata( $post );?>
            <div id="post-<?php echo $post -> ID; ?> <?php post_class(); ?>>
            <?php if(!has_post_thumbnail()){ ?>
                 <div id="post-image" class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div id="post-image" class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large');  ?></a></div>
            <?php } ?>
            <div class="category-container">
            <h3 class="posttitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
             <div class="post-author"> <span class="icon-user" ></span><b>&nbsp;by</b>
                  <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php  if ( get_the_author_meta('first_name', get_the_author_meta( 'ID' )) ) {echo get_the_author_meta('first_name',get_the_author_meta( 'ID' )); }?>
                      <?php  if ( get_the_author_meta('last_name', get_the_author_meta( 'ID' )) ) {echo get_the_author_meta('last_name',get_the_author_meta( 'ID' )); }?></a>
                  <p><span class="icon-globe" ></span><b> <?php echo usercountry_name_display(get_the_author_meta( 'ID' ));?></b></p>
           </div>
            <div id="post-content">
               <?php $excerpt = get_the_excerpt();
                echo mb_substr($excerpt, 0,400);echo '...'; ?>
            <?php if($funded_amount == $target or $funded_amount > $target){ ?>
                <div class="project-successful">
                    <strong><?php _e('Successful!', 'funding'); ?></strong>
                </div>
            <?php }elseif($project_expired){ ?>
                        <div class="project-unsuccessful">
                            <strong><?php _e('Unsuccessful!', 'funding'); ?></strong>
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
   <?php $category = get_the_category(); $catid = $category[0]->cat_ID; ?>
            <a class="edit-button button-small button-green" href="<?php echo get_category_link($catid); ?>"><?php _e("View all projects", 'funding'); ?></a>
            </div> <!--post-content -->
          </div> <!-- category-container -->
           <?php
                break;
             }} wp_reset_postdata();
    $response = ob_get_contents();
    ob_end_clean();
    echo $response;
    die(1);
 unset($_SESSION['displ']);
}elseif($_SESSION['displ'] == 3){ ////////////////////////////////////sucessful
?>
 <?php       foreach ( $posts as $post ) {
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
            }
                   setup_postdata( $post );?>
            <div id="post-<?php echo $post -> ID; ?> <?php post_class(); ?>>
           <?php if(!has_post_thumbnail()){ ?>
                 <div id="post-image" class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div id="post-image" class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large');  ?></a></div>
            <?php } ?>
            <div class="category-container">
            <h3 class="posttitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
               <div class="post-author"> <span class="icon-user" ></span><b>&nbsp;by</b>
                  <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php  if ( get_the_author_meta('first_name', get_the_author_meta( 'ID' )) ) {echo get_the_author_meta('first_name',get_the_author_meta( 'ID' )); }?>
                      <?php  if ( get_the_author_meta('last_name', get_the_author_meta( 'ID' )) ) {echo get_the_author_meta('last_name',get_the_author_meta( 'ID' )); }?></a>
                  <p><span class="icon-globe" ></span><b> <?php echo usercountry_name_display(get_the_author_meta( 'ID' ));?></b></p>
           </div>
            <div id="post-content">
            <?php $excerpt = get_the_excerpt();
                echo mb_substr($excerpt, 0,400);echo '...'; ?>
            <?php if($funded_amount == $target or $funded_amount > $target){ ?>
                <div class="project-successful">
                    <strong><?php _e('Successful!', 'funding'); ?></strong>
                </div>
            <?php }elseif($project_expired){ ?>
                        <div class="project-unsuccessful">
                            <strong><?php _e('Unsuccessful!', 'funding'); ?></strong>
                        </div>
           <?php }else{ ?>
            <div class="progress progress-striped active bar-green"><div style="width: <?php printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?>%" class="bar"></div></div>
            <?php } ?>
            <ul class="project-stats">
                <li class="first funded">
                     <strong><?php printf(__('%u%%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, round($target)) ?></strong><?php _e('funded', 'funding'); ?>
                </li>
                <li class="pledged">
                    <strong>
                         <?php print $project_currency_sign; print number_format(round((int)$target), 0, '.', ',');?></strong><?php _e('target', 'funding'); ?>
                </li>
                <li data-end_time="2013-02-24T08:41:18Z" class="last ksr_page_timer">
                    <?php
                    if(!$project_expired) : ?>
                        <strong><?php print F_Controller::timesince(time(), strtotime($project_settings['date']), 1, ''); ?></strong>
                        <?php _e('days to go', 'funding') ?>
                    <?php endif; ?>
                </li>
            </ul>
            <?php $category = get_the_category(); $catid = $category[0]->cat_ID; ?>
            <a class="edit-button button-small button-green" href="<?php echo get_category_link($catid); ?>"><?php _e('View all projects', 'funding'); ?></a>
         </div> <!--post-content -->
          </div> <!-- category-container -->
            <?php
                break;
             } wp_reset_postdata();
    $response = ob_get_contents();
    ob_end_clean();
    echo $response;
    die(1);
 unset($_SESSION['displ']);
}elseif($_SESSION['displ'] == 4){//////////////////////////////ending
            global $post;
            $cat_id = $_POST[ 'cat' ];
            $args = array (
            'showposts' => -1,
            'post_type' => 'project',
            'orderby' => 'meta_value',
            'meta_key' => 'datum',
            'order' => 'ASC',
            'cat' => $cat_id);
        $posts = get_posts( $args );
        ob_start ();
        foreach ( $posts as $post ) {
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
            }
                   setup_postdata( $post );?>
            <div id="post-<?php echo $post -> ID; ?> <?php post_class(); ?>>
                <?php if(!has_post_thumbnail()){ ?>
                 <div id="post-image" class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div id="post-image" class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large');  ?></a></div>
            <?php } ?>
            <div class="category-container">
            <h3 class="posttitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="post-author"> <span class="icon-user" ></span><b>&nbsp;by</b>
                  <a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>"><?php  if ( get_the_author_meta('first_name', get_the_author_meta( 'ID' )) ) {echo get_the_author_meta('first_name',get_the_author_meta( 'ID' )); }?>
                      <?php  if ( get_the_author_meta('last_name', get_the_author_meta( 'ID' )) ) {echo get_the_author_meta('last_name',get_the_author_meta( 'ID' )); }?></a>
                  <p><span class="icon-globe" ></span><b> <?php echo usercountry_name_display(get_the_author_meta( 'ID' ));?></b></p>
           </div>
            <div id="post-content">
              <?php $excerpt = get_the_excerpt();
                echo mb_substr($excerpt, 0,400);echo '...'; ?>
            <?php if($funded_amount == $target or $funded_amount > $target){ ?>
                <div class="project-successful">
                    <strong><?php _e('Successful!', 'funding'); ?></strong>
                </div>
            <?php }elseif($project_expired){ ?>
                        <div class="project-unsuccessful">
                            <strong><?php _e('Unsuccessful!', 'funding'); ?></strong>
                        </div>
            <?php }else{ ?>
            <div class="progress progress-striped active bar-green"><div style="width: <?php printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?>%" class="bar"></div></div>
            <?php } ?>
            <ul class="project-stats">
                <li class="first funded">
                     <strong><?php printf(__('%u%%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, round($target)) ?></strong><?php _e('funded', 'funding'); ?>
                </li>
                <li class="pledged">
                    <strong>
                         <?php print $project_currency_sign; print number_format(round((int)$target), 0, '.', ',');?></strong><?php _e('target', 'funding'); ?>
                </li>
                <li data-end_time="2013-02-24T08:41:18Z" class="last ksr_page_timer">
                    <?php
                    if(!$project_expired) : ?>
                        <strong><?php print F_Controller::timesince(time(), strtotime($project_settings['date']), 1, ''); ?></strong>
                        <?php _e('days to go', 'funding') ?>
                    <?php endif; ?>
                </li>
            </ul>
            <?php $category = get_the_category(); $catid = $category[0]->cat_ID; ?>
            <a class="edit-button button-small button-green" href="<?php echo get_category_link($catid); ?>"><?php _e('View all projects', 'funding'); ?></a>
               </div> <!--post-content -->
          </div> <!-- category-container -->
             <?php
  break;} wp_reset_postdata();
    $response = ob_get_contents();
    ob_end_clean();
    echo $response;
    die(1);
 unset($_SESSION['displ']);
}elseif($_SESSION['displ'] == 5){ ///////////////all projects
    foreach ( $posts as $post ) {
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
            }
                   setup_postdata( $post );?>
      <div class="project-card span3">
             <?php if(!has_post_thumbnail()){ ?>
                 <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div  class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium-img');  ?></a></div>
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
                    <strong><?php _e('Successful!', 'funding'); ?></strong>
                </div>
            <?php }elseif($project_expired){ ?>
                        <div class="project-unsuccessful">
                            <strong><?php _e('Unsuccessful!', 'funding'); ?></strong>
                        </div>
            <?php }else{ ?>
            <div class="progress progress-striped active bar-green"><div style="width: <?php printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?>%" class="bar"></div></div>
            <?php } ?>
            <ul class="project-stats">
                <li class="first funded">
                     <strong><?php printf(__('%u%%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, round($target)) ?></strong><?php _e('funded', 'funding'); ?>
                </li>
                <li class="pledged">
                    <strong>
                         <?php print $project_currency_sign; print number_format(round((int)$target), 0, '.', ',');?></strong><?php _e('target', 'funding'); ?>
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
 <?php
               } wp_reset_postdata();
    $response = ob_get_contents();
    ob_end_clean();
    echo $response;
    die(1);
unset($_SESSION['displ']);
}}
// Fix post counts
function fix_post_counts($views) {
    global $current_user, $wp_query;
    unset($views['mine']);
    $types = array(
        array( 'status' =>  NULL ),
        array( 'status' => 'publish' ),
        array( 'status' => 'draft' ),
        array( 'status' => 'pending' ),
        array( 'status' => 'trash' )
    );
    foreach( $types as $type ) {
        $query = array(
            'author'      => $current_user->ID,
            'post_type'   => 'post',
            'post_status' => $type['status']
        );
        $result = new WP_Query($query);
        if( $type['status'] == NULL ):
            $class = ($wp_query->query_vars['post_status'] == NULL) ? ' class="current"' : '';
            $views['all'] = sprintf(__('<a href="%s"'. $class .'>All <span class="count">(%d)</span></a>', 'funding'),
                admin_url('edit.php?post_type=post'),
                $result->found_posts);
        elseif( $type['status'] == 'publish' ):
            $class = ($wp_query->query_vars['post_status'] == 'publish') ? ' class="current"' : '';
            $views['publish'] = sprintf(__('<a href="%s"'. $class .'>Published <span class="count">(%d)</span></a>', 'funding'),
                admin_url('edit.php?post_status=publish&post_type=post'),
                $result->found_posts);
        elseif( $type['status'] == 'draft' ):
            $class = ($wp_query->query_vars['post_status'] == 'draft') ? ' class="current"' : '';
            $views['draft'] = sprintf(__('<a href="%s"'. $class .'>Draft'. ((sizeof($result->posts) > 1) ? "s" : "") .' <span class="count">(%d)</span></a>', 'funding'),
                admin_url('edit.php?post_status=draft&post_type=post'),
                $result->found_posts);
        elseif( $type['status'] == 'pending' ):
            $class = ($wp_query->query_vars['post_status'] == 'pending') ? ' class="current"' : '';
            $views['pending'] = sprintf(__('<a href="%s"'. $class .'>Pending <span class="count">(%d)</span></a>', 'funding'),
                admin_url('edit.php?post_status=pending&post_type=post'),
                $result->found_posts);
        elseif( $type['status'] == 'trash' ):
            $class = ($wp_query->query_vars['post_status'] == 'trash') ? ' class="current"' : '';
            $views['trash'] = sprintf(__('<a href="%s"'. $class .'>Trash <span class="count">(%d)</span></a>', 'funding'),
                admin_url('edit.php?post_status=trash&post_type=post'),
                $result->found_posts);
        endif;
    }
    return $views;
}
// Fix media counts
function fix_media_counts($views) {
    global $wpdb, $current_user, $post_mime_types, $avail_post_mime_types;
    $views = array();
    $count = $wpdb->get_results( "
        SELECT post_mime_type, COUNT( * ) AS num_posts
        FROM $wpdb->posts
        WHERE post_type = 'attachment'
        AND post_author = $current_user->ID
        AND post_status != 'trash'
        GROUP BY post_mime_type
    ", ARRAY_A );
    foreach( $count as $row )
        $_num_posts[$row['post_mime_type']] = $row['num_posts'];
    $_total_posts = array_sum($_num_posts);
    $detached = isset( $_REQUEST['detached'] ) || isset( $_REQUEST['find_detached'] );
    if ( !isset( $total_orphans ) )
        $total_orphans = $wpdb->get_var("
            SELECT COUNT( * )
            FROM $wpdb->posts
            WHERE post_type = 'attachment'
            AND post_author = $current_user->ID
            AND post_status != 'trash'
            AND post_parent < 1
        ");
    $matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
    foreach ( $matches as $type => $reals )
        foreach ( $reals as $real )
            $num_posts[$type] = ( isset( $num_posts[$type] ) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];
    $class = ( empty($_GET['post_mime_type']) && !$detached && !isset($_GET['status']) ) ? ' class="current"' : '';
    $views['all'] = "<a href='upload.php'$class>" . sprintf( __('All <span class="count">(%s)</span>', 'funding' ), number_format_i18n( $_total_posts )) . '</a>';
    foreach ( $post_mime_types as $mime_type => $label ) {
        $class = '';
        if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
            continue;
        if ( !empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
            $class = ' class="current"';
        if ( !empty( $num_posts[$mime_type] ) )
            $views[$mime_type] = "<a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), $num_posts[$mime_type] ) . '</a>';
    }
    $views['detached'] = '<a href="upload.php?detached=1"' . ( $detached ? ' class="current"' : '' ) . '>' . sprintf( __( 'Unattached <span class="count">(%s)</span>', 'funding' ), $total_orphans ) . '</a>';
    return $views;
}
//login with ajax
class LoginWithAjax {
    /**
     * If logged in upon instantiation, it is a user object.
     * @var WP_User
     */
    var $current_user;
    /**
     * List of templates available in the plugin dir and theme (populated in init())
     * @var array
     */
    var $templates = array();
    /**
     * Name of selected template (if selected)
     * @var string
     */
    var $template;
    /**
     * lwa_data option
     * @var array
     */
    var $data;
    /**
     * Location of footer file if one is found when generating a widget, for use in loading template footers.
     * @var string
     */
    var $footer_loc;
    /**
     * URL for the AJAX Login procedure in templates (including callback and template parameters)
     * @var string
     */
    var $url_login;
    /**
     * URL for the AJAX Remember Password procedure in templates (including callback and template parameters)
     * @var string
     */
    var $url_remember;
    /**
     * URL for the AJAX Registration procedure in templates (including callback and template parameters)
     * @var string
     */
    var $url_register;
    // Class initialization
    function LoginWithAjax() {
        //Set when to run the plugin
        add_action('widgets_init', array(&$this, 'init'));
    }
    // Actions to take upon initial action hook
    function init() {
        //Load LWA options
        $this -> data = get_option('lwa_data');
        //Remember the current user, in case there is a logout
        $this -> current_user = wp_get_current_user();
        //Generate URLs for login, remember, and register
        $this -> url_login = $this -> template_link(site_url('wp-login.php', 'login_post'));
        $this -> url_register = $this -> template_link(site_url('wp-login.php?action=register', 'login_post'));
        $this -> url_remember = $this -> template_link(site_url('wp-login.php?action=lostpassword', 'login_post'));
        //Make decision on what to display
        if (isset($_REQUEST["login-with-ajax"])) {//AJAX Request
            $this -> ajax();
        } elseif (isset($_REQUEST["login-with-ajax-widget"])) {//Widget Request via AJAX
            $instance = (!empty($_REQUEST["template"])) ? array('template' => $_REQUEST["template"]) : array();
            $instance['is_widget'] = false;
            $instance['profile_link'] = (!empty($_REQUEST["lwa_profile_link"])) ? $_REQUEST['lwa_profile_link'] : 0;
            $this -> widget(array(), $instance);
            exit();
        }
    }
    /*
     * LOGIN OPERATIONS
     */
    // Decides what action to take from the ajax request
    function ajax() {
        switch ( $_REQUEST["login-with-ajax"] ) {
            case 'login' :
                //A login has been requested
                $return = $this -> json_encode($this -> login());
                break;
            case 'register' :
                //A login has been requested
                $return = $this -> json_encode($this -> register());
                break;
            case 'remember' :
                //Remember the password
                $return = $this -> json_encode($this -> remember());
                break;
            default :
                //Don't know
                $return = $this -> json_encode(array('result' => 0, 'error' => 'Unknown command requested'));
                break;
        }
        echo $return;
        exit();
    }
    // Reads ajax login creds via POSt, calls the login script and interprets the result
    function login() {
        $return = array();
        //What we send back
        if (!empty($_REQUEST['log']) && !empty($_REQUEST['pwd']) && trim($_REQUEST['log']) != '' && trim($_REQUEST['pwd'] != '')) {
            $loginResult = wp_signon();
            $user_role = 'null';
            if (strtolower(get_class($loginResult)) == 'wp_user') {
                //User login successful
                $this -> current_user = $loginResult;
                /* @var $loginResult WP_User */
                $return['result'] = true;
                $return['message'] = __("Login Successful, redirecting...", 'funding');
                //Do a redirect if necessary
                $redirect = $this -> getLoginRedirect($this -> current_user);
                if ($redirect != '') {
                    $return['redirect'] = $redirect;
                }
                //If the widget should just update with ajax, then supply the URL here.
                if (!empty($this -> data['no_login_refresh']) && $this -> data['no_login_refresh'] == 1) {
                    //Is this coming from a template?
                    $query_vars = ($_GET['template'] != '') ? "&template={$_GET['template']}" : '';
                    $query_vars .= ($_REQUEST['lwa_profile_link'] == '1') ? "&lwa_profile_link=1" : '';
                    $return['widget'] =  site_url(). "?login-with-ajax-widget=1$query_vars";
                    $return['message'] = __("Login successful, updating...", 'funding');
                }
            } elseif (strtolower(get_class($loginResult)) == 'wp_error') {
                //User login failed
                /* @var WP_Error $loginResult */
                $return['result'] = false;
                $return['error'] = $loginResult -> get_error_message();
            } else {
                //Undefined Error
                $return['result'] = false;
                $return['error'] = __('An undefined error has ocurred', 'funding');
            }
        } else {
            $return['result'] = false;
            $return['error'] = __('Please supply your username and password.', 'funding');
        }
        //Return the result array with errors etc.
        return $return;
    }
    /**
     * Checks post data and registers user
     * @return string
     */
    function register() {
        if (!empty($_REQUEST['lwa'])) {
            $return = array();
            if ('POST' == $_SERVER['REQUEST_METHOD']) {
                require_once (ABSPATH . WPINC . '/registration.php');
                $errors = register_new_user($_POST['user_login'], $_POST['user_email']);
                if (!is_wp_error($errors)) {
                    //Success
                    $return['result'] = true;
                    $return['message'] = __('Registration complete. Please check your e-mail.', 'funding');
                } else {
                    //Something's wrong
                    $return['result'] = false;
                    $return['error'] = $errors -> get_error_message();
                }
            }
            echo $this -> json_encode($return);
            exit();
        }
    }
    // Reads ajax login creds via POSt, calls the login script and interprets the result
    function remember() {
        $return = array();
        //What we send back
        $result = retrieve_password();
        if ($result === true) {
            //Password correctly remembered
            $return['result'] = true;
            $return['message'] = __("We have sent you an email", 'funding');
        } elseif (strtolower(get_class($result)) == 'wp_error') {
            //Something went wrong
            /* @var $result WP_Error */
            $return['result'] = false;
            $return['error'] = $result -> get_error_message();
        } else {
            //Undefined Error
            $return['result'] = false;
            $return['error'] = __('An undefined error has ocurred', 'funding');
        }
        //Return the result array with errors etc.
        return $return;
    }
    /*
     * Redirect Functions
     */
    function logoutRedirect() {
        $redirect = $this -> getLogoutRedirect();
        if ($redirect != '') {
            wp_redirect($redirect);
            exit();
        }
    }
    function getLogoutRedirect() {
        $data = $this -> data;
        if (!empty($data['logout_redirect'])) {
            $redirect = $data['logout_redirect'];
        }
        if (strtolower(get_class($this -> current_user)) == "wp_user") {
            //Do a redirect if necessary
            $data = $this -> data;
            $user_role = array_shift($this -> current_user -> roles);
            //Checking for role-based redirects
            if (!empty($data["role_logout"]) && is_array($data["role_logout"]) && isset($data["role_logout"][$user_role])) {
                $redirect = $data["role_logout"][$user_role];
            }
        }
        $redirect = str_replace("%LASTURL%", $_SERVER['HTTP_REFERER'], $redirect);
        return $redirect;
    }
    function loginRedirect($redirect, $redirect_notsurewhatthisis, $user) {
        $data = $this -> data;
        if (is_user_logged_in()) {
            $lwa_redirect = $this -> getLoginRedirect($user);
            if ($lwa_redirect != '') {
                wp_redirect($lwa_redirect);
                exit();
            }
        }
        return $redirect;
    }
    function getLoginRedirect($user) {
        $data = $this -> data;
        if ($data['login_redirect'] != '') {
            $redirect = $data["login_redirect"];
        }
        if (strtolower(get_class($user)) == "wp_user") {
            $user_role = array_shift($user -> roles);
            //Checking for role-based redirects
            if (isset($data["role_login"][$user_role])) {
                $redirect = $data["role_login"][$user_role];
            }
        }
        //Do string replacements
        $redirect = str_replace('%USERNAME%', $user -> user_login, $redirect);
        $redirect = str_replace("%LASTURL%", $_SERVER['HTTP_REFERER'], $redirect);
        return $redirect;
    }
    /*
     * Auxillary Functions
     */
    //Checks a directory for folders and populates the template file
    function find_templates($dir) {
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($dir . $file) && $file != '.' && $file != '..' && $file != '.svn') {
                        //Template dir found, add it to the template array
                        $this -> templates[$file] = path_join($dir, $file);
                    }
                }
                closedir($dh);
            }
        }
    }
    //Add template link and JSON callback var to the URL
    function template_link($content) {
        if (strstr($content, '?')) {
            $content .= '&amp;callback=?&amp;template=' . $this -> template;
        } else {
            $content .= '?callback=?&amp;template=' . $this -> template;
        }
        return $content;
    }
    //PHP4 Safe JSON encoding
    function json_encode($array) {
        if (!function_exists("json_encode")) {
            $return = json_encode($array);
        } else {
            $return = $this -> array_to_json($array);
        }
        if (isset($_REQUEST['callback']) && preg_match("/^jQuery[_a-zA-Z0-9]+$/", $_REQUEST['callback'])) {
            $return = $_GET['callback'] . "($return)";
        }
        return $return;
    }
    //PHP4 Compatible json encoder function
    function array_to_json($array) {
        //PHP4 Comapatability - This encodes the array into JSON. Thanks go to Andy - http://www.php.net/manual/en/function.json-encode.php#89908
        if (!is_array($array)) {
            return false;
        }
        $associative = count(array_diff(array_keys($array), array_keys(array_keys($array))));
        if ($associative) {
            $construct = array();
            foreach ($array as $key => $value) {
                // We first copy each key/value pair into a staging array,
                // formatting each key and value properly as we go.
                // Format the key:
                if (is_numeric($key)) {
                    $key = "key_$key";
                }
                $key = "'" . addslashes($key) . "'";
                // Format the value:
                if (is_array($value)) {
                    $value = $this -> array_to_json($value);
                } else if (is_bool($value)) {
                    $value = ($value) ? "true" : "false";
                } else if (!is_numeric($value) || is_string($value)) {
                    $value = "'" . addslashes($value) . "'";
                }
                // Add to staging array:
                $construct[] = "$key: $value";
            }
            // Then we collapse the staging array into the JSON form:
            $result = "{ " . implode(", ", $construct) . " }";
        } else {// If the array is a vector (not associative):
            $construct = array();
            foreach ($array as $value) {
                // Format the value:
                if (is_array($value)) {
                    $value = $this -> array_to_json($value);
                } else if (!is_numeric($value) || is_string($value)) {
                    $value = "'" . addslashes($value) . "'";
                }
                // Add to staging array:
                $construct[] = $value;
            }
            // Then we collapse the staging array into the JSON form:
            $result = "[ " . implode(", ", $construct) . " ]";
        }
        return $result;
    }
}//Template Tag
function login_with_ajax($atts = '') {
    global $LoginWithAjax;
    $atts = shortcode_parse_atts($atts);
    echo $LoginWithAjax -> shortcode($atts);
}// Start plugin
global $LoginWithAjax;
$LoginWithAjax = new LoginWithAjax();
// Breadcrumbs
function pg(){
    $pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'meta_value' => 'tmp-blog.php'
));
foreach($pages as $page){
    return $page->post_name;
}}
function rh_get_page_id($name)
{
global $wpdb;
// get page id using custom query
$page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE ( post_name = '".$name."' or post_title = '".$name."' ) and post_status = 'publish' and post_type='page' ");
return $page_id;
}
function rh_get_page_permalink($name)
{
$page_id = rh_get_page_id($name);
return get_permalink($page_id);
}
function the_breadcrumb() {
     if (!is_home()) {
        echo '<a href="';
        echo home_url();
        echo '">';
        echo __('Home','funding');
        echo "</a> / ";
        if(get_post_type() == 'project'){
             echo '<a href="';
        echo rh_get_page_permalink(''.pg());
        echo '">';
        echo __('Projects','funding');
        echo "</a> ";
            if (is_single()) {
                echo " / ";
                the_title();
            }
        }elseif (is_single()) {
        echo '<a href="';
        echo rh_get_page_permalink(''.pg());
        echo '">';
        echo __('Blog','funding');
        echo "</a> ";
            if (is_single()) {
                echo " / ";
                the_title();
            }
        }elseif(is_category()){
        echo single_cat_title();
        }elseif(is_404()){
        echo '404';
         }elseif(is_author()){
        $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author')); echo $curauth->user_nicename;
        } elseif (is_page()) {
            echo the_title();
        }
     if(is_admin()){
        $current_user= wp_get_current_user();
        $level = $current_user->user_level;
        if($level == 1){
            global $wp_post_types; $obj = $wp_post_types['project'];print $obj->labels->singular_name;
        }}
    }
}
//allow redirection, even if my theme starts to send output to the browser
add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
}
function adminclass(){
    $current_user= wp_get_current_user();
    $level = $current_user->user_level;
    if($level == 1){
      $classes = 'user_project';
      return $classes;
    }
}
add_action('admin_body_class', 'adminclass');
add_action('after_setup_theme', 'funding');
function funding() {
      include_once(TEMPLATEPATH.'/funding/funding.php');
}
add_action('after_setup_theme', 'country');
function country() {
      include_once(TEMPLATEPATH.'/themeOptions/admin/country/usercountry.php');
}
//custom excerpt lenght
function custom_excerpt_length( $length ) {
    return 150;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
//pagination
function kriesi_pagination($pages = '', $range = 1)
{
$showitems = ($range * 1)+1;
$general_show_page  = of_get_option('general_post_show');
global $paged;
global $paginate;
if(empty($paged)) $paged = 1;
if($pages == '')
{
global $wp_query;
$pages = $wp_query->max_num_pages;
if(!$pages)
{
$pages = 1;
}
}
if(1 != $pages)
{
$url= get_template_directory_uri();
$leftpager= '&laquo;';
$rightpager= '&raquo;';
if($paged > 2 && $paged > $range+1 && $showitems < $pages) $paginate.=  "";
if($paged > 1 ) $paginate.=  "<a class='page-selector' href='".get_pagenum_link($paged - 1)."'>". $leftpager. "</a>";
for ($i=1; $i <= $pages; $i++)
{
if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
{
$paginate.=  ($paged == $i)? "<li><a href='".get_pagenum_link($i)."'  class='active'>".$i."</a></li>":"<li><a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a></li>";
}
}
if ($paged < $pages ) $paginate.=  "<li><a class='page-selector' href='".get_pagenum_link($paged + 1)."' >". $rightpager. "</a></li>";
}
return $paginate;
}
/* add_action( 'pre_get_posts', 'author_pagination' ); ////////ubijalo slider videti da li se koristi negde !!!!!!!!!!!!!!!!!
function author_pagination( &$query ) {
    $showposts = of_get_option('blognum');
    if ($query->is_author or $query->is_archive)
        $query->set(  'posts_per_page', $showposts  );
} */
if ( function_exists( 'add_image_size' ) ) {
    add_image_size( 'category-thumb', 320, 200, true );
    add_image_size( 'projects', 200, 150, true );
}
function custom_excerpt_length_pro( $length ) {
    return 20;
}
function get_custom_field($key, $echo = FALSE) { /*OVO JE ZA PREUZIMANJE CUSTOM FIELDA*/
    global $post;
    $custom_field = get_post_meta($post->ID, $key, true);
    if ($echo == FALSE) return $custom_field;
    echo $custom_field;
}
function add_projects_custom_box() {
add_meta_box('postcustom', __('Update Fields', 'funding'), 'post_custom_meta_box', 'project', 'normal', 'core');
}
add_action( 'add_meta_boxes', 'add_projects_custom_box' );
add_filter( 'postmeta_form_limit', 'wpse_73543_hide_meta_start' );
function wpse_73543_hide_meta_start( $num )
{
    add_filter( 'query', 'wpse_73543_hide_meta_filter' );
    return $num;
}
function wpse_73543_hide_meta_filter( $query )
{
    // Protect further queries.
    remove_filter( current_filter(), __FUNCTION__ );
    $forbidden = array ( 'aq_block_1' ,'aq_block_2','aq_block_3','aq_block_4','aq_block_5','aq_block_6','aq_block_7','aq_block_8','aq_block_9','aq_block_10','project_video_link','preapproval_key',
    'allorany', 'charged', 'date','datum','field_1', 'field_2','hide_on_screen','layout','my_meta_box_check','my_meta_box_select','my_meta_box_text', 'notified','paypal_email','position',
    'available' ,'funder','funding_amount','page-option-choose-left-sidebar','page-option-choose-right-sidebar', 'page-option-item-xml', 'page-option-show-content','rule',
    'page-option-sidebar-template', 'page-option-show-title', 'page-option-top-slider-height', 'page-option-top-slider-types', 'page-option-top-slider-xml', 'reward', 'settings');
    $where     = "WHERE meta_key NOT IN('" . join( "', '", $forbidden ) . "') ";
    $find      = "GROUP BY";
    $query     = str_replace( $find, "$where\n$find", $query );
  return $query;
}
function custom_comments($comment, $args, $depth) {
  $GLOBALS['comment'] = $comment;
    $GLOBALS['comment_depth'] = $depth;
  ?>
   <div class="project-comment row">
        <div class="comment-author span1 vcard"><?php commenter_avatar() ?></div>
  <?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved'>Your comment is awaiting moderation.</span>\n", 'funding') ?>
          <div class="comment-content span6">
             <div class="comment-info"> <?php commenter_link() ?> <?php _e("on", 'funding');?> <?php the_title(); ?> <?php comment_time('M j, Y @ G:i'); ?> </div>
            <div class="comment-content"> <?php comment_text() ?></div>
        </div>
</div>
<?php } // end custom_comments
function custom_pings($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment;
        ?>
         <div class="project-comment row">
                <div class="comment-author"><?php printf(__('By %1$s on %2$s at %3$s', 'funding'),
                        get_comment_author_link(),
                        get_comment_date(),
                        get_comment_time() );
                        edit_comment_link(__('Edit', 'funding'), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?></div>
    <?php if ($comment->comment_approved == '0') _e('\t\t\t\t\t<span class="unapproved">Your trackback is awaiting moderation.</span>\n', 'funding') ?>
            <div class="comment-content span6">
                <?php comment_text() ?>
            </div>
            </div>
<?php
} // end custom_pings
// Produces an avatar image with the hCard-compliant photo class
function commenter_link() {
   $commenter = get_comment_author_link();
    if ( ereg( '<a[^>]* class=[^>]+>', $commenter ) ) {
        $commenter = ereg_replace( '(<a[^>]* class=[\'"]?)', '\\1url ' , $commenter );
    } else {
        $commenter = ereg_replace( '(<a )/', '\\1class="url "' , $commenter );
    }
    echo ' <span class="comment-info">' . $commenter . '</span>';
} // end commenter_link
function commenter_avatar() {
    $avatar_email = get_comment_author_email();
    $avatar = str_replace( "class='avatar", "class='photo avatar", get_avatar( $avatar_email, 80 ) );
    echo $avatar;
} // end commenter_link
// get the "contributor" role object
$obj_existing_role = get_role( 'contributor' );
// add the "organize_gallery" capability
$obj_existing_role->add_cap( 'edit_published_posts' );
//allow user to select only one category
if(strstr($_SERVER['REQUEST_URI'], 'wp-admin/post-new.php') or strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php') ) {
ob_start('one_category_only');
}
function one_category_only($content) {
$content = str_replace('type="checkbox" ', 'type="radio" ', $content);
return $content;
}
function input_fix(){?>
<script>
jQuery('#_smartmeta_my-awesome-field2').clone().attr('type','checkbox').insertAfter('#_smartmeta_my-awesome-field2').prev().remove();
jQuery('#comment_status').clone().attr('type','checkbox').insertAfter('#comment_status').prev().remove();
jQuery('#ping_status').clone().attr('type','checkbox').insertAfter('#ping_status').prev().remove();
</script>
<?php
}
 add_action( 'admin_footer', 'input_fix' );
//add video metabox
add_smart_meta_box('my-meta-box', array(
'title' => __('Video url', 'funding'), // the title of the meta box
'pages' => array('project'),  // post types on which you want the metabox to appear
'context' => 'normal', // meta box context (see above)
'priority' => 'high', // meta box priority (see above)
'fields' => array( // array describing our fields
array(
'name' => __('Put your project embed video URL here', 'funding'),
'id' => 'my-awesome-field',
'type' => 'textarea',
),)));
//add staff checkbox
add_smart_meta_box('my-meta-box2', array(
'title' => __('Staff picks', 'funding'), // the title of the meta box
'pages' => array('project'),  // post types on which you want the metabox to appear
'context' => 'normal', // meta box context (see above)
'priority' => 'high', // meta box priority (see above)
'fields' => array( // array describing our fields
array(
'name' => __('Staff picks', 'funding'),
'id' => 'my-awesome-field2',
'type' => 'checkbox',
),)));
//limit media to logged user
add_action('pre_get_posts','restrict_media_library');
function restrict_media_library( $wp_query_obj ) {
    global $current_user, $pagenow;
    if( !is_a( $current_user, 'WP_User') )
    return;
    if( 'admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments' )
    return;
    if( !current_user_can('manage_media_library') )
    $wp_query_obj->set('author', $current_user->ID );
    return;
}
add_image_size( 'medium-img', 200, 150, true );
//return url of thumb
function return_thumb_url(){
global $post;
$thumb_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID) , 'medium-img');
return $thumb_url[0]; }
add_theme_support( 'automatic-feed-links' );
//add js and css files for projects slider
function carousel_add_javascript_files(){
wp_register_style('css_file', get_template_directory_uri().'/css/custom-style.css');
wp_enqueue_style('css_file');
wp_enqueue_script('jquery');
wp_register_script( 'tiny_js', get_template_directory_uri().'/js/jquery.carouFredSel-6.1.0.js');
wp_enqueue_script('tiny_js');
wp_register_script( 'custom_js',  get_template_directory_uri().'/js/custom.js');
wp_enqueue_script('custom_js');}
add_action('init', 'carousel_add_javascript_files');
function my_scripts(){
wp_register_script( 'custom_js1',  get_template_directory_uri().'/js/login-with-ajax.js');
wp_enqueue_script('custom_js1');
wp_register_script( 'custom_js2',  get_template_directory_uri().'/js/login-with-ajax.source.js');
wp_enqueue_script('custom_js2');
wp_register_script( 'custom_js3',  get_template_directory_uri().'/js/jquery.validate.min.js');
wp_enqueue_script('custom_js3');
wp_register_script( 'custom_js4',  get_template_directory_uri().'/js/verify.js');
wp_enqueue_script('custom_js4');
wp_register_script( 'custom_js5',   get_template_directory_uri().'/js/jquery-ui-1.10.2.custom.js');
wp_enqueue_script('custom_js5');
}
add_action('wp_enqueue_scripts', 'my_scripts');
function my_styles(){
wp_register_style('css_file1', get_template_directory_uri().'/css/admin.css');
wp_enqueue_style('css_file1');
}
add_action('admin_head', 'my_styles');
add_action('admin_enqueue_scripts', 'admin_scripts');
function admin_scripts(){
wp_register_script( 'custom_js77',  get_template_directory_uri().'/ckeditor/ckeditor.js');
wp_enqueue_script('custom_js77');
}
/**
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/pluginactivation.php';
add_action( 'tgmpa_register', 'my_theme_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function my_theme_register_required_plugins() {
    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
        // This is an example of how to include a plugin pre-packaged with a theme
        array(
            'name'                  => 'Paralax slider', // The plugin name
            'slug'                  => 'layerslider', // The plugin slug (typically the folder name)
            'source'                => get_stylesheet_directory() . '/plugins/layerslider.zip', // The plugin source
            'required'              => true, // If false, the plugin is only 'recommended' instead of required
            'version'               => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
            'force_activation'      => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
            'force_deactivation'    => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
            'external_url'          => '', // If set, overrides default API URL and points to an external URL
        ),
    );
    // Change this to your theme text domain, used for internationalising strings
    $theme_text_domain = 'funding';
    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'domain'            => $theme_text_domain,          // Text domain - likely want to be the same as your theme.
        'default_path'      => '',                          // Default absolute path to pre-packaged plugins
        'parent_menu_slug'  => 'themes.php',                // Default parent menu slug
        'parent_url_slug'   => 'themes.php',                // Default parent URL slug
        'menu'              => 'install-required-plugins',  // Menu slug
        'has_notices'       => true,                        // Show admin notices or not
        'is_automatic'      => true,                       // Automatically activate plugins after installation or not
        'message'           => '',                          // Message to output right before the plugins table
        'strings'           => array(
            'page_title'                                => __( 'Install Required Plugins', 'funding' ),
            'menu_title'                                => __( 'Install Plugins', 'funding' ),
            'installing'                                => __( 'Installing Plugin: %s', 'funding' ), // %1$s = plugin name
            'oops'                                      => __( 'Something went wrong with the plugin API.', 'funding' ),
            'notice_can_install_required'               => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'funding' ), // %1$s = plugin name(s)
            'notice_can_install_recommended'            => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'funding' ), // %1$s = plugin name(s)
            'notice_cannot_install'                     => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'funding' ), // %1$s = plugin name(s)
            'notice_can_activate_required'              => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' , 'funding'), // %1$s = plugin name(s)
            'notice_can_activate_recommended'           => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'funding' ), // %1$s = plugin name(s)
            'notice_cannot_activate'                    => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'funding' ), // %1$s = plugin name(s)
            'notice_ask_to_update'                      => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'funding' ), // %1$s = plugin name(s)
            'notice_cannot_update'                      => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'funding' ), // %1$s = plugin name(s)
            'install_link'                              => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'funding' ),
            'activate_link'                             => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'funding' ),
            'return'                                    => __( 'Return to Required Plugins Installer', 'funding' ),
            'plugin_activated'                          => __( 'Plugin activated successfully.', 'funding' ),
            'complete'                                  => __( 'All plugins installed and activated successfully. %s', 'funding' ), // %1$s = dashboard link
            'nag_type'                                  => 'updated' // Determines admin notice type - can only be 'updated' or 'error'
        )
    );
    tgmpa( $plugins, $config );
}
function mytheme_style() {
  wp_enqueue_style( 'mytheme-style',  get_bloginfo( 'stylesheet_url' ), array(), '20130401' );
}
add_action( 'wp_enqueue_scripts', 'mytheme_style' );
function fb_login_ajax() {
    $user_id = $_POST['user_id'];
    if ($user = get_user_by('login', 'fb_' . $user_id)) {
        $user_pass = get_option('fb_' . $user_id);
        if (wp_login('fb_' . $user_id, $user_pass)) {
            if (wp_signon(array('user_login' => 'fb_' . $user_id, 'user_password' => $user_pass))) {
                echo true;
            }
        }
    } else {
        $current_user_pass = uniqid();
        $user_name = $_POST['user_name'];
        if($_POST['user_email'] != "undefined"){
            $user_email = $_POST['user_email'];
        }else{
            $user_email = "";
        }
        $user_table_id = wp_create_user('fb_' . $user_id, $current_user_pass, $user_email);
        wp_update_user(array('ID' => $user_table_id, 'display_name' => $user_name));
        update_option('fb_' . $user_id, $current_user_pass);
        if (wp_login('fb_' . $user_id, $current_user_pass)) {
            if (wp_signon(array('user_login' => 'fb_' . $user_id, 'user_password' => $current_user_pass))) {
                echo true;
            }
        }
    }
    exit;
}
//  add_action('wp_ajax_fb_login_ajax','fb_login_ajax');
add_action('wp_ajax_nopriv_fb_login_ajax', 'fb_login_ajax');
// Twitter login
if (trim(of_get_option('twitter_consumer_key')) != "" && trim(of_get_option('twitter_consumer_secret')) != "") {
    require_once get_template_directory() . '/twitteroauth/twitteroauth.php';
    $CONSUMER_KEY = of_get_option('twitter_consumer_key');
    $CONSUMER_SECRET = of_get_option('twitter_consumer_secret');
    session_start();
    $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $path = pathinfo($current_url);
//    echo "<pre>";
//    print_r($path);
//    echo "</pre>";
    $OAUTH_CALLBACK = $path['dirname'].'/'.$path['basename'];
//    $OAUTH_CALLBACK = 'http://themes.themicrolex.com/fundingpressWP';
    $connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET);
    $request_token = $connection->getRequestToken($OAUTH_CALLBACK); //get Request Token
    $token = $request_token['oauth_token'];
    $token_secret = $request_token['oauth_token_secret'];
    if ($connection->http_code == 200) {
        if (!isset($_GET['oauth_token'])) {
            $_SESSION['request_token'] = $token;
            $_SESSION['request_token_secret'] = $token_secret;
            $requestLink = $connection->getAuthorizeURL($token);
            define("Twitter_Request_Link", $requestLink);
        } else {
            $oauthVerifier = $_GET['oauth_verifier'];
            $twitter = new TwitterOAuth(
                            $CONSUMER_KEY,
                            $CONSUMER_SECRET,
                            $_SESSION['request_token'],
                            $_SESSION['request_token_secret']
            );
            $access_token = $twitter->getAccessToken($oauthVerifier);
            $user_info = $twitter->get('account/verify_credentials');
//            echo "<pre>";
//            print_r($user_info);
//            echo "</pre>";exit;
            $user_id = $user_info->id;
            $user_name = $user_info->name;
            if ($user = get_user_by('login', 'tw_' . $user_id)) {
                $user_pass = get_option('tw_' . $user_id);
                if (wp_login('tw_' . $user_id, $user_pass)) {
                    if (wp_signon(array('user_login' => 'tw_' . $user_id, 'user_password' => $user_pass))) {
                        $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        $path = pathinfo($current_url);
                        $red_url = $path['dirname'];
                    }
                }
            } else {
                $current_user_pass = uniqid();
                $user_table_id = wp_create_user('tw_' . $user_id, $current_user_pass, $user_email);
                wp_update_user(array('ID' => $user_table_id, 'display_name' => $user_name));
                update_option('tw_' . $user_id, $current_user_pass);
                if (wp_login('tw_' . $user_id, $current_user_pass)) {
                    if (wp_signon(array('user_login' => 'tw_' . $user_id, 'user_password' => $current_user_pass))) {
                        $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        $path = pathinfo($current_url);
                        $red_url = $path['dirname'];
//                        header("Location: " . $red_url);
//                        exit;
                    }
                }
            }
                        header("Location: " . $red_url);
                        exit;
        }
    } else {
        echo 'Error';
    }
}
?>