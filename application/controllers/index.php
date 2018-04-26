<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends CI_Controller {
    public function __construct() {
		parent::__construct();
	}

    public function index() {
		$this->session->sess_destroy();
        $this->load->view('frontend/login/header');
        $this->load->view('frontend/index');
        $this->load->view('frontend/login/footer');
    }
}
