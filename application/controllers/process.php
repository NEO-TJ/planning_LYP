<?php
class Process extends CI_Controller {
// Property.
	private $inputModeName = [1 => 'New Process', 2 => 'Edit Process', 3 => 'Copy Process'];
	private $paginationLimit = 50;
// End Property.

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
			if($rowID <= 0){
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
	public function ajaxSaveFullProcess(){
		if(!($this->is_logged())) {exit(0);}
		$result = 1;
		$resultSave = false;

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			// -------------- Save Process Part ------------------------------------------
			$processId = $this->input->post('processId');
			$dataProcessToStore = array(
				'Name' 		=> $this->input->post('processName'),
				'DESC' 		=> $this->input->post('processDesc'),
				'DESC_Thai'	=> $this->input->post('processDescThai')
			);
			// ----------- Prepare data Step Part ----------------------------------------
			$dsStep = $this->input->post('dsStep');

			$this->load->model('process_m');
			$resultSave = $this->process_m->transactionSaveFullProcess($processId, $dataProcessToStore, $dsStep);

			$result = ($resultSave ? 0 : 1);
		}

		echo $result;
	}

	public function ajaxGetProcessList(){
		if(!($this->is_logged())) {exit(0);}

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			// -------------- Save Process Part ------------------------------------------
			$rProcessID = $this->getPostArrayHelper($this->input->post('rProcessID'));
			$pageCode = $this->input->post('pageCode');


			$totalPages = count($this->getDsProcess($rProcessID, null, null));
			$rData['dsView'] = $this->getDsProcess($rProcessID, $this->paginationLimit, $pageCode);
			$rData['pageCode'] = $pageCode;

			$data['htmlTableBody'] = $this->load->view("frontend/process/list/bodyTableProcess_v", $rData, TRUE);
			$data["paginationLinks"] = getPaginationHtml($totalPages, $this->paginationLimit, $pageCode);

			echo json_encode($data);
		}
	}





	// ********************************************************* Private function ****************************
	// -------------------------------------------------------- Initial view mode ----------------------------
	private function getDataToDisplayViewMode(){
		$data['dsProcess'] = $this->getDsProcess();
		$data["paginationLinks"] = getPaginationHtml();

		return $data;
	}
	// ------------------------------------------------------- Set input display mode ----------------------------
	private function setInputDisplayMode($inputMode=1, $rowID=0){
		$userData['level'] = $this->session->userdata('level');
		$data = $this->getDataToDisplayInputMode($rowID);
		$data['inputModeName'] = $this->inputModeName[$inputMode];
		$data['inputMode'] = $inputMode;
		if($inputMode == 3) {
			// Process Part.
			$data['dsProcess']['id'] = 0;
			$data['dsProcess']['Name'] = "Copy - " . $data['dsProcess']['Name'];
			// Step Part.
			foreach($data['dsStep'] as $row) {
				$row['id'] = 0;
			}
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
		$dsProcess = $this->process_m->get_row_by_id($rowID);
		$result['dsProcess'] = ((count($dsProcess) > 0) ? $dsProcess[0] : $this->process_m->get_template());

		if(count($result['dsProcess']) > 0) {
			$this->load->model('step_m');
			$dsStep = $this->getDsStep($result['dsProcess']['id']);
			$result['dsStep'] = ((count($dsStep) > 0) ? $dsStep : $this->step_m->get_template());
			//echo(json_encode($dsStep));exit;
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
		$data['dsProcess'] = $this->getDsProcess();

		$data['dsLine'] = $this->getDsLine(0);
		$data['dsMachine'] = $this->getDsMachine(0);
		$data['dsSubAssembly'] = $this->getDsSubAssembly(0);

		return $data;
	}
	
	
	// -------------------------------------------------------- Get DB to table view --------------------------
	private function getDsProcess($rProcessID=[], $limit=null, $offset=null){
		$this->load->model('process_m');
		$dsProcess = $this->process_m->getRowByArrayID($rProcessID, $limit, $offset);

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