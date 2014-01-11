<?php
/*
 * Template name: My projects
 */
?>
<?php get_header(); ?>
<!-- Page content
    ================================================== -->
<!-- Wrap the rest of the page in another container to center all the content. -->
<div class="row page-title">
  <div class="container">
    <h1><?php echo get_the_title(); ?></h1>
    <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="row profile">
  <div class="container">
    <div class="profile-info row">
        <div class="span3"><?php echo get_avatar( get_the_author_meta('ID'), 250 ); ?><p style="margin-top:10px"><small><i><?php _e("To change your user image, please use the same email as your Gravatar", 'funding'); ?></i></small></p></div>
          <div class="tabbable"> <!-- Only required for left/right tabs -->
                <ul class="nav nav-tabs">
                     <li class="active"><a class="button-small button-green" data-toggle="tab" href="#profile"><?php _e("My profile", 'funding'); ?></a></li>
                     <li><a class="button-small button-green" data-toggle="tab" href="#profile-edit"><?php _e("Edit profile", 'funding'); ?></a></li>
                     <li><a class="edit-button button-small button-green" href="<?php echo get_bloginfo('url'); ?>/wp-admin/edit.php?post_type=project&page=funding-settings"><?php _e("Paypal details", 'funding'); ?></a></li>
                </ul>
                 <div class="tab-content">
          <div id="profile" class="tab-pane active">
          <div class="row">
            <div class="span9">
            <h1><?php
    if (get_the_author_meta('display_name', get_current_user_id())) {echo get_the_author_meta('display_name', get_current_user_id());
    }
?></h1>
              <dl>
                  <?php
                    if (get_the_author_meta('first_name', get_current_user_id())){ ?>
                  <dt><small><?php _e("Name:", 'funding'); ?></small></dt>
                  <dd> <?php echo get_the_author_meta('first_name', get_current_user_id()); if (get_the_author_meta('last_name', get_current_user_id())){
                      echo ' ';echo get_the_author_meta('last_name', get_current_user_id()); }?>
                  </dd>
                    <?php } ?>
                    <?php if (usercountry_name_display(get_current_user_id()) != ""){ ?>
                  <dt><small><?php _e("Country:", 'funding'); ?></small></dt>
                  <dd><?php echo usercountry_name_display(get_current_user_id()); ?></dd>
                   <?php } ?>
                  <?php if (get_the_author_meta('user_registered', get_current_user_id())) { ?>
                <dt><small><?php _e("Member Since:", 'funding'); ?></small></dt>
                <dd><?php echo date("F Y", strtotime(get_userdata(get_current_user_id()) -> user_registered));?>
               </dd>
                <?php } ?>
                 <?php   if (get_the_author_meta('user_url', get_current_user_id())) { ?>
                  <dt><small><?php _e("Website:", 'funding'); ?></small></dt>
                  <dd><a target="_blank" href="<?php
                    if (get_the_author_meta('user_url', get_current_user_id())) {echo get_the_author_meta('user_url', get_current_user_id());}?>">
                    <?php echo get_the_author_meta('user_url', get_current_user_id());?></a></dd>
                <?php } ?>
              </dl>
              <?php  if (get_the_author_meta('description', get_current_user_id())){ ?>
                <div class="biography"><p><?php echo get_the_author_meta('description', get_current_user_id());?></p></div>
            <?php } ?>
        </div>   <!-- /.span9 -->
          </div>
          <!-- /.row -->
        </div>
        <!-- profile tab end -->
         <div id="profile-edit" class="tab-pane">
          <div class="row">
            <div class="span9">
            <?php
                global $current_user, $wp_roles;
                get_currentuserinfo();
                /* Load the registration file. */
                require_once (ABSPATH . WPINC . '/registration.php');
                $error = array();
                /* If profile was saved, update profile. */
                if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['action']) && $_POST['action'] == 'update-user') {
                    /* Update user password. */
                    if (!empty($_POST['pass1']) && !empty($_POST['pass2'])) {
                        if ($_POST['pass1'] == $_POST['pass2'])
                            wp_update_user(array('ID' => $current_user -> ID, 'user_pass' => esc_attr($_POST['pass1'])));
                        else
                            $error[] = __('The passwords you entered do not match.  Your password was not updated.', 'funding');
                    }
                    /* Update user information. */
                    //website
                    wp_update_user( array ('ID' => $current_user -> ID, 'user_url' => esc_url($_POST['user_url'])) ) ;
                    if (!empty($_POST['email'])) {
//                        echo email_exists(esc_attr($_POST['email']));echo $current_user -> ID;exit;
                        if (!is_email(esc_attr($_POST['email'])))
                            $error[] = __('The Email you entered is not valid.  please try again.', 'funding');
                        elseif (trim (email_exists(esc_attr($_POST['email']))) != "" && email_exists(esc_attr($_POST['email'])) != $current_user -> ID)
                            $error[] = __('This email is already used by another user.  try a different one.', 'funding');
                        else {
                            wp_update_user(array('ID' => $current_user -> ID, 'user_email' => esc_attr($_POST['email'])));
                        }
                    }
                    if(!empty($_POST['usercountry_id']))
                         update_user_meta($current_user -> ID, 'usercountry_id', esc_attr($_POST['usercountry_id']));
                    if (!empty($_POST['first-name']))
                        update_user_meta($current_user -> ID, 'first_name', esc_attr($_POST['first-name']));
                    if (!empty($_POST['last-name']))
                        update_user_meta($current_user -> ID, 'last_name', esc_attr($_POST['last-name']));
                    if (!empty($_POST['description']))
                        update_user_meta($current_user -> ID, 'description', esc_attr($_POST['description']));
                    /* Redirect so the page will show updated info.*/
                    /*I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ */
                    if (count($error) == 0) {
                        //action hook for plugins and extra fields saving
                        do_action('edit_user_profile_update', $current_user -> ID);
//                        wp_redirect(get_permalink());
                        wp_redirect('http://themes.themicrolex.com/fundingpressWP/my-projects/');
                        exit ;
                    }
                }
            ?>
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>">
        <div class="entry-content entry">
            <?php the_content(); ?>
            <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'funding'); ?>
                    </p><!-- .warning -->
            <?php else : ?>
                <?php
                if (count($error) > 0)
                    echo '<p class="error">' . implode("<br />", $error) . '</p>';
 ?>
                <form method="post" id="adduser" action="<?php the_permalink(); ?>">
                    <p class="form-username">
                        <label for="first-name"><?php _e('First Name', 'funding'); ?></label>
                        <input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta('first_name', $current_user -> ID); ?>" />
                    </p><!-- .form-username -->
                    <p class="form-username">
                        <label for="last-name"><?php _e('Last Name', 'funding'); ?></label>
                        <input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta('last_name', $current_user -> ID); ?>" />
                    </p><!-- .form-username -->
                    <p class="form-email">
                        <label for="email"><?php _e('E-mail *', 'funding'); ?></label>
                        <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta('user_email', $current_user -> ID); ?>" />
                    </p><!-- .form-email -->
                    <p class="form-url">
                        <label for="user_url"><?php _e('Website', 'funding'); ?></label>
                        <input class="text-input" name="user_url" type="text" id="user_url" value="<?php the_author_meta('user_url', $current_user -> ID); ?>" />
                    </p><!-- .form-url -->
                    <p class="form-password">
                        <label for="pass1"><?php _e('Password *', 'funding'); ?> </label>
                        <input class="text-input" name="pass1" type="password" id="pass1" />
                    </p><!-- .form-password -->
                    <p class="form-password">
                        <label for="pass2"><?php _e('Repeat Password *', 'funding'); ?></label>
                        <input class="text-input" name="pass2" type="password" id="pass2" />
                    </p><!-- .form-password -->
                    <p class="form-textarea">
                        <label for="description"><?php _e('Biographical Information', 'funding') ?></label>
                        <textarea name="description" id="description" rows="3" cols="250"><?php the_author_meta('description', $current_user -> ID); ?></textarea>
                    </p><!-- .form-textarea -->
               <?php    $id = $current_user -> ID;
                        $usercountry_id = get_user_meta($id, 'usercountry_id');?>
    <label for="usercountry_id">Country</label>
     <?php   global $wpdb;
        $table = $wpdb->prefix."user_countries";
        $countries = $wpdb->get_results("SELECT * FROM $table ORDER BY `name`");
    ?><select name="usercountry_id">
    <option value="0"><?php _e('- Select -','funding') ?></option>
    <?php
        foreach ($countries as $country) {
            $selected="";
            if ($usercountry_id[0]==$country->id_country) { $selected="selected";}
            echo '<option '.$selected.' value="'.$country->id_country.'">'.$country->name.'</option>';
        }?>
    </select>
                    <p class="form-submit">
                        <?php echo $referer; ?>
                        <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'funding'); ?>" />
                        <?php wp_nonce_field( 'update-user' ) ?>
                        <input name="action" type="hidden" id="action" value="update-user" />
                    </p><!-- .form-submit -->
                </form><!-- #adduser -->
            <?php endif; ?>
        </div><!-- .entry-content -->
    </div><!-- .hentry .post -->
    <?php endwhile; ?>
<?php else: ?>
    <p class="no-data">
        <?php _e('Sorry, no page matched your criteria.', 'funding'); ?>
    </p><!-- .no-data -->
<?php endif; ?>
             </div>
            <!-- /.span9 -->
          </div>
          <!-- /.row -->
        </div>
        <!-- profile tab end -->
        </div>
     </div>
    </div>
  </div>
</div>
<div class="profile-projects">
<div class="container blog">
  <div class="row">
<h2>Projects</h2>
<?php   if ( is_user_logged_in() ){
            global $current_user;
            get_currentuserinfo(); ?>
    <div class="span12">
        <?php
          global $post;
          $args = array(
            'post_type'=> 'project',
            'areas'    => 'painting',
            'order'    => 'ASC',
            'author' => $current_user->ID,
            'posts_per_page' => -1,
            'post_status' => array( 'pending', 'draft', 'future', 'publish', 'private' )
         );
        $wp_query = new WP_Query( $args);
         if ( $wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post();
            global $f_currency_signs;
            $project_settings = (array) get_post_meta($post -> ID, 'settings', true);
            $project_expired = strtotime($project_settings['date']) < time();
            $project_currency_sign = $f_currency_signs[$project_settings['currency']];
            $target = $project_settings['target'];
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
            }?>
         <div class="project-card span3">
             <?php if(!has_post_thumbnail()){ ?>
                 <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
            <?php }else{ ?>
                <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('projects'); ?></a></div>
            <?php } ?>
             <h5 class="bbcard_name"><a href="<?php the_permalink(); ?>"><?php $title = get_the_title(); echo mb_substr($title, 0,20); if(strlen($title) > 23){echo '...';}?></a></h5>
            <p> <?php $excerpt = get_the_excerpt();
            echo mb_substr($excerpt, 0, 80);
            echo '...';
             ?></p>
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
                     <strong><?php printf(__('%u%%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?></strong><?php _e('funded', 'funding'); ?>
                </li>
                <li class="pledged">
                    <strong>
                         <?php print $project_currency_sign; print number_format(round((int)$target), 0, '.', ',');?></strong><?php _e('target', 'funding'); ?>
                </li>
                <li data-end_time="2013-02-24T08:41:18Z" class="last ksr_page_timer">
                    <?php
                    if(!$project_expired) : ?>
                        <strong><?php print F_Controller::timesince(time(), strtotime($project_settings['date']), 1, ''); ?></strong>
                        <?php _e('days to go', 'funding'); ?>
                    <?php endif; ?>
                </li>
            </ul>
            <a class="edit-button button-small button-green" href="<?php echo home_url(); ?>/wp-admin/post.php?post=<?php echo $post -> ID; ?>&action=edit"><?php _e('Edit project', 'funding'); ?></a>
            <div id="prostatus"><?php _e("Project status: ", 'funding'); echo get_post_status($post->ID);?></div>
           <div class="clear"></div>
        </div>
        <!-- /.blog-post -->
   <?php endwhile; endif; ?>
        <div class="clear"></div>
    </div>
    <!-- /.span12 -->
<?php } ?>
 </div>
  <!-- /.row -->
</div>
<!-- /.container -->
</div> <!-- /.profile -->
<?php get_footer(); ?>