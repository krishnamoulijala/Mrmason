<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Staff extends REST_Controller
{
    private $staffTable = "staff";

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
     * @param $SER_PER_SEQ_ID
     * @return mixed
     */
    private function getStaffDetails($SER_PER_SEQ_ID)
    {
        $this->db->select("*");
        $this->db->from($this->staffTable);
        $this->db->where('SER_PER_SEQ_ID', $SER_PER_SEQ_ID);
        return $this->db->get()->row_array();
    }

    public function register_post()
    {
        try {
            $NAME = trim($this->post("NAME"));
            $CONTACT_NO = trim($this->post("CONTACT_NO"));
            $EMAIL_ID = trim($this->post("EMAIL_ID"));
            $CITY = trim($this->post("CITY"));
            $STATE = trim($this->post("STATE"));
            $DISTRICT = trim($this->post("DISTRICT"));
            $PINCODE = trim($this->post("PINCODE"));
            $SERVICE_NAME = trim($this->post("SERVICE_NAME"));
            $EXPERIENCE = trim($this->post("EXPERIENCE"));
            $QUALIFICATION = trim($this->post("QUALIFICATION"));
            $CERTIFICATE = trim($this->post("CERTIFICATE"));
            $WITH_IN_RANGE = trim($this->post("WITH_IN_RANGE"));
            $REGISTERED_BY = trim($this->post("REGISTERED_BY"));


            if (!empty($CONTACT_NO)) {
                if (!is_numeric($CONTACT_NO) && strlen($CONTACT_NO) < 10) {
                    $this->response(['status' => false, 'message' => "Invalid mobile number"], 200);
                }
            }

            if (!empty($EMAIL_ID)) {
                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->response(['status' => false, 'message' => "Invalid email address"], 200);
                }

                $whereArray = array('EMAIL_ID' => $EMAIL_ID);
                $tempResult = $this->Users_model->check($this->staffTable, $whereArray);
                if ($tempResult->num_rows() > 0) {
                    $this->response(["status" => false, "message" => "Service person already registered with email address"], 200);
                }
            }

            reGenerate:
            $SER_PER_SEQ_ID = $this->utility->generateUID(10);
            $whereArray = array('SER_PER_SEQ_ID' => $SER_PER_SEQ_ID);
            $temp = $this->Users_model->check($this->staffTable, $whereArray);
            if ($temp->num_rows() > 0) {
                goto reGenerate;
            }

            $saveArray = array(
                'SER_PER_SEQ_ID' => $SER_PER_SEQ_ID,
                'NAME' => $NAME,
                'CONTACT_NO' => $CONTACT_NO,
                'EMAIL_ID' => $EMAIL_ID,
                'CITY' => $CITY,
                'STATE' => $STATE,
                'DISTRICT' => $DISTRICT,
                'PINCODE' => $PINCODE,
                'SERVICE_NAME' => $SERVICE_NAME,
                'EXPERIENCE' => $EXPERIENCE,
                'QUALIFICATION' => $QUALIFICATION,
                'CERTIFICATE' => $CERTIFICATE,
                'WITH_IN_RANGE' => $WITH_IN_RANGE,
                'REGISTERED_BY' => $REGISTERED_BY,
                'AVAILABLE_STATUS' => 'UNAVAILABLE',
                'STATUS' => 'INACTIVE',
                'REGISTERED_DATETIME ' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->staffTable, $saveArray);
            if ($result) {
                $responseArray = $this->getStaffDetails($SER_PER_SEQ_ID);
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
            $SER_PER_SEQ_ID = trim($this->post("SER_PER_SEQ_ID"));
            $NAME = trim($this->post("NAME"));
            $CONTACT_NO = trim($this->post("CONTACT_NO"));
            $EMAIL_ID = trim($this->post("EMAIL_ID"));
            $CITY = trim($this->post("CITY"));
            $STATE = trim($this->post("STATE"));
            $DISTRICT = trim($this->post("DISTRICT"));
            $PINCODE = trim($this->post("PINCODE"));
            $SERVICE_NAME = trim($this->post("SERVICE_NAME"));
            $EXPERIENCE = trim($this->post("EXPERIENCE"));
            $QUALIFICATION = trim($this->post("QUALIFICATION"));
            $CERTIFICATE = trim($this->post("CERTIFICATE"));
            $WITH_IN_RANGE = trim($this->post("WITH_IN_RANGE"));
            $REGISTERED_BY = trim($this->post("REGISTERED_BY"));

            $whereArray = array('SER_PER_SEQ_ID' => $SER_PER_SEQ_ID);
            $temp = $this->Users_model->check($this->staffTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->response(["status" => false, "message" => "User not found"], 200);
            }

            if (!empty($CONTACT_NO)) {
                if (!is_numeric($CONTACT_NO) && strlen($CONTACT_NO) < 10) {
                    $this->response(['status' => false, 'message' => "Invalid mobile number"], 200);
                }
            }

            if (!empty($EMAIL_ID)) {
                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->response(['status' => false, 'message' => "Invalid email address"], 200);
                }

                $whereString = "EMAIL_ID='$EMAIL_ID' AND SER_PER_SEQ_ID !='$SER_PER_SEQ_ID'";
                $tempResult = $this->Users_model->check($this->staffTable, $whereString);
                if ($tempResult->num_rows() > 0) {
                    $this->response(["status" => false, "message" => "Service person already registered with email address"], 200);
                }
            }

            $saveArray = array(
                'NAME' => $NAME,
                'CONTACT_NO' => $CONTACT_NO,
                'EMAIL_ID' => $EMAIL_ID,
                'CITY' => $CITY,
                'STATE' => $STATE,
                'DISTRICT' => $DISTRICT,
                'PINCODE' => $PINCODE,
                'SERVICE_NAME' => $SERVICE_NAME,
                'EXPERIENCE' => $EXPERIENCE,
                'QUALIFICATION' => $QUALIFICATION,
                'CERTIFICATE' => $CERTIFICATE,
                'WITH_IN_RANGE' => $WITH_IN_RANGE,
                'REGISTERED_BY' => $REGISTERED_BY,
                'UPDATE_DATETIME ' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->staffTable, $whereArray, $saveArray);
            if ($result) {
                $responseArray = $this->getStaffDetails($SER_PER_SEQ_ID);
                $this->response(["status" => true, "message" => "Details updated", "data" => $responseArray], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to update details"], 200);
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
            $temp = $this->Users_model->check($this->staffTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->response(["status" => false, "message" => "Service person not found"], 200);
            }

            if (!in_array($STATUS, array("ACTIVE", "INACTIVE"))) {
                $this->response(["status" => false, "message" => "Invalid input from STATUS"], 200);
            }

            $updateArray = array(
                'STATUS' => $STATUS,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->staffTable, $whereArray, $updateArray);
            if ($result) {
                $this->response(["status" => true, "message" => "Service person status changed"], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to change the service person status"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function updateAvailability_post()
    {
        try {
            $SER_PER_SEQ_ID = trim($this->post('SER_PER_SEQ_ID'));
            $AVAILABLE_STATUS = trim($this->post('AVAILABLE_STATUS'));

            if (empty($SER_PER_SEQ_ID) || empty($AVAILABLE_STATUS)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            if (!in_array($AVAILABLE_STATUS, array("AVAILABLE", "UNAVAILABLE"))) {
                $this->response(["status" => false, "message" => "Invalid input in AVAILABLE_STATUS"], 200);
            }

            $whereArray = array('SER_PER_SEQ_ID' => $SER_PER_SEQ_ID);
            $temp = $this->Users_model->check($this->staffTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->response(["status" => false, "message" => "Service person not found"], 200);
            }

            $updateArray = array(
                'AVAILABLE_STATUS' => $AVAILABLE_STATUS,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->staffTable, $whereArray, $updateArray);
            if ($result) {
                $responseArray = $this->getStaffDetails($SER_PER_SEQ_ID);
                $this->response(["status" => true, "message" => "Service person available status updated", "data" => $responseArray], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to change the service person status"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function getFilteredServicePersons_get()
    {
        try {
            $SERVICE_NAME = trim($this->get('SERVICE_NAME'));
            $CITY = strtolower(trim($this->get('CITY')));

            if (empty($SERVICE_NAME)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            $this->db->select("*");
            $this->db->from($this->staffTable);
            $this->db->where('AVAILABLE_STATUS', "AVAILABLE");
            $this->db->where('SERVICE_NAME', $SERVICE_NAME);
            if (!empty($CITY)) {
                $this->db->where("LOWER(CITY) LIKE '%$CITY%'");
            }
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->response(["status" => true, "message" => "Available service persons list", "data" => $result->result_array()], 200);
            } else {
                $this->response(["status" => false, "message" => "No available service persons found"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function getAvailableServicePersons_get()
    {
        try {
            $this->db->select("*");
            $this->db->from($this->staffTable);
            $this->db->where('AVAILABLE_STATUS', "AVAILABLE");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->response(["status" => true, "message" => "All Available service persons list", "data" => $result->result_array()], 200);
            } else {
                $this->response(["status" => false, "message" => "No available service persons found"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function getDetails_get()
    {
        try {
            $SER_PER_SEQ_ID = trim($this->get('SER_PER_SEQ_ID'));

            if (empty($SER_PER_SEQ_ID)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            $responseArray = $this->getStaffDetails($SER_PER_SEQ_ID);
            if (!empty($responseArray)) {
                $this->response(["status" => true, "message" => "Details of service person", "data" => $responseArray], 200);
            } else {
                $this->response(["status" => false, "message" => "Service person not found"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
