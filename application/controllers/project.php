<?php
class Project extends CI_Controller {
	public function __construct(){
		parent::__construct();

		$this->is_logged();
	}
    
    
	public function index(){
		if(!($this->is_logged())) {exit(0);}
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');

		$this->load->view('frontend/project/header',$userData);
		$this->load->view('frontend/project/project_v', $data);
		$this->load->view('frontend/project/footer');
	}


	// ****************************************** AJAX function ****************************************
	// ----------------------------------------- Save data to DB ---------------------------------------
	// __________________________________________ Save Project _________________________________________
	public function ajaxSaveProject(){
		if(!($this->is_logged())) {exit(0);}
		$arrResult = array();

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$projectID = $this->input->post('projectID');
			$data_to_store = array(
				'Name' 				=> $this->input->post('projectName'),
				'FK_ID_Customer' 	=> $this->input->post('customerID'),
			);

			$this->load->model('project_m');
			$projectID = $this->project_m->save_return_id($projectID, $data_to_store);
			$result = (($projectID > -1) ? 0 : 1);
			$dsProject = $this->getDsProject(0);

			$arrResult = array(
				'result'	=> $result,
				'projectID'	=> $projectID,
				'dsProject'	=> $dsProject,
			);
		}

		echo json_encode($arrResult);
	}
	// ____________________________________________ Save Job ___________________________________________
	public function ajaxSaveJob(){
		if(!($this->is_logged())) {exit(0);}
		$arrResult = array();

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$jobID = $this->input->post('jobID');
			$projectID = $this->input->post('projectID');
			$data_to_store = array(
				'Name' 				=> $this->input->post('jobName'),
				'model'				=> $this->input->post('modelName'),
				'FK_ID_Project' 	=> $projectID,
				'FK_ID_BOM'		 	=> $this->input->post('bomID'),
				'Qty_Order'			=> $this->input->post('qtyOrder'),
				'Qty_Plan_Product'	=> $this->input->post('qtyPlanProduct'),
				'FK_ID_Job_Type' 	=> $this->input->post('jobTypeID'),
				'FK_ID_Job_Status' 	=> $this->input->post('jobStatusID'),
			);

			$this->load->model('job_m');
			$jobID = $this->job_m->save_return_id($jobID, $data_to_store);
			$result = (($jobID > -1) ? 0 : 1);
			$dsJob = $this->getDsJobByProjectID($projectID);

			$arrResult = array(
				'result'	=> $result,
				'jobID'		=> $jobID,
				'dsJob'		=> $dsJob,
			);
		}

		echo json_encode($arrResult);
	}
	// __________________________________________ Save Full BOM ________________________________________
	public function ajaxSaveFullBom(){
		if(!($this->is_logged())) {exit(0);}
		$arrResult = array();
		$resultBom = false;
		$resultBomRm = true;

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			// ---------------- Save Bom Part --------------------------------------------
			$bomID = $this->input->post('bomID');
			$dataBom_to_store = array(
				'Name' 				=> $this->input->post('bomName'),
				'DESC' 				=> $this->input->post('bomDesc'),
				'DESC_Thai'		 	=> $this->input->post('bomDescThai'),
			);

			$this->load->model('bom_m');
			if($bomID == 0) {					// Check Add mode of BOM ?
				$insertID = $this->bom_m->insert_row($dataBom_to_store);
				if($insertID != 0) {
					$bomID = $insertID;
					$resultBom = true;
				}
			} else {							// Check Edit mode of BOM ?
				$resultBom = $this->bom_m->update_row($bomID, $dataBom_to_store);
			}


			// ---------------- Save Bom_Rm Part -----------------------------------------
			$dsRm = $this->input->post('dsRm');
			$oldStrBomRmID = $this->input->post('oldStrBomRmID');

			$arrBomRmID = json_decode('[' . $oldStrBomRmID . ']', true);
			$oldRecCount = count($arrBomRmID);
			$newRecCount = count($dsRm);

			$this->load->model('bomRm_m');
			$i = 0;
			foreach($dsRm as $row){
				$dataBomRm_to_store = array(
					'FK_ID_BOM'	=> $bomID,
					'FK_ID_RM'	=> $dsRm[$i]['rmID'],
					'Qty'		=> $dsRm[$i]['qty']
				);

				if($oldRecCount > $i) {
					// Update.
					$bomRm_id = $arrBomRmID[$i];
					$resultBomRm = $resultBomRm && $this->bomRm_m->update_row($bomRm_id, $dataBomRm_to_store);
				} else {
					// Insert.
					$resultBomRm = $resultBomRm && $this->bomRm_m->insert_row($dataBomRm_to_store);
				}

				$i++;
			}

			// ------------ Check remain rec bom_rm in DB.
			$remainRecCount = $oldRecCount - $newRecCount;
			if($remainRecCount > 0) {
				for($j=0; $j < $remainRecCount; $j++) {
					// Delete.
					$bomRm_id = $arrBomRmID[$newRecCount + $j];
					$resultBomRm = $resultBomRm && $this->bomRm_m->delete_row($bomRm_id);
				}
			}


			$result = (($resultBom && $resultBomRm) ? 0 : 1);
			$dsBom = $this->getDsBOM(0);

			$arrResult = array(
				'result'	=> $result,
				'bomID'		=> $bomID,
				'dsBom'		=> $dsBom,
			);
		}

		echo json_encode($arrResult);
	}
	// __________________________________________ Save Process _________________________________________
	public function ajaxSaveProcess(){
		if(!($this->is_logged())) {exit(0);}
		$arrResult = array();

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			// -------------- Save Process Part ------------------------------------------
			$processID = $this->input->post('processID');
			$dataProcess_to_store = array(
				'Name' 				=> $this->input->post('processName'),
				'DESC' 				=> $this->input->post('processDesc'),
				'DESC_Thai'		 	=> $this->input->post('processDescThai')
			);

			$this->load->model('process_m');
			if($processID == 0) {					// Check Add mode of Process ?
				$insertProcessID = $this->process_m->insert_row($dataProcess_to_store);
				if($insertProcessID != 0) {
					$processID = $insertProcessID;
					$resultProcess = true;
				}
			} else {								// Check Edit mode of Process ?
				$resultProcess = $this->process_m->update_row($processID, $dataProcess_to_store);
			}

			$result = (($resultProcess) ? 0 : 1);
			$dsProcess = $this->getDsProcess(0);

			$arrResult = array(
				'result'	=> $result,
				'processID'	=> $processID,
				'dsProcess'	=> $dsProcess,
			);
		}
	
		echo json_encode($arrResult);
	}
	// ________________________________________ Save All Project _______________________________________
	public function ajaxSaveAllProject(){
		if(!($this->is_logged())) {exit(0);}
		$result = 2;
		$resultSave = false;
		$resultQtyPlanProduct = false;

		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			// -------- Prepare data Job and BOM Part ------------------------------------
			$jobID = $this->input->post('jobID');
			$bomID = $this->input->post('bomID');
			$processID = $this->input->post('processID');
			$dsStep = $this->input->post('dsStep');
			$qtyPlanProduct = $this->getQtyPlanProduct($jobID);

			if($qtyPlanProduct > -1) {
				$resultQtyPlanProduct = true;
				// ----------- Prepare data StepStock Part ----------------------------------------
				$this->load->model('process_m');
				$resultSave = $this->process_m->transactionSaveFullProject(
					$jobID, $bomID, $processID, $dsStep, $qtyPlanProduct);
			}

			if($resultQtyPlanProduct) {
				$result = ($resultSave ? 0 : 1);
			} else {
				$result = 2;
			}
		}

		echo $result;
	}





	// ------------------------------------------------------------- Get data set -------------------------------
	public function ajaxGetDsProjectListJob(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$dsProject = [];
			$dsJob = [];

			$projectID = $this->input->post('projectID');
			$dsProject = $this->getDsProject($projectID);

			if(count($dsProject > 0)) {
				$dsJob = $this->getDsJobByProjectID($projectID);
			}

			$data = array(
				'dsProject'	=> $dsProject,
				'dsJob'		=> $dsJob
			);

			echo json_encode($data);
		}
	}

	public function ajaxGetDsJobListBomAndProcess(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$dsJob = [];
			$dsBom = [];
			$dsProcess = [];

			$id = $this->input->post('jobID');
			$dsJob = $this->getDsJob($id);

			if(count($dsJob > 0)) {
				if(!empty($dsJob[0]['FK_ID_BOM'])) {
					$dsBom = $this->getDsBOM($dsJob[0]['FK_ID_BOM']);
				} else {
					$dsBom = $this->getDsBOM(0);
				}

				if(!empty($dsJob[0]['FK_ID_Process'])) {
					$dsProcess = $this->getDsProcess($dsJob[0]['FK_ID_Process']);
				} else {
					$dsProcess = $this->getDsProcess(0);
				}
			}


			$data = array(
				'dsJob'		=> $dsJob,
				'dsBom'		=> $dsBom,
				'dsProcess' => $dsProcess
			);

			echo json_encode($data);
		}
	}

	public function ajaxGetDsFullProcess(){
		if(!($this->is_logged())) {exit(0);}

		$dsProcess = [];
		$dsFullStep = [];
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$jobID = $this->input->post('jobID');
			$processID = $this->input->post('processID');

			$dsProcess = $this->getDsProcess($processID);
			$dsFullStep = $this->getDsFullStep($jobID, $processID);

			$data = array('dsProcess' => $dsProcess, 'dsFullStep' => $dsFullStep);

			echo json_encode($data);
		}
	}

	public function ajaxGetDsFullBom(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$id = $this->input->post('bomID');

			$dsBom = $this->getDsBOM($id);
			$dsFullBom = $this->getDsFullBom($id);

			$data = array(
				'dsBom' 	=> $dsBom,
				'dsFullBom'	=> $dsFullBom
			);

			echo json_encode($data);
		}
	}

	public function ajaxGetUnitName(){
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$id = $this->input->post('rmID');
			$dsFullRm = $this->getUnitNameByRm($id);

			if(count($dsFullRm) > 0) {
				$unitName = $dsFullRm[0]['unit_name'];
			} else {
				$unitName = ' - ';
			}

			echo $unitName;
		}
	}








	// ***************************************** Private function **************************************
	// ------------------------------------------ Save data to DB --------------------------------------
	private function updateJob($jobID, $bomID, $processID){
		$result = false;

		$data_to_store['FK_ID_Process'] = $processID;
		if($bomID > 0) { $data_to_store['FK_ID_BOM'] = $bomID; }

		$this->load->model('job_m');
		$result = $this->job_m->update_row($jobID, $data_to_store);

		return $result;
	}

	// ----------------------------------------- Initial combobox --------------------------------------
	private function getInitialDataToDisplay(){
		$data['dsProject'] = $this->getDsProject(0);
		$data['dsCustomer'] = $this->getDsCustomer(0);
		$data['dsJobType'] = $this->getDsJobType(0);
		$data['dsJobStatus'] = $this->getDsJobStatus(0);

		$data['dsRM'] = $this->getDsRM(0);

		$data['dsLine'] = $this->getDsLine(0);
		$data['dsMachine'] = $this->getDsMachine(0);
		$data['dsSubAssembly'] = $this->getDsSubAssembly(0);

		return $data;
	}
	// ------------------------------------------ Get DB to combobox -----------------------------------
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Project Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsProject($id){
		$this->load->model('project_m');
		$dsProject = (($id === 0) ? $this->project_m->get_row() : $this->project_m->get_row_by_id($id));

		return $dsProject;
	}
	private function getDsJobByProjectID($projectID){
		$this->load->model('job_m');
		$dsJob=[];

		$dsJob = $this->job_m->get_row_by_projectID($projectID);
		if(count($dsJob) < 1) {
			$dsJob = $this->job_m->get_row_by_projectAvailable();
		}

		return $dsJob;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Job Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsJob($id){
		$this->load->model('job_m');
		$dsJob = (($id === 0) ? $this->job_m->get_row() : $this->job_m->get_row_by_id($id));

		return $dsJob;
	}
	private function getQtyPlanProduct($jobID){
		$qtyPlanProduct = -1;

		$dsJob = $this->getDsJob($jobID);
		if(count($dsJob) > 0){
			$qtyPlanProduct = $dsJob[0]['Qty_Plan_Product'];
		}

		return $qtyPlanProduct;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% BOM Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsFullBom($id){
		$this->load->model('bomRm_m');
		$dsFullBom = $this->bomRm_m->getFullBom_row_by_id($id);
		
		return $dsFullBom;
	}
	private function getDsBOM($id){
		$this->load->model('bom_m');
		$dsBOM = (($id == 0) ? $this->bom_m->get_row() : $this->bom_m->get_row_by_id($id));

		return $dsBOM;
	}
	private function getDsRM($id){
		$this->load->model('rm_m');
		$dsRM = (($id == 0) ? $this->rm_m->get_row() : $this->rm_m->get_row_by_id($id));

		return $dsRM;
	}
	private function getUnitNameByRm($id){
		$this->load->model('rm_m');
		if($id != 0) {
			$dsFullRm = $this->rm_m->getFullRm_row_by_id($id);
		}

		return $dsFullRm;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Process Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsProcess($id){
		$this->load->model('process_m');
		$dsProcess = (($id === 0) ? $this->process_m->get_row() : $this->process_m->get_row_by_id($id));

		return $dsProcess;
	}
	private function getDsFullStep($jobID, $processID){
		$dsFullStep=[];
		$this->load->model('step_m');
		$dsFullStep = $this->step_m->getFullStepStock($jobID, $processID);

		return $dsFullStep;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Support Combobox %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsCustomer($id){
		$this->load->model('customer_m');
		$dsCustomer = (($id == 0) ? $this->customer_m->get_row() : $this->customer_m->get_row_by_id($id));

		return $dsCustomer;
	}
	private function getDsJobType($id){
		$this->load->model('jobType_m');
		$dsJobType = (($id == 0) ? $this->jobType_m->get_row() : $this->jobType_m->get_row_by_id($id));

		return $dsJobType;
	}
	private function getDsJobStatus($id){
		$this->load->model('jobStatus_m');
		$dsJobStatus = (($id == 0) ? $this->jobStatus_m->get_row() : $this->jobStatus_m->get_row_by_id($id));

		return $dsJobStatus;
	}
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
	// ------------------------------------------ Get DB to combobox -----------------------------------







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