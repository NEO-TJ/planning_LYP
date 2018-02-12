<?php
class ActivityRevoke extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->is_logged();
	}


// ****************************************** Routing function *************************************
	public function index() {
	}

	public function activityQtyInput() {
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');
		
		$this->load->view('frontend/activityRevoke/header', $userData);
		$this->load->view('frontend/activityRevoke/actQtyInput_v', $data);
		$this->load->view('frontend/activityRevoke/footerActQtyInput');
	}

	public function activityRecoveryNG() {
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');

		$this->load->view('frontend/activityRevoke/header', $userData);
		$this->load->view('frontend/activityRevoke/actRecoveryNG_v', $data);
		$this->load->view('frontend/activityRevoke/footerActRecoveryNG');
	}
// **************************************** End Routing function ***********************************



// ******************************************* AJAX function ***************************************
	// ------------------------------------------ Get data set ---------------------------------------
	public function ajaxGetActivityQtyInput() {
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayStepID = $this->getPostArrayHelper($this->input->post('stepID'));
			$arrayLineID = $this->getPostArrayHelper($this->input->post('lineID'));

			$dsActQtyInput = $this->getDsActQtyInput(1000, $arrayJobID, $arrayStepID, $arrayLineID);

			echo json_encode($dsActQtyInput);
		}
	}

	public function ajaxGetActivityRecoveryNG() {
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayStepID = $this->getPostArrayHelper($this->input->post('stepID'));
			$arrayLineID = $this->getPostArrayHelper($this->input->post('lineID'));

			$dsActQtyInput = $this->getDsActRecoveryNG(1000, $arrayJobID, $arrayStepID, $arrayLineID);

			echo json_encode($dsActQtyInput);
		}
	}
// ****************************************** End AJAX function ****************************************




// ***************************************** Get function ******************************************
	// ----------------------------------------- Initial combobox --------------------------------------
	private function getInitialDataToDisplay() {
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
		$data['dsStep'] = $this->getDsStep(0);
		$data['dsLine'] = $this->getDsLine(0);

		return $data;
	}

	// ------------------------------------------ Get DB to combobox -----------------------------------
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Job Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsJobStatusOpen($id){
		$this->load->model('job_m');
		$dsJob = (($id == 0)
			? $this->job_m->get_all_row_status_open()
			: $this->job_m->get_row_status_open_by_id($id));

		return $dsJob;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsStep($stepID) {
		$this->load->model('step_m');
		$dsStep = $this->step_m->get_by_where_array(array('id' => $stepID));
	
		return $dsStep;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Line Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsLine($id) {
		$this->load->model('line_m');
		$dsLine = (($id == 0) ? $this->line_m->get_row() : $this->line_m->get_row_by_id($id));

		return $dsLine;
	}
	// ---------------------------------------- End Get DB to combobox -----------------------------------



	// -------------------------------------- Initial Last activity table ------------------------------
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Last activity Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsActQtyInput($rowNumber=10, $arrayJobID=[], $arrayStepID=[], $arrayLineID=[]) {
		$this->load->model('activity_m');
		$dsActQtyInput = $this->activity_m->get_last_full_activity(
			$rowNumber, $arrayJobID, $arrayStepID, $arrayLineID);
	
		return $dsActQtyInput;
	}

	private function getDsActRecoveryNG($rowNumber=10, $arrayJobID=[], $arrayStepID=[], $arrayLineID=[]) {
		$this->load->model('activity_m');
		$dsActRecoveryNG = $this->activity_m->get_last_full_recovery_NG_activity(
			$rowNumber, $arrayJobID, $arrayStepID, $arrayLineID);

		return $dsActRecoveryNG;
	}
// ***************************************** End Get function **************************************









// ************************************************ Helper *****************************************
	private function getPostArrayHelper($arrayData) {
		return (((count($arrayData) == 1) && ($arrayData[0] == '')) ? $arrayData = [] : $arrayData);
	}
// ************************************************ End Helper *****************************************



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
// ********************************************************** End logged **********************************
}