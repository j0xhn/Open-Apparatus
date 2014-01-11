<?php $current_user= wp_get_current_user();
$level = $current_user->user_level;
if($level == 10){
    ?><div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2><?php _e('Funding Settings', 'funding'); ?></h2>

    <form action="" method="POST">
        <h3><?php _e("Fundit Settings", 'funding'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="paypal-email"><?php _e('PayPal Email Address', 'funding');?></label></th>
                    <td>
                        <input type="text" name="email" id="paypal-email" class="regular-text" value="<?php print @$f_paypal['email']; ?>" />
                        <div class="description">
                            <?php print __('The PayPal email address you want to be paid into.', 'funding') ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <h3>PayPal API Credentials</h3>

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="paypal-mode"><?php _e('Mode', 'funding') ?></label></th>
                    <td>
                        <select name="mode">
                            <option value="sandbox" <?php selected('sandbox', @$f_paypal['mode']) ?>><?php _e("Sandbox", 'funding'); ?></option>
                            <option value="production" <?php selected('production', @$f_paypal['mode']) ?>><?php _e("Production", 'funding'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="paypal-app-id"><?php _e('PayPal Application ID', 'funding') ?></label></th>
                    <td>
                        <input type="text" name="app_id" id="paypal-app-id" class="regular-text" value="<?php print @$f_paypal['app_id']; ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="paypal-api-username"><?php _e('PayPal API Username', 'funding') ?></label></th>
                    <td>
                        <input type="text" name="api_username" id="paypal-api-username" class="regular-text" value="<?php print @$f_paypal['api_username']; ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="paypal-api-password"><?php _e('PayPal API Password', 'funding') ?></label></th>
                    <td>
                        <input type="text" name="api_password" id="paypal-api-password" class="regular-text" value="<?php print @$f_paypal['api_password']; ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="paypal-api-signature"><?php _e('PayPal API Signature', 'funding') ?></label></th>
                    <td>
                        <input type="text" name="api_signature" id="paypal-api-signature" class="regular-text" value="<?php print @$f_paypal['api_signature']; ?>" />
                    </td>
                </tr>

            </tbody>
        </table>

        <p>
            <?php wp_nonce_field('funding_settings') ?>
            <input class="button-primary" type="submit" value="Save Changes" name="submit" />
        </p>
    </form>
</div>

<?php }else{ ?>
    <div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h2><?php _e('Funding Settings', 'funding') ?></h2>

    <form action="" method="POST">
        <h3><?php _e("Fundit Settings", 'funding'); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="paypal_email"><?php _e('PayPal Email Address', 'funding'); ?></label></th>
                    <td> <?php
                    if($_POST['paypal_email'] == ''){}else{ global $wpdb;
                    $updatemail =  $_POST['paypal_email'];  $result = mysql_query("UPDATE ".$wpdb->prefix."users SET paypal_email = '".$updatemail."' WHERE ID = '".get_current_user_id()."'"); }?>
                    <input type="text" name="paypal_email" id="paypal_email" class="regular-text" value="<?php  $result = mysql_query("SELECT paypal_email FROM ".$wpdb->prefix."users WHERE ID = '".get_current_user_id()."'");
                    if($result){$row = mysql_fetch_array($result); echo $row['paypal_email'];}   ?>" />
                        <div class="description">
                            <?php print __('The PayPal email address you want to be paid into.', 'funding') ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <p>

            <input class="button-primary" type="submit" value="Save Changes" name="submit" />
        </p>
    </form>


</div>

<?php } ?>