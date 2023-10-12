<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class ServiceRequest extends REST_Controller
{
    private $serviceRequestTable = "service_request_details";
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
     * #API_63 || Filter on service request details table
     */
    public function getFilteredReport_get()
    {
        try {
            $LOCATION = strtolower(trim($this->get("LOCATION")));
            $SERVICE_NAME = strtolower(trim($this->get("SERVICE_NAME")));
            $STATUS = strtolower(trim($this->get("STATUS")));

            $whereString = "";
            if (!empty($LOCATION)) {
                $whereString .= "LOWER(`REQ_PINCODE`) LIKE '%$LOCATION%'";
            }
            if (!empty($SERVICE_NAME)) {
                $whereString .= " AND LOWER(`SERVICE_NAME`) LIKE '%$SERVICE_NAME%'";
            }
            if (!empty($STATUS)) {
                $whereString .= " AND LOWER(`STATUS`) LIKE '%$STATUS%'";
            }

            $this->db->select("*");
            $this->db->from($this->serviceRequestTable);
            if (!empty($whereString)) {
                $this->db->where($whereString);
            }
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Service requests list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No filtered data found"]);
            }

        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_64 || Insert service request details
     */
    public function insert_post()
    {
        try {
            $SERVICE_NAME = trim($this->inputData["SERVICE_NAME"]);
            $REQUESTED_BY = trim($this->inputData["REQUESTED_BY"]);
            $DESCRIPTION = trim($this->inputData["DESCRIPTION"]);
            $REQ_PINCODE = trim($this->inputData["REQ_PINCODE"]);
            $REMARKS = trim($this->inputData["REMARKS"]);

            $REQUEST_ID = "MMKE" . time() . $this->utility->generateUID(4);

            $saveArray = array(
                'SERVICE_NAME' => $SERVICE_NAME,
                'REQUESTED_BY' => $REQUESTED_BY,
                'DESCRIPTION' => $DESCRIPTION,
                'REQ_PINCODE' => $REQ_PINCODE,
                'STATUS' => "NEW",
                'REMARKS' => $REMARKS,
                'REQUEST_ID' => $REQUEST_ID,
                'SERVICE_DATE' => date('Y-m-d'),
                'SERVICE_REQUEST_DATE' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->serviceRequestTable, $saveArray);
            if ($result) {
                $this->db->select("*");
                $this->db->from($this->serviceRequestTable);
                $this->db->where("REQUEST_ID", $REQUEST_ID);
                $result = $this->db->get()->row_array();
                $this->utility->sendForceJSON(["status" => true, "message" => "Service request inserted", "data" => $result]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to insert service request"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
