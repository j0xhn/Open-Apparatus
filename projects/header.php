<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html <?php language_attributes(); ?>><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title><?php
    /*
     * Print the <title> tag based on what is being viewed.
     */
    global $page, $paged;
    wp_title( '|', true, 'right' );
    // Add the blog name.
    bloginfo( 'name' );
    // Add the blog description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        echo " | $site_description";
    // Add a page number if necessary:
    if ( $paged >= 2 || $page >= 2 )
        echo ' | ' . sprintf( __( 'Page %s', 'funding' ), max( $paged, $page ) );
    ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="<?php echo of_get_option('favicon'); ?>" />
     <?php include_once 'colorpicker.php'; ?>
<?php wp_head(); ?>
<script src="//connect.facebook.net/en_US/all.js"></script>
<?php
if(trim(of_get_option('facebook_api_key')) != ""){
?>
<script>
  FB.init({
    appId  : '<?php echo of_get_option('facebook_api_key');?>',
    status : true, // check login status
    cookie : true, // enable cookies to allow the server to access the session
    xfbml  : true, // parse XFBML
    channelUrl : '<?php echo home_url(); ?>', // channel.html file
    oauth  : true // enable OAuth 2.0
  });
  function fbLogin(){
      FB.login(function(response){
          //console.log(response);
          var fb_user_name = "";
          var fb_user_email = "";
          FB.api('/me', function(name_response) {
              //console.log(name_response);
                  fb_user_name = name_response.name;
                  fb_user_email = name_response.email;
          if(response.authResponse.userID){
              jQuery.post('<?php echo home_url(); ?>/wp-admin/admin-ajax.php',
            {
                action: 'fb_login_ajax',
                user_id: response.authResponse.userID,
                                user_name : fb_user_name,
                                user_email : fb_user_email
            },
            get_responce
        );
          }
              function get_responce(result){
               //   console.log(result);
                  if(result == 1){
                      window.location = document.location.href;
                  }
              }
        });
      },{scope:'email'});
  }
</script>
<?php
}
?>
</head>
<?php if(is_admin() or is_author()) { ?>
<body>
<?php }else{ ?>
<body <?php body_class(); ?>>
<?php } ?>
<header>
    <div class="navbartop-wrapper" >
        <div class="container">
        <div class="search-wrapper">
            <?php include_once 'searchformprojects.php'; ?>
        </div>
        <div class="top-right">
           <?php if ( is_user_logged_in() ) { ?>
                <a href="<?php echo wp_logout_url( home_url()) ?>" class="logout-top "><?php _e("Log out", 'funding'); ?></a>
                <a href="<?php echo home_url(); ?>/wp-admin/post-new.php?post_type=project" class="submit-top"><i class="icon-fire"></i><?php _e("Submit a project", 'funding'); ?></a>
                <a href="<?php echo get_permalink( get_page_by_path( 'my-projects' ) ); ?>" class="account-top "><i class="icon-user" s></i><?php _e("My account", 'funding'); ?></a>
           <?php }else{ ?>
                <a href="#myModalL" role="button" class="login-top" data-toggle="modal"><?php _e("Login", 'funding'); ?></a>
                <a href="#myModalR" role="button" class="register-top" data-toggle="modal"><?php _e("Register", 'funding'); ?></a>
            <?php } ?>
            <ul class="social-media">
                <?php if ( of_get_option('facebook') ) { ?><li><a target="_blank" class="facebook"href="<?php echo of_get_option('facebook_link'); ?>"><?php _e("facebook", 'funding'); ?></a></li><?php } ?>
                <?php if ( of_get_option('twitter') ) { ?><li><a target="_blank" class="twitter" href="<?php echo of_get_option('twitter_link'); ?>"><?php _e("twitter", 'funding'); ?></a></li><?php } ?>
                <?php if ( of_get_option('rss') ) { ?><li><a target="_blank" class="rss" href="<?php echo of_get_option('rss_link'); ?>"><?php _e("rss", 'funding'); ?></a></li><?php } ?>
                <?php if ( of_get_option('googleplus') ) { ?> <li><a target="_blank" class="google" href="<?php echo of_get_option('google_link'); ?>"><?php _e("google", 'funding'); ?></a></li><?php } ?>
                <?php if ( of_get_option('skype') ) { ?><li><a target="_blank" class="skype" href="skype:<?php echo of_get_option('skype_name'); ?>?add"><?php _e("skype", 'funding'); ?></a></li><?php } ?>
            </ul>
<div id="myModalL" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3><?php _e("Login", 'funding'); ?></h3>
  </div>
  <div class="modal-body">
<?php
if ( is_user_logged_in() ) {
    global $current_user;
?>
<div id="LoginWithAjax">
    <?php
        global $current_user;
        global $user_level;
        global $wpmu_version;
        get_currentuserinfo();
    ?>
    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td class="avatar" id="LoginWithAjax_Avatar">
                <?php echo get_avatar( $current_user->ID, $size = '50' );  ?>
            </td>
            <td>
                  <a id="wp-logout" href="<?php echo wp_logout_url( home_url()) ?>"><?php echo strtolower(__( 'Log Out', 'funding' )) ?></a><br />
            </td>
        </tr>
    </table>
</div>
<?php
    }else{
?>
    <div id="LoginWithAjax" class="default"><?php //ID must be here, and if this is a template, class name should be that of template directory ?>
        <span id="LoginWithAjax_Status"></span>
        <?php include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); ?>
        <form name="LoginWithAjax_Form" id="LoginWithAjax_Form" action="<?php echo home_url()?><?php echo (!is_plugin_active('better-wp-security/better-wp-security.php')) ? '/wp-login.php?' : '/?'; ?>callback=?&template=" method="post">
            <table width='100%' cellspacing="0" cellpadding="0">
                <tr id="LoginWithAjax_Username">
                    <td class="username_input">
                        <input type="text" name="log" placeholder="Username" id="lwa_user_login" class="input" value="" />
                    </td>
                </tr>
                <tr id="LoginWithAjax_Password">
                    <td class="password_input">
                        <input type="password" placeholder="Password" name="pwd" id="lwa_user_pass" class="input" value="" />
                    </td>
                </tr>
                <tr><td colspan="2"><?php do_action('login_form'); ?></td></tr>
                <tr id="LoginWithAjax_Submit">
                    <td id="LoginWithAjax_SubmitButton">
                         <input name="rememberme" type="checkbox" id="lwa_rememberme" value="forever" /> <label ><?php _e( 'Remember Me', 'funding' ) ?></label>
                        <a id="LoginWithAjax_Links_Remember"href="<?php echo site_url('wp-login.php?action=lostpassword', 'login') ?>" title="<?php _e('Password Lost and Found', 'funding') ?>"><?php _e('Lost your password?', 'funding') ?></a>
                        <br /><br />
                        <input type="submit"  class="button-green button-small"  name="wp-submit" id="lwa_wp-submit" value="<?php _e('Log In', 'funding'); ?>" tabindex="100" />
                        <?php
                        if(trim(of_get_option('facebook_api_key')) != ""){
                        ?>
                        <div id="fb_login_button">
                            <a  onclick="fbLogin()">
                                <span>
                                    Log in
                                </span>
                            </a>
                        </div>
                        <?php
                        }
                        if(trim(of_get_option('twitter_consumer_key')) != "" && trim(of_get_option('twitter_consumer_secret')) != ""){
                        ?>
                        <a id="twitter_login_link" href="<?php echo Twitter_Request_Link;?>"><?php _e("Twitter login", 'funding'); ?></a>
                        <?php
                        }
                        ?>
                        <input type="hidden" name="redirect_to" value="http://<?php echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ?>" />
                        <input type="hidden" name="testcookie" value="1" />
                        <input type="hidden" name="lwa_profile_link" value="<?php echo $lwa_data['profile_link'] ?>" />
                    </td>
                </tr>
            </table>
        </form>
        <form name="LoginWithAjax_Remember" id="LoginWithAjax_Remember" action="<?php echo home_url()?><?php echo (!is_plugin_active('better-wp-security/better-wp-security.php')) ? '/wp-login.php?' : '/?'; ?>callback=?&template=" method="post">
            <table width='100%' cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <strong><?php echo __("Forgotten Password", 'funding'); ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class="forgot-pass-email">
                        <?php $msg = __("Enter username or email", 'funding'); ?>
                        <input type="text" name="user_login" id="lwa_user_remember" value="<?php echo $msg ?>" onfocus="if(this.value == '<?php echo $msg ?>'){this.value = '';}" onblur="if(this.value == ''){this.value = '<?php echo $msg ?>'}" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" class="button-green button-small"  value="<?php echo __("Get New Password", 'funding'); ?>" />
                          <a href="#" id="LoginWithAjax_Links_Remember_Cancel"><?php _e("Cancel", 'funding'); ?></a>
                        <input type="hidden" name="login-with-ajax" value="remember" />
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php } ?>
  </div>
</div>
<div id="myModalR" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3><?php _e("Register" , 'funding'); ?></h3>
  </div>
  <div class="modal-body">
    <div id="LoginWithAjax_Footer">
        <div id="LoginWithAjax_Register"  class="default">
                <span id="LoginWithAjax_Register_Status"></span>
            <h4 class="message register"><?php _e('Register For This Site', 'funding') ?></h4>
            <form name="LoginWithAjax_Register" id="LoginWithAjax_Register_Form" action="<?php echo home_url(); ?>/wp-login.php?action=register&callback=?&template=" method="post">
                <p>
                    <label><input type="text" placeholder="Username" name="user_login" id="user_login" class="input" size="20" tabindex="10" /></label>
                </p>
                <p>
                    <label><input type="text" placeholder="E-mail" name="user_email" id="user_email" class="input" size="25" tabindex="20" /></label>
                </p>
                <?php do_action('register_form'); ?>
                <p id="reg_passmail"><?php _e('A password will be e-mailed to you.', 'funding') ?></p>
                <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-green button-small" value="<?php esc_attr_e('Register', 'funding'); ?>" tabindex="100" /></p>
                <input type="hidden" name="lwa" value="1" />
            </form>
        </div>
    </div>
  </div>
</div>
        </div>
 </div><!-- top right -->
</div><!-- Container -->
    <!-- NAVBAR
    ================================================== -->
 <div class="navbar-wrapper">
      <!-- Wrap the .navbar in .container to center it within the absolutely positioned parent. -->
      <div class="container">
        <div class="logo-wrapper">
             <?php if (of_get_option('logo')!=""){ ?>
                 <a href="<?php  echo home_url(); ?>"> <img src="<?php echo of_get_option('logo'); ?>" alt="logo"  /> </a>
             <?php } ?>
        </div>
        <div class="navbar navbar-inverse">
          <div class="navbar-inner">
            <!-- Responsive Navbar Part 1: Button for triggering responsive navbar (not covered in tutorial). Include responsive CSS to utilize. -->
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            </a>
            <!-- Responsive Navbar Part 2: Place all navbar contents you want collapsed withing .navbar-collapse.collapse. -->
            <div class="nav-collapse collapse">
                <?php wp_nav_menu( array( 'theme_location'  => 'header-menu', 'depth' => 0,'sort_column' => 'menu_order', 'items_wrap' => '<ul  class="nav">%3$s</ul>', 'container_class' => 'menu-header' ) ); ?>
            </div><!--/.nav-collapse -->
          </div><!-- /.navbar-inner -->
        </div><!-- /.navbar -->
      </div> <!-- /.container -->
    </div><!-- /.navbar-wrapper -->
</header><!-- /.header -->
    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->