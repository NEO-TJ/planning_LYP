<?php
class WorkingCapacityReport extends CI_Controller {

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
		$this->load->view('frontend/report/workingCapacity_v', $data);
		$this->load->view('frontend/report/footer');
		$this->load->view('frontend/report/lastFooterWorkingCapacity');
	}


	// ************************************************************* AJAX function ******************************
	// ------------------------------------------------------------- Get data set -------------------------------
	public function ajaxGetWorkingCapacityReport(){
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$arrayCustomerID = $this->getPostArrayHelper($this->input->post('customerID'));
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayLineID = $this->getPostArrayHelper($this->input->post('lineID'));
			$arraySubAssemblyID = $this->getPostArrayHelper($this->input->post('subAssemblyID'));

			$dsWorkingCapacity = $this->getDsWorkingCapacity($arrayCustomerID, $arrayJobID
				, $arrayLineID, $arraySubAssemblyID);

			echo json_encode($dsWorkingCapacity);
		}
	}

	//_______________________________________________________ Get Job by Customer ID ____________________________
	public function ajaxGetDsJobByCustomerID(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$arrayCustomerID = $this->getPostArrayHelper($this->input->post('customerID'));

			$dsJob = $this->getDsJobByCustomerID($arrayCustomerID);

			echo json_encode($dsJob);
		}
	}

	//_________________________________________________ Get Line And SubAssembly by Job ID ______________________
	public function ajaxGetDsLineDsSubAssemblyByJobID(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));

			$dsLine = $this->getDsLineByJobID($arrayJobID);
			$dsSubAssembly = $this->getDsSubAssemblyByJobID($arrayJobID);

			$data = array(
				'dsLine'		=> $dsLine,
				'dsSubAssembly'	=> $dsSubAssembly
			);

			echo json_encode($data);
		}
	}






	// ********************************************************* Private function ****************************
	// --------------------------------------------------------- Initial combobox ----------------------------
	private function getInitialDataToDisplay(){
		$data['dsCustomer'] = $this->getDsCustomer(0);
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
		$data['dsLine'] = $this->getDsLine(0);
		$data['dsSubAssembly'] = $this->getDsSubAssembly(0);

		return $data;
	}




	// -------------------------------------------------------- Get DB to combobox ----------------------------
	private function getDsWorkingCapacity($arrayCustomerID=[], $arrayJobID=[], $arrayLineID=[], $arraySubAssemblyID=[]){
		$this->load->model('plan_m');
		$dsWorkingCapacity = $this->plan_m->get_workingCapacity($arrayCustomerID, $arrayJobID, $arrayLineID, $arraySubAssemblyID);

		return $dsWorkingCapacity;
	}
 

	private function getDsJobStatusOpen($id) {
		$this->load->model('job_m');
		$dsResult = (($id == 0) 
			? $this->job_m->get_all_row_status_open() 
			: $this->job_m->get_row_status_open_by_id($id));

		return $dsResult;
	}


	private function getDsCustomer($id){
		$this->load->model('customer_m');
		$dsCustomer = (($id == 0) ? $this->customer_m->get_row() : $this->customer_m->get_row_by_id($id));

		return $dsCustomer;
	}
	private function getDsJob($id){
		$this->load->model('job_m');
		$dsJob = (($id === 0) ? $this->job_m->get_row() : $this->job_m->get_row_by_id($id));

		return $dsJob;
	}
	private function getDsLine($id){
		$this->load->model('line_m');
		$dsResult = (($id == 0) ? ($this->line_m->get_row()) : ($this->line_m->get_row_by_id($id)));

		return $dsResult;
	}
	private function getDsSubAssembly($id){
		$this->load->model('subAssembly_m');
		$dsSubAssembly = (($id == 0) ? $this->subAssembly_m->get_row() : $this->subAssembly_m->get_row_by_id($id));
	
		return $dsSubAssembly;
	}


	private function getDsJobByCustomerID($arrayCustomerID=[]){
		$this->load->model('job_m');
		$dsResult = $this->job_m->getJob_Customer_ID($arrayCustomerID);

		return $dsResult;
	}
	private function getDsLineByJobID($arrayJobID=[]){
		$this->load->model('step_m');
		$dsResult = $this->step_m->getLine_Job_ID($arrayJobID);

		return $dsResult;
	}
	private function getDsSubAssemblyByJobID($arrayJobID=[]){
		$this->load->model('step_m');
		$dsResult = $this->step_m->getSubAssembly_Job_ID($arrayJobID);

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