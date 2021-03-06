<?php
class JobRemove extends CI_Controller {
	// Property.
		private $paginationLimit = 50;
	// End Property.

	public function __construct(){
		parent::__construct();
		$this->is_logged();
	}


	public function index(){
		$data = [];

		if(!($this->is_logged())) {exit(0);}
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');

		$this->load->view('frontend/job/header', $userData);
		$this->load->view('frontend/job/jobRemove_v', $data);
		$this->load->view('frontend/job/footer');
	}


	// ************************************************************* AJAX function ******************************
	// ------------------------------------------------------------- Get data set -------------------------------
	public function ajaxGetDsFullJob(){
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayjobTypeID = $this->getPostArrayHelper($this->input->post('jobTypeID'));
			$arrayjobStatusID = $this->getPostArrayHelper($this->input->post('jobStatusID'));
			$pageCode = $this->input->post('pageCode');


			$totalPages = count($this->getDsFullJob($arrayJobID, $arrayjobTypeID, $arrayjobStatusID, null, null));
			$rData['dsView'] = $this->getDsFullJob($arrayJobID, $arrayjobTypeID, $arrayjobStatusID, $this->paginationLimit, $pageCode);
			$rData['pageCode'] = $pageCode;

			$data['htmlTableBody'] = $this->load->view("frontend/job/bodyTableJob_v", $rData, TRUE);
			$data["paginationLinks"] = getPaginationHtml($totalPages, $this->paginationLimit, $pageCode);

			echo json_encode($data);
		}
	}


	//_______________________________________________ Get Job by Job Type ID and Job Status ID __________________
	public function ajaxGetDsJobByJobTypeJobStatusID(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$arrayjobTypeID = $this->getPostArrayHelper($this->input->post('jobTypeID'));
			$arrayjobStatusID = $this->getPostArrayHelper($this->input->post('jobStatusID'));

			$dsJob = $this->getDsJobByJobTypeJobStatusID($arrayjobTypeID, $arrayjobStatusID);

			echo json_encode($dsJob);
		}
	}

	//______________________________________________________________ Adjust Stock _______________________________
	public function ajaxRemoveJob(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$afftectedRows = -1;

			$jobID = $this->input->post('jobID');

			$this->load->model('job_m');
			$result = $this->job_m->removeJob($jobID);

			echo $result;
		}
	}





	// ********************************************************* Private function ****************************
	// --------------------------------------------------------- Initial combobox ----------------------------
	private function getInitialDataToDisplay(){
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
		$data['dsJobType'] = $this->getDsJobType(0);
		$data['dsJobStatus'] = $this->getDsJobStatus(0);
		$data["paginationLinks"] = getPaginationHtml();

		return $data;
	}


	// -------------------------------------------------------- Get DB to table view --------------------------
	private function getDsFullJob($arrayJobID=[], $arrayjobTypeID=[], $arrayjobStatusID=[], $limit=null, $offset=null){
		$this->load->model('job_m');
		$dsFullJob = $this->job_m->getJobByMultiJobIdTypeStatus($arrayJobID, $arrayjobTypeID, $arrayjobStatusID, $limit, $offset);

		return $dsFullJob;
	}


	// -------------------------------------------------------- Get DB to combobox ----------------------------
	private function getDsJobStatusOpen($id) {
		$this->load->model('job_m');
		$dsResult = (($id == 0) 
			? $this->job_m->get_all_row_status_open() 
			: $this->job_m->get_row_status_open_by_id($id));

		return $dsResult;
	}


	private function getDsJob($id){
		$this->load->model('job_m');
		$dsJob = (($id === 0) ? $this->job_m->get_row() : $this->job_m->get_row_by_id($id));

		return $dsJob;
	}
	private function getDsJobType($id){
		$this->load->model('jobType_m');
		$dsJobType = (($id === 0) ? $this->jobType_m->get_row() : $this->jobType_m->get_row_by_id($id));

		return $dsJobType;
	}
	private function getDsJobStatus($id){
		$this->load->model('jobStatus_m');
		$dsJobStatus = (($id === 0) ? $this->jobStatus_m->get_row() : $this->jobStatus_m->get_row_by_id($id));

		return $dsJobStatus;
	}


	private function getDsJobByJobTypeJobStatusID($arrayjobTypeID=[], $arrayjobStatusID=[]){
		$this->load->model('job_m');
		$dsResult = $this->job_m->get_row_by_type_status($arrayjobTypeID, $arrayjobStatusID);

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
			if($this->session->userdata('level') == 1) {
				return true;
			} else {
				$this->logout();
				return false;
			}
		}
	}
	private function logout(){
		$this->load->view('frontend/include/header');
		$this->load->view('frontend/logout');
		$this->load->view('frontend/include/footer');
	}
}	// ********************************************************** End logged **********************************