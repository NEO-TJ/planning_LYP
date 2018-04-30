<?php
class TopRejectReport extends CI_Controller {

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
		$this->load->view('frontend/report/topReject_v', $data);
		$this->load->view('frontend/report/footer');
		$this->load->view('frontend/report/lastFooterTopReject');
	}


	// ************************************************************* AJAX function ******************************
	// ------------------------------------------------------------- Get data set -------------------------------
	public function ajaxGetTopRejectReport(){
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$strDateStart = $this->input->post('strDateStart');
			$strDateEnd = $this->input->post('strDateEnd');
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayStepID = $this->getPostArrayHelper($this->input->post('stepID'));

			$dsNGPercent = $this->getDsNGPercent($strDateStart, $strDateEnd, $arrayJobID, $arrayStepID);
			$dsTopReject = $this->getDsTopReject($strDateStart, $strDateEnd, $arrayJobID, $arrayStepID);

			$data = array(
					'dsNGPercent'	=> $dsNGPercent,
					'dsTopReject'	=> $dsTopReject
			);

			echo json_encode($data);
		}
	}




	// ********************************************************* Private function ****************************
	// --------------------------------------------------------- Initial combobox ----------------------------
	private function getInitialDataToDisplay(){
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
		$data['dsStep'] = $this->getDsStepJobOpen();

		return $data;
	}




	// -------------------------------------------------------- Get DB to combobox ----------------------------
	private function getDsNGPercent($strDateStart, $strDateEnd, $arrayJobID=[], $arrayStepID=[]){
		$this->load->model('activity_m');
		$dsNGPercent = $this->activity_m->get_ngPercentByStep($strDateStart, $strDateEnd, $arrayJobID, $arrayStepID);

		return $dsNGPercent;
	}
	private function getDsTopReject($strDateStart, $strDateEnd, $arrayJobID=[], $arrayStepID=[]){
		$this->load->model('activity_m');
		$dsTopReject = $this->activity_m->get_topReject($strDateStart, $strDateEnd, $arrayJobID, $arrayStepID);

		return $dsTopReject;
	}
 

	private function getDsJobStatusOpen($id) {
		$this->load->model('job_m');
		$dsResult = (($id == 0) 
			? $this->job_m->get_all_row_status_open() 
			: $this->job_m->get_row_status_open_by_id($id));

		return $dsResult;
	}
	private function getDsStepJobOpen() {
		$this->load->model('step_m');
		$dsResult = $this->step_m->getStep_Job_Open();

		return $dsResult;
	}


	private function getDsJob($id){
		$this->load->model('job_m');
		$dsJob = (($id === 0) ? $this->job_m->get_row() : $this->job_m->get_row_by_id($id));
	
		return $dsJob;
	}
	private function getDsStep($id){
		$this->load->model('step_m');
		$dsStep = (($id === 0) ? $this->step_m->get_row() : $this->step_m->get_row_by_id($id));

		return $dsStep;
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