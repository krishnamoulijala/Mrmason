<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Orders extends REST_Controller
{
    private $ordersTable = "orders";
    private $userTable = "users";
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
     * #API_36 || Order Insert || Last Modification : 2022-12-22
     */
    public function insert_post()
    {
        try {

            $DATA = $this->inputData["DATA"];
            if (empty($DATA)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }
            if (!is_array($DATA)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Invalid DATA input, must be an array"]);
            }
            $result = false;
            foreach ($DATA as $eachOne) {
                $EMAIL_ID = strtolower(trim($eachOne["EMAIL_ID"]));
                $RET_EMAIL_ID = strtolower(trim($eachOne["RET_EMAIL_ID"]));
                $S_NAME = trim($eachOne["S_NAME"]);
                $B_NAME = trim($eachOne["B_NAME"]);
                $BRAND_NAME = trim($eachOne["BRAND_NAME"]);
                $SIZE = trim($eachOne["SIZE"]);
                $N_ITEMS = trim($eachOne["N_ITEMS"]);
                $SIZE_QNTY = trim($eachOne["SIZE_QNTY"]);
                $QNTY = trim($eachOne["QNTY"]);
                $I_PRICE = trim($eachOne["I_PRICE"]);
                $T_PRICE = trim($eachOne["T_PRICE"]);
                $STATUS = trim($eachOne["STATUS"]);
                $DELIVERY_TYPE = trim($eachOne["DELIVERY_TYPE"]);

                /*if (empty($EMAIL_ID) || empty($RET_EMAIL_ID) || empty($STATUS) || empty($S_NAME) || empty($B_NAME) || empty($SIZE) || empty($N_ITEMS) || empty($I_PRICE) || empty($T_PRICE)) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
                }*/

                reGenerate:
                $ORDER_ID = "ORD" . $this->utility->generateUID(10);
                $whereArray = array('ORD_ID' => $ORDER_ID);
                $temp = $this->Users_model->check($this->ordersTable, $whereArray);
                if ($temp->num_rows() > 0) {
                    goto reGenerate;
                }

                $saveArray = array(
                    "ORD_ID" => $ORDER_ID,
                    "EMAIL_ID" => $EMAIL_ID,
                    "RET_EMAIL_ID" => $RET_EMAIL_ID,
                    "S_NAME" => $S_NAME,
                    "B_NAME" => $B_NAME,
                    "BRAND_NAME" => $BRAND_NAME,
                    "SIZE" => $SIZE,
                    "N_ITEMS" => $N_ITEMS,
                    "SIZE_QNTY" => $SIZE_QNTY,
                    "QNTY" => $QNTY,
                    "I_PRICE" => $I_PRICE,
                    "T_PRICE" => $T_PRICE,
                    "STATUS" => $STATUS,
                    "DELIVERY_TYPE" => $DELIVERY_TYPE,
                    "CREATED_DATETIME" => date("Y-m-d H:i:s")
                );
                $result = $this->Users_model->save($this->ordersTable, $saveArray);
            }
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Order placed successfully"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to place the order"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_37 || Order Status Change || Last Modification : 2022-12-22
     */
    public function getDetails_get()
    {
        try {
            $EMAIL_ID = strtolower(trim($this->inputData["EMAIL_ID"]));

            if (empty($EMAIL_ID)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $this->db->select(trim("
            ord.ORD_ID,
            ord.B_NAME,
            ord.S_NAME,
            ord.SIZE,
            ord.N_ITEMS,
            ord.SIZE_QNTY,
            ord.QNTY,
            ord.I_PRICE,
            ord.T_PRICE,
            ord.STATUS,
            ord.CREATED_DATETIME,
            usr.NAME,
            usr.EMAIL_ID,
            usr.MOBILE_NO,
            usr.PINCODE_NO,
            usr.CITY,
            usr.ADDRESS
            "));
            $this->db->from($this->ordersTable . " ord");
            $this->db->where("ord.EMAIL_ID", $EMAIL_ID);
            $this->db->join($this->userTable . " usr", "ord.EMAIL_ID=usr.EMAIL_ID", "left");
            $this->db->group_by("usr.EMAIL_ID");
            $temp = $this->db->get();
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Orders not found"]);
            } else {
                $result = $temp->result_array();
                if ($result) {
                    $this->utility->sendForceJSON(["status" => true, "message" => "Order Details", "data" => $result]);
                } else {
                    $this->utility->sendForceJSON(["status" => false, "message" => "Failed to get order details"]);
                }
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_38 || Order Status Change || Last Modification : 2022-12-22
     */
    public function changeStatus_post()
    {
        try {
            $ORD_ID = trim($this->inputData["ORD_ID"]);
            $STATUS = trim($this->inputData["STATUS"]);

            if (empty($ORD_ID) || empty($STATUS)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('ORD_ID' => $ORD_ID);
            $temp = $this->Users_model->check($this->ordersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Order not found"]);
            }

            $updateArray = array(
                'STATUS' => $STATUS,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->ordersTable, $whereArray, $updateArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "Order status changed"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the order status"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }
}
