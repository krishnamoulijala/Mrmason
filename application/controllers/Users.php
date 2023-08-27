<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

class Users extends REST_Controller
{
    private $usersTable = "users";
    private $otpsTable = "otps";
    private $usersImagesTable = "images";
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
     * @param $BOD_SEQ_NO
     * @return mixed
     */
    private function getUserDetails($BOD_SEQ_NO)
    {
        $this->db->select("*");
        $this->db->from($this->usersTable);
        $this->db->where('BOD_SEQ_NO', $BOD_SEQ_NO);
        return $this->db->get()->row_array();
    }

    /**
     * #API_1 || User Registration
     */
    public function register_post()
    {
        try {
            $NAME = trim($this->inputData["NAME"]);
            $BUSINESS_NAME = trim($this->inputData["BUSINESS_NAME"]);
            $MOBILE_NO = trim($this->inputData["MOBILE_NO"]);
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $ADDRESS = trim($this->inputData["ADDRESS"]);
            $CITY = trim($this->inputData["CITY"]);
            $STATE = trim($this->inputData["STATE"]);
            $DISTRICT = trim($this->inputData["DISTRICT"]);
            $PINCODE_NO = trim($this->inputData["PINCODE_NO"]);
            $USER_TYPE = trim($this->inputData["USER_TYPE"]);
            $PASSWORD = trim($this->inputData["PASSWORD"]);

            if (!empty($MOBILE_NO)) {
                if (!is_numeric($MOBILE_NO) && strlen($MOBILE_NO) < 10) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid mobile number"]);
                }
            }
            $MOBILE_NO = substr($MOBILE_NO, -10);
            if (!empty($EMAIL_ID)) {

                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid email address"]);
                }

                $whereArray = array('EMAIL_ID' => $EMAIL_ID);
                $tempResult = $this->Users_model->check($this->usersTable, $whereArray);
                if ($tempResult->num_rows() > 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "User already registered with email address"]);
                }
            }

            reGenerate:
            $BOD_SEQ_NO = $this->utility->generateUID(10);
            $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() > 0) {
                goto reGenerate;
            }

            $saveArray = array(
                'BOD_SEQ_NO' => $BOD_SEQ_NO,
                'NAME' => $NAME,
                'BUSINESS_NAME' => $BUSINESS_NAME,
                'MOBILE_NO' => $MOBILE_NO,
                'EMAIL_ID' => $EMAIL_ID,
                'ADDRESS' => $ADDRESS,
                'CITY' => $CITY,
                'STATE' => $STATE,
                'DISTRICT' => $DISTRICT,
                'PINCODE_NO' => $PINCODE_NO,
                'USER_TYPE' => $USER_TYPE,
                'PASSWORD' => $PASSWORD,
                'VERIFIED' => 'NO',
                'STATUS' => 'INACTIVE',
                'REGISTRATION_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->usersTable, $saveArray);
            if ($result) {
                $responseArray = $this->getUserDetails($BOD_SEQ_NO);
                unset($responseArray['PASSWORD']);

                $OTP = $this->utility->generate_otp();
                $OTPArray = array(
                    'MOBILE_NO' => $MOBILE_NO,
                    'OTP' => $OTP,
                    'CREATED_DATETIME' => date('Y-m-d H:i:s')
                );
                $this->Users_model->save($this->otpsTable, $OTPArray);

                $SMS_Message = "Thanks for registering with us. Your OTP to verify your mobile number is $OTP - www.mistermason.in";
                $this->utility->sendSMS($MOBILE_NO, $SMS_Message);
                $Body = '<p>To verify your email <a href="http://65.1.178.54/app/index.php/Users/verify?ref=' . $BOD_SEQ_NO . '">click here</a></p>';
                $this->utility->sendEMAIL($EMAIL_ID, $Body, "Email Verification || MrMason");

                $this->utility->sendForceJSON(["status" => true, "message" => "Registration successful", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to register user"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_2 ||User details Update
     */
    public function update_post()
    {
        try {
            $BOD_SEQ_NO = trim($this->inputData["BOD_SEQ_NO"]);
            $NAME = trim($this->inputData["NAME"]);
            $BUSINESS_NAME = trim($this->inputData["BUSINESS_NAME"]);
            $MOBILE_NO = trim($this->inputData["MOBILE_NO"]);
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $ADDRESS = trim($this->inputData["ADDRESS"]);
            $CITY = trim($this->inputData["CITY"]);
            $STATE = trim($this->inputData["STATE"]);
            $DISTRICT = trim($this->inputData["DISTRICT"]);
            $PINCODE_NO = trim($this->inputData["PINCODE_NO"]);

            $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }

            if (!empty($MOBILE_NO)) {
                if (!is_numeric($MOBILE_NO) && strlen($MOBILE_NO) < 10) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid mobile number"]);
                }
            }
            $MOBILE_NO = substr($MOBILE_NO, -10);
            if (!empty($EMAIL_ID)) {

                if (!$this->utility->validEmail($EMAIL_ID)) {
                    $this->utility->sendForceJSON(['status' => false, 'message' => "Invalid email address"]);
                }
                $whereString = "EMAIL_ID='$EMAIL_ID' AND BOD_SEQ_NO !='$BOD_SEQ_NO'";
                $tempResult = $this->Users_model->check($this->usersTable, $whereString);
                if ($tempResult->num_rows() > 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "User already registered with email address"]);
                }
            }

            $updateArray = array(
                'NAME' => $NAME,
                'BUSINESS_NAME' => $BUSINESS_NAME,
                'MOBILE_NO' => $MOBILE_NO,
                'EMAIL_ID' => $EMAIL_ID,
                'ADDRESS' => $ADDRESS,
                'CITY' => $CITY,
                'STATE' => $STATE,
                'DISTRICT' => $DISTRICT,
                'PINCODE_NO' => $PINCODE_NO,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);
            if ($result) {
                $responseArray = $this->getUserDetails($BOD_SEQ_NO);
                unset($responseArray['PASSWORD']);
                $this->utility->sendForceJSON(["status" => true, "message" => "Details updated", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to update user details"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_3 || User Login
     */
    public function login_post()
    {
        try {

            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $PASSWORD = trim($this->inputData["PASSWORD"]);

            if (empty($EMAIL_ID) || empty($PASSWORD)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }
            $whereArray = array('EMAIL_ID' => $EMAIL_ID);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $whereArray = array('MOBILE_NO' => $EMAIL_ID);
                $temp = $this->Users_model->check($this->usersTable, $whereArray);
                if ($temp->num_rows() == 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
                }
            }
            $result = $temp->row_array();

            if ($result['STATUS'] == "INACTIVE") {
                $this->utility->sendForceJSON(["status" => false, "message" => "Please verify email to login"]);
            }

            if ($result['PASSWORD'] == $PASSWORD) {
                $responseArray = $this->getUserDetails($result['BOD_SEQ_NO']);
                unset($responseArray['PASSWORD']);
                $this->utility->sendForceJSON(["status" => true, "message" => "Login successful", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Incorrect password"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_4 || User Change Password
     */
    public function changePassword_post()
    {
        try {
            $BOD_SEQ_NO = trim($this->inputData["BOD_SEQ_NO"]);
            $NEW_PASSWORD = trim($this->inputData["NEW_PASSWORD"]);

            if (empty($BOD_SEQ_NO) || empty($NEW_PASSWORD)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }
            $updateArray = array(
                'PASSWORD' => $NEW_PASSWORD,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);

            if ($result) {
                $responseArray = $this->getUserDetails($BOD_SEQ_NO);
                unset($responseArray['PASSWORD']);
                $this->utility->sendForceJSON(["status" => true, "message" => "Password changed", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the password"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_5 || User Status Change
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
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }

            if (!in_array($STATUS, array("ACTIVE", "INACTIVE"))) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Invalid input from STATUS"]);
            }

            $updateArray = array(
                'STATUS' => $STATUS,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);
            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "User status changed"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the user status"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_31 || Get All Users
     */
    public function getAllUsers_get()
    {
        try {
            $USER_TYPE = strtolower(trim($this->get("USER_TYPE")));
            $REGISTRATION_DATE = strtolower(trim($this->get("REGISTRATION_DATE")));

            $this->db->select("EMAIL_ID,NAME,BUSINESS_NAME,MOBILE_NO,USER_TYPE,ADDRESS,STATUS,REGISTRATION_DATETIME,PINCODE_NO,CITY");
            $this->db->from($this->usersTable);
            if (!empty($USER_TYPE)) {
                $this->db->where("LOWER(USER_TYPE) LIKE '%$USER_TYPE%'");
            }
            if (!empty($REGISTRATION_DATE)) {
                $this->db->where("REGISTRATION_DATETIME LIKE '%$REGISTRATION_DATE%'");
            }
            $this->db->order_by("NAME", "ASC");
            $result = $this->db->get();
            if ($result->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            } else {
                $this->utility->sendForceJSON(["status" => true, "message" => "All Users", "data" => $result->result_array()]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_17 || Get User Details
     */
    public function getDetails_get()
    {
        try {
            $BOD_SEQ_NO = trim($this->get("BOD_SEQ_NO"));

            if (!empty($BOD_SEQ_NO)) {
                $whereArray = array('BOD_SEQ_NO' => $BOD_SEQ_NO);
                $temp = $this->Users_model->check($this->usersTable, $whereArray);
                if ($temp->num_rows() == 0) {
                    $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
                } else {
                    $responseArray = $this->getUserDetails($BOD_SEQ_NO);
                    unset($responseArray['PASSWORD']);
                    $this->utility->sendForceJSON(["status" => true, "message" => "User details", "data" => $responseArray]);
                }
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_18 || Upload User Images
     */
    public function uploadImages_post()
    {
        try {
            $EMAIL_ID = trim($this->post("EMAIL_ID"));
            if (empty($EMAIL_ID)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"], 200);
            }
            $result = false;
            if (isset($_FILES["IMAGES"])) {
                foreach ($_FILES["IMAGES"]["tmp_name"] as $key => $tmp_name) {
                    $reports = $_FILES["IMAGES"]["name"][$key];
                    $ext = pathinfo($reports, PATHINFO_EXTENSION);
                    $reports_file_new = $this->utility->getGUID() . "." . $ext;
                    $filePath = IMAGES_PATH . $reports_file_new;
                    move_uploaded_file($_FILES["IMAGES"]["tmp_name"][$key], $filePath);

                    $data = array('EMAIL_ID' => $EMAIL_ID, 'IMAGE_PATH' => $filePath, 'CREATED_DATETIME' => date('Y-m-d H:i:s'));
                    $result = $this->Users_model->save($this->usersImagesTable, $data);
                }
            } else {
                $this->response(["status" => false, "message" => "Please select images"], 200);
            }

            if ($result) {
                $this->utility->sendForceJSON(['status' => true, 'message' => "Images uploaded successfully"], 200);
            } else {
                $this->utility->sendForceJSON(['status' => false, 'message' => "Unable to upload images"], 200);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_19 || Get Uploaded Images
     */
    public function getImages_get()
    {
        try {
            $EMAIL_ID = trim($this->get("EMAIL_ID"));
            if (empty($EMAIL_ID)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }
            $whereArray = array('EMAIL_ID' => $EMAIL_ID);
            $temp = $this->Users_model->selectedCheck("IMAGE_PATH,CREATED_DATETIME", $this->usersImagesTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "No images found"]);
            } else {
                $responseArray['base_url'] = base_url();
                $responseArray['paths'] = $temp->result_array();
                $this->utility->sendForceJSON(["status" => true, "message" => "User images", "data" => $responseArray]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_20 || Delete Uploaded Image
     */
    public function deleteImage_post()
    {
        try {
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $IMAGE_PATH = trim($this->inputData["IMAGE_PATH"]);
            if (empty($EMAIL_ID) || empty($IMAGE_PATH)) {
                $this->response(["status" => false, "message" => "Required fields missing"], 200);
            }
            $whereArray = array('EMAIL_ID' => $EMAIL_ID, 'IMAGE_PATH' => $IMAGE_PATH);
            $temp = $this->Users_model->check($this->usersImagesTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "No images found"]);
            } else {
                if (file_exists(FCPATH . $IMAGE_PATH)) {
                    $result = unlink(FCPATH . $IMAGE_PATH);
                    if ($result) {
                        $whereArray = array('EMAIL_ID' => $EMAIL_ID, 'IMAGE_PATH' => $IMAGE_PATH);
                        $this->Users_model->delete($this->usersImagesTable, $whereArray);
                        $this->utility->sendForceJSON(["status" => true, "message" => "Image deleted"]);
                    } else {
                        $this->response(["status" => false, "message" => "Unable to delete image"], 200);
                    }
                } else {
                    $this->response(["status" => false, "message" => "Image not found"], 200);
                }
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_23 || User Forgot Password (Update)
     */
    public function forgotPassword_post()
    {
        try {
            $EMAIL_ID = trim($this->inputData["EMAIL_ID"]);
            $NEW_PASSWORD = trim($this->inputData["NEW_PASSWORD"]);

            if (empty($EMAIL_ID) || empty($NEW_PASSWORD)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('EMAIL_ID' => $EMAIL_ID);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }
            $updateArray = array(
                'PASSWORD' => $NEW_PASSWORD,
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);

            if ($result) {
                $responseArray = $this->getUserDetails($EMAIL_ID);
                unset($responseArray['PASSWORD']);
                $this->utility->sendForceJSON(["status" => true, "message" => "Password changed", "data" => $responseArray]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the password"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_35 || Validate OTP
     */
    public function validateOTP_post()
    {
        try {
            $MOBILE_NO = trim($this->inputData["MOBILE_NO"]);
            $OTP = trim($this->inputData["OTP"]);

            if (empty($MOBILE_NO) || empty($OTP)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('MOBILE_NO' => $MOBILE_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }

            $whereArray = array("MOBILE_NO" => $MOBILE_NO, "STATUS" => 0);
            $this->db->select("OTP,ID");
            $this->db->from($this->otpsTable);
            $this->db->where($whereArray);
            $this->db->order_by("CREATED_DATETIME", "DESC");
            $this->db->limit(1);
            $temp = $this->db->get();
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Invalid OTP Request"]);
            } else {
                $STORED_OTP = $temp->row_array()["OTP"];
                $ID = $temp->row_array()["ID"];
                if ($STORED_OTP == $OTP) {
                    $updateArray = array(
                        'STATUS' => 1,
                        'UPDATE_DATETIME' => date('Y-m-d H:i:s')
                    );
                    $result1 = $this->Users_model->update($this->otpsTable, array('ID' => $ID), $updateArray);

                    $updateArray = array(
                        'VERIFIED' => 'YES',
                        'UPDATE_DATETIME' => date('Y-m-d H:i:s')
                    );
                    $result = $this->Users_model->update($this->usersTable, array('MOBILE_NO' => $MOBILE_NO), $updateArray);
                    if ($result && $result1) {
                        $this->utility->sendForceJSON(["status" => true, "message" => "OTP Verified"]);
                    } else {
                        $this->utility->sendForceJSON(["status" => false, "message" => "Failed to verify OTP"]);
                    }
                } else {
                    $this->utility->sendForceJSON(["status" => false, "message" => "Invalid OTP(mismatch)"]);
                }
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_60 || User Forgot Password send OTP
     */
    public function forgotPasswordNew_post()
    {
        try {
            $MOBILE_NO = trim($this->inputData["MOBILE_NO"]);

            if (empty($MOBILE_NO)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('MOBILE_NO' => $MOBILE_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }

            $OTP = $this->utility->generate_otp();
            $OTPArray = array(
                'MOBILE_NO' => $MOBILE_NO,
                'OTP' => $OTP,
                'CREATED_DATETIME' => date('Y-m-d H:i:s')
            );
            $result = $this->Users_model->save($this->otpsTable, $OTPArray);

            $SMS_Message = "Thanks for registering with us. Your OTP to verify your mobile number is $OTP - www.mistermason.in";
            $this->utility->sendSMS($MOBILE_NO, $SMS_Message);

            if ($result) {
                $this->utility->sendForceJSON(["status" => true, "message" => "OTP triggered"]);
            } else {
                $this->utility->sendForceJSON(["status" => false, "message" => "Failed to trigger OTP"]);
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    /**
     * #API_61 || User Password (Update)
     */
    public function updatePassword_post()
    {
        try {
            $MOBILE_NO = trim($this->inputData["MOBILE_NO"]);
            $NEW_PASSWORD = trim($this->inputData["NEW_PASSWORD"]);
            $OTP = trim($this->inputData["OTP"]);

            if (empty($MOBILE_NO) || empty($NEW_PASSWORD) || empty($OTP)) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Required fields missing"]);
            }

            $whereArray = array('MOBILE_NO' => $MOBILE_NO);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "User not found"]);
            }
            $whereArray = array("MOBILE_NO" => $MOBILE_NO, "STATUS" => 0);
            $this->db->select("OTP,ID");
            $this->db->from($this->otpsTable);
            $this->db->where($whereArray);
            $this->db->order_by("CREATED_DATETIME", "DESC");
            $this->db->limit(1);
            $temp = $this->db->get();
            if ($temp->num_rows() == 0) {
                $this->utility->sendForceJSON(["status" => false, "message" => "Invalid OTP Request"]);
            } else {
                $STORED_OTP = $temp->row_array()["OTP"];
                $ID = $temp->row_array()["ID"];
                if ($STORED_OTP == $OTP) {
                    $updateArray = array(
                        'STATUS' => 1,
                        'UPDATE_DATETIME' => date('Y-m-d H:i:s')
                    );
                    $result1 = $this->Users_model->update($this->otpsTable, array('ID' => $ID), $updateArray);
                    $updateArray = array(
                        'PASSWORD' => $NEW_PASSWORD,
                        'UPDATE_DATETIME' => date('Y-m-d H:i:s')
                    );
                    $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);
                    if ($result) {
                        $this->utility->sendForceJSON(["status" => true, "message" => "Password changed"]);
                    } else {
                        $this->utility->sendForceJSON(["status" => false, "message" => "Failed to change the password"]);
                    }
                } else {
                    $this->utility->sendForceJSON(["status" => false, "message" => "Invalid OTP"]);
                }
            }
        } catch (Exception $e) {
            $this->logAndThrowError($e, true);
        }
    }

    public function verify_get()
    {
        try {
            $ref = strtolower(trim($this->get("ref")));

            if (empty($ref)) {
                echo "Invalid link";
                exit;
            }

            $whereArray = array('BOD_SEQ_NO' => $ref);
            $temp = $this->Users_model->check($this->usersTable, $whereArray);
            if ($temp->num_rows() == 0) {
                echo "Invalid link";
                exit;
            }
            $row_array = $temp->row_array();
            if ($row_array['STATUS'] == 'ACTIVE') {
                echo "Email already verified";
                exit;
            }
            $updateArray = array(
                'STATUS' => 'ACTIVE',
                'UPDATE_DATETIME' => date('Y-m-d H:i:s')
            );

            $result = $this->Users_model->update($this->usersTable, $whereArray, $updateArray);
            if ($result) {
                echo "Email verified";
                exit;
            } else {
                echo "Failed to verify email address";
                exit;
            }
        } catch (Exception $e) {
            echo "Something went wrong error:" . $e->getMessage();
            exit;
        }
    }
}