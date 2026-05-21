<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Setting_model');
    }

    public function index() {
        $data['settings'] = $this->Setting_model->get_all();
        $this->load->view('frontend/home', $data);
    }
}
