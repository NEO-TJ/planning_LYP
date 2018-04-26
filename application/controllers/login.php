<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{
	public function __construct() {
		parent::__construct();
		// set default data to view
		$this->data = array();
	}

	public function index() {
		$this->session->sess_destroy();
		$this->load->view('frontend/login/header');
		$this->load->view('frontend/index');
		$this->load->view('frontend/login/footer');
	}

	public function validate() {
		$user = $this->user_m->validate();
		$data = array();

		if($user == true) {
			foreach ($user as $u) {
				$data['id']		= $u->id;
				$data['name']	= $u->Name;
				$data['userID']	= $u->User_ID;
				$data['lineID']	= $u->FK_ID_Line;
				$data['level']	= $u->Level;
			}
			
			switch ($u->Level) {
				case '1':
					$this->session->set_userdata($data);
					redirect("planning");
					break;
				case '2':
					$this->session->set_userdata($data);
					redirect("planning");
					break;
				case '3':
					$this->session->sess_destroy();
					$this->session->set_flashdata('msg',  'ท่านไม่ได้รับอนุญาติให้ใช้งานในระบบนี้');
					header('Location: ../');
					break;
				default:
					$this->session->sess_destroy();
					redirect("planning");
					break;
			}
		} else {
			$this->session->sess_destroy();
			$this->session->set_flashdata('msg',  'ข้อมูลไม่ถูกต้องกรุณาลองใหม่อีกครั้ง');
			header('Location: ../');
		}
	}
	
	
	
	public function logout() {
		$this->load->view('frontend/include/header');
		$this->load->view('frontend/logout');
		$this->load->view('frontend/include/footer');
	}
}
