<?php

require_once(dirname(__FILE__).'/lib/PayPal/AdaptivePayments.php');

/**
 * An implementation of the payment interface for PayPal.
 */

class F_PayPal{
     
    const DEVELOPER_PORTAL = 'https://developer.paypal.com';
    const DEVICE_ID = 'PayPal_Platform_PHP_SDK';
   
    const SANDBOX_ENDPOINT = 'https://svcs.sandbox.paypal.com/';
    const PRODUCTION_ENDPOINT = 'https://svcs.paypal.com/';

    function __construct(){
        // Set the options based WordPress options
        if(!defined('FUNDIT_PAYPAL_ENDPOINT')){
            $mode = get_option('fundit_paypal_mode');
            switch($mode){
                case 'sandbox' : define('FUNDIT_PAYPAL_ENDPOINT', self::SANDBOX_ENDPOINT); break;
                case 'production' : define('FUNDIT_PAYPAL_ENDPOINT', self::PRODUCTION_ENDPOINT); break;
            }
        }
    }
	


    /**
     * Get the PayPal authentication URL
     * @param mixed $funder The funder
     */
    function get_auth_url($project, $reward, $funder){
    		
    	$f_paypal = get_option('funding_paypal'); 
		
	if($f_paypal['mode'] =='sandbox'){
		
		define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/webscr&cmd=');
		define('PAYPAL_HOST', 'www.sandbox.paypal.com');
		define('APPLICATION_ID', 'APP-80W284485P519543T');
			
	}else{
	
		define('PAYPAL_REDIRECT_URL', 'https://www.paypal.com/webscr&cmd=');
		define('PAYPAL_HOST', 'www.paypal.com');
		define('APPLICATION_ID', $f_paypal['app_id']);
		
	} 
	
		
        $preapproval = new PreapprovalRequest();

        // Get all the meta data
        $funding_amount = get_post_meta($funder->ID, 'funding_amount', true);
        $project_settings = (array) get_post_meta($project->ID, 'settings', true);

        $preapproval->cancelUrl = add_query_arg(array('f' => 'cancel_funding', 'funder_id' => $funder->ID), get_site_url());
        $preapproval->returnUrl = add_query_arg(array('f' => 'funded', 'funder_id' => $funder->ID), get_site_url());
        $preapproval->ipnNotificationUrl = add_query_arg(array(
            'f' => 'paypal_ipn',
            'funder_id' => $funder->ID,
        ), get_site_url());

        $preapproval->memo = sprintf(__("Funding '%s' from %s for the reward '%s' if we reach our target.", 'funding'), $project->post_title, get_bloginfo('name'), $reward->post_title);
	
        // The client details
        $preapproval->clientDetails = new ClientDetailsType();
        $preapproval->clientDetails->applicationId = APPLICATION_ID;
        $preapproval->clientDetails->model = get_bloginfo('name');
        $preapproval->clientDetails->partnerName = get_bloginfo('name');
        $preapproval->clientDetails->deviceId = self::DEVICE_ID;
        $preapproval->clientDetails->ipAddress = $_SERVER['SERVER_ADDR'];
        $preapproval->clientDetails->customerId = $funder->ID;
        $preapproval->clientDetails->customerType = 'funder';

        // The start and end dates
        if(empty($project->end_date)) $end_time = time() + 365*86400; // 1 full year time
        else $end_time = strtotime($project->end_date) + 7*86400; // Specific end date.

        $preapproval->startingDate = date('c');
        $preapproval->endingDate = date('c', $end_time);

        // The payment details
        $preapproval->currencyCode = $project_settings['currency'];
        $preapproval->maxAmountPerPayment = floatval($funding_amount);
        $preapproval->maxTotalAmountOfAllPayments = floatval($funding_amount);
        $preapproval->maxNumberOfPayments = 1;

        // The request envolope
        $preapproval->requestEnvelope = new RequestEnvelope();
        $preapproval->requestEnvelope->errorLanguage = "en_US";

        // Execute the preapproval request
        $adaptivePayment = new AdaptivePayments();
        $response = $adaptivePayment->Preapproval($preapproval);

        if(strtoupper($adaptivePayment->isSuccess) == 'FAILURE'){
            throw new Exception('Preapproval failed: '.$adaptivePayment->getErrorMessage());
        }
        else{
            // Save the preapproval key for this transaction
            add_post_meta($funder->ID, 'preapproval_key', $response->preapprovalKey, true);
            return PAYPAL_REDIRECT_URL.'_ap-preapproval&preapprovalkey='.$response->preapprovalKey;
        }
    }

    /**
     * Contact PayPal and check that this funder has verified their funding.
     */
    function check_auth($funder){
        // Contact paypal and get the status of the preapproval
        $preapproval_key = get_post_meta($funder->ID, 'preapproval_key',true);

        $PDRequest = new PreapprovalDetailsRequest();
        $PDRequest->requestEnvelope = new RequestEnvelope();
        $PDRequest->requestEnvelope->errorLanguage = "en_US";
        $PDRequest->preapprovalKey = $preapproval_key;

        $adaptivePayment = new AdaptivePayments();
        $response = $adaptivePayment->PreapprovalDetails($PDRequest);

        if(strtoupper($adaptivePayment->isSuccess) == 'FAILURE'){
            // TODO throw an exception based on what type of error this was
            throw new Exception('Error processing approval refresh.');
        }

        if(!empty($response->senderEmail)){
            // Store their PayPal email.
            add_post_meta($funder->ID, 'paypal_email', $response->senderEmail, true);
        }

        // Update the status of the funder
        switch(strtoupper($response->status)){
            case 'ACTIVE':
                wp_update_post(array(
                    'ID' => $funder->ID,
                    'post_status' => 'publish',
                ));
                return true;
                break;

            case 'CANCELED':
                wp_update_post(array(
                    'ID' => $funder->ID,
                    'post_status' => 'trash',
                ));
                return false;
                break;

            case 'DEACTIVED':
                wp_update_post(array(
                    'ID' => $funder->ID,
                    'post_status' => 'trash',
                ));
                return false;
                break;
        }
    }

    /**
     * Executes the preapproval using PayPal
     * @param $funder
     */
    function charge_funder($funder){
    	
	$f_paypal = get_option('funding_paypal');
		
    if($f_paypal['mode'] =='sandbox'){
		
		define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/webscr&cmd=');
		define('PAYPAL_HOST', 'www.sandbox.paypal.com');
		define('APPLICATION_ID', 'APP-80W284485P519543T');
			
	}else{
		
		define('PAYPAL_REDIRECT_URL', 'https://www.paypal.com/webscr&cmd=');
		define('PAYPAL_HOST', 'www.paypal.com');
		define('APPLICATION_ID', $f_paypal['app_id']);
		
	} 
        // Check that this funder hasn't already funded
        $executed = get_post_meta($funder->ID, 'charged', true);

        if(!empty($executed)) throw new Exception('This funder has already funded the project.', self::ERROR_ALREADY_FUNDED);

        $reward = get_post($funder->post_parent);
        $project = get_post($reward->post_parent);

        $project_settings = get_post_meta($project->ID, 'settings', true);
        global $f_paypal;

        $payRequest = new PayRequest();
        $payRequest->actionType = 'PAY';
        $payRequest->returnUrl = get_site_url();
        $payRequest->cancelUrl = get_site_url();

        $payRequest->currencyCode = $project_settings['currency'];
        $payRequest->preapprovalKey = get_post_meta($funder->ID, 'preapproval_key', true);
        $payRequest->senderEmail = get_post_meta($funder->ID, 'paypal_email', true);

        // The client details
        $payRequest->clientDetails = new ClientDetailsType();
        $payRequest->clientDetails->applicationId = APPLICATION_ID;
        $payRequest->clientDetails->deviceId = self::DEVICE_ID;
        $payRequest->clientDetails->ipAddress = $_SERVER['SERVER_ADDR'];

        // Request envolope
        $payRequest->requestEnvelope = new RequestEnvelope();
        $payRequest->requestEnvelope->errorLanguage = "en_US";

        // The receiver
        $admin_info = get_userdata($project->post_author);

        $receivers = array();
        $receiver = new receiver();
        $amm = get_post_meta($funder->ID, 'funding_amount', true);
        $per =  of_get_option('commision');
        $totadminamount = $per /100;
        $receiver->email = $f_paypal['email'];
        $receiver->amount = $totadminamount*$amm;

        $receiver1 = new receiver();
        $receiver1->email = $admin_info->paypal_email;
        $receiver1->amount = $amm - ($totadminamount*$amm);

        $receivers[0] = $receiver;
        $receivers[1] = $receiver1;
        $payRequest->receiverList = $receivers;

        // Send the pay request
        $adaptivePayment = new AdaptivePayments();
        $response = $adaptivePayment->Pay($payRequest);


        // TODO fill all this stuff in
        if(strtoupper($adaptivePayment->isSuccess) == 'FAILURE') {
            throw new Exception($adaptivePayment->getErrorMessage());
        }
        elseif(strtoupper($adaptivePayment->isSuccess) == 'SUCCESS') {
            update_post_meta($funder->ID, 'charged', true);
        }
    }

    /**
     * Verifies an IPN
     */
    private function verify_ipn($vars = null){
        if($vars == null) $vars = $_POST;
        $req = 'cmd=_notify-validate';
        if(function_exists('get_magic_quotes_gpc')){
            $get_magic_quotes_exits = true;
        }

        // Extract all the post variables
        foreach ($vars as $key => $value) {
            // Handle escape characters, which depends on setting of magic quotes
            if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1){
                $value = urlencode(stripslashes($value));
            }
            else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        // Post back to PayPal to validate
        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        $fp = fsockopen ('ssl://'.PAYPAL_HOST, 443, $errno, $errstr, 30);

        if (!$fp) {
            // There was an HTTP error.
            fclose ($fp);
            return false;
        }

        $result = '';
        fputs ($fp, $header . $req);
        while (!feof($fp)) $result .= fgets ($fp, 1024);

        if (strcmp ($res, "VERIFIED") == 0){
            // The IPN was verified
            fclose ($fp);
            return false;
        }
        fclose ($fp);
        return true;
    }

    /**
     * Processes an IPN from PayPal
     *
     * @todo Fix all of this.
     *
     * @param $vars
     * @param $verify
     */
    public function process_ipn($vars = null, $verify = true){
        if($vars == null) $vars = $_POST;
        if($verify) {
            if(!$this->verify_ipn($vars)){
                // TODO throw an exception or something
                return false;
            }
        }

        global $wpdb;
        switch(strtoupper($vars['transaction_type'])){
            case 'ADAPTIVE PAYMENT PREAPPROVAL':
                // Get the funder based on the preapproval key
                $funder_id = $wpdb->get_var($wpdb->prepare("
                    SELECT DISTINCT posts.ID
                    FROM $wpdb->posts AS posts
                    JOIN $wpdb->postmeta AS meta ON meta.post_id = posts.ID
                    WHERE
                        meta_key = 'preapproval_key'
                        AND meta_value = %s
                ", array($vars['preapproval_key'])));

                if(empty($funder)) return false;

                // TODO add wordpress hooks here
                switch(strtoupper($vars['status'])){
                    case 'ACTIVE':
                        wp_update_post(array(
                            'ID' => $funder_id,
                            'post_status' => 'publish',
                        ));
                        return true;
                        break;

                    case 'CANCELED':
                        wp_update_post(array(
                            'ID' => $funder_id,
                            'post_status' => 'trash',
                        ));
                        return false;
                        break;

                    case 'DEACTIVED':
                        wp_update_post(array(
                            'ID' => $funder_id,
                            'post_status' => 'trash',
                        ));
                        return false;
                        break;
                }
                break;
        }

        return true;
    }
}