<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Services extends REST_Controller
{
    private $servicesTable = "services";
    private $serviceTypeTable = "serviceTypes";
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
            $this->db->select("ID,SERVICE_TYPE");
            $this->db->from($this->serviceTypeTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("SERVICE_TYPE", "ASC");
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
}
