<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */
function optionsframework_option_name() {
    // This gets the theme name from the stylesheet (lowercase and without spaces)
    $themename = wp_get_theme();
    $themename = $themename['Name'];
    $themename = preg_replace("/\W/", "", strtolower($themename) );
    $optionsframework_settings = get_option('optionsframework');
    $optionsframework_settings['id'] = $themename;
    update_option('optionsframework', $optionsframework_settings);
}
function optionsframework_options() {
    // Slider Options
    $slider_choice_array = array("none" => "No Showcase", "accordion" => "Accordion", "wpheader" => "WordPress Header", "image" => "Your Image", "easing" => "Easing Slider", "custom" => "Custom Slider");
    // Pull all the categories into an array
    $options_categories = array();
    $options_categories_obj = get_categories();
    foreach ($options_categories_obj as $category) {
        $options_categories[$category->cat_ID] = $category->cat_name;
    }
    // Pull all the pages into an array
    $options_pages = array();
    $options_pages_obj = get_pages('sort_column=post_parent,menu_order');
    $options_pages[''] = 'Select a page:';
    foreach ($options_pages_obj as $page) {
        $options_pages[$page->ID] = $page->post_title;
    }
    // If using image radio buttons, define a directory path
    $radioimagepath =  get_stylesheet_directory_uri() . '/themeOptions/images/';
    // define sample image directory path
    $imagepath =  get_template_directory_uri() . '/images/demo/';
    $options = array();
    $options[] = array( "name" => __("General  Settings",'funding'),
                        "type" => "heading");
    $options[] = array( "name" => __("Upload Your Logo",'funding'),
                        "desc" => __("Upload your logo. I recommend keeping it within reasonable size. Max 150px and minimum height of 90px but not more than 120px.",'funding'),
                        "id" => "logo",
                        "std" => get_template_directory_uri()."/img/logo.jpg",
                        "type" => "upload");
            $options[] = array( "name" => __("Upload Your Favicon",'funding'),
                        "desc" => __("Upload your Favicon. I recommend keeping it within reasonable size. ",'funding'),
                        "id" => "favicon",
                        "std" => get_template_directory_uri()."/img/favicon.png",
                        "type" => "upload");
    $options[] = array( "name" => __("Blog settings",'funding'),
                        "type" => "info");
    $options[] = array( "name" => __("Blog category",'funding'),
                        "desc" => __("Insert ID of blog categories, comma separated.",'funding'),
                        "id" => "blogcat",
                        "std" => "",
                        "type" => "text");
    $options[] = array( "name" => __("Blog number of posts",'funding'),
                        "desc" => __("Insert number of posts that you want to show on blog, category and author pages.",'funding'),
                        "id" => "blognum",
                        "std" => "",
                        "type" => "text");
    $options[] = array( "name" => __("All projects page settings",'funding'),
                        "type" => "info");
    $options[] = array( "name" => __("Number of projects per page",'funding'),
                        "desc" => __("Insert number of projects that you want to show on all projects page.",'funding'),
                        "id" => "projectnum",
                        "std" => "",
                        "type" => "text");
 $options[] = array( "name" => __("Social Log in",'funding'),
                        "type" => "info");
        $options[] = array( "name" => __("Twitter Consumer key",'funding'),
                        "desc" => __("Insert Twitter Consumer key for enable login via Twitter.",'funding'),
                        "id" => "twitter_consumer_key",
                        "std" => "QNPcwwd6iz7ijPYPw7UkQ",
                        "type" => "text");
        $options[] = array( "name" => __("Twitter Consumer secret",'funding'),
                        "desc" => __("Insert Twitter Consumer secret for enable login via Twitter.",'funding'),
                        "id" => "twitter_consumer_secret",
                        "std" => "qjS1HKQ7ZmWnLryOVCnyP0Bwd9fgximYkfsZhCY0",
                        "type" => "text");
                $options[] = array( "name" => __("Facebook api key",'funding'),
                        "desc" => __("Insert Facebook api key for enable login via Facebook.",'funding'),
                        "id" => "facebook_api_key",
                        "std" => "537546412995779",
                        "type" => "text");
// Colour Settings
    $options[] = array( "name" => __("Colours",'funding'),
                        "type" => "heading");
    $options[] = array( "name" => __("Primary Color",'funding'),
    "desc" => __("The primary color for the site.",'funding'),
    "id" => "primary_color",
    "std" => "#76cc1e",
    "type" => "color" );
$options[] = array( "name" => __("Button colors",'funding'),
                        "type" => "info");
    //regular
    $options[] = array(
    "name" => __("Button color",'funding'),
    "desc" => __("Button color <a class='button-medium'  >Example</a>.",'funding'),
    "id" => "button_green",
    "std" => "#76cc1e",
    "type" => "color");
     $options[] = array(
    "name" => __("Button hover color",'funding'),
    "desc" => __("Button hover color",'funding'),
    "id" => "button_hover",
    "std" => "#689c06",
    "type" => "color");
    //border
    $options[] = array(
    "name" => __("Button border color",'funding'),
    "desc" => __("Color for button border <a class='button-medium'  >Example</a>.",'funding'),
    "id" => "button_border",
    "std" => "#689c06",
    "type" => "color");
// Footer section start
    $options[] = array( "name" => __("Footer",'funding'), "type" => "heading");
                $options[] = array( "name" => __("Copyright",'funding'),
                        "desc" => __("Enter your copyright text.",'funding'),
                        "id" => "copyright",
                        "std" => __("Made by Skywarrior Themes.",'funding'),
                        "type" => "textarea");
                $options[] = array( "name" => __("Copyright year",'funding'),
                        "desc" => __("Enter your copyright year.",'funding'),
                        "id" => "year",
                        "std" => "2012",
                        "type" => "text");
                $options[] = array( "name" => __("Privacy link",'funding'),
                        "desc" => __("Enter your privacy link. Please include http://",'funding'),
                        "id" => "privacy",
                        "std" => "http://www.skywarriorthemes.com/",
                        "type" => "text");
                $options[] = array( "name" => __("Terms link",'funding'),
                        "desc" => __("Enter your terms link. Please include http://",'funding'),
                        "id" => "terms",
                        "std" => "http://www.skywarriorthemes.com/",
                        "type" => "text");
// contact page code
$options[] = array( "name" => __("Contact",'funding'),
                        "type" => "heading");
    $options[] = array( "name" => __("Enter admin email address ",'funding'),
                        "desc" => __(" Enter your email address.",'funding'),
                        "id" => "contact_email",
                        "std" => "admin@gmail.com",
                        "type" => "text");
    $options[] = array( "name" => __("Enter sidebar title ",'funding'),
                        "desc" => __(" Enter title for sidebar for contact page.",'funding'),
                        "id" => "sidebar_title",
                        "std" => "",
                        "type" => "text");
    $options[] = array( "name" => __("Enter sidebar content ",'funding'),
                        "desc" => __(" Enter content for sidebar for contact page.",'funding'),
                        "id" => "sidebar_content",
                        "std" => "",
                        "type" => "textarea");
// end contact page code
// Social Media
    $options[] = array( "name" => __("Social Media",'funding'),
                        "type" => "heading");
// Social Network setup
    /*$options[] = array( "name" => "Facebook App ID",
                        "desc" => "Add your Facebook App ID here",
                        "id" => "facebook_app",
                        "std" => "1234567890",
                        "type" => "text");
*/
    $options[] = array( "name" => __("Enable Twitter",'funding'),
                        "desc" => __("Show or hide the Twitter icon that shows on the header section.",'funding'),
                        "id" => "twitter",
                        "std" => "0",
                        "type" => "jqueryselect");
    $options[] = array( "name" => __("Twitter Link",'funding'),
                        "desc" => __("Paste your twitter link here.",'funding'),
                        "id" => "twitter_link",
                        "std" => "#",
                        "type" => "text");
    $options[] = array( "name" => __("Enable Facebook",'funding'),
                        "desc" => __("Show or hide the Facebook icon that shows on the header section.",'funding'),
                        "id" => "facebook",
                        "std" => "0",
                        "type" => "jqueryselect");
    $options[] = array( "name" => __("Facebook Link",'funding'),
                        "desc" => __("Paste your facebook link here.",'funding'),
                        "id" => "facebook_link",
                        "std" => "#",
                        "type" => "text");
    $options[] = array( "name" => __("Enable Google+",'funding'),
                        "desc" => __("Show or hide the Google+ icon that shows on the header section.",'funding'),
                        "id" => "googleplus",
                        "std" => "0",
                        "type" => "jqueryselect");
    $options[] = array( "name" => __("Google+ Link",'funding'),
                        "desc" => __("Paste your google+ link here.",'funding'),
                        "id" => "google_link",
                        "std" => "#",
                        "type" => "text");
    $options[] = array( "name" => __("Enable skype",'funding'),
                        "desc" => __("Show or hide the skype icon that shows on the header section.",'funding'),
                        "id" => "skype",
                        "std" => "0",
                        "type" => "jqueryselect");
    $options[] = array( "name" => __("Skype name",'funding'),
                        "desc" => __("Paste your skype name here.",'funding'),
                        "id" => "skype_name",
                        "std" => "#",
                        "type" => "text");
    $options[] = array( "name" => __("Enable RSS",'funding'),
                        "desc" => __("Show or hide the RSS icon that shows on the header section.",'funding'),
                        "id" => "rss",
                        "std" => "0",
                        "type" => "jqueryselect");
    $options[] = array( "name" => __("RSS Link",'funding'),
                        "desc" => __("Paste your RSS link here.",'funding'),
                        "id" => "rss_link",
                        "std" => "#",
                        "type" => "text");
// Funding section
    $options[] = array( "name" => __("Funding",'funding'),
                        "type" => "heading");
                        $options[] = array( "name" => __("Add Text",'funding'),
                        "desc" => __("Enter the important text on the commit to funding page.",'funding'),
                        "id" => "important_text",
                        "type" => "textarea");
$options[] = array( "name" => __("Enable collect fundings",'funding'),
                        "desc" => __("Enable users to collect fundings before project ends.",'funding'),
                        "id" => "colfun",
                        "std" => "0",
                        "type" => "jqueryselect");
$options[] = array( "name" => __("Admin commission",'funding'),
                        "desc" => __("Enter admin commission(Amount is in percents. Enter amount without % sign ) for project funding ",'funding'),
                        "id" => "commision",
                        "std" => "1",
                        "type" => "text");
    return $options;
}
?>