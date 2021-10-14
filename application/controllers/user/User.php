<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('user/User_model');
    }

    // ///////////////////////////////////////////////
    public function index () {
        $has_session_username = $this->session->has_userdata('username');
        $session_status = $this->session->has_userdata('is_logged_in');

        if ($has_session_username && $session_status) {
            $this->load->view('user/user_dashboard');
        } else {
            $this->load->view('user/user_login');
        }
    }

    // ///////////////////////////////////////////////
    public function user_register_form () {
        $has_session_username = $this->session->has_userdata('username');
        $session_status = $this->session->has_userdata('is_logged_in');

        if ($has_session_username && $session_status) {
            $this->load->view('user/user_dashboard');
        } else {
            $this->load->view('user/user_register');
        }
    }

    // ///////////////////////////////////////////////

    public function check_username_availability () {
        // USERNAME VALIDATION
        $this->load->library('form_validation');
        
        $username_regex = '/^(?=[a-zA-Z0-9._]{4,20}$)(?!.*[_.]{2})[^_.].*[^_.]$/';
        $input_username = $this->input->post('username');

        if (preg_match($username_regex, $input_username)) {
            $this->form_validation->set_rules('username', 'Username', array('required', function ($input_username) {
                $query_to_model = $this->User_model->check_username_availability($input_username);

                if ($query_to_model->num_rows() == 0) {
                    return true;
                }
            }));

            if ($this->form_validation->run()) {
                print_r(json_encode(array('result' => true, 'response' => "<span style='color: green; font-size: 1rem;'>Username available</span>")));
            } else {
                print_r(json_encode(array('result' => false, 'response' => "<span style='color: orangered; font-size: 1rem;'>Username unavailable</span>")));
            }
            
        } else {
            print_r(json_encode(array('result' => false, 'response' =>"<span style='color: red; font-size: 1rem;'>Invalid username</span>")));
        }
    }

    // ///////////////////////////////////////////////

    public function check_email_availability () {
        // USERNAME VALIDATION
        $this->load->library('form_validation');
        
        $email_regex = '/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
        
        $input_email = $this->input->post('email');
        
        if (preg_match($email_regex, $input_email)) {
            $this->form_validation->set_rules('email', 'Email', array('required', function ($input_email) {
                $query_to_model = $this->User_model->check_email_availability($input_email);

                if ($query_to_model->num_rows() == 0) {
                    return true;
                }
            }));

            if ($this->form_validation->run()) {
                print_r(json_encode(array('result' => true, 'response' => "<span style='color: green; font-size: 1rem;'>Email available</span>")));
            } else {
                print_r(json_encode(array('result' => false, 'response' => "<span style='color: orangered; font-size: 1rem;'>Email unavailable</span>")));
            }
            
        } else {
            print_r(json_encode(array('result' => false, 'response' =>"<span style='color: red; font-size: 1rem;'>Invalid Email</span>")));
        }
    }

    // ///////////////////////////////////////////////

    public function user_register () {
        $data['username'] = $this->input->post('username');
        $data['email'] = $this->input->post('email');
        $data['password'] = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
        $data['newsletter_signup'] = $this->input->post('newsletter_signup');

        $username_validation = $this->User_model->check_username_availability($data['username']);
        $email_validation = $this->User_model->check_email_availability($data['email']);

        $username = false;
        if ($username_validation && $username_validation->num_rows() == 0) {
            $username = true;
        }

        $email = false;
        if ($email_validation && $email_validation->num_rows() == 0) {
            $email = true;
        }

        if ($username && $email && $data['password']) {
            if ($data['newsletter_signup'] == 0 || $data['newsletter_signup']) {
                $response = array('status' => true, 'response' => "<span style='color: green; font-size: 2rem;>Successfully registered</span>'");

                function verification_code($length) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $charactersLength = strlen($characters);
                    $randomString = '';
                    for ($i = 0; $i < $length; $i++) {
                        $randomString .= $characters[rand(0, $charactersLength - 1)];
                    }
                    return $randomString;
                }

                $verification_password = verification_code(20);

                $data_to_model = array('username' => $data['username'], 'email' => $data['email'], 'password' => $data['password'], 'newsletter_subscription' => $data['newsletter_signup'], 'verification_code' => $verification_password);

                $query_to_model = $this->User_model->register_user($data_to_model);

                if ($query_to_model) {
                    $this->session->set_userdata(array('username' => $data['username'], 'is_logged_in' => true));
                    $this->session->set_flashdata('welcome_message', '<span style="color: green; font-size: 1rem;">Welcome '. $data['username'] .' Registration successful.');
                    print_r(json_encode($response));
                }
            }
        } else {
            $response = array('status' => false, 'response' => "<span style='color: green; font-size: 2rem;>Registration unsuccessful</span>'");
            print_r(json_encode($response));
        }
    }

    // ///////////////////////////////////////////////


    // ///////////////////////////////////////////////

}