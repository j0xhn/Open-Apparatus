<?php
// Include the origin controller
require_once (dirname(__FILE__).'/lib/Controller.php');
require_once (dirname(__FILE__).'/paypal.php');
require_once (dirname(__FILE__).'/globals.php');
require_once (dirname(__FILE__).'/admin.php');
function siteorigin_funding_activate(){
    F_Controller::action_init();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'siteorigin_funding_activate');
/**
 * Front end controller
 */
class F_Controller extends Origin_Controller{
    public function __construct(){
        return parent::__construct(false, 'f');
    }
    static function single(){
        return parent::single(__CLASS__);
    }
    ///////////////////////////////////////////////////////////////////
    // Action Functions
    ///////////////////////////////////////////////////////////////////
    function action_init(){
        global $f_paypal;
        if(empty($_REQUEST['mode']) or empty($_REQUEST['app_id']) or empty($_REQUEST['api_username']) or empty($_REQUEST['api_password']) or empty($_REQUEST['api_signature']) or empty($_REQUEST['email'])){
            $f_paypal = get_option('funding_paypal');
            $mode = $f_paypal["mode"];
            $appid = $f_paypal["app_id"];
            $appusername = $f_paypal["api_username"];
            $apppassword = $f_paypal["api_password"];
            $appsingature = $f_paypal["api_signature"];
            $email = $f_paypal["email"];
        }else{
            $mode = $_REQUEST['mode'];
            $appid = $_REQUEST['app_id'];
            $appusername = $_REQUEST['api_username'];
            $apppassword = $_REQUEST['api_password'];
            $appsingature = $_REQUEST['api_signature'];
            $email = $_REQUEST['email'];
        }
        if(empty($f_paypal)){
            $f_paypal = array(
                'mode' => $mode,
                'app_id' => $appid,
                'api_username' => $appusername,
                'api_password' => $apppassword,
                'api_signature' => $appsingature,
                'email' => $email,
            );
            update_option('funding_paypal', $f_paypal);
        }
        $f_paypal = get_option('funding_paypal');
        define(
            'X_PAYPAL_API_BASE_ENDPOINT',
            $f_paypal['mode'] == 'sandbox' ? 'https://svcs.sandbox.paypal.com/' : 'https://svcs.paypal.com/'
        );
        // This is dirty, but the Paypal API likes constants
        define('SOCF_API_USERNAME', $f_paypal['api_username']);
        define('SOCF_API_PASSWORD', $f_paypal['api_password']);
        define('SOCF_API_SIGNATURE', $f_paypal['api_signature']);
        define('SOCF_APPLICATION_ID', $f_paypal['app_id']);
        // Some more PayPal settings
        define('X_PAYPAL_ADAPTIVE_SDK_VERSION','PHP_SOAP_SDK_V1.4_MODIFIED');
        define('X_PAYPAL_REQUEST_DATA_FORMAT','SOAP11');
        define('X_PAYPAL_RESPONSE_DATA_FORMAT','SOAP11');
        // Create project custom post type
        register_post_type('project',array(
            'label' => __('Projects', 'funding'),
            'taxonomies' => array('category', 'fundit_project'),
            'labels' => array(
                'name' => __('Projects', 'funding'),
                'singular_name' => __('Project', 'funding'),
                'add_new' => __('Create Project', 'funding'),
                'edit_item' => __('Edit Project', 'funding'),
                'add_new_item' => __('Add New Project', 'funding'),
                'edit_item' => __('Edit Project', 'funding'),
                'new_item' => __('New Project', 'funding'),
                'view_item' => __('View Project', 'funding'),
                'search_items' => __('Search Projects', 'funding'),
                'not_found' => __('No Projects Found', 'funding'),
            ),
            'description' => __('A fundable project.', 'funding'),
            'public' => true,
            '_builtin' =>  false,
            'supports' => array(
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'comments',
                'revisions',
            ),
            'rewrite' => true,
            'query_var' => 'project',
            'menu_icon' => get_template_directory_uri().'/funding/admin/images/project.png',
        ));
        register_taxonomy_for_object_type('tag', 'project');
        // Create reward custom post type
        register_post_type('reward',array(
            'label' => __('Reward', 'funding'),
            'description' => __('A reward for funding a project.', 'funding'),
            'public' => false,
        ));
        // Create funder custom post type
        register_post_type('funder',array(
            'label' => __('Funder', 'funding'),
            'description' => __('A reward for funding a project.', 'funding'),
            'public' => false,
        ));
    }
    /**
     * Render the project page.
     */
    function action_template_redirect(){
        global $post;
        if(is_single() && $post->post_type == 'project'){
            $step = isset($_GET['step']) ? intval($_GET['step']) : 0;
            $project_settings = (array) get_post_meta($post->ID, 'settings', true);
            $project_expired = strtotime($project_settings['date']) < time();
            global $f_currency_signs;
            $project_currency_sign = $f_currency_signs[$project_settings['currency']];
            $rewards = get_children(array(
                'post_parent' => $post->ID,
                'post_type' => 'reward',
                'order' => 'ASC',
                'orderby' => 'meta_value_num',
                'meta_key' => 'funding_amount',
            ));
            if(!empty($rewards)){
                $keys = array_keys($rewards);
                $lowest_reward = $keys[0];
                $funding_minimum = get_post_meta($lowest_reward, 'funding_amount', true);
            }
            // Get all funders
            $funders = array();
            $funded_amount = 0;
            $chosen_reward = null;
            foreach($rewards as $reward){
                $these_funders = get_children(array(
                    'post_parent' => $reward->ID,
                    'post_type' => 'funder',
                    'post_status' => 'publish'
                ));
                foreach($these_funders as $this_funder){
                    $funding_amount = get_post_meta($this_funder->ID, 'funding_amount', true);
                    $funders[] = $this_funder;
                    $funded_amount += $funding_amount;
                }
            }
            // The chosen reward
            $reward = null;
            if(isset($_REQUEST['chosen_reward'])){
                $reward = get_post(intval($_REQUEST['chosen_reward']));
                $reward_funding_amount = get_post_meta($reward->ID, 'funding_amount', true);
                $reward_available = get_post_meta($reward->ID, 'available', true);
            }
            if($project_expired && $step > 0) {
                header('Location: '.get_permalink($post->ID), true, 301);
                exit();
            }
            if($step == 2){
                $name = $_REQUEST['name'];
                $mail = $_REQUEST['email'];
                $funders = get_posts(array(
                    'numberposts'     => -1,
                    'post_type' => 'funder',
                    'post_parent' => $reward->ID,
                    'post_status' => 'publish'
                ));
                $valid = false;
                $step = 1;
                if(empty($name)){
                    $message = __('Please insert your name.', 'funding');
                }
                elseif(empty($mail)){
                    $message = __('Please insert your e-mail.', 'funding');
                }
                elseif(empty($reward)){
                    $message = __('Please choose a valid reward.', 'funding');
                }
                elseif(empty($_REQUEST['amount'])){
                    $message = __('Please choose an amount.', 'funding');
                }
                elseif(empty($reward)){
                    $message = __('Please choose a valid reward.', 'funding');
                }
                elseif(floatval($_REQUEST['amount']) < $reward_funding_amount){
                    $message = __('You need to fund more for this reward.', 'funding');
                    $_REQUEST['amount'] = $reward_funding_amount;
                }
                elseif(!empty($reward_available) && count($funders) >= $reward_available){
                    $message = __('The reward you chose is no longer available.', 'funding');
                }
                else{
                    $valid = true;
                    $step = 2;
                    // Create funder post
                    $funding_id = wp_insert_post(array(
                        'post_parent' => $reward->ID,
                        'post_type' => 'funder',
                        'post_status' => 'draft',
                        'post_content' => $_REQUEST['message'],
                    ));
                    add_post_meta($funding_id, 'funder', array(
                        'name' => $_REQUEST['name'],
                        'email' => $_REQUEST['email'],
                        'website' => $_REQUEST['website'],
                    ), true);
                    add_post_meta($funding_id, 'funding_amount', floatval($_REQUEST['amount']), true);
                    // Redirect to PayPal
                    $paypal = new F_PayPal();
                    $funding = get_post($funding_id);
                    // Redirect
                    $url = $paypal->get_auth_url($post, $reward, $funding); ?>

<script type="text/javascript">
window.location.href='<?php echo $url; ?>';
</script>
<?php
                    exit;
                }
            }
            $templates = array(
                0 => 'f-project.php',
                1 => 'f-fund-project.php',
                2 => 'f-user-details.php',
            );
            $template = $templates[$step];
            $file = locate_template($template);
            if(empty($file)) $file = dirname(__FILE__).'/tpl/'.$template;
            // Include the CSS and Javascript
            if(file_exists(STYLESHEETPATH.'/f/f.css')) wp_enqueue_style('funding', get_stylesheet_directory_uri().'/f/f.css');
            elseif(file_exists(TEMPLATEPATH.'/f/f.css')) wp_enqueue_style('funding', get_template_directory_uri().'/f/f.css');
            else wp_enqueue_style('funding',get_template_directory_uri().'/funding/tpl/f.css');
            if(file_exists(STYLESHEETPATH.'/f/f.js')) wp_enqueue_script('funding', get_stylesheet_directory_uri().'/f/f.js', array('jquery'));
            elseif(file_exists(TEMPLATEPATH.'/f/f.js')) wp_enqueue_script('funding', get_template_directory_uri().'/f/f.js', array('jquery'));
            else wp_enqueue_script('funding', get_template_directory_uri().'/funding/tpl/f.js', array('jquery'));
            if($template == ""){include(dirname(__FILE__) .'/404.php');}else{
            include($file);}
            do_action('wp_shutdown');
            exit();
        }
    }
    /**
     * Handle IPN from PayPal
     */
    function method_paypal_ipn(){
        $this->method_funded();
    }
    /**
     * Handle a user returning from PayPal
     */
    function method_funded($funder_id = null){
        if(empty($funder_id)) $funder_id = intval($_REQUEST['funder_id']);
        $paypal = new F_PayPal();
        $funder = get_post($funder_id);
        // Check authentication and update the funder status
        $auth = $paypal->check_auth($funder);
        $reward = get_post($funder->post_parent);
        $project = get_post($reward->post_parent);
        $project_settings = (array) get_post_meta($project->ID, 'settings', true);
        $notified = get_post_meta($funder->ID, 'notified', true);
        global $f_currency_signs;
        $project_currency_sign = $f_currency_signs[$project_settings['currency']];
        if($auth && empty($notified)){
            // Email the  and the author
            $author = get_userdata($project->post_author);
            $rewards = get_children(array(
                'post_parent' => $project->ID,
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
            $site = site_url();
            $funder_details = get_post_meta($funder->ID, 'funder', true);
            $funding_amount = get_post_meta($funder->ID, 'funding_amount', true);
            $preapproval_key = get_post_meta($funder->ID, 'preapproval_key',true);
            // Send an email to the post author
            $to_author = file_get_contents(dirname(__FILE__).'/emails/funded_to_author.txt');
            $to_author = wordwrap(sprintf(
                $to_author,
                $author->user_nicename,
                ucfirst($funder_details['name']),
                $project->post_title,
                $project_currency_sign.$funded_amount,
                round($funded_amount/$project_settings['target']*100),
                $project_currency_sign.$project_settings['target'],
                self::timesince(time(), strtotime($project_settings['date']), 2, ' and '),
                $funder->ID,
                $funder_details['name'],
                $funder_details['email'],
                $project_currency_sign.$funding_amount,
                $preapproval_key,
                $reward->post_title,
                $funder->post_content
            ), 75);
            @wp_mail(
                $author->user_email,
                sprintf(__('New Funder For %s', 'funding'), $project->post_title),
                $to_author,
                'From: "'.$site->site_name.'" <funding@'.$site->domain.'>'."\r\n"
            );
            // Send an email to the funder
            $funder_paypal_email = get_post_meta($funder->ID, 'paypal_email', true);
            $to_funder = file_get_contents(dirname(__FILE__).'/emails/funded_to_funder.txt');
            $to_funder = wordwrap(sprintf(
                $to_funder,
                $funder_details['name'],
                $project->post_title,
                round($funded_amount/$project_settings['target']*100),
                $project_currency_sign.$project_settings['target'],
                self::timesince(time(), strtotime($project_settings['date']), 2, ' and '),
                $funder->ID,
                $funder_details['name'],
                $funder_details['email'],
                $funding_amount,
                $preapproval_key,
                $reward->post_title,
                $funder->post_content,
                get_permalink($project->ID),
                get_bloginfo('name'),
                site_url()
            ),75);
            @wp_mail(
                $funder_paypal_email,
                sprintf(__('Thanks For Funding %s', 'funding'), $project->post_title),
                $to_funder,
                'From: "'.$site->site_name.'" <funding@'.$site->domain.'>'."\r\n"
            );
            update_post_meta($funder->ID, 'notified', true);
        }
        $url = add_query_arg('thanks', 1, get_post_permalink($project->ID));
        header("Location: ".$url, true, 303);
    }
    ///////////////////////////////////////////////////////////////////
    // Support functions
    ///////////////////////////////////////////////////////////////////
    /**
    * Returns a string representation of the time between $time and $time2
    *
    * @param int $time A unix timestamp of the start time.
    * @param int $time2 A unix timestamp of the end time.
    * @param int $precision How many parts to include
    */
    static function timesince($time, $time2 = null, $precision = 2, $separator = ' '){
        if(empty($time2)) $time2 = time();
        $seconds_in = array(
        /*  'week' => 604800,*/
            '' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1,
        );
        $time_diff = $time2 - $time;
        $diff = array();
        foreach($seconds_in as $key => $seconds){
            $diff[$key] = floor($time_diff/$seconds);
            $time_diff -= $diff[$key]*$seconds;
        }
        $return = array();
        foreach($diff as $key => $count){
            if($count > 0){
                $precision--;
                $return[] = $count.' '.$key.($count == 1 ? '' : '');
            }
            if($precision == 0) break;
        }
        return trim(implode($separator,$return));
    }
    static function get_funders($project_id){
        $rewards = get_children(array(
            'post_parent' => $project_id,
            'post_type' => 'reward',
            'order' => 'ASC',
            'orderby' => 'meta_value_num',
            'meta_key' => 'funding_amount',
        ));
        $funders = array();
        foreach($rewards as $this_reward){
            $these_funders = get_children(array(
                'post_parent' => $this_reward->ID,
                'post_type' => 'funder',
                'post_status' => 'publish'
            ));
            $funders = array_merge($funders, (array) $these_funders);
        }
        return $funders;
    }
}
F_Controller::single();