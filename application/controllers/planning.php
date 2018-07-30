<?php
class Planning extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->is_logged();
	}


	public function index() {
		$data = [];
		
		if(!($this->is_logged())) {exit(0);}
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');

		$this->load->view('frontend/planning/header', $userData);
		$this->load->view('frontend/planning/planning_v', $data);
		$this->load->view('frontend/planning/footer');
	}


	// ************************************************************* AJAX function ******************************
	// ___________________________________________________________ Search & Refresh _____________________________
	public function ajaxGetFullDsPlanning() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$dsFullPlanning = [];
	
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayStepID = $this->getPostArrayHelper($this->input->post('stepID'));
			$arrayLineID = $this->getPostArrayHelper($this->input->post('lineID'));
			$arrayJobTypeID = $this->getPostArrayHelper($this->input->post('jobTypeID'));
			$diffStartCurrentDate = $this->input->post('diffStartCurrentDate');
			$totalSlotDate = $this->input->post('totalSlotDate');

			$dsFullPlanning = $this->getDsFullPlan($diffStartCurrentDate, $arrayJobID, $arrayStepID
			, $arrayLineID, $arrayJobTypeID, $totalSlotDate);

			$data = array(
				'dsFullPlanning'				=> $dsFullPlanning,
				'diffStartCurrentDate'	=> $diffStartCurrentDate,
				'userLevel'							=> $this->session->userdata('level')
			);

			echo json_encode($data);
		}
	}

  //_____________________________________________________________ OK Qty Plan save ____________________________
	public function ajaxSaveOKQtyPlan() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$stockID = $this->input->post('stockID');
			$diffStartCurrentDate = $this->input->post('diffStartCurrentDate');
			$okQtyPlan = $this->input->post('okQtyPlan');
			
			$this->load->model('plan_m');
			$result = $this->plan_m->saveOKQtyPlan($stockID, $diffStartCurrentDate, $okQtyPlan);

			echo $result;
		}
	}
	//____________________________________________________________Worker Qty Plan save___________________________
	public function ajaxSaveWorkerQtyPlan() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$stockID = $this->input->post('stockID');
			$diffStartCurrentDate = $this->input->post('diffStartCurrentDate');
			$workerQtyPlan = $this->input->post('workerQtyPlan');
	
			$this->load->model('plan_m');
			$result = $this->plan_m->saveWorkerQtyPlan($stockID, $diffStartCurrentDate, $workerQtyPlan);
	
			echo $result;
		}
	}

	//________________________________________________ Shift date plan delay with offset sunday _________________
	public function ajaxShiftDatePlanDelayWithOffsetSun() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$afftectedRows = -1;
			
			$stockID = $this->input->post('stockID');
			$delayDayQty = $this->input->post('delayDayQty');
	
			$this->load->model('plan_m');
			$afftectedRows = $this->plan_m->shiftDatePlanDelayWithOffsetSun($stockID, $delayDayQty);
	
			echo $afftectedRows;
		}
	}
	
	
	//_________________________________________________________ Get Step by Job ID ______________________________
	public function ajaxGetDsStepByJobID() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));

			$dsStep = $this->getDsStepJobOpenAndJobID($arrayJobID);
	
			echo json_encode($dsStep);
		}
	}
	//_____________________________________________________ Get Step and Line by Job ID __________________________
	public function ajaxGetDsStepLineByJobID() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));

			$dsStep = $this->getDsStepJobOpenAndJobID($arrayJobID);
			$dsLine = $this->getDsLineJobOpenAndJobID($arrayJobID);
			$rResult = array(
				"dsStep"	=> $dsStep,
				"dsLine"	=> $dsLine
			);
	
			echo json_encode($rResult);
		}
	}








	// ********************************************************* Private function ****************************
	// --------------------------------------------------------- Initial combobox ----------------------------
	private function getInitialDataToDisplay() {
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
		$data['dsJobType'] = $this->getDsJobType(0);
		$data['dsStep'] = $this->getDsStepJobOpen();
		$data['dsLine'] = $this->getDsLineJobOpen();
		
		return $data;
	}




	// -------------------------------------------------------- Get DB to combobox ----------------------------
	private function getDsFullPlan($diffStartCurrentDate, $arrayJobID=[], $arrayStepID=[]
	, $arrayLineID=[], $arrayJobTypeID=[], $totalSlotDate=20) {
		$arrayJobStatusID=[1];
		$this->load->model('plan_m');
		$dsFullPlanning = $this->plan_m->getFullPlanRow($diffStartCurrentDate, $arrayJobID, $arrayStepID
		, $arrayLineID, $arrayJobTypeID, $totalSlotDate);

		return $dsFullPlanning;
	}
	
	private function getDsJobStatusOpen($id) {
		$this->load->model('job_m');
		$dsResult = (($id == 0) 
					? $this->job_m->get_all_row_status_open() 
					: $this->job_m->get_row_status_open_by_id($id));

		return $dsResult;
	}
	private function getDsJobType($id) {
		$this->load->model('jobType_m');
		$dsResult = (($id == 0) ? $this->jobType_m->get_row() : $this->jobType_m->get_row_by_id($id));

		return $dsResult;
	}
	private function getDsStepJobOpen() {
		$this->load->model('step_m');
		$dsResult = $this->step_m->getStep_Job_Open();

		return $dsResult;
	}
	private function getDsLineJobOpen() {
		$this->load->model('line_m');
		$dsResult = $this->line_m->getLine_Job_Open();

		return $dsResult;
	}


	private function getDsStepJobOpenAndJobID($arrayJobID=[]) {
		$this->load->model('step_m');
		$dsResult = $this->step_m->getStep_Job_Open_Job_ID($arrayJobID);
	
		return $dsResult;
	}
	private function getDsLineJobOpenAndJobID($arrayJobID=[]) {
		$this->load->model('line_m');
		$dsResult = $this->line_m->getLine_Job_Open_Job_ID($arrayJobID);
	
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
			if( ($this->session->userdata('level') == 1) or ($this->session->userdata('level') == 2) ) {
				return true;
			} else {
				$this->logout();
				return false;
			}
		}
	}
	private function logout() {
		$this->load->view('frontend/include/header');
		$this->load->view('frontend/logout');
		$this->load->view('frontend/include/footer');
	}
}	// ********************************************************** End logged **********************************