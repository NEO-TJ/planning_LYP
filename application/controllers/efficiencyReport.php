<?php
class EfficiencyReport extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->is_logged();
	}
    
    
	public function index(){
		$data = [];

		if(!($this->is_logged())) {exit(0);}
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');

		$this->load->view('frontend/report/header', $userData);
		$this->load->view('frontend/report/efficiency_v', $data);
		$this->load->view('frontend/report/footer');
		$this->load->view('frontend/report/lastFooterEfficiency');
	}


	// ************************************************************* AJAX function ******************************
	// ------------------------------------------------------------- Get data set -------------------------------
	public function ajaxGetEfficiencyReport(){
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$strDateStart = $this->input->post('strDateStart');
			$strDateEnd = $this->input->post('strDateEnd');
			$arrayLineID = $this->getPostArrayHelper($this->input->post('lineID'));

			$dsEfficiency = $this->getDsEfficiency($strDateStart, $strDateEnd, $arrayLineID);

			echo json_encode($dsEfficiency);
		}
	}




	// ********************************************************* Private function ****************************
	// --------------------------------------------------------- Initial combobox ----------------------------
	private function getInitialDataToDisplay(){
		$data['dsLine'] = $this->getDsLine(0);

		return $data;
	}




	// -------------------------------------------------------- Get DB to combobox ----------------------------
	private function getDsEfficiency($strDateStart, $strDateEnd, $arrayLineID=[]){
		$this->load->model('activity_m');
		$dsEfficiency = $this->activity_m->get_efficiency($strDateStart, $strDateEnd, $arrayLineID);
		
		return $dsEfficiency;
	}


	private function getDsLine($id){
		$this->load->model('line_m');
		$dsResult = (($id == 0) ? ($this->line_m->get_row()) : ($this->line_m->get_row_by_id($id)));

		return $dsResult;
	}






	// ************************************************ Helper *****************************************
	private function getPostArrayHelper($arrayData){
		return (((count($arrayData) == 1) && ($arrayData[0] == '')) ? $arrayData = [] : $arrayData);
	}




	// ********************************************************* Check logged *********************************
	private function is_logged(){
		if(!$this->session->userdata('id')){
			$this->logout();
			return false;
		} else {
			return true;
		}
	}
	private function logout(){
		$this->load->view('frontend/include/header');
		$this->load->view('frontend/logout');
		$this->load->view('frontend/include/footer');
	}
}	// ********************************************************** End logged **********************************