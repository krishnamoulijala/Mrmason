<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Services extends REST_Controller
{
    private $servicesTable = "services";
    private $serviceTypeTable = "serviceTypes";

    /**
     * Loading the required classes
     * Services constructor.
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Index method called first if correct method not given
     * returns a json response
     */
    public function index_get()
    {
        $this->response(['status' => false, 'message' => 'Invalid end point'], 200);
    }

    public function insertName_post()
    {
        try {
            $SERVICE_NAME = strtolower(trim($this->post("SERVICE_NAME")));

            if (empty($SERVICE_NAME)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            $whereString = "LOWER(SERVICE_NAME)='$SERVICE_NAME'";
            $tempResult = $this->Users_model->check($this->servicesTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->response(["status" => false, "message" => "Service name already exists"], 200);
            }

            $saveArray = array(
                'SERVICE_NAME' => strtoupper($SERVICE_NAME),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->servicesTable, $saveArray);
            if ($result) {
                $this->response(["status" => true, "message" => "Service name added"], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to add service name"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function updateName_post()
    {
        try {
            $SERVICE_NAME = strtolower(trim($this->post("SERVICE_NAME")));
            $ID = strtolower(trim($this->post("ID")));

            if (empty($SERVICE_NAME) || empty($ID)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }

            $whereArray = array('ID' => $ID);
            $tempResult = $this->Users_model->check($this->servicesTable, $whereArray);
            if ($tempResult->num_rows() > 0) {
                $this->response(["status" => false, "message" => "Service ID not found"], 200);
            }

            $whereString = "LOWER(SERVICE_NAME)='$SERVICE_NAME' AND ID !='$ID'";
            $tempResult = $this->Users_model->check($this->servicesTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->response(["status" => false, "message" => "Service name already exists"], 200);
            }

            $updateArray = array(
                'SERVICE_NAME' => strtoupper($SERVICE_NAME),
                'UPDATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->servicesTable, $whereArray, $updateArray);
            if ($result) {
                $this->response(["status" => true, "message" => "Service name updated"], 200);
            } else {
                $this->response(["status" => false, "message" => "Failed to update service name"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function getAllActiveServiceName_get()
    {
        try {
            $this->db->select("ID,SERVICE_NAME");
            $this->db->from($this->servicesTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("SERVICE_NAME", "ASC");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->response(["status" => true, "message" => "Active services list", "data" => $result->result_array()], 200);
            } else {
                $this->response(["status" => false, "message" => "No active services found"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function getAllActiveServiceTypes_get()
    {
        try {
            $this->db->select("ID,SERVICE_TYPE");
            $this->db->from($this->serviceTypeTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("SERVICE_TYPE", "ASC");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->response(["status" => true, "message" => "Active service types list", "data" => $result->result_array()], 200);
            } else {
                $this->response(["status" => false, "message" => "No active service types found"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
