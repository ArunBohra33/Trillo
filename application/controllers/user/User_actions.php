<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_actions extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->model('user/User_model');
    }

    // ///////////////////////////////////////////////

    public function destroy_user_session() {
        $session_username = $this->session->has_userdata('username');
        $session_status = $this->session->has_userdata('is_logged_in');

        if ($session_status && $session_username) {
            $this->session->sess_destroy();

            redirect('user/User_actions/user_login');
        }
    }


    // ///////////////////////////////////////////////
    
    public function user_profile() {
        $session_username = $this->session->has_userdata('username');
        $session_status = $this->session->has_userdata('is_logged_in');
        
        if ($session_username && $session_status) {
            $this->load->view('user/user_profile');
        } else {
            $this->load->view('err_404');
        }
    }
    
    // https://i.postimg.cc/nLBZk1Kh/profile-pic.jpg

    // ///////////////////////////////////////////////

    /* public function send_email () {
        $this->load->library('email');

        $from = 'trillo.m12@gmail.com';
        $to = 'arun.b@notifyvisitors.com';

        $this->email->from($from, 'Trillo');
        $this->email->to($to);
        $this->email->subject('Verification');
        $this->email->message('Code: aaflskdjafs02839hasnfkuha8932rfhdkv');

        if ($this->email->send()) {
            $data = array('');
            // Load view
        } else {
            echo 'Email was not sent';
        }
    }
    */


    // ///////////////////////////////////////////////

    public function user_login() {
        $has_session_username = $this->session->has_userdata('username');
        $session_status = $this->session->has_userdata('is_logged_in');

        if (!$has_session_username || !$session_status) {
            $this->load->view('user/user_login');
        } else {
            redirect('user/User');
        }
    }

    public function test() {
        $verified_email_query = $this->User_model->has_verified_email('arun');

        print_r($verified_email_query);
    }

    public function has_verified_email($username) {
        $verified_email_query = $this->User_model->has_verified_email($username);

        if ($verified_email_query) {
            echo true;
        } else {
            echo false;
        }
    }

    public function check_login_details() {
        $login_username_input = $this->input->post('login_username');
        $login_password_input = $this->input->post('login_password');

        if ($login_username_input && $login_password_input) {
            
            $query_to_model = $this->User_model->login_user($login_username_input, $login_password_input);

            if ($query_to_model) {
                $this->session->set_userdata(array('username' => $login_username_input, 'is_logged_in' => true));

                

                // if ($verified_email_query) {
                //     $this->session->set_userdata('has_verified_email', 1);
                // } else {
                //     $this->session->set_userdata('has_verified_email', 0);
                // }
                $this->session->set_flashdata('welcome_back_message', '<span style="color: green; font-size: 1rem;">Welcome back '. $login_username_input .' Login successful.');


                $data = array('status' => 'success', 'message' => 'Logged in successfully.');
                print_r(json_encode($data));
            } else {
                $data = array('status' => 'fail', 'message' => 'Wrong credentials.');
                print_r(json_encode($data));
            }
        }
    }


    // ///////////////////////////////////////////////

    public function set_user_details() {
        $profile_element = $this->input->post('profile_element');
        
        if ($profile_element == 'picture') {
            $profile_url = $this->input->post('url');

            if (strpos($profile_url, '.png') !== false || strpos($profile_url, '.jpg') !== false || strpos($profile_url, '.jpeg') !== false) {
                $data = array('profile_element' => 'picture', 'url' => $profile_url, 'username' => $this->session->userdata('username'));
                $query_to_model = $this->User_model->set_user_details($data);

                if ($query_to_model) {
                    echo $profile_url;
                } else {
                    echo false;
                }
            } else {
                echo false;
            }
        } else if ($profile_element == 'social_links') {
            $social_links = $this->input->post('social_links');

            if ($social_links && is_array($social_links)) {
                $data = array('profile_element' => 'social_links', 'social_links' => json_encode($social_links), 'username' => $this->session->userdata('username'));

                $query_to_model = $this->User_model->set_user_details($data);
                
                if ($query_to_model) {
                    print_r(json_encode($social_links));
                } else {
                    echo false;
                }
            }

            // print_r($social_links);
        }
    }

    // ///////////////////////////////////////////////

    public function get_user_details() {
        $has_session_username = $this->session->has_userdata('username');
        if ($has_session_username) {
            $session_username = $this->session->userdata('username');
            $query_to_model = $this->User_model->get_user_details($session_username);

            $data = array('');

            if ($data && $query_to_model) {
                $data = array('profile_pic' => $query_to_model->profile_pic, 'social_links' => $query_to_model->user_social_links, 'user_bookings' => $query_to_model->user_bookings, 'user_ratings' => $query_to_model->user_ratings);
            } else {
                $data = false;
            }

            print_r(json_encode($data));
        }
    }

    // ///////////////////////////////////////////////

    public function verify_user_email () {
        $email_verification_code_input = $this->input->post('email_verification_code');

        $query_to_model = $this->User_model->verify_user_email($email_verification_code_input, $this->session->userdata('username'));

        if ($query_to_model) {
            $data = array('status' => 'success');
            print_r(json_encode($data));
        } else {
            $data = array('status' => 'fail');
            print_r(json_encode($data));
        }
    }

    // ///////////////////////////////////////////////

    public function delete_user() {
        $has_session_username = $this->session->has_userdata('username');
        $session_status = $this->session->has_userdata('is_logged_in');

        if ($has_session_username && $session_status) {
            $username = $this->session->userdata('username');

            $query_to_model = $this->User_model->delete_user($username);

            if ($query_to_model) {
                $session_username = $this->session->has_userdata('username');
                $session_status = $this->session->has_userdata('is_logged_in');

                if ($session_status && $session_username) {
                    $data = json_encode(array('status' => 'success'));
                    $this->session->sess_destroy();
                    print_r($data);
                }
            } else {
                $data = json_encode(array('status' => 'fail'));
                print_r($data);
            }
        }
    }
}