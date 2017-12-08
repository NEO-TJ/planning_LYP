<?php
class DailyTargetReport extends CI_Controller {

	/**
    * Responsable for auto load the model
    * @return void
	*/
	public function __construct() {
		parent::__construct();

		$this->is_logged();
	}
    
    
	public function index() {
		$data = [];
	
		if(!($this->is_logged())) {exit(0);}
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');
			
		$this->load->view('frontend/report/header', $userData);
		$this->load->view('frontend/report/dailyTarget_v', $data);
		$this->load->view('frontend/report/footer');
		$this->load->view('frontend/report/lastFooterDailyTarget');
	}
    

	// ************************************************************* AJAX function ******************************
	// ------------------------------------------------------------- Get data set -------------------------------
	public function ajaxGetDailyTargetReport() {
		if(!($this->is_logged())) {exit(0);}
			
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$useDataPlan = $this->input->post('useDataPlan');
			$strDateStart = $this->input->post('strDateStart');
			$strDateEnd = $this->input->post('strDateEnd');
			$lineID = $this->input->post('lineID');
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayStepID = $this->getPostArrayHelper($this->input->post('stepID'));

			$dsDailyTarget = $this->getDsDailyTarget($useDataPlan, $strDateStart, $strDateEnd
			, $lineID, $arrayJobID, $arrayStepID);

			echo json_encode($dsDailyTarget);
		}
	}

	//__________________________________________________________ Get Job by Line ID ______________________________
	public function ajaxGetDsJobByLineID() {
		if(!($this->is_logged())) {exit(0);}

    	if ($this->input->server('REQUEST_METHOD') === 'POST') {
				$lineID = $this->input->post('lineID');
    		$dsJob = $this->getDsJobByLineID($lineID);

    		echo json_encode($dsJob);
    	}
	}
    
    
    

	// ********************************************************* Private function ****************************
	// --------------------------------------------------------- Initial combobox ----------------------------
	private function getInitialDataToDisplay() {
		$data['dsLine'] = $this->getDsLine(0);
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
		$data['dsStep'] = $this->getDsStepJobOpen();
		
		return $data;
	}




	// -------------------------------------------------------- Get DB to combobox ----------------------------
	private function getDsDailyTarget($useDataPlan, $strDateStart, $strDateEnd, $lineID, $arrayJobID=[], $arrayStepID=[]) {
		if($useDataPlan == 1) {
			$this->load->model('plan_m');
			$dsDailyTarget = $this->plan_m->get_daily_target($strDateStart, $strDateEnd, $lineID, $arrayJobID, $arrayStepID);
		} else {
			$this->load->model('job_m');
			$dsDailyTarget = $this->job_m->get_print_process_barcode($lineID, $arrayJobID, $arrayStepID);
		}

		return $dsDailyTarget;
	}

	private function getDsJobByLineID($lineID=0) {
		$this->load->model('job_m');
		$dsJob = $this->job_m->getJobByLineID($lineID);

		return $dsJob;
	}

	private function getDsLine($id) {
		$this->load->model('line_m');
		$dsResult = (($id == 0) ? ($this->line_m->get_row()) : ($this->line_m->get_row_by_id($id)));
	
		return $dsResult;
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






	// ************************************************ Helper *****************************************
	private function getPostArrayHelper($arrayData) {
		return (((count($arrayData) == 1) && ($arrayData[0] == '')) ? $arrayData = [] : $arrayData);
	}




	// ********************************************************* Check logged *********************************
	private function is_logged() {
		if(!$this->session->userdata('id')){
			$this->logout();
			return false;
		} else {
			return true;
		}
	}
	private function logout() {
		$this->load->view('frontend/include/header');
		$this->load->view('frontend/logout');
		$this->load->view('frontend/include/footer');
	}
}	// ********************************************************** End logged **********************************