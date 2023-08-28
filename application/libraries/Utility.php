<?php

/**
 * Description of Utility
 * All common methods are written here in the utility class
 * @author Krishna
 */

class Utility
{
    /**
     * Utility constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->helper('url');
        $this->CI->load->library('session');
        $this->CI->load->library('email');
        $this->CI->load->database();
    }

    /**
     * Generating six digits random number
     * @return int
     */
    public function generate_otp()
    {
        return mt_rand(100000, 999999);
    }

    /**
     * Generating 32 characters GUID
     * @return string
     */
    public function getGUID()
    {
        $guid = '';
        $namespace = rand(11111, 99999);
        $uid = uniqid('', true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtolower(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash, 0, 8) .
            substr($hash, 8, 4) .
            substr($hash, 12, 4) .
            substr($hash, 16, 4) .
            substr($hash, 20, 12);
        return $guid;
    }

    /**
     * Generating Numeric string
     * @param $length
     * @return string
     */
    public function generateUID($length)
    {
        $rand = "";
        $seed = str_split("0123456789" . "0123456789" . "0123456789");
        shuffle($seed);
        foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];
        return $rand;
    }

    /**
     * Helper method for validating email
     * @param $email
     * @return bool
     */
    public function validEmail($email)
    {
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
                return false;
            }
        }
        if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                    return false;
                }
            }
        }
        return true;
    }

    public function sendForceJSON($array)
    {
        header('Content-Type: application/json');
        echo json_encode($array);
        exit;
    }

    /**
     * Sending SMS to mobiles with custom message || Modified : 2022-12-06
     * @param $mobile_number
     * @param $message
     * @return mixed
     */
    public function sendSMS($mobile_number, $message)
    {
        /**
         * API-KEY :- bpzNEtJGgKE-k8XO8wzS41OuCWD3EeJ5wx8LNL5wEf
         * Sender id :- MRMASN
         * SINGLE MESSAGE : https://api.textlocal.in/send?apikey=XxXXX&message=XXXXXX&sender=XXXXXXX&numbers=91XXXXXX
         */
        if (!empty($mobile_number) && is_numeric($mobile_number) && !empty($message)) {
            $mobile_number = substr($mobile_number, -10);
            $data = "apikey=bpzNEtJGgKE-k8XO8wzS41OuCWD3EeJ5wx8LNL5wEf&message=$message&sender=MRMASN&numbers=91$mobile_number";
            $ch = curl_init("https://api.textlocal.in/send?");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            /*$info = curl_getinfo($ch);
            log_message("ERROR", $info);*/
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Sending mail using the custom message and subject
     * @param $to_email
     * @param $message
     * @param $subject
     * @return bool
     */
    public function sendEMAIL($to_email, $message, $subject)
    {
        if (!empty($to_email)) {
            $CI =& get_instance();
            $CI->email->initialize(array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
//                'smtp_user' => "carstd@kosuriauto.com",
//                'smtp_pass' => "CarStd23@",
                'smtp_user' => "mrmason.in@kosuriers.com",
                'smtp_pass' => "BabuMekanik24@",
                'smtp_port' => 465,
                'mailtype' => 'html',
                'crlf' => "\r\n",
                'newline' => "\r\n",
                'charset' => "utf-8"
            ));
            $CI->email->from("mrmason.in@kosuriers.com", "MrMason");
            $CI->email->to($to_email);
            $CI->email->subject($subject);
            $CI->email->message($message);
            $result = $CI->email->send();
//            echo $CI->email->print_debugger();
            if ($result) {
                return true;
            } else {
                return $result;
            }
        } else {
            return "failed";
        }
    }
}