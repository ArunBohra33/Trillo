<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manager extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    // ///////////////////////////////////////////////
    public function index () {
        // $has_session_username = $this->session->has_userdata('manager_name');
        // $session_status = $this->session->has_userdata('is_logged_in');

        // if ($has_session_username && $session_status) {
        //     $this->load->view('manager/manager_dashboard');
        // } else {
        //     $this->load->view('manager/manager_register');
        // }
        $this->load->view('manager/manager_register');
    }

    // ///////////////////////////////////////////////
    public function manager_register_form () {
        // $has_session_username = $this->session->has_userdata('manager_name');
        // $session_status = $this->session->has_userdata('is_logged_in');

        // if ($has_session_username && $session_status) {
        //     $this->load->view('manager/manager_dashboard');
        // } else {
        //     $this->load->view('manager/manager_register');
        // }
        $this->load->view('manager/manager_register');
    }

}