<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Staff extends REST_Controller
{
    private $staffTable = "staff";
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

    /**
     * #API_8 || Register service persons
     */
    public function register_post()
    {
        try {
            $NAME = trim($this->inputData["NAME"]);
            $CONTACT_NO = trim($this->inputData["CONTACT_NO"]);
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $CITY = trim($this->inputData["CITY"]);
            $STATE = trim($this->inputData["STATE"]);
            $DISTRICT = trim($this->inputData["DISTRICT"]);
            $PINCODE = trim($this->inputData["PINCODE"]);
            $SERVICE_NAME = trim($this->inputData["SERVICE_NAME"]);
            $EXPERIENCE = trim($this->inputData["EXPERIENCE"]);
            $QUALIFICATION = trim($this->inputData["QUALIFICATION"]);
            $CERTIFICATE = trim($this->inputData["CERTIFICATE"]);
            $WITH_IN_RANGE = trim($this->inputData["WITH_IN_RANGE"]);
            $REGISTERED_BY = trim($this->inputData["REGISTERED_BY"]);


            if (!empty($CONTACT_NO)) {
                if (!is_numeric($CONTACT_NO) && strlen($CONTACT_NO) < 10) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid mobile number"]);
                }
            }

            if (!empty($EMAIL_ID)) {
                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid email address"]);
                }

                $whereArray = array('EMAIL_ID' => $EMAIL_ID);
                $tempResult = $this->Users_model->check($this->staffTable, $whereArray);
                if ($tempResult->num_rows() > 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "Service person already registered with email address"]);
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
                $this->utility->sendForceJSON(["status" => true, "message" => "Registration successful", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to register user"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_9 || Update service persons
     */
    public function update_post()
    {
        try {
            $SER_PER_SEQ_ID = trim($this->inputData["SER_PER_SEQ_ID"]);
            $NAME = trim($this->inputData["NAME"]);
            $CONTACT_NO = trim($this->inputData["CONTACT_NO"]);
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $CITY = trim($this->inputData["CITY"]);
            $STATE = trim($this->inputData["STATE"]);
            $DISTRICT = trim($this->inputData["DISTRICT"]);
            $PINCODE = trim($this->inputData["PINCODE"]);
            $SERVICE_NAME = trim($this->inputData["SERVICE_NAME"]);
            $EXPERIENCE = trim($this->inputData["EXPERIENCE"]);
            $QUALIFICATION = trim($this->inputData["QUALIFICATION"]);
            $CERTIFICATE = trim($this->inputData["CERTIFICATE"]);
            $WITH_IN_RANGE = trim($this->inputData["WITH_IN_RANGE"]);
            $REGISTERED_BY = trim($this->inputData["REGISTERED_BY"]);

            $whereArray = array('SER_PER_SEQ_ID' => $SER_PER_SEQ_ID);
            $temp = $this->Users_model->check($this->staffTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }

            if (!empty($CONTACT_NO)) {
                if (!is_numeric($CONTACT_NO) && strlen($CONTACT_NO) < 10) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid mobile number"]);
                }
            }

            if (!empty($EMAIL_ID)) {
                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid email address"]);
                }

                $whereString = "EMAIL_ID='$EMAIL_ID' AND SER_PER_SEQ_ID !='$SER_PER_SEQ_ID'";
                $tempResult = $this->Users_model->check($this->staffTable, $whereString);
                if ($tempResult->num_rows() > 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "Service person already registered with email address"]);
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
                $this->utility->sendForceJSON(["status" => true, "message" => "Details updated", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to update details"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_10 || Change the status to active and inactive
     */
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
            $temp = $this->Users_model->check($this->staffTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Service person not found"]);
            }

            if (!in_array($STATUS, array("ACTIVE", "INACTIVE"))) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Invalid input from STATUS"]);
            }

            $updateArray = array(
                'STATUS' => $STATUS,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->staffTable, $whereArray, $updateArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Service person status changed"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the service person status"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_11 || Update the availability
     */
    public function updateAvailability_post()
    {
        try {
            $SER_PER_SEQ_ID = trim($this->inputData['SER_PER_SEQ_ID']);
            $AVAILABLE_STATUS = trim($this->inputData['AVAILABLE_STATUS']);

            if (empty($SER_PER_SEQ_ID) || empty($AVAILABLE_STATUS)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            if (!in_array($AVAILABLE_STATUS, array("AVAILABLE", "UNAVAILABLE"))) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Invalid input in AVAILABLE_STATUS"]);
            }

            $whereArray = array('SER_PER_SEQ_ID' => $SER_PER_SEQ_ID);
            $temp = $this->Users_model->check($this->staffTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Service person not found"]);
            }

            $updateArray = array(
                'AVAILABLE_STATUS' => $AVAILABLE_STATUS,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->staffTable, $whereArray, $updateArray);
            if ($result) {
                $responseArray = $this->getStaffDetails($SER_PER_SEQ_ID);
                $this->utility->sendForceJSON(["status" => true, "message" => "Service person available status updated", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the service person status"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_12 || Filter Available Service Persons
     */
    public function getFilteredServicePersons_get()
    {
        try {
            $SERVICE_NAME = strtolower(trim($this->get('SERVICE_NAME')));
            $CITY = strtolower(trim($this->get('CITY')));

            if (empty($SERVICE_NAME)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $this->db->select("*");
            $this->db->from($this->staffTable);
            $this->db->where("LOWER(SERVICE_NAME) LIKE '%$SERVICE_NAME%'");
            if (!empty($CITY)) {
                $this->db->where("LOWER(CITY) LIKE '%$CITY%'");
            }
            $result = $this->db->get();

            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Available service persons list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No available service persons found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_13 || Get All Available Service Persons
     */
    public function getAvailableServicePersons_get()
    {
        try {
            $this->db->select("*");
            $this->db->from($this->staffTable);
            $this->db->where('AVAILABLE_STATUS', "AVAILABLE");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "All Available service persons list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No available service persons found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_14 || Get Service Person Details
     */
    public function getDetails_get()
    {
        try {
            $SER_PER_SEQ_ID = trim($this->get('SER_PER_SEQ_ID'));

            if (empty($SER_PER_SEQ_ID)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $responseArray = $this->getStaffDetails($SER_PER_SEQ_ID);
            if (!empty($responseArray)) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Details of service person", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Service person not found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
