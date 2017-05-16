<?php
/**
 * Created by PhpStorm.
 * User: mohammed
 * Date: 4/12/17
 * Time: 3:35 PM
 */
class Login extends CI_Controller {
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->model('Login_model');

    }

    public function index() {
        //$this->load->view('login_view');
    }

    /*User Registeration for Individual and Corporate*/

    public function user_register() {

        if(isset($_POST)) {

            $usertype = $this->input->post('user_type');

            /*Checking of Individual or Corporate*/

            if($usertype == 'individual') {

                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $email = $this->input->post('email');
                $phone_no = $this->input->post('phone_no');
                $gender_id = $this->input->post('gender_id');
                $status_id = '1';
                $otp = rand(100000, 999999);
                $type = "Registeration";

                /*Setting Sessions For the User*/

                $session_data = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone_no' => $phone_no,
                    'gender_id' => $gender_id,
                    'status_id' => $status_id,
                    'real_otp' => $otp,
                    'usertype' => $usertype
                );

                $this->session->set_userdata('logged_in', $session_data);
				
				$user_exists = $this->Login_model->individual_user_existance($phone_no);
				
				 if($user_exists) {
                    @$response['status'] = "User Already Exists";
                    print_r(json_encode($response));
                }
				else{
					/*Calling Send OTP Function*/

					$otp_response = $this->sendOtp($otp, $phone_no, $type);

					echo json_encode($session_data);
				}

                
            }

            elseif($usertype == 'corporate') {

                $company_name = $this->input->post('company_name');
                $company_url = $this->input->post('company_url');
                $email = $this->input->post('email');
                $phone_no = $this->input->post('phone_no');
                $admin_name = $this->input->post('admin_name');
                $status_id = '1';
                $otp = rand(100000, 999999);
                $type = "Registeration";

                /*Setting Sessions For the User*/

                $session_data = array(
                    'company_name' => $company_name,
                    'company_url' => $company_url,
                    'email' => $email,
                    'phone_no' => $phone_no,
                    'admin_name' => $admin_name,
                    'status_id' => $status_id,
                    'real_otp' => $otp,
                    'usertype' => $usertype
                );

                $this->session->set_userdata('logged_in', $session_data);
				
				$user_exists = $this->Login_model->corporate_user_existance($phone_no);
                if($user_exists) {
                    @$response['status'] = "User Already Exists";
                    print_r(json_encode($response));
                }
				else {
					/*Calling Send OTP Function*/

					$otp_response = $this->sendOtp($otp, $phone_no, $type);

					echo json_encode($session_data);
				}

                
            }

        }

    }




    /*Verification of OTP*/

    public function verify_otp() {

        $otp = $this->input->post('otp');
        $real_otp = $this->input->post('real_otp');
        $usertype = $this->input->post('user_type');

        /*Checking of Individual or Corporate*/

        if($usertype == 'individual') {

            $first_name = $this->input->post('first_name');
            $last_name = $this->input->post('last_name');
            $email = $this->input->post('email');
            $phone_no = $this->input->post('phone_no');
            $gender_id = $this->input->post('gender_id');
            $status_id = '1';

            /*OTP Camparision*/

            if($otp == $real_otp) {
                $user_data_db = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone_no' => $phone_no,
                    'gender_id' => $gender_id,
                    'status_id' => $status_id
                );

                 $individual_response = $this->Login_model->insert_individual_user($user_data_db);
                    @$response['status'] = "Success";
                    print_r(json_encode($response));
               
                
            }
            else{
                @$response['status'] = "OTP Mismatch";
                print_r(json_encode($response));
            }
        }

        elseif($usertype == 'corporate') {

            $corp_id = rand(11111111,99999999);
            $corporate_id = "C".$corp_id;
            $company_name = $this->input->post('company_name');
            $company_url = $this->input->post('company_url');
            $email = $this->input->post('email');
            $phone_no = $this->input->post('phone_no');
            $admin_name = $this->input->post('admin_name');
            $status_id = '1';

            /*OTP Camparision*/

            if($otp == $real_otp) {
				 /*Corporate Submit*/
                    $corporate_data_db = array(
                        'id' => $corporate_id,
                        'company_name' => $company_name,
                        'company_url' => $company_url,
                        'email' => $email,
                        'status_id' => $status_id
                    );
                    $corp_response = $this->Login_model->insert_corporate($corporate_data_db);
                    if($corp_response) {
                        /*Corporate Users Submit*/
                        $corporate_user_db = array(
                            'corporates_id' => $corporate_id,
                            'admin_name' => $admin_name,
                            'admin_phone' => $phone_no,
                            'status_id' => $status_id,
                        );
                        $corp_user_response = $this->Login_model->insert_corporate_users($corporate_user_db);
                        @$response['status'] = "Success";
                        print_r(json_encode($response));
                    }
                
                
               
            }
            else{
                @$response['status'] = "OTP Mismatch";
                print_r(json_encode($response));
            }
        }

    }


    /*User Login For Individual and Corporate*/

    public function user_login() {
        if(isset($_POST)) {
            $usertype = $this->input->post('user_type');
            $otp = rand(100000, 999999);
            $type = "Login";

            /*Checking of Individual or Corporate*/
            if($usertype == 'individual') {
                $phone_no = $this->input->post('phone_no');

                /*Checking If User Exists*/

                $user_exists = $this->Login_model->individual_user_existance($phone_no);
                if($user_exists) {
                    $otp_response = $this->sendOtp($otp, $phone_no, $type);
                    
					@$response['status'] = "Success";
					@$response['real_otp'] = $otp;
					@$response['phone_no'] = $phone_no;
                    print_r(json_encode($response));
                }
				else{
                    @$response['status'] = "Register Please";
                    print_r(json_encode($response));
                }
            }
            elseif($usertype == 'corporate') {
                $phone_no = $this->input->post('phone_no');
                $corporate_id = $this->input->post('corporate_id');

                /*Checking If User Exists*/

                $user_exists = $this->Login_model->corporate_user_check($phone_no,$corporate_id);
                if($user_exists) {
                    $otp_response = $this->sendOtp($otp, $phone_no, $type);
					@$response['status'] = "Success";
                    @$response['real_otp'] = $otp;
					@$response['phone_no'] = $phone_no;
                    print_r(json_encode($response));
                }
                else{
                    @$response['status'] = "Register Please";
                    print_r(json_encode($response));
                }
            }
        }
    }

    /*Verication of OTP For Login*/

    public function login_verify() {
        $otp = $this->input->post('otp');
        $real_otp = $this->input->post('real_otp');
        $usertype = $this->input->post('user_type');

        /*OTP Camparision*/

		$phone_no = $this->input->post('phone_no');

        /*OTP Camparision*/

        if($otp == $real_otp) {
            if($usertype == 'individual') {
                $user_resp = $this->Login_model->fetch_logged_individual($phone_no);
                @$response['status'] = "Individual Success";
				 @$response['type'] = "Individual";
                @$response['user'] = $user_resp;
                print_r(json_encode($response));
            }
            elseif($usertype == 'corporate') {
                $user_resp = $this->Login_model->fetch_logged_corporate($phone_no);
                @$response['status'] = "Corporate Success";
				@$response['type'] = "Individual";
                @$response['user'] = $user_resp;
                print_r(json_encode($response));
            }
        }
        else{
            @$response['status'] = "OTP Mismatch";
            print_r(json_encode($response));
        }

    }

    /*Send OTP Functionality*/

    public function sendOtp($otp, $phone_no, $type) {

        //This is the sms text that will be sent via sms
        $sms_content = 'OneFoneCall verification code is '.$otp.' Please enter this to confirm your '.$type.' with us';

        //Encoding the text in url format
        $sms_text = urlencode($sms_content);

        //This is the Actual API URL concatnated with required values
        $api_url = 'http://smscloud.ozonetel.com/GatewayAPI/rest?send_to='.urlencode($phone_no).'&msg='.$sms_text.'.&msg_type=text&loginid=fonekart&auth_scheme=plain&password=pC0c98ZUK&v=1.1&format=text&method=sendMessage&mask=OFNCAL';

        //Envoking the API url and getting the response
        $response = file_get_contents( $api_url);

        //Returning the response
        return $response;
    }


    public function test() {
        $array_test = array(
            "name" => "Test"
        );

        print_r(json_encode($array_test));
    }



}