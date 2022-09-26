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
     * Sending mail using the custom message and subject
     * @param $to_email
     * @param $message
     * @param $subject
     * @return bool
     */
    public function send_email($to_email, $message, $subject)
    {
        if (!empty($to_email)) {
            $CI =& get_instance();
            $CI->email->initialize(array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_user' => SYSTEM_EMAIL_ADDRESS,
                'smtp_pass' => SYSTEM_EMAIL_PASSWORD,
                'smtp_port' => 465,
                'mailtype' => 'html',
                'crlf' => "\r\n",
                'newline' => "\r\n",
                'charset' => "utf-8"
            ));
            $CI->email->from(SYSTEM_EMAIL_ADDRESS, SYSTEM_EMAIL_NAME);
            $CI->email->to($to_email);
            $CI->email->subject($subject);
            $CI->email->message($message);
            $result = $CI->email->send();
            if ($result) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Encryption of database values
     * @param $regularString
     * @return string
     */
    public function encryptData($regularString)
    {
        if (!empty($regularString)) {
            return openssl_encrypt($regularString, DATABASE_ENCRYPTION_CIPHERING,
                DATABASE_ENCRYPTION_KEY, DATABASE_ENCRYPTION_OPTION, DATABASE_ENCRYPTION_IV);
        } else {
            return "";
        }
    }

    /**
     * Decryption of database values
     * @param $encryptedString
     * @return string
     */
    public function decryptData($encryptedString)
    {
        if (!empty($encryptedString)) {
            return openssl_decrypt($encryptedString, DATABASE_ENCRYPTION_CIPHERING,
                DATABASE_ENCRYPTION_KEY, DATABASE_ENCRYPTION_OPTION, DATABASE_ENCRYPTION_IV);
        } else {
            return "";
        }
    }

    /**
     * Sending email with custom message text & attachment
     * @param $to_email
     * @param $message
     * @param $subject
     * @param $attachments
     * @return mixed
     */
    public function send_email_with_attachment($to_email, $message, $subject, $attachments)
    {
        $CI =& get_instance();
        $CI->email->initialize(array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'hcc.v.2.0@gmail.com',
            'smtp_pass' => 'HCC@12345',
            'smtp_port' => 465,
            'mailtype' => 'html',
            'crlf' => "\r\n",
            'newline' => "\r\n",
            'charset' => "utf-8"
        ));
        $CI->email->from('hcc.v.2.0@gmail.com', 'HCC');
        $CI->email->to($to_email);
        $CI->email->subject($subject);
        $CI->email->message($message);
        foreach ($attachments as $file_name) {
            $CI->email->attach($file_name);
        }
        $result = $CI->email->send();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Optimized new email function with and without attachments
     * @param $toEmail
     * @param $subject
     * @param $content
     * @param $attachmentsArray
     * @return mixed
     */
    public function sendEmail($toEmail, $subject, $content, $attachmentsArray = array())
    {
        if ($this->validEmail($toEmail)) {
            $CI =& get_instance();
            $CI->email->initialize(array(
                'protocol' => 'smtp',
                'smtp_host' => 'ssl://smtp.googlemail.com',
                'smtp_user' => SYSTEM_EMAIL_ADDRESS,
                'smtp_pass' => SYSTEM_EMAIL_PASSWORD,
                'smtp_port' => 465,
                'mailtype' => 'html',
                'crlf' => "\r\n",
                'newline' => "\r\n",
                'charset' => "utf-8"
            ));
            $CI->email->from(SYSTEM_EMAIL_ADDRESS, SYSTEM_EMAIL_NAME);
            $CI->email->to($toEmail);
            $CI->email->subject($subject);
            $CI->email->message($content);
            if (!empty($attachmentsArray)) {
                foreach ($attachmentsArray as $eachFileName) {
                    $CI->email->attach($eachFileName);
                }
            }
            $result = $CI->email->send();
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function sendWhatsAppMessage($mobileNumber, $message)
    {
        try {
            $url = "https://graph.facebook.com/v14.0/104820685600597/messages";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
                "Authorization: Bearer EAAr1yBIGJJ0BALsx1dlIxNfiwHGRQHiBNbR4S6TAJ38WjxJ3seDU13VZCwOpGJI96sIjKA51ncRqkGQMZBHomLD0wZAvzoXIfsTh6rUSxrRN3H7xh7IbqdZCnHvjbOI5XzWMlZARHGeG8a46yfz7WHjrFyrLNHsZCUv7QZBA058qdJ1qU8rq61eDZBZCTI7oXZCz3ENCZAmlrzEDgZDZD",
                "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $templateArray = array(
                "messaging_product" => "whatsapp",
                "to" => "919014555055",
                "type" => "template",
                "template" => array(
                    "name" => "welcome_team",
                    "language" => array(
                        "code" => "en_US"
                    ),
                    "components" => array(
                        array(
                            "type" => "body",
                            "parameters" => array(
                                array(
                                    "type" => "text", "text" => "Prabhu"
                                )
                            )
                        )
                    )
                )
            );

            $array = array(
                "messaging_product" => "whatsapp",
                "to" => $mobileNumber,
                "recipient_type" => "individual",
                "type" => "text",
                "text" => array(
                    "preview_url" => false,
                    "body" => $message
                )
            );

            $data = (string)json_encode($array);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);
            var_dump($resp);
        } catch (Exception $e) {
            log_message('ERROR', $e->getMessage());
        }
    }

    /**
     * Sending SMS to mobiles with custom message || Modified : 2021-07-01
     * @param $mobile_number
     * @param $message
     * @return mixed
     */
    public function sendSMS($mobile_number, $message)
    {
        /**
         * Username :- nushift
         * Password:- Nushift@123 (N is Capital)
         * Credits :- 1,00,000
         * Sender id :-NUSHFT
         * SINGLE MESSAGE : https://103.229.250.200/smpp/sendsms?username=XxXXX&password=XXXXXX&to=XXXXXXX&from=XXXXXX&text=XXXXXX
         */
        if (!empty($mobile_number) && is_numeric($mobile_number)) {

//            $this->sendWhatsAppMessage($mobile_number, $message);
            $data = 'username=nushift&password=Nushift@123&to=' . $mobile_number . '&from=NUSHFT&text=' . $message;
            $ch = curl_init('https://103.229.250.200/smpp/sendsms?');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            return curl_exec($ch);
        } else {
            return false;
        }
    }

    /**
     * Sending Firebase Push notification for different applications
     * @param $application
     * @param $deviceTokens
     * @param $triggerType
     * @param $notificationArray
     */
    public function triggerNotification($application, $deviceTokens, $triggerType, $notificationArray)
    {
        try {
            $url = 'https://fcm.googleapis.com/fcm/send';
            //Android -> 'data'
            //iOS -> 'notification->content'
            if ($triggerType === "bulk") {
                $fields = array(
                    'registration_ids' => $deviceTokens,
                    'notification' => $notificationArray,
                    'data' => $notificationArray['content']
                );
            } else {
                $fields = array(
                    'to' => $deviceTokens,
                    'notification' => $notificationArray,
                    'data' => $notificationArray['content']
                );
            }
            $application = strtoupper($application);
            switch ($application) {
                case "B":
                    $headers = array('Authorization:key=AAAAkSoqbEU:APA91bG29qC1PQRJ4EfT9WumwNyQmLv2ifdurpwogC_hQKPmJPQ69crRbHEijVXxeVWKFC_NQ0QavUUa62mmOD7-e08NMjQlQmpBW7IFAaAeHaQ-XceYEz81msKiDo3Hz0kkUkv8uY9O', 'Content-Type:application/json');
                    break;
                case "C":
                    $headers = array('Authorization:key=AAAAT5i0UOM:APA91bGOplsG6ApQhl_8INuiySp9BWJZggrjqL4w40C9X6JdbbyCaiwnAsJCqlrrzinfiZT0rCqOtwFprf-hwRj8o5UJiJXKTSeWG2mOS87z81013bGVo5wIR9GJYX2iICx6bIuVTU5K', 'Content-Type:application/json');
                    break;
                case "D":
                    $headers = array('Authorization:key=AAAA4WivL44:APA91bGbuRoib2le0vvGAJlvlwcB77pIzHqQh9OHxxCrYH3z5-BWh_BB1nUYZWBVNurF0qZc6V1Lt_mdSAdVfk_tbNjkSgtC9EeZMknyUzNVkpP7Dad8mmNMnPY7ZgPL8qOkoDMdgFVB', 'Content-Type:application/json');
                    break;
                case "H":
                    $headers = array('Authorization:key=AAAAUZnKFPE:APA91bEsRIZEeJWnHIWzkWw7drY3MOUjwxDY25iMwWBnoCTrEl5K8WczGRDD_7VC7oDpKAwrPk1zI-a9FQFLThTs6fF2LxzW2HSghDF-XjtVpWSsHHFMSp8a3EhQdOV2JJVbJ-wzQ8QP', 'Content-Type:application/json');
                    break;
                case "L":
                    $headers = array('Authorization:key=AAAAsjQsJH0:APA91bE-WwtmFcqpW1wIcXZWm-nqo05WPHVNWShf2j9izF1SsZgssMJeywTK7xE2bMVwcLDCzDZm6o3i4P4GqSoSodM9IhtL8qlM8Shrn5OJQ9mwEZrG3cRBipJkLERLFn6kvxif12xK', 'Content-Type:application/json');
                    break;
                case "P":
                    $headers = array('Authorization:key=AAAArDIk1EA:APA91bExtAwhkEADXapbEuZiPlQ6CcDriHThwVoUtLqhF32pMHYH4vDaNp9Tzg6gE3IlDPtwBde_HazKM8wLsc4TKv8arq17PITw_D6BIIH_UTyQNRGPYHV3EXZ42m8VNBM1C5gfoa8K', 'Content-Type:application/json');
                    break;
                default:
                    $headers = array();
            }
            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarily
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                log_message('ERROR', curl_error($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            log_message('ERROR', $e->getMessage());
        }
        /*echo json_encode($fields);*/
    }

    /*------------------------------------ Firebase Notifications --------------------------------------*/
    /**
     * Doctors Application
     * Sending Push notification to the mobile devices
     * @param $deviceToken
     * @param $notificationArray
     */
    public function sendPushNotification($deviceToken, $notificationArray)
    {
        try {
            $url = 'https://fcm.googleapis.com/fcm/send';

            //data for Android, in notification->content iOS
            $fields = array(
                'to' => $deviceToken,
                'notification' => $notificationArray,
                'data' => $notificationArray['content']
            );

            // Firebase API Key
            // Live Firebase key added -> Krishna
            $headers = array('Authorization:key=AAAA4WivL44:APA91bGbuRoib2le0vvGAJlvlwcB77pIzHqQh9OHxxCrYH3z5-BWh_BB1nUYZWBVNurF0qZc6V1Lt_mdSAdVfk_tbNjkSgtC9EeZMknyUzNVkpP7Dad8mmNMnPY7ZgPL8qOkoDMdgFVB', 'Content-Type:application/json');
            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarily
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                log_message('error', curl_error($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
        /*echo json_encode($fields);*/
    }

    /**
     * Doctors Application
     * Sending multiple push notifications
     * @param $tokensArray
     * @param $notificationArray
     */
    public function sendMultiplePushNotification($tokensArray, $notificationArray)
    {
        try {
            $url = 'https://fcm.googleapis.com/fcm/send';

            //data for Android, in notification->content iOS
            $fields = array(
                'registration_ids' => $tokensArray,
                'notification' => $notificationArray,
                'data' => $notificationArray['content']
            );

            // Firebase API Key
            // Live Firebase key added -> Krishna
            $headers = array('Authorization:key=AAAA4WivL44:APA91bGbuRoib2le0vvGAJlvlwcB77pIzHqQh9OHxxCrYH3z5-BWh_BB1nUYZWBVNurF0qZc6V1Lt_mdSAdVfk_tbNjkSgtC9EeZMknyUzNVkpP7Dad8mmNMnPY7ZgPL8qOkoDMdgFVB', 'Content-Type:application/json');
            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarily
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                log_message('error', curl_error($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
        /*echo json_encode($fields);*/
    }

    /*---------------------------------------- Patients---------------------------------------------*/

    /**
     * Patients Application
     * Sending Push notification to the mobile devices
     * @param $deviceToken
     * @param $notificationArray
     */
    public function sendPushNotificationP($deviceToken, $notificationArray)
    {
        try {
            $url = 'https://fcm.googleapis.com/fcm/send';

            //data for Android, in notification->content iOS
            $fields = array(
                'to' => $deviceToken,
                'notification' => $notificationArray,
                'data' => $notificationArray['content']
            );

            // Firebase API Key
            // Live Firebase key added -> Krishna
            $headers = array('Authorization:key=AAAArDIk1EA:APA91bExtAwhkEADXapbEuZiPlQ6CcDriHThwVoUtLqhF32pMHYH4vDaNp9Tzg6gE3IlDPtwBde_HazKM8wLsc4TKv8arq17PITw_D6BIIH_UTyQNRGPYHV3EXZ42m8VNBM1C5gfoa8K', 'Content-Type:application/json');
            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarily
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                log_message('error', curl_error($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
        /*echo json_encode($fields);*/
    }

    /**
     * Patients Application
     * Sending multiple push notifications
     * @param $tokensArray
     * @param $notificationArray
     */
    public function sendMultiplePushNotificationP($tokensArray, $notificationArray)
    {
        try {
            $url = 'https://fcm.googleapis.com/fcm/send';

            //data for Android, in notification->content iOS
            $fields = array(
                'registration_ids' => $tokensArray,
                'notification' => $notificationArray,
                'data' => $notificationArray['content']
            );

            // Firebase API Key
            // Live Firebase key added -> Krishna
            $headers = array('Authorization:key=AAAArDIk1EA:APA91bExtAwhkEADXapbEuZiPlQ6CcDriHThwVoUtLqhF32pMHYH4vDaNp9Tzg6gE3IlDPtwBde_HazKM8wLsc4TKv8arq17PITw_D6BIIH_UTyQNRGPYHV3EXZ42m8VNBM1C5gfoa8K', 'Content-Type:application/json');
            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarily
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                log_message('error', curl_error($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
        /*echo json_encode($fields);*/
    }

    /*---------------------------------------- Delivery Boy---------------------------------------------*/

    /**
     * Patients Application
     * Sending Push notification to the mobile devices
     * @param $deviceToken
     * @param $notificationArray
     */
    public function sendPushNotificationB($deviceToken, $notificationArray)
    {
        try {
            $url = 'https://fcm.googleapis.com/fcm/send';

            //data for Android, in notification->content iOS
            $fields = array(
                'to' => $deviceToken,
                'notification' => $notificationArray,
                'data' => $notificationArray['content']
            );

            // Firebase API Key
            // Live Firebase key added -> Krishna
            $headers = array('Authorization:key=AAAAkSoqbEU:APA91bG29qC1PQRJ4EfT9WumwNyQmLv2ifdurpwogC_hQKPmJPQ69crRbHEijVXxeVWKFC_NQ0QavUUa62mmOD7-e08NMjQlQmpBW7IFAaAeHaQ-XceYEz81msKiDo3Hz0kkUkv8uY9O', 'Content-Type:application/json');
            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarily
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                log_message('error', curl_error($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
        /*echo json_encode($fields);*/
    }

    /**
     * Patients Application
     * Sending multiple push notifications
     * @param $tokensArray
     * @param $notificationArray
     */
    public function sendMultiplePushNotificationB($tokensArray, $notificationArray)
    {
        try {
            $url = 'https://fcm.googleapis.com/fcm/send';

            //data for Android, in notification->content iOS
            $fields = array(
                'registration_ids' => $tokensArray,
                'notification' => $notificationArray,
                'data' => $notificationArray['content']
            );

            // Firebase API Key
            // Live Firebase key added -> Krishna
            $headers = array('Authorization:key=AAAAkSoqbEU:APA91bG29qC1PQRJ4EfT9WumwNyQmLv2ifdurpwogC_hQKPmJPQ69crRbHEijVXxeVWKFC_NQ0QavUUa62mmOD7-e08NMjQlQmpBW7IFAaAeHaQ-XceYEz81msKiDo3Hz0kkUkv8uY9O', 'Content-Type:application/json');
            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarily
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                log_message('error', curl_error($ch));
            }
            curl_close($ch);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
        /*echo json_encode($fields);*/
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

    public function removeItemString($str, $item)
    {
        $parts = explode(',', $str);
        while (($i = array_search($item, $parts)) !== false) {
            unset($parts[$i]);
        }
        $data_array = implode(',', $parts);
        return $data_array;
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

    public function maskString($string, $length)
    {
        if (!empty($string) && strlen($string) > $length) {
            $mask_number = str_repeat("*", strlen($string) - $length) . substr($string, -$length);
            return $mask_number;
        } else {
            return $string;
        }
    }

    /**
     * Adding Short URL to database
     * @param $URL
     * @param $app
     * @param $ed
     * @return mixed
     */
    public function generateShortURL($URL, $app, $ed = NULL)
    {
        try {
            // Create connection
            $conn = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, "shrls");
            if ($conn->connect_error) {
                log_message("ERROR", "Connection failed: " . $conn->connect_error);
                return "";
            } else {
                if (!empty($URL)) {
                    $query = "SELECT * FROM surls WHERE url = '$URL'";
                    $result = $conn->query($query);
                    if ($result->num_rows == 0) {
                        $id = $this->getGUID();
                        $cd = date('Y-m-d H:i:s');
                        $cde = $this->URLCodeGenerate($conn);
                        $sql = "INSERT INTO `surls` (`id`, `url`, `cde`, `app`, `cd`, `ed`) VALUES ('$id', '$URL', '$cde', '$app', '$cd', '$ed')";
                        if ($conn->query($sql) === TRUE) {
                            $conn->close();
                            return "https://nushift.org/" . $cde;
                        } else {
                            log_message("Error", $sql . "<br>" . $conn->error);
                            return "";
                        }
                    } else {
                        $row = $result->fetch_assoc();
                        return "https://nushift.org/" . $row['cde'];
                    }
                }
            }
        } catch (Exception $e) {
            log_message("Error", $e->getMessage());
            return "";
        }
    }

    /**
     * Short url generation code
     *
     * REF for developing : https://nomadphp.com/blog/64/creating-a-url-shortener-application-in-php-mysql
     */
    private function URLCodeGenerate($conn)
    {
        reGenerate:
        $urlCode = "";
        $seed = str_split("abcdefghijklmnopqrstuvwxyz" . "0123456789" . "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        shuffle($seed);
        foreach (array_rand($seed, 7) as $k) $urlCode .= $seed[$k];

        if ($conn->connect_error) {
            log_message("ERROR", "Connection failed: " . $conn->connect_error);
            return "";
        } else {
            $sql = "SELECT `cde` FROM `surls` WHERE `sts` IS NULL AND `cde`='$urlCode'";
            $result = $conn->query($sql);
            if ($result->num_rows == 0) {
                return $urlCode;
            } else {
                goto reGenerate;
            }
        }

        /*$text = strtolower($urlCode);
        $bad = array('dirty', 'porn', 'sex', 'adult', 'hot', 'fuck');
        $rep = array('***', '***', '***');
        $newtext = str_replace($bad, $rep, $text);
        if ($text != $newtext) {
            goto reGenerate;
        } else {
            return $urlCode;
        }*/
    }
}