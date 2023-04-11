<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Admin extends REST_Controller
{
    private $sizesTable = "sizes";
    private $brandsTable = "brands";
    private $materialTable = "materials";
    private $heightTable = "heights";
    private $bTypesTable = "btypes";
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
     * #API_25 || Add Size Number And Brand
     */
    public function insertSizeBrand_post()
    {
        try {
            $SIZE = trim($this->inputData["SIZE"]);
            $BRAND = strtolower(trim($this->inputData["BRAND"]));
            $result = false;

            if (!empty($BRAND)) {
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
            }

            if (!empty($SIZE)) {
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
            }
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add"]);
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
     * #API_39 || Add Height
     */
    public function insertHeight_post()
    {
        try {
            $HEIGHT = strtolower(trim($this->inputData["HEIGHT"]));

            if (empty($HEIGHT)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(HEIGHT)='$HEIGHT'";
            $tempResult = $this->Users_model->check($this->heightTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Brand already exists"]);
            }

            $saveArray = array(
                'HEIGHT' => strtoupper($HEIGHT),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->heightTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Height added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add height"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_42 || Add Business Type
     */
    public function insertBusinessType_post()
    {
        try {
            $BTYPE = strtolower(trim($this->inputData["BTYPE"]));

            if (empty($BTYPE)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(BTYPE)='$BTYPE'";
            $tempResult = $this->Users_model->check($this->bTypesTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Business Type already exists"]);
            }

            $saveArray = array(
                'BTYPE' => strtoupper($BTYPE),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->bTypesTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Business Type added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add business type"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_40 || Delete Height
     */
    public function deleteHeight_post()
    {
        try {
            $HEIGHT = strtolower(trim($this->inputData["HEIGHT"]));

            if (empty($HEIGHT)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(HEIGHT)='$HEIGHT'";
            $tempResult = $this->Users_model->check($this->heightTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Height not found"]);
            }

            $result = $this->Users_model->delete($this->heightTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Height deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete height"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_43 || Delete Business Type
     */
    public function deleteBusinessType_post()
    {
        try {
            $BTYPE = strtolower(trim($this->inputData["BTYPE"]));

            if (empty($BTYPE)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(BTYPE)='$BTYPE'";
            $tempResult = $this->Users_model->check($this->bTypesTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Business Type not found"]);
            }

            $result = $this->Users_model->delete($this->bTypesTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Business Type deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete business type"]);
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

    /**
     * #API_41 || Get Heights
     */
    public function getHeights_get()
    {
        try {
            $this->db->select("HEIGHT");
            $this->db->from($this->heightTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("HEIGHT", "ASC");
            $this->db->group_by("HEIGHT");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Heights list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No heights found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_44 || Get Business Types
     */
    public function getBusinessTypes_get()
    {
        try {
            $this->db->select("BTYPE");
            $this->db->from($this->bTypesTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("BTYPE", "ASC");
            $this->db->group_by("BTYPE");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Business Types list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No business types found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
