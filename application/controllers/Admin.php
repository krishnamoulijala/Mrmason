<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Admin extends REST_Controller
{
    private $sizesTable = "sizes";
    private $brandsTable = "brands";
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
     * #API_25 || Add Size Number
     */
    public function insertSize_post()
    {
        try {
            $SIZE = trim($this->inputData["SIZE"]);

            if (empty($SIZE)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "SIZE='$SIZE'";
            $tempResult = $this->Users_model->check($this->sizesTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Size already exists"]);
            }

            $saveArray = array(
                'SIZE' => strtoupper($SIZE),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->sizesTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Size added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add size"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_26 || Delete Size Number
     */
    public function deleteSize_post()
    {
        try {
            $SIZE = trim($this->inputData["SIZE"]);

            if (empty($SIZE)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('SIZE' => $SIZE);
            $tempResult = $this->Users_model->check($this->sizesTable, $whereArray);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Size not found"]);
            }

            $result = $this->Users_model->delete($this->sizesTable, $whereArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Size deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete size"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_27 || Add Brand Name
     */
    public function insertBrand_post()
    {
        try {
            $BRAND = strtolower(trim($this->inputData["BRAND"]));

            if (empty($BRAND)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(BRAND)='$BRAND'";
            $tempResult = $this->Users_model->check($this->brandsTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Brand already exists"]);
            }

            $saveArray = array(
                'BRAND' => strtoupper($BRAND),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->brandsTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Brand added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add brand"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_28 || Delete Brand Name
     */
    public function deleteBrand_post()
    {
        try {
            $BRAND = strtolower(trim($this->inputData["BRAND"]));

            if (empty($BRAND)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(BRAND)='$BRAND'";
            $tempResult = $this->Users_model->check($this->brandsTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Brand not found"]);
            }

            $result = $this->Users_model->delete($this->brandsTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Brand deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete brand"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_32 || Add Material
     */
    public function insertMaterial_post()
    {
        try {
            $MATERIAL = strtolower(trim($this->inputData["MATERIAL"]));

            if (empty($MATERIAL)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(MATERIAL)='$MATERIAL'";
            $tempResult = $this->Users_model->check($this->materialTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Brand already exists"]);
            }

            $saveArray = array(
                'MATERIAL' => strtoupper($MATERIAL),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->materialTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Brand added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add brand"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_33 || Delete Material
     */
    public function deleteMaterial_post()
    {
        try {
            $MATERIAL = strtolower(trim($this->inputData["MATERIAL"]));

            if (empty($MATERIAL)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(MATERIAL)='$MATERIAL'";
            $tempResult = $this->Users_model->check($this->materialTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Brand not found"]);
            }

            $result = $this->Users_model->delete($this->materialTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Brand deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete brand"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_29 || Get Sizes
     */
    public function getSizes_get()
    {
        try {
            $this->db->select("SIZE");
            $this->db->from($this->sizesTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("SIZE", "ASC");
            $this->db->group_by("SIZE");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Sizes list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No sizes found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_30 || Get Brand Names
     */
    public function getBrands_get()
    {
        try {
            $this->db->select("BRAND");
            $this->db->from($this->brandsTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("BRAND", "ASC");
            $this->db->group_by("BRAND");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Brands list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No brands found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_34 || Get Materials
     */
    public function getMaterials_get()
    {
        try {
            $this->db->select("MATERIAL");
            $this->db->from($this->materialTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("MATERIAL", "ASC");
            $this->db->group_by("MATERIAL");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Materials list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No materials found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
