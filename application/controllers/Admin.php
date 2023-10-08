<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Admin extends REST_Controller
{
    private $sizesTable = "sizes";
    private $brandsTable = "brands";
    private $materialTable = "materials";
    private $shapeTable = "shapes";
    private $subCatTable = "subCats";
    private $perimeterTable = "perimeters";
    private $lengthTable = "lengths";
    private $weightTable = "weights";
    private $thicknessTable = "thickness";
    private $measureTable = "measures";
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
     * #API_39 || Add Shape
     */
    public function insertShape_post()
    {
        try {
            $SHAPE = strtolower(trim($this->inputData["SHAPE"]));

            if (empty($SHAPE)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(SHAPE)='$SHAPE'";
            $tempResult = $this->Users_model->check($this->shapeTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Shape already exists"]);
            }

            $saveArray = array(
                'SHAPE' => strtoupper($SHAPE),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->shapeTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Shape added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add shape"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_42 || Add Sub Category
     */
    public function insertSubCategory_post()
    {
        try {
            $SUB_CATEGORY = strtolower(trim($this->inputData["SUB_CATEGORY"]));

            if (empty($SUB_CATEGORY)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(SUB_CATEGORY)='$SUB_CATEGORY'";
            $tempResult = $this->Users_model->check($this->subCatTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Sub category already exists"]);
            }

            $saveArray = array(
                'SUB_CATEGORY' => strtoupper($SUB_CATEGORY),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->subCatTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Sub category added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add sub category"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_40 || Delete Shape
     */
    public function deleteShape_post()
    {
        try {
            $SHAPE = strtolower(trim($this->inputData["SHAPE"]));

            if (empty($SHAPE)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(SHAPE)='$SHAPE'";
            $tempResult = $this->Users_model->check($this->shapeTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Shape not found"]);
            }

            $result = $this->Users_model->delete($this->shapeTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Shape deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete shape"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_43 || Delete Sub Category
     */
    public function deleteSubCategory_post()
    {
        try {
            $SUB_CATEGORY = strtolower(trim($this->inputData["SUB_CATEGORY"]));

            if (empty($SUB_CATEGORY)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(SUB_CATEGORY)='$SUB_CATEGORY'";
            $tempResult = $this->Users_model->check($this->subCatTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Sub Category not found"]);
            }

            $result = $this->Users_model->delete($this->subCatTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Sub category deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete sub category"]);
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
     * #API_41 || Get Shapes
     */
    public function getShapes_get()
    {
        try {
            $this->db->select("SHAPE");
            $this->db->from($this->shapeTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("SHAPE", "ASC");
            $this->db->group_by("SHAPE");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Shapes list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No shapes found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_44 || Get Sub Categories
     */
    public function getSubCategories_get()
    {
        try {
            $this->db->select("SUB_CATEGORY");
            $this->db->from($this->subCatTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("SUB_CATEGORY", "ASC");
            $this->db->group_by("SUB_CATEGORY");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Sub Categories list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No sub categories found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /*-----------------------------New APIs for Perimeter, Length, Thickness, Weight----------------------------*/
    /**
     * #API_45 || Add Perimeter
     */
    public function insertPerimeter_post()
    {
        try {
            $PERIMETER = strtolower(trim($this->inputData["PERIMETER"]));

            if (empty($PERIMETER)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(PERIMETER)='$PERIMETER'";
            $tempResult = $this->Users_model->check($this->perimeterTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Perimeter already exists"]);
            }

            $saveArray = array(
                'PERIMETER' => strtoupper($PERIMETER),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->perimeterTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Perimeter added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add perimeter"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_46 || Delete Perimeter
     */
    public function deletePerimeter_post()
    {
        try {
            $PERIMETER = strtolower(trim($this->inputData["PERIMETER"]));

            if (empty($PERIMETER)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(PERIMETER)='$PERIMETER'";
            $tempResult = $this->Users_model->check($this->perimeterTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Perimeter not found"]);
            }

            $result = $this->Users_model->delete($this->perimeterTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Perimeter deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete perimeter"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_47 || Get Perimeters
     */
    public function getPerimeters_get()
    {
        try {
            $this->db->select("PERIMETER");
            $this->db->from($this->perimeterTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("PERIMETER", "ASC");
            $this->db->group_by("PERIMETER");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Perimeters list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No perimeter found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_48 || Add Length
     */
    public function insertLength_post()
    {
        try {
            $LENGTH = strtolower(trim($this->inputData["LENGTH"]));

            if (empty($LENGTH)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(LENGTH)='$LENGTH'";
            $tempResult = $this->Users_model->check($this->lengthTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Length already exists"]);
            }

            $saveArray = array(
                'LENGTH' => strtoupper($LENGTH),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->lengthTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Length added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add length"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_49 || Delete Length
     */
    public function deleteLength_post()
    {
        try {
            $LENGTH = strtolower(trim($this->inputData["LENGTH"]));

            if (empty($LENGTH)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(LENGTH)='$LENGTH'";
            $tempResult = $this->Users_model->check($this->lengthTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Length not found"]);
            }

            $result = $this->Users_model->delete($this->lengthTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Length deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete length"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_50 || Get Lengths
     */
    public function getLengths_get()
    {
        try {
            $this->db->select("LENGTH");
            $this->db->from($this->lengthTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("LENGTH", "ASC");
            $this->db->group_by("LENGTH");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Lengths list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No length found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_51 || Add Thickness
     */
    public function insertThickness_post()
    {
        try {
            $THICKNESS = strtolower(trim($this->inputData["THICKNESS"]));

            if (empty($THICKNESS)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(THICKNESS)='$THICKNESS'";
            $tempResult = $this->Users_model->check($this->thicknessTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Thickness already exists"]);
            }

            $saveArray = array(
                'THICKNESS' => strtoupper($THICKNESS),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->thicknessTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Thickness added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add thickness"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_52 || Delete Thickness
     */
    public function deleteThickness_post()
    {
        try {
            $THICKNESS = strtolower(trim($this->inputData["THICKNESS"]));

            if (empty($THICKNESS)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(THICKNESS)='$THICKNESS'";
            $tempResult = $this->Users_model->check($this->thicknessTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Thickness not found"]);
            }

            $result = $this->Users_model->delete($this->thicknessTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Thickness deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete thickness"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_53 || Get Thickness
     */
    public function getThickness_get()
    {
        try {
            $this->db->select("THICKNESS");
            $this->db->from($this->thicknessTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("THICKNESS", "ASC");
            $this->db->group_by("THICKNESS");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Thickness list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No thickness found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_54 || Add Weight
     */
    public function insertWeight_post()
    {
        try {
            $WEIGHT = strtolower(trim($this->inputData["WEIGHT"]));

            if (empty($WEIGHT)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(WEIGHT)='$WEIGHT'";
            $tempResult = $this->Users_model->check($this->weightTable, $whereString);
            if ($tempResult->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Weight already exists"]);
            }

            $saveArray = array(
                'WEIGHT' => strtoupper($WEIGHT),
                'CREATED' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->weightTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Weight added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add weight"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_55 || Delete Weight
     */
    public function deleteWeight_post()
    {
        try {
            $WEIGHT = strtolower(trim($this->inputData["WEIGHT"]));

            if (empty($WEIGHT)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereString = "LOWER(WEIGHT)='$WEIGHT'";
            $tempResult = $this->Users_model->check($this->weightTable, $whereString);
            if ($tempResult->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Weight not found"]);
            }

            $result = $this->Users_model->delete($this->weightTable, $whereString);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Weight deleted"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to delete weight"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_56 || Get Weight
     */
    public function getWeights_get()
    {
        try {
            $this->db->select("WEIGHT");
            $this->db->from($this->weightTable);
            $this->db->where("STATUS", "ACTIVE");
            $this->db->order_by("WEIGHT", "ASC");
            $this->db->group_by("WEIGHT");
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Weights list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "No weight found"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_57 || Add Measures
     */
    public function insertMeasures_post()
    {
        try {
            $BRAND = trim($this->inputData["BRAND"]);
            $CATEGORY = trim($this->inputData["CATEGORY"]);
            $SUB_CATEGORY = trim($this->inputData["SUB_CATEGORY"]);
            $SHAPE = trim($this->inputData["SHAPE"]);
            $WEIGHT = trim($this->inputData["WEIGHT"]);
            $THICKNESS = trim($this->inputData["THICKNESS"]);
            $LENGTH = trim($this->inputData["LENGTH"]);
            $PERIMETER = trim($this->inputData["PERIMETER"]);

            $saveArray = array(
                "BRAND" => $BRAND,
                "CATEGORY" => $CATEGORY,
                "SUB_CATEGORY" => $SUB_CATEGORY,
                "SHAPE" => $SHAPE,
                "WEIGHT" => $WEIGHT,
                "THICKNESS" => $THICKNESS,
                "LENGTH" => $LENGTH,
                "PERIMETER" => $PERIMETER
            );
            $result = $this->Users_model->save($this->measureTable, $saveArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Measures added"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to add measures"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_58 || get Measures
     */
    public function getMeasures_get()
    {
        try {
            $this->db->select("*");
            $this->db->from($this->measureTable);
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Measures list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to get measures"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_59 || get Filter Measures
     */
    public function getFilterMeasures_get()
    {
        try {
            $BRAND = trim($this->get("BRAND"));
            $CATEGORY = trim($this->get("CATEGORY"));
            $SUB_CATEGORY = trim($this->get("SUB_CATEGORY"));
            $SHAPE = trim($this->get("SHAPE"));
            $whereArray = array();
            if (!empty($BRAND)) {
                $whereArray['BRAND'] = $BRAND;
            }
            if (!empty($CATEGORY)) {
                $whereArray['CATEGORY'] = $CATEGORY;
            }
            if (!empty($SUB_CATEGORY)) {
                $whereArray['SUB_CATEGORY'] = $SUB_CATEGORY;
            }
            if (!empty($SHAPE)) {
                $whereArray['SHAPE'] = $SHAPE;
            }
            $this->db->select("*");
            $this->db->from($this->measureTable);
            if (!empty($whereArray)) {
                $this->db->where($whereArray);
            }
            $result = $this->db->get();
            if ($result->num_rows() > 0) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Measures list", "data" => $result->result_array()]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to get measures"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function test_get()
    {
        try {
            echo "hello";
            $Body = '<p>To verify your email <a href="http://65.1.178.54/app/index.php/Users/verify?ref=9132534448">click here</a></p>';
            $this->utility->sendEMAIL("krishnamouli143@gmail.com", $Body, "Email Verification || MrMason");
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}