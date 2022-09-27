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
     * Getting current IP Address
     * @return string
     */
    public function getCurrentIpAddress()
    {
        $ip = "";
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }

    /**
     * Generating six digits random number
     * @return int
     */
    public function generate_otp()
    {
        return mt_rand(1000, 9999);
    }

    /**
     * Generating unique id with the length of 10
     * @return float|int
     */
    public function generate_uid()
    {
        return hexdec(substr(uniqid(), 0, 16));
    }

    /**
     * Generating key using the mobile number and key
     * @param $mobile_number
     * @param $token
     * @return bool|string
     */
    public function generateKey($mobile_number, $token)
    {
        $salt = hash('sha256', time() . mt_rand() . base64_encode($mobile_number . $token));
        $key = substr($salt, 0, config_item('rest_key_length'));
        return $key;
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
     * Generating Alphanumeric string
     * @param $length
     * @return string
     */
    public function generatePassword($length = 10)
    {
        $rand = "";
        $seed = str_split("0123456789" . "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        shuffle($seed);
        foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];
        return $rand;
    }

    /**
     * Generating Alphanumeric string
     * @param $length
     * @return string
     */
    public function generateRandomString($length = 10)
    {
        $rand = "";
        $seed = str_split("abcdefghijklmnopqrstuvwxyz" . "0123456789" . "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        shuffle($seed);
        foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];
        return $rand;
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

    public function ageCalculate($dob)
    {
        if (!empty($dob)) {
            $from = new DateTime($dob);
            $to = new DateTime("today");
            return $from->diff($to)->y;
        } else {
            return "";
        }
    }

    public function dateTime2TimeAgo($dateTime)
    {
        $time = strtotime($dateTime);
        // Calculate difference between current
        // time and given timestamp in seconds
        $diff = time() - $time;

        // Time difference in seconds
        $sec = $diff;

        // Convert time difference in minutes
        $min = round($diff / 60);

        // Convert time difference in hours
        $hrs = round($diff / 3600);

        // Convert time difference in days
        $days = round($diff / 86400);

        // Convert time difference in weeks
        $weeks = round($diff / 604800);

        // Convert time difference in months
        $mnths = round($diff / 2600640);

        // Convert time difference in years
        $yrs = round($diff / 31207680);

        // Check for seconds
        if ($sec <= 60) {
            return "$sec seconds ago";
        } // Check for minutes
        else if ($min <= 60) {
            if ($min == 1) {
                return "one minute ago";
            } else {
                return "$min minutes ago";
            }
        } // Check for hours
        else if ($hrs <= 24) {
            if ($hrs == 1) {
                return "an hour ago";
            } else {
                return $hrs . " hours ago";
            }
        } // Check for days
        else if ($days <= 7) {
            return date("D, h:i:s A", $time);
        } // Check for weeks
        else if ($weeks <= 4.3) {
            return date("d M, h:i:s A", $time);
        } // Check for months
        else if ($mnths <= 12) {
            return date("d M, h:i:s A", $time);
        } // Check for years
        else {
            return date("d/m/y, h:i:s A", $time);
        }
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
        echo json_encode($array,JSON_FORCE_OBJECT);
        exit;
    }
}