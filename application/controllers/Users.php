<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller
{
    private $usersTable = "users";

    /**
     * Loading the required classes
     * Services constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
        $this->load->library('utility');
    }

    /**
     * Index method called first if correct method not given
     * returns a json response
     */
    public function index_get()
    {
        $this->response(['status' => false, 'message' => 'Invalid end point'], 200);
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
            $NAME = trim($this->post("NAME"));
            $BUSINESS_NAME = trim($this->post("BUSINESS_NAME"));
            $MOBILE_NO = trim($this->post("MOBILE_NO"));
            $EMAIL_ID = trim($this->post("EMAIL_ID"));
            $ADDRESS = trim($this->post("ADDRESS"));
            $CITY = trim($this->post("CITY"));
            $STATE = trim($this->post("STATE"));
            $DISTRICT = trim($this->post("DISTRICT"));
            $PINCODE_NO = trim($this->post("PINCODE_NO"));
            $USER_TYPE = trim($this->post("USER_TYPE"));
            $PASSWORD = trim($this->post("PASSWORD"));
            $REFERENCE_ID = trim($this->post("REFERENCE_ID"));


            if (!empty($MOBILE_NO)) {
                if (!is_numeric($MOBILE_NO) && strlen($MOBILE_NO) < 10) {
                    $this->response(['status' => false, 'message' => "Invalid mobile number"], 200);
                }
            }

            if (!empty($EMAIL_ID)) {

                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->response(['status' => false, 'message' => "Invalid email address"], 200);
                }

                $whereArray = array('EMAIL_ID' => $EMAIL_ID);
                $tempResult = $this->Users_model->check($this->usersTable, $whereArray);
                if ($tempResult->num_rows() > 0) {
                    $this->response(["status" => false, "message" => "User already registered with email address"], 200);
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
                $this->response(["status" => true, "message" => "Registration successful", "data" => $responseArray], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to register user"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function update_post()
    {
        try {
            $BOD_SEQ_NO = trim($this->post("BOD_SEQ_NO"));
            $NAME = trim($this->post("NAME"));
            $BUSINESS_NAME = trim($this->post("BUSINESS_NAME"));
            $MOBILE_NO = trim($this->post("MOBILE_NO"));
            $EMAIL_ID = trim($this->post("EMAIL_ID"));
            $ADDRESS = trim($this->post("ADDRESS"));
            $CITY = trim($this->post("CITY"));
            $STATE = trim($this->post("STATE"));
            $DISTRICT = trim($this->post("DISTRICT"));
            $PINCODE_NO = trim($this->post("PINCODE_NO"));

            $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->response(["status" => false, "message" => "User not found"], 200);
            }

            if (!empty($MOBILE_NO)) {
                if (!is_numeric($MOBILE_NO) && strlen($MOBILE_NO) < 10) {
                    $this->response(['status' => false, 'message' => "Invalid mobile number"], 200);
                }
            }

            if (!empty($EMAIL_ID)) {

                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->response(['status' => false, 'message' => "Invalid email address"], 200);
                }
                $whereString = "EMAIL_ID='$EMAIL_ID' AND BOD_SEQ_NO !='$BOD_SEQ_NO'";
                $tempResult = $this->Users_model->check($this->usersTable, $whereString);
                if ($tempResult->num_rows() > 0) {
                    $this->response(["status" => false, "message" => "User already registered with email address"], 200);
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
                $this->response(["status" => true, "message" => "Details updated", "data" => $responseArray], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to update user details"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function login_post()
    {
        try {
            $EMAIL_ID = trim($this->post("EMAIL_ID"));
            $PASSWORD = trim($this->post("PASSWORD"));

            if (empty($EMAIL_ID) || empty($PASSWORD)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            $whereArray = array('EMAIL_ID' => $EMAIL_ID);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $whereArray = array('MOBILE_NO' => $EMAIL_ID);
                $temp = $this->Users_model->check($this->usersTable, $whereArray);
                if ($temp->num_rows() == 0) {
                    $this->response(["status" => false, "message" => "User not found"], 200);
                }
            }

            $result = $temp->row_array();

            if ($result['PASSWORD'] == $PASSWORD) {
                $responseArray = $this->getUserDetails($result['BOD_SEQ_NO']);
                unset($responseArray['PASSWORD']);
                $this->response(["status" => true, "message" => "Login successful", "data" => $responseArray], 200);
            } else {
                $this->response(["status" => false, "message" => "Incorrect password"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function changeStatus_post()
    {
        try {
            $EMAIL_ID = trim($this->post("EMAIL_ID"));
            $STATUS = trim($this->post("STATUS"));

            if (empty($EMAIL_ID) || empty($STATUS)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            if (!$this->utility->validEmail($EMAIL_ID)) {
                $this->response(['status' => false, 'message' => "Invalid email address"], 200);
            }

            $whereArray = array('EMAIL_ID' => $EMAIL_ID);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->response(["status" => false, "message" => "User not found"], 200);
            }

            if (!in_array($STATUS, array("ACTIVE", "INACTIVE"))) {
                $this->response(["status" => false, "message" => "Invalid input from STATUS"], 200);
            }

            $updateArray = array(
                'STATUS' => $STATUS,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);
            if ($result) {
                $this->response(["status" => true, "message" => "User status changed"], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to change the user status"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function changePassword_post()
    {
        try {
            $BOD_SEQ_NO = trim($this->post("BOD_SEQ_NO"));
            $NEW_PASSWORD = trim($this->post("NEW_PASSWORD"));

            if (empty($BOD_SEQ_NO) || empty($NEW_PASSWORD)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->response(["status" => false, "message" => "User not found"], 200);
            }
            $updateArray = array(
                'PASSWORD' => $NEW_PASSWORD,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);

            if ($result) {
                $responseArray = $this->getUserDetails($BOD_SEQ_NO);
                unset($responseArray['PASSWORD']);
                $this->response(["status" => true, "message" => "Password changed", "data" => $responseArray], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to change the password"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
