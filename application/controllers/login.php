<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		// set default data to view
        $this->data = array();
	}

	public function index()
	{
		$this->load->view('frontend/index');
	}

	public function validate()
	{
		// get data from db
		$user = $this->user_m->validate();

		$data = array();

		if($user == true)
		{

			foreach ($user as $u) {
				$data['id']		= $u->id;
				$data['name']	= $u->Name;
				$data['userID']	= $u->User_ID;
				$data['lineID']	= $u->Line_ID;
				$data['level']	= $u->Level;
			} // end foreach
			
			// set data to session
			$this->session->set_userdata($data);
			
			//check level user
			switch ($u->Level) {
				case '1':
					// redirect page to admin page
					redirect("planning");
					break;
				case '2':
					// redirect page to user page
					redirect("planning");
					break;
				default:
					// redirect page to user page
					redirect("planning");
					break;
			} // end switch
			
		} else {
			// redirect with session msessage
			$this->session->set_flashdata('msg',  'ข้อมูลไม่ถูกต้องกรุณาลองใหม่อีกครั้ง');
			header('Location: ../');
		}// end else
	}
	
	
	
	public function logout()
	{
		$this->load->view('frontend/include/header');
		$this->load->view('frontend/logout');
		$this->load->view('frontend/include/footer');
	}
}
