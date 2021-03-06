<?php
class AchievementReport extends CI_Controller {

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
		$this->load->view('frontend/report/achievement_v', $data);
		$this->load->view('frontend/report/footer');
		$this->load->view('frontend/report/lastFooterAchievement');
	}
    

	// ************************************************************* AJAX function ******************************
	// ------------------------------------------------------------- Get data set -------------------------------
	public function ajaxGetAchievementReport() {
		if(!($this->is_logged())) {exit(0);}
			
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$strDateStart = $this->input->post('strDateStart');
			$strDateEnd = $this->input->post('strDateEnd');
			$arrayLineID = $this->getPostArrayHelper($this->input->post('lineID'));
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayStepID = $this->getPostArrayHelper($this->input->post('stepID'));

			$dsAchievement = $this->getDsAchievement($strDateStart, $strDateEnd, $arrayLineID
			, $arrayJobID, $arrayStepID);

			echo json_encode($dsAchievement);
		}
	}

	//_________________________________________________________ Get Step by Job ID ______________________________
	public function ajaxGetDsStepByJobID() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));

			$dsStep = $this->getDsStepByJobID($arrayJobID);

			echo json_encode($dsStep);
		}
	}

	//________________________________________________________ Get Line by Step ID ______________________________
	public function ajaxGetDsLineByStepID() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$arrayStepID = $this->getPostArrayHelper($this->input->post('stepID'));

			$dsLine = $this->getDsLineByStepID($arrayStepID);

			echo json_encode($dsLine);
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
	private function getDsAchievement($strDateStart, $strDateEnd, $arrayLineID=[], $arrayJobID=[], $arrayStepID=[]) {
		$this->load->model('plan_m');
		$dsResult = $this->plan_m->getDsAchievement($strDateStart, $strDateEnd, $arrayLineID, $arrayJobID, $arrayStepID);

		// Calc achievement.
		$rMyId = array_column($dsResult["dsActivityOk"], 'myId');
		$i=0;
		foreach($dsResult["dsPlanOk"] as $row) {
			$key = array_search($row["myId"], $rMyId);
			if($key > 0) {
				$dsResult["dsPlanOk"][$i]["actualOkQty"] = $dsResult["dsActivityOk"][$key]["actualOkQty"];
				$dsResult["dsPlanOk"][$i]["achievementOkQty"] = $dsResult["dsActivityOk"][$key]["actualOkQty"]
					/ (
						(($dsResult["dsPlanOk"][$i]["planOkQty"] == 0)
						? (($dsResult["dsActivityOk"][$key]["actualOkQty"] > 0) ? 100 :1)
						: $dsResult["dsPlanOk"][$i]["planOkQty"])
					) * 100;
				unset($dsResult["dsActivityOk"][$key]);
			}
			$i++;
		}
		$dsAchievement = array_merge($dsResult["dsPlanOk"], $dsResult["dsActivityOk"]);

		// Sort rows achievement.
		foreach($dsAchievement as $key=>$row) {
			$sortJobName[] = $row['jobName'];
			$sortStepNumber[] = $row['Number'];
			$sortDateStamp[] = $row['dateStamp'];
		}

		array_multisort($sortJobName, SORT_ASC, $sortStepNumber, SORT_ASC, $sortDateStamp, SORT_ASC, $dsAchievement);



		return $dsAchievement;
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


	private function getDsJob($id) {
		$this->load->model('job_m');
		$dsJob = (($id === 0) ? $this->job_m->get_row() : $this->job_m->get_row_by_id($id));

		return $dsJob;
	}
	private function getDsStep($id) {
		$this->load->model('step_m');
		$dsStep = (($id === 0) ? $this->step_m->get_row() : $this->step_m->get_row_by_id($id));

		return $dsStep;
	}
	private function getDsLine($id) {
		$this->load->model('line_m');
		$dsResult = (($id == 0) ? ($this->line_m->get_row()) : ($this->line_m->get_row_by_id($id)));

		return $dsResult;
	}


	private function getDsStepByJobID($arrayJobID=[]) {
		$this->load->model('step_m');
		$dsResult = $this->step_m->getStep_Job_ID($arrayJobID);

		return $dsResult;
	}
	private function getDsLineByStepID($arrayStepID=[]) {
		$this->load->model('step_m');
		$dsResult = $this->step_m->getLine_Step_ID($arrayStepID);

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