<?php
/****************************************************
CallerServices.php
This file uses the constants.php to get parameters including the PayPal Webservice Host URL needed
to make an API call and calls the server.
Called by PayReceipt.php, PaymentDetails, etc.,
****************************************************/
require_once 'SOAPEncoder/SOAPEncoder.php';
require_once 'Exceptions/FatalException.php';
require_once 'Log.php';
class Paypal_CallerServices
{
    ////////////////////////////////////////
    // public variables
    ////////////////////////////////////////

    /*
     * Error ID
     */
    public $error_id = '';

    /*
     * Error Message
     */
    public $error_message = '';

    /*
     * Result FAILURE or SUCCESS
     */
    public $isSuccess;

    /*
     * Sandbox Email Address
     */
    public $sandBoxEmailAddress;

    /*
     * Last Error
     */
    private $LastError;


    /*
     * Calls the actual WEB Service and returns the response.
     */
    function call($request, $serviceName)
    {
        $response = null;

        try {
            $endpoint = X_PAYPAL_API_BASE_ENDPOINT . $serviceName;

            $response = paypal_adaptive_call($request, $endpoint, $this->sandBoxEmailAddress);
            $isFault  = false;
            if (empty($response) || trim($response) == '') {
                $isFault            = true;
                $fault              = new FaultMessage();
                $errorData          = new ErrorData();
                $errorData->errorId = 'API Error';
                $errorData->message = 'response is empty.';
                $fault->error       = $errorData;

                $this->isSuccess = 'Failure';
                $this->LastError = $fault;
                $response        = null;

            } else {
                $isFault = false;

                $this->isSuccess = 'Success';
                $response        = SoapEncoder::Decode($response, $isFault);
                if ($isFault) {
                    $this->isSuccess = 'Failure';
                    $this->LastError = $response;
                    $response        = null;
                }
            }
        }
        catch (Exception $ex) {
            throw new FatalException('Error occurred in call method');
        }
        return $response;
    }


    /*
     * Calls the actual WEB Service and returns the response.
     */
    function callWebService($request, $serviceName, $simpleXML)
    {
        $response = null;

        try {
            $endpoint = X_PAYPAL_API_BASE_ENDPOINT . $serviceName;
            $response = paypal_adaptive_call($request, $endpoint, $this->sandBoxEmailAddress, $simpleXML);
        }
        catch (Exception $ex) {
            throw new FatalException('Error occurred in call method');
        }
        return $response;
    }


    /*
     * Returns Error ID
     */
    function getErrorId()
    {
        $errorId = '';
        if ($this->LastError != null) {
            if (is_array($this->LastError->error)) {
                $errorId = $this->LastError->error[0]->errorId;
            } else {
                $errorId = $this->LastError->error->errorId;
            }
        }
        return $errorId;

    }

    /*
     * Returns Error Message
     */
    function getErrorMessage()
    {
        $errorMessage = '';
        if ($this->LastError != null) {
            if (is_array($this->LastError->error)) {
                $errorMessage = $this->LastError->error[0]->message;
            } else {
                $errorMessage = $this->LastError->error->message;
            }
        }
        return $errorMessage;

    }

    /*
     * Returns Last error
     */
    public function getLastError()
    {
        return $this->LastError;
    }

    /*
     * Sets the Last error
     */
    public function setLastError($error)
    {
        $this->LastError = $error;
    }
}
/**
 * call: Function to perform the API call to PayPal using API signature
 * @methodName is name of API  method.
 * @a is  String
 * $serviceName is String
 * returns an associtive array containing the response from the server.
 */
function paypal_adaptive_call($MsgStr, $endpoint, $sandboxEmailAddress = '')
{
    //setting the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);

    //turning off the server and peer verification(TrustManager Concept)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/Certs/api_cert_chain.crt');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers_array = paypal_adaptive_setupHeaders();
    if (!empty($sandboxEmailAddress)) {
        $headers_array[] = "X-PAYPAL-SANDBOX-EMAIL-ADDRESS: " . $sandboxEmailAddress;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_array);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $logger = &Log::singleton('error_log');

    if (X_PAYPAL_REQUEST_DATA_FORMAT == 'JSON') {
        $log_data = '#####JSON#####';
        $logger->log($log_data);
    }

    if (X_PAYPAL_REQUEST_DATA_FORMAT == 'SOAP11') {
        $log_data = '#####SOAP#####';
        $logger->log($log_data);
    }

    if (X_PAYPAL_REQUEST_DATA_FORMAT == 'XML') {
        $log_data = '#####XML#####';
        $logger->log($log_data);
    }

    $logger->log("request: $MsgStr");

    curl_setopt($ch, CURLOPT_POSTFIELDS, $MsgStr);

    if (isset($_SESSION['curl_error_no'])) {
        unset($_SESSION['curl_error_no']);
    }

    if (isset($_SESSION['curl_error_msg'])) {
        unset($_SESSION['curl_error_msg']);
    }


    //getting response from server
    $response = curl_exec($ch);
    $logger->log("response: $response");
    $logger->close();

    if (curl_errno($ch)) {
        error_log('Error [' . curl_errno($ch) . '] - ' . curl_error($ch));
        throw new Exception('Error [' . curl_errno($ch) . '] - ' . curl_error($ch));
    } else {
        //closing the curl
        curl_close($ch);
    }

    return $response;
}
function paypal_adaptive_setupHeaders()
{
    $headers_arr = array();

    $headers_arr[] = "X-PAYPAL-SECURITY-SIGNATURE: " . SOCF_API_SIGNATURE;
    $headers_arr[] = "X-PAYPAL-SECURITY-USERID:  " . SOCF_API_USERNAME;
    $headers_arr[] = "X-PAYPAL-SECURITY-PASSWORD: " . SOCF_API_PASSWORD;
    $headers_arr[] = "X-PAYPAL-APPLICATION-ID: " . SOCF_APPLICATION_ID;
    $headers_arr[] = "X-PAYPAL-REQUEST-SOURCE: " . X_PAYPAL_ADAPTIVE_SDK_VERSION;
    $headers_arr[] = "X-PAYPAL-DEVICE-IPADDRESS: " . $_SERVER['HTTP_HOST'];
    if (strtoupper(X_PAYPAL_REQUEST_DATA_FORMAT) == "SOAP11" || strtoupper(X_PAYPAL_RESPONSE_DATA_FORMAT) == "SOAP11") {
        $headers_arr[] = "X-PAYPAL-MESSAGE-PROTOCOL: SOAP11";
    } else {
        $headers_arr[] = "X-PAYPAL-REQUEST-DATA-FORMAT: " . X_PAYPAL_REQUEST_DATA_FORMAT;
        $headers_arr[] = "X-PAYPAL-RESPONSE-DATA-FORMAT: " . X_PAYPAL_RESPONSE_DATA_FORMAT;
    }

    if (!defined('X_PAYPAL_REQUEST_SOURCE')) {
        $headers_arr[] = "X-PAYPAL-REQUEST-SOURCE: " . X_PAYPAL_ADAPTIVE_SDK_VERSION;
    } else
        $headers_arr[] = "X-PAYPAL-REQUEST-SOURCE: " . X_PAYPAL_ADAPTIVE_SDK_VERSION . ":" . X_PAYPAL_REQUEST_SOURCE;
    return $headers_arr;

}