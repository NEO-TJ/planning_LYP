<?php
class ProcessCreate extends CI_Controller {
	public function __construct(){
		parent::__construct();

		$this->is_logged();
	}


	public function index(){
		if(!($this->is_logged())) {exit(0);}
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');

		$this->load->view('frontend/process/header', $userData);
		$this->load->view('frontend/process/ProcessCreate_v', $data);
		$this->load->view('frontend/process/footer');
	}


	// ************************************************************* AJAX function ******************************
	// ________________________________________ Save New Full Process _______________________________________
	public function ajaxSaveNewFullProcess(){
		if(!($this->is_logged())) {exit(0);}
		$result = 1;
		$resultSave = false;
		$resultQtyPlanProduct = false;
		$cloneMode = false;

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			// -------------- Save Process Part ------------------------------------------
			$dataProcess_to_store = array(
				'Name' 		=> $this->input->post('processName'),
				'DESC' 		=> $this->input->post('processDesc'),
				'DESC_Thai'	=> $this->input->post('processDescThai')
			);
			// ----------- Prepare data Step Part ----------------------------------------
			$dsStep = $this->input->post('dsStep');

			$this->load->model('process_m');
			$resultSave = $this->process_m->transaction_save_full_process(0, $dataProcess_to_store, $dsStep);

			$result = ($resultSave ? 0 : 1);
		}

		echo $result;
	}





	// ********************************************************* Private function ****************************
	// --------------------------------------------------------- Initial combobox ----------------------------
	private function getInitialDataToDisplay(){
		$data['dsProcess'] = $this->getDsProcess(0);

		$data['dsLine'] = $this->getDsLine(0);
		$data['dsMachine'] = $this->getDsMachine(0);
		$data['dsSubAssembly'] = $this->getDsSubAssembly(0);

		return $data;
	}
	
	
	// -------------------------------------------------------- Get DB to table view --------------------------
	private function getDsProcess($id){
		$this->load->model('process_m');
		$dsProcess = (($id === 0) ? $this->process_m->get_row() : $this->process_m->get_row_by_id($id));

		return $dsProcess;
	}



	// -------------------------------------------------------- Get DB to combobox ----------------------------
	private function getDsLine($id){
		$this->load->model('line_m');
		$dsLine = (($id == 0) ? $this->line_m->get_row() : $this->line_m->get_row_by_id($id));

		return $dsLine;
	}
	private function getDsMachine($id){
		$this->load->model('machine_m');
		$dsMachine = (($id == 0) ? $this->machine_m->get_row() : $this->machine_m->get_row_by_id($id));

		return $dsMachine;
	}
	private function getDsSubAssembly($id){
		$this->load->model('subAssembly_m');
		$dsSubAssembly = (($id == 0) ? $this->subAssembly_m->get_row() : $this->subAssembly_m->get_row_by_id($id));

		return $dsSubAssembly;
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