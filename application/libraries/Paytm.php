<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Paytm {

    private $paytm_url = "";
    private $paytm_secret_key = "";
    private $ci;

    public function __construct() {
        $this->ci = get_instance();

        //get credentials
        $paytm_config = $this->ci->Payment_methods_model->get_oneline_payment_method("paytm");
        $this->paytm_secret_key = $paytm_config->secret_key;

        if ($paytm_config->paytm_testing_environment == "1") {
            $this->paytm_url = "https://securegw-stage.paytm.in/theia/processTransaction";
        } else {
            $this->paytm_url = "https://securegw.paytm.in/theia/processTransaction";
        }

        /*
          File from https://github.com/Paytm-Payments/Paytm_Web_Sample_Kit_PHP
          There has global variables used in this file to initiate refund operation.
          Since the refund facilities not available in this app, we don't need that.
          But to implement those, we've to modify this library file, otherwise there will be error.
         */
        require_once(APPPATH . "third_party/Paytm/encdec_paytm.php");
    }

    public function get_paytm_url() {
        return $this->paytm_url;
    }

    //get checksum hash to send to the paytm
    public function get_paytm_checksum_hash($values_array = array(), $verification_data_params = "") {
        if (!($values_array && $verification_data_params)) {
            return false;
        }

        //save verification data
        $payment_verification_code = make_random_string();
        $verification_data = array(
            "type" => "invoice_payment",
            "code" => $payment_verification_code,
            "params" => serialize(json_decode($verification_data_params, true))
        );

        if (!$this->ci->Verification_model->save($verification_data)) {
            return false;
        }

        //update callback url, since the before and after generating the checksum hash the callback url should be same
        $values_array["CALLBACK_URL"] = get_uri("paytm_redirect/index/$payment_verification_code");

        $checksum_hash = getChecksumFromArray($values_array, $this->paytm_secret_key);
        if (!$checksum_hash) {
            return false;
        }

        return array(
            "payment_verification_code" => $payment_verification_code,
            "checksum_hash" => $checksum_hash
        );
    }

    //verify the checksum hash after transaction
    public function is_valid_checksum_hash($post_data) {
        if (count($post_data)) {
            $paytm_checksum = isset($post_data["CHECKSUMHASH"]) ? $post_data["CHECKSUMHASH"] : "";
            $is_valid_checksum_hash = verifychecksum_e($post_data, $this->paytm_secret_key, $paytm_checksum);

            if ($is_valid_checksum_hash == "TRUE") {
                return true;
            } else {
                return false;
            }
        }
    }

}
