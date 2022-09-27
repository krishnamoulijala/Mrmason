<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller
{
    private $usersTable = "users";
    private $inputData = "";

    /**
     * Loading the required classes
     * Services constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
        $this->load->library('utility');
        $this->inputData = json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Index method called first if correct method not given
     * returns a json response
     */
    public function index_get()
    {
        $this->utility->sendForceJSON(['status' => false, 'message' => 'Invalid end point']);
    }

    /**
     * Helper method for fetching details
     * @param $BOD_SEQ_NO
     * @return mixed
     */
    private function getUserDetails($BOD_SEQ_NO)
    {
        $this->db->select("*");
        $this->db->from($this->usersTable);
        $this->db->where('BOD_SEQ_NO', $BOD_SEQ_NO);
        return $this->db->get()->row_array();
    }

    public function register_post()
    {
        try {
            $NAME = trim($this->inputData["NAME"]);
            $BUSINESS_NAME = trim($this->inputData["BUSINESS_NAME"]);
            $MOBILE_NO = trim($this->inputData["MOBILE_NO"]);
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $ADDRESS = trim($this->inputData["ADDRESS"]);
            $CITY = trim($this->inputData["CITY"]);
            $STATE = trim($this->inputData["STATE"]);
            $DISTRICT = trim($this->inputData["DISTRICT"]);
            $PINCODE_NO = trim($this->inputData["PINCODE_NO"]);
            $USER_TYPE = trim($this->inputData["USER_TYPE"]);
            $PASSWORD = trim($this->inputData["PASSWORD"]);
            $REFERENCE_ID = trim($this->inputData["REFERENCE_ID"]);


            if (!empty($MOBILE_NO)) {
                if (!is_numeric($MOBILE_NO) && strlen($MOBILE_NO) < 10) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid mobile number"]);
                }
            }

            if (!empty($EMAIL_ID)) {

                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid email address"]);
                }

                $whereArray = array('EMAIL_ID' => $EMAIL_ID);
                $tempResult = $this->Users_model->check($this->usersTable, $whereArray);
                if ($tempResult->num_rows() > 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "User already registered with email address"]);
                }
            }

            reGenerate:
            $BOD_SEQ_NO = $this->utility->generateUID(10);
            $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() > 0) {
                goto reGenerate;
            }

            $saveArray = array(
                'BOD_SEQ_NO' => $BOD_SEQ_NO,
                'NAME' => $NAME,
                'BUSINESS_NAME' => $BUSINESS_NAME,
                'MOBILE_NO' => $MOBILE_NO,
                'EMAIL_ID' => $EMAIL_ID,
                'ADDRESS' => $ADDRESS,
                'CITY' => $CITY,
                'STATE' => $STATE,
                'DISTRICT' => $DISTRICT,
                'PINCODE_NO' => $PINCODE_NO,
                'USER_TYPE' => $USER_TYPE,
                'PASSWORD' => $PASSWORD,
                'VERIFIED' => 'NO',
                'STATUS' => 'INACTIVE',
                'REGISTRATION_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->usersTable, $saveArray);
            if ($result) {
                $responseArray = $this->getUserDetails($BOD_SEQ_NO);
                unset($responseArray['PASSWORD']);
                $this->utility->sendForceJSON(["status" => true, "message" => "Registration successful", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to register user"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function update_post()
    {
        try {
            $BOD_SEQ_NO = trim($this->inputData["BOD_SEQ_NO"]);
            $NAME = trim($this->inputData["NAME"]);
            $BUSINESS_NAME = trim($this->inputData["BUSINESS_NAME"]);
            $MOBILE_NO = trim($this->inputData["MOBILE_NO"]);
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $ADDRESS = trim($this->inputData["ADDRESS"]);
            $CITY = trim($this->inputData["CITY"]);
            $STATE = trim($this->inputData["STATE"]);
            $DISTRICT = trim($this->inputData["DISTRICT"]);
            $PINCODE_NO = trim($this->inputData["PINCODE_NO"]);

            $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }

            if (!empty($MOBILE_NO)) {
                if (!is_numeric($MOBILE_NO) && strlen($MOBILE_NO) < 10) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid mobile number"]);
                }
            }

            if (!empty($EMAIL_ID)) {

                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid email address"]);
                }
                $whereString = "EMAIL_ID='$EMAIL_ID' AND BOD_SEQ_NO !='$BOD_SEQ_NO'";
                $tempResult = $this->Users_model->check($this->usersTable, $whereString);
                if ($tempResult->num_rows() > 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "User already registered with email address"]);
                }
            }

            $updateArray = array(
                'NAME' => $NAME,
                'BUSINESS_NAME' => $BUSINESS_NAME,
                'MOBILE_NO' => $MOBILE_NO,
                'EMAIL_ID' => $EMAIL_ID,
                'ADDRESS' => $ADDRESS,
                'CITY' => $CITY,
                'STATE' => $STATE,
                'DISTRICT' => $DISTRICT,
                'PINCODE_NO' => $PINCODE_NO,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);
            if ($result) {
                $responseArray = $this->getUserDetails($BOD_SEQ_NO);
                unset($responseArray['PASSWORD']);
                $this->utility->sendForceJSON(["status" => true, "message" => "Details updated", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to update user details"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function login_post()
    {
        try {

            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $PASSWORD = trim($this->inputData["PASSWORD"]);

            if (empty($EMAIL_ID) || empty($PASSWORD)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('EMAIL_ID' => $EMAIL_ID);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $whereArray = array('MOBILE_NO' => $EMAIL_ID);
                $temp = $this->Users_model->check($this->usersTable, $whereArray);
                if ($temp->num_rows() == 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
                }
            }

            $result = $temp->row_array();

            if ($result['PASSWORD'] == $PASSWORD) {
                $responseArray = $this->getUserDetails($result['BOD_SEQ_NO']);
                unset($responseArray['PASSWORD']);
                $this->utility->sendForceJSON(["status" => true, "message" => "Login successful", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Incorrect password"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function changeStatus_post()
    {
        try {
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $STATUS = trim($this->inputData["STATUS"]);

            if (empty($EMAIL_ID) || empty($STATUS)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            if (!$this->utility->validEmail($EMAIL_ID)) {
                $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid email address"]);
            }

            $whereArray = array('EMAIL_ID' => $EMAIL_ID);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }

            if (!in_array($STATUS, array("ACTIVE", "INACTIVE"))) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Invalid input from STATUS"]);
            }

            $updateArray = array(
                'STATUS' => $STATUS,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "User status changed"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the user status"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function changePassword_post()
    {
        try {
            $BOD_SEQ_NO = trim($this->inputData["BOD_SEQ_NO"]);
            $NEW_PASSWORD = trim($this->inputData["NEW_PASSWORD"]);

            if (empty($BOD_SEQ_NO) || empty($NEW_PASSWORD)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }
            $updateArray = array(
                'PASSWORD' => $NEW_PASSWORD,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);

            if ($result) {
                $responseArray = $this->getUserDetails($BOD_SEQ_NO);
                unset($responseArray['PASSWORD']);
                $this->utility->sendForceJSON(["status" => true, "message" => "Password changed", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the password"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
