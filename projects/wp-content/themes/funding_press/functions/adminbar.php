<?php $current_user= wp_get_current_user();
$level = $current_user->user_level;
if($level == 10){}else{
function admin_style(){
       wp_enqueue_style('admin.css', get_template_directory_uri().'/css/admin.css');
}
add_action('admin_head', 'admin_style');


//disable admin bar
if (!function_exists('disableAdminBar')) {

    function disableAdminBar(){

    remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 ); // for the admin page
    remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 ); // for the front end

    function remove_admin_bar_style_backend() {  // css override for the admin page
      echo '<style>body.admin-bar #wpcontent, body.admin-bar #adminmenu { padding-top: 0px !important; }</style>';
    }

    add_filter('admin_head','remove_admin_bar_style_backend');

    function remove_admin_bar_style_frontend() { // css override for the frontend
      echo '<style type="text/css" media="screen">
      html { margin-top: 0px !important; }
      * html body { margin-top: 0px !important; }
      </style>';
    }
    add_filter('wp_head','remove_admin_bar_style_frontend', 99);
  }
}
//add_filter('admin_head','remove_admin_bar_style_backend'); // Original version
add_action('init','disableAdminBar'); // New version
add_action( 'admin_menu' , 'admin_menu_wp' );
function admin_menu_wp() {
    $current_user= wp_get_current_user();
    $level = $current_user->user_level;
    if($level == 1){
if( is_admin_bar_showing()){  remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 ); }
require_once(ABSPATH . 'wp-admin/includes/admin.php');
function front_header(){
get_header();
}
// Add hook for admin <head></head>
add_action('admin_head', 'front_header');
// Add hook for front-end <head></head>
add_action('wp_head', 'front_header');

add_filter('update_footer', 'footer_admin'); //change admin footer text
function footer_admin () {
        get_footer();
}

}

}
    function remove_style_admin()
        {
            echo '<style type="text/css">html.wp-toolbar,html.wp-toolbar #wpcontent,html.wp-toolbar #adminmenu,html.wp-toolbar #wpadminbar,body.admin-bar,body.admin-bar #wpcontent,body.admin-bar #adminmenu,body.admin-bar #wpadminbar{padding-top:0px !important}</style>';
        }
    add_action('admin_print_styles', 'remove_style_admin', 21);


    function removetool($wp_toolbar)
        {
            $wp_toolbar->remove_node('wp-logo');
            $wp_toolbar->remove_node('wp-logo-default');
            $wp_toolbar->remove_node('about');
            $wp_toolbar->remove_node('wp-logo-external');
            $wp_toolbar->remove_node('wporg');
            $wp_toolbar->remove_node('documentation');
            $wp_toolbar->remove_node('support-forums');
            $wp_toolbar->remove_node('feedback');
            $wp_toolbar->remove_node('site-name');
            $wp_toolbar->remove_node('site-name-default');
            $wp_toolbar->remove_node('view-site');
            $wp_toolbar->remove_node('comments');
            $wp_toolbar->remove_node('updates');
            $wp_toolbar->remove_node('view');
            $wp_toolbar->remove_node('new-content');
            $wp_toolbar->remove_node('new-content-default');
            $wp_toolbar->remove_node('new-post');
            $wp_toolbar->remove_node('new-media');
            $wp_toolbar->remove_node('new-link');
            $wp_toolbar->remove_node('new-page');
            $wp_toolbar->remove_node('new-user');
            $wp_toolbar->remove_node('top-secondary');
            $wp_toolbar->remove_node('my-account');
            $wp_toolbar->remove_node('user-actions');
            $wp_toolbar->remove_node('user-info');
            $wp_toolbar->remove_node('edit-profile');
            $wp_toolbar->remove_node('logout');
            $wp_toolbar->remove_node('search');
            $wp_toolbar->remove_node('my-sites');
            $wp_toolbar->remove_node('my-sites-list');
            $wp_toolbar->remove_node('blog-1');
            $wp_toolbar->remove_node('blog-1-default');
            $wp_toolbar->remove_node('blog-1-d');
            $wp_toolbar->remove_node('blog-1-n');
            $wp_toolbar->remove_node('blog-1-c');
            $wp_toolbar->remove_node('blog-1-v');
            $wp_toolbar->remove_node('blog-2');
            $wp_toolbar->remove_node('blog-2-default');
            $wp_toolbar->remove_node('blog-2-d');
            $wp_toolbar->remove_node('blog-2-n');
            $wp_toolbar->remove_node('blog-2-c');
            $wp_toolbar->remove_node('blog-2-v');
            $wp_toolbar->remove_node('blog-3');
            $wp_toolbar->remove_node('blog-3-default');
            $wp_toolbar->remove_node('blog-3-d');
            $wp_toolbar->remove_node('blog-3-n');
            $wp_toolbar->remove_node('blog-3-c');
            $wp_toolbar->remove_node('blog-3-v');
            $wp_toolbar->remove_node('blog-4');
            $wp_toolbar->remove_node('blog-4-default');
            $wp_toolbar->remove_node('blog-4-d');
            $wp_toolbar->remove_node('blog-4-n');
            $wp_toolbar->remove_node('blog-4-c');
            $wp_toolbar->remove_node('blog-4-v');
            $wp_toolbar->remove_node('blog-5');
            $wp_toolbar->remove_node('blog-5-default');
            $wp_toolbar->remove_node('blog-5-d');
            $wp_toolbar->remove_node('blog-5-n');
            $wp_toolbar->remove_node('blog-5-c');
            $wp_toolbar->remove_node('blog-5-v');
            $wp_toolbar->remove_node('blog-6');
            $wp_toolbar->remove_node('blog-6-default');
            $wp_toolbar->remove_node('blog-6-d');
            $wp_toolbar->remove_node('blog-6-n');
            $wp_toolbar->remove_node('blog-6-c');
            $wp_toolbar->remove_node('blog-6-v');
            $wp_toolbar->remove_node('blog-7');
            $wp_toolbar->remove_node('blog-7-default');
            $wp_toolbar->remove_node('blog-7-d');
            $wp_toolbar->remove_node('blog-7-n');
            $wp_toolbar->remove_node('blog-7-c');
            $wp_toolbar->remove_node('blog-7-v');
            $wp_toolbar->remove_node('blog-8');
            $wp_toolbar->remove_node('blog-8-default');
            $wp_toolbar->remove_node('blog-8-d');
            $wp_toolbar->remove_node('blog-8-n');
            $wp_toolbar->remove_node('blog-8-c');
            $wp_toolbar->remove_node('blog-8-v');
            $wp_toolbar->remove_node('blog-9');
            $wp_toolbar->remove_node('blog-9-default');
            $wp_toolbar->remove_node('blog-9-d');
            $wp_toolbar->remove_node('blog-9-n');
            $wp_toolbar->remove_node('blog-9-c');
            $wp_toolbar->remove_node('blog-9-v');
            $wp_toolbar->remove_node('wpseo-menu');
            $wp_toolbar->remove_node('wpseo-menu-default');
            $wp_toolbar->remove_node('wpseo-kwresearch');
            $wp_toolbar->remove_node('wpseo-kwresearch-default');
            $wp_toolbar->remove_node('wpseo-adwordsexternal');
            $wp_toolbar->remove_node('wpseo-googleinsights');
            $wp_toolbar->remove_node('wpseo-wordtracker');
            $wp_toolbar->remove_node('wpseo-settings');
            $wp_toolbar->remove_node('wpseo-settings-default');
            $wp_toolbar->remove_node('wpseo-titles');
            $wp_toolbar->remove_node('wpseo-social');
            $wp_toolbar->remove_node('wpseo-xml');
            $wp_toolbar->remove_node('wpseo-permalinks');
            $wp_toolbar->remove_node('wpseo-internal-links');
            $wp_toolbar->remove_node('wpseo-rss');
            $wp_toolbar->remove_node('ngg-menu');
            $wp_toolbar->remove_node('ngg-menu-default');
            $wp_toolbar->remove_node('ngg-menu-overview');
            $wp_toolbar->remove_node('ngg-menu-add-gallery');
            $wp_toolbar->remove_node('ngg-menu-manage-gallery');
            $wp_toolbar->remove_node('ngg-menu-manage-album');
            $wp_toolbar->remove_node('ngg-menu-tags');
            $wp_toolbar->remove_node('ngg-menu-options');
            $wp_toolbar->remove_node('ngg-menu-style');
            $wp_toolbar->remove_node('ngg-menu-about');
            $wp_toolbar->remove_node('cloudflare');
            $wp_toolbar->remove_node('cloudflare-default');
            $wp_toolbar->remove_node('cloudflare-my-websites');
            $wp_toolbar->remove_node('cloudflare-analytics');
            $wp_toolbar->remove_node('cloudflare-account');
            $wp_toolbar->remove_node('w3tc');
            $wp_toolbar->remove_node('w3tc-default');
            $wp_toolbar->remove_node('w3tc-empty-caches');
            $wp_toolbar->remove_node('w3tc-faq');
            $wp_toolbar->remove_node('w3tc-support');
        }
    add_action('admin_bar_menu', 'removetool', 999);
    $wp_scripts = new WP_Scripts();
    wp_deregister_script('admin-bar');
    $wp_styles = new WP_Styles();
    wp_deregister_style('admin-bar');
    add_filter( 'contextual_help', 'mytheme_remove_help_tabs', 999, 3 );
    function mytheme_remove_help_tabs($old_help, $screen_id, $screen){
    $screen->remove_help_tabs();
    return $old_help;
}
add_filter('screen_options_show_screen', '__return_false'); //UKLONIIIIIIIIIIIIIIIIIIIIIIIIIIIIII ZA ADMINAAA
}?>