<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Services extends REST_Controller
{
    private $servicesTable = "services";
    private $serviceTypeTable = "serviceTypes";
    private $materialTable = "materials";
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
     * #API_6 || Service Names List
     */
    public function getAllActiveServiceNames_get()
    {
        try {
            $this->db->select("ID,SERVICE_NAME");
            $this->db->from($this->servicesTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("SERVICE_NAME", "ASC");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Active services list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No active services found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_7 || Service Types List
     */
    public function getAllActiveServiceTypes_get()
    {
        try {
            $this->db->select("MATERIAL");
            $this->db->from($this->materialTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("MATERIAL", "ASC");
            $this->db->group_by("MATERIAL");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Active service types list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No active service types found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_15 || Add Service Name
     */
    public function insertName_post()
    {
        try {
            $SERVICE_NAME = strtolower(trim($this->inputData["SERVICE_NAME"]));

            if (empty($SERVICE_NAME)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(SERVICE_NAME)='$SERVICE_NAME'";
            $tempResult = $this->Users_model->check($this->servicesTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Service name already exists"]);
            }

            $saveArray = array(
                'SERVICE_NAME' => strtoupper($SERVICE_NAME),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->servicesTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Service name added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add service name"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_16 || Update Service Name
     */
    public function updateName_post()
    {
        try {
            $SERVICE_NAME = strtolower(trim($this->inputData["SERVICE_NAME"]));
            $ID = strtolower(trim($this->inputData["ID"]));

            if (empty($SERVICE_NAME) || empty($ID)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('ID' => $ID);
            $tempResult = $this->Users_model->check($this->servicesTable, $whereArray);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Service ID not found"]);
            }

            $whereString = "LOWER(SERVICE_NAME)='$SERVICE_NAME' AND ID !='$ID'";
            $tempResult = $this->Users_model->check($this->servicesTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Service name already exists"]);
            }

            $updateArray = array(
                'SERVICE_NAME' => strtoupper($SERVICE_NAME),
                'UPDATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->servicesTable, $whereArray, $updateArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Service name updated"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to update service name"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_21 || Add Service Types
     */
    public function insertType_post()
    {
        try {
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $SERVICE_TYPE = strtolower(trim($this->inputData["SERVICE_TYPE"]));
            $BUSINESS_NAME = trim($this->inputData["BUSINESS_NAME"]);
            $BUSINESS_TYPE = trim($this->inputData["BUSINESS_TYPE"]);
            $BRAND_NAME = trim($this->inputData["BRAND_NAME"]);
            $DOOR_DELIVERY = trim($this->inputData["DOOR_DELIVERY"]);
            $DESCRIPTION = trim($this->inputData["DESCRIPTION"]);
            $WEIGHT = trim($this->inputData["WEIGHT"]);
            $HEIGHT = trim($this->inputData["HEIGHT"]);
            $MRP = trim($this->inputData["MRP"]);
            $PRICE = trim($this->inputData["PRICE"]);

            if (empty($SERVICE_TYPE) || empty($EMAIL_ID)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(SERVICE_TYPE)='$SERVICE_TYPE' AND EMAIL_ID='$EMAIL_ID'";
            $tempResult = $this->Users_model->check($this->serviceTypeTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Service type already exists"]);
            }

            $saveArray = array(
                'SERVICE_TYPE' => strtoupper($SERVICE_TYPE),
                'BUSINESS_NAME' => $BUSINESS_NAME,
                'EMAIL_ID' => $EMAIL_ID,
                'BUSINESS_TYPE' => $BUSINESS_TYPE,
                'BRAND_NAME' => $BRAND_NAME,
                'DOOR_DELIVERY' => $DOOR_DELIVERY,
                'DESCRIPTION' => $DESCRIPTION,
                'WEIGHT' => $WEIGHT,
                'HEIGHT' => $HEIGHT,
                'MRP' => $MRP,
                'PRICE' => $PRICE,
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->serviceTypeTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Service type added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add service type"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_22 || Update Service Types
     */
    public function updateType_post()
    {
        try {
            $BUSINESS_NAME = trim($this->inputData["BUSINESS_NAME"]);
            $BUSINESS_TYPE = trim($this->inputData["BUSINESS_TYPE"]);
            $BRAND_NAME = trim($this->inputData["BRAND_NAME"]);
            $DOOR_DELIVERY = trim($this->inputData["DOOR_DELIVERY"]);
            $DESCRIPTION = trim($this->inputData["DESCRIPTION"]);
            $WEIGHT = trim($this->inputData["WEIGHT"]);
            $HEIGHT = trim($this->inputData["HEIGHT"]);
            $MRP = trim($this->inputData["MRP"]);
            $PRICE = trim($this->inputData["PRICE"]);
            $ID = trim($this->inputData["ID"]);

            if (empty($ID)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('ID' => $ID);
            $tempResult = $this->Users_model->check($this->serviceTypeTable, $whereArray);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Service ID not found"]);
            }

            $updateArray = array(
                'BUSINESS_NAME' => $BUSINESS_NAME,
                'BUSINESS_TYPE' => $BUSINESS_TYPE,
                'BRAND_NAME' => $BRAND_NAME,
                'DOOR_DELIVERY' => $DOOR_DELIVERY,
                'DESCRIPTION' => $DESCRIPTION,
                'WEIGHT' => $WEIGHT,
                'HEIGHT' => $HEIGHT,
                'MRP' => $MRP,
                'PRICE' => $PRICE,
                'UPDATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->serviceTypeTable, $whereArray, $updateArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Service type updated"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to update service type"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_24 || Filter Available Service Types Related Persons
     */
    public function getFilteredServiceTypePersons_get()
    {
        try {
            $SERVICE_TYPE = strtolower(trim($this->get('SERVICE_TYPE')));
            $CITY = strtolower(trim($this->get('CITY')));
            $BUSINESS_TYPE = strtolower(trim($this->get('BUSINESS_TYPE')));

            if (empty($SERVICE_TYPE) || empty($CITY)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            $this->db->select("st.*,u.NAME,u.CITY,u.MOBILE_NO,u.PINCODE_NO,u.ADDRESS");
            $this->db->from("$this->serviceTypeTable st");
            if (!empty($SERVICE_TYPE)) {
                $this->db->where("LOWER(st.SERVICE_TYPE) LIKE '%$SERVICE_TYPE%'");
            }
            if (!empty($CITY)) {
                $this->db->where("LOWER(u.CITY) LIKE '%$CITY%'");
            }
            if (!empty($BUSINESS_TYPE)) {
                $this->db->where("LOWER(st.BUSINESS_TYPE) LIKE '%$BUSINESS_TYPE%'");
            }
            $this->db->join('users u', 'st.EMAIL_ID=u.EMAIL_ID', 'left');
            $this->db->group_by('st.EMAIL_ID');
            $result = $this->db->get();

            if ($result->num_rows() > 0) {
                $resultArray = $result->result_array();
                $this->utility->sendForceJSON(["status" => true, "message" => "Available service type persons list", "data" => $resultArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No available service type persons found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
