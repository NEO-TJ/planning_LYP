<?php
class Process extends CI_Controller {
	// Variable.
	private $inputModeName = [1 => 'New Process', 2 => 'Edit Process', 3 => 'Copy Process'];


	// Construction.
	public function __construct(){
		parent::__construct();

		$this->is_logged();
	}



	// ************************************************************ Public function ******************************
	public function index(){
		if(!($this->is_logged())) {exit(0);}
		$data = $this->getDataToDisplayViewMode();
		$userData['level'] = $this->session->userdata('level');

		$this->load->view('frontend/process/list/header', $userData);
		$this->load->view('frontend/process/list/body_v', $data);
		$this->load->view('frontend/process/list/footer');
	}
	public function addNew(){
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$inputMode=1;
			$rowID = 0;

			$this->setInputDisplayMode($inputMode, $rowID);
		}
	}
	public function edit(){
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$rowID = $this->input->post('rowID');
			if($rowID < 0){
				$inputMode = 3;
				$rowID *= -1;
			}else{
				$inputMode = 2;
			}

			$this->setInputDisplayMode($inputMode, $rowID);
		}
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
	// -------------------------------------------------------- Initial view mode ----------------------------
	private function getDataToDisplayViewMode(){
		$data['dsView'] = $this->getDsProcess(0);

		return $data;
	}
	// ------------------------------------------------------- Set input display mode ----------------------------
	private function setInputDisplayMode($inputMode=1, $rowID=0){
		$userData['level'] = $this->session->userdata('level');
		$data = $this->getDataToDisplayInputMode($rowID);
		$data['inputModeName'] = $this->inputModeName[$inputMode];
		$data['inputMode'] = $inputMode;
		if($inputMode == 3) {
			$data['dsProcess']['id'] = 0;
			$data['dsProcess']['Name'] = "Copy - " . $data['dsProcess']['Name'];
		}

		$this->load->view('frontend/process/input/header', $userData);
		$this->load->view('frontend/process/input/body_v', $data);
		$this->load->view('frontend/process/input/footer');
	}
	// -------------------------------------------------------- Initial input mode -------------------------------
	private function getDataToDisplayInputMode($rowID=0) {
		// Get ds for combobox.
		$result['dsLine'] = $this->getDsLine(0);
		$result['dsMachine'] = $this->getDsMachine(0);
		$result['dsSubAssembly'] = $this->getDsSubAssembly(0);

		$this->load->model('process_m');
		$dataset = $this->process_m->get_row_by_id($rowID);
		$result['dsProcess'] = ((count($dataset) > 0) ? $dataset[0] : $this->process_m->get_template());

		if(count($result['dsProcess']) > 0) {
			$result['dsStep'] = $this->getDsStep($result['dsProcess']['id']);
		}

		return $result;
	}

	private function getDsStep($processID){
		$dsStep = [];
		$this->load->model('step_m');
		$dsStep = $this->step_m->get_by_where_array(["FK_ID_Process" => $processID]);

		return $dsStep;
	}


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