<?php
class StockAdjust extends CI_Controller {
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

		$this->load->view('frontend/stock/header', $userData);
		$this->load->view('frontend/stock/stockAdjust_v', $data);
		$this->load->view('frontend/stock/footer');
	}


	// ************************************************************* AJAX function ******************************
	// ------------------------------------------------------------- Get data set -------------------------------
	public function ajaxGetDsFullStock(){
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$arrayJobID = $this->getPostArrayHelper($this->input->post('jobID'));
			$arrayStepID = $this->getPostArrayHelper($this->input->post('stepID'));
			$pageCode = $this->input->post('pageCode');


			$totalPages = count($this->getDsStock($arrayJobID, $arrayStepID, null, null));
			$rData['dsView'] = $this->getDsStock($arrayJobID, $arrayStepID, $this->paginationLimit, $pageCode);
			$rData['pageCode'] = $pageCode;

			$data['htmlTableBody'] = $this->load->view("frontend/stock/bodyTableStock_v", $rData, TRUE);
			$data["paginationLinks"] = getPaginationHtml($totalPages, $this->paginationLimit, $pageCode);

			echo json_encode($data);
		}
	}

	//______________________________________________________________ Adjust Stock _______________________________
	public function ajaxAdjustStock(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$afftectedRows = -1;

			$jobID = $this->input->post('jobID');
			$stepID = $this->input->post('stepID');
			$firstStepFlag = $this->input->post('firstStepFlag');
			$stockQty = $this->input->post('stockQty');

			$result = $this->adjustStock($jobID, $stepID, $firstStepFlag, $stockQty);

			echo $result;
		}
	}





	// ********************************************************* Private function ****************************
	// --------------------------------------------------------- Initial combobox ----------------------------
	private function getInitialDataToDisplay(){
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
		$data['dsStep'] = $this->getDsStepJobOpen(0);
		$data["paginationLinks"] = getPaginationHtml();

		return $data;
	}



	// ----------------------------------------------------------- Adjust Stock -------------------------------
	private function adjustStock($jobID, $stepID, $firstStepFlag, $stockQty){
		if($firstStepFlag == 0) {
			$data_to_store = array('Qty_OK'	=> $stockQty,);
		}
		else {
			$data_to_store = array('Qty_OK_First_Step'	=> $stockQty,);
		}

		$this->load->model('stock_m');
		$result = $this->stock_m->update_row_by_job_id_and_step_id($jobID, $stepID, $data_to_store);

		return $result;
	}

	// -------------------------------------------------------- Get DB to combobox ----------------------------
	// -------------------------------------------------------- Get DB to table view --------------------------
	private function getDsStock($arrayJobID=[], $arrayStepID=[], $limit=null, $offset=null){
		$this->load->model('stock_m');
		$dsStock = $this->stock_m->getStepStockByMultiJobAndStepId($arrayJobID, $arrayStepID, $limit, $offset);

		return $dsStock;
	}


	// -------------------------------------------------------- Get DB to combobox ----------------------------
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