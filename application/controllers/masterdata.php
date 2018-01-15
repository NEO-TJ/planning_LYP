<?php
class Masterdata extends CI_Controller {

	// Variable.
	private $dataTypeName = ['User', 'Customer', 'Line', 'Machine', 'Sub Assembly'
							, 'Defect', 'Raw Material', 'Unit', 'Job Type', 'Job Status'];
	private $inputTypeName = ['inputSubUser_v', 'inputSubCustomer_v', 'inputSubLine_v'
							, 'inputSubMachine_v', 'inputSubSubAssembly_v', 'inputSubDefect_v'
							, 'inputSubRawMaterial_v', 'inputSubUnit_v', 'inputSubJobType_v', 'inputSubJobStatus_v'];
	private $inputModeName = [
								1 => 'Add new data',
								2 => 'Edit data'
							];
	
	
    /**
    * Responsable for auto load the model
    * @return void
    */
    public function __construct()
    {
        parent::__construct();

		$this->is_logged();
    }
    
    
    public function index()
    {
    	$this->view(1);
    }
    
    
    
    
    // ************************************************************ Public function ******************************
    // --------------------------------------------------------------- For view ----------------------------------
    public function view($dataType=1)
    {
    	if(!($this->is_logged())) {exit(0);}
    	$data = $this->getDataToDisplayViewMode($dataType);
    	$userData['level'] = $this->session->userdata('level');
		
    	$this->load->view('frontend/masterdata/header', $userData);
    	$this->load->view('frontend/masterdata/view_v', $data);
    	$this->load->view('frontend/masterdata/footer');
    }
    public function addNew()
    {
    	if(!($this->is_logged())) {exit(0);}
    	
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$dataType = $this->input->post('dataType');
			$inputMode=1;
			$rowID = 0;

			$this->setInputDisplayMode($dataType, $inputMode, $rowID);
		}
    }
    public function edit()
    {
    	if(!($this->is_logged())) {exit(0);}
    	
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$dataType = $this->input->post('dataType');
			$inputMode=2;
			$rowID = $this->input->post('rowID');

			$this->setInputDisplayMode($dataType, $inputMode, $rowID);
		}
    }

    
    // *********************************************** AJAX function *********************************************
    // ---------------------------------------------- Save data to DB --------------------------------------------
    public function ajaxSaveInputData()
    {
		if(!($this->is_logged())) {exit(0);}
		$result = 1;
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$allDataPost = $this->input->post(NULL, TRUE);
			
			$result = $this->saveDataToDB($allDataPost);
		}

		$result = (($result) ? 0 : 1);
		echo $result;
    }
    
    
    

    // ********************************************************* Private function ********************************
    // ------------------------------------------------------- Set input display mode ----------------------------
    private function setInputDisplayMode($dataType=1, $inputMode=1, $rowID=0)
    {
    	$dataInputHeader['dataType'] = $dataType;
    	$dataInputHeader['dataTypeName'] = $this->dataTypeName[$dataType];
    	$dataInputHeader['inputModeName'] = $this->inputModeName[$inputMode];
    	$data = $this->getDataToDisplayInputMode($dataType, $rowID);
    	$data['dataType'] = $dataType;
    	
    	$userData['level'] = $this->session->userdata('level');
    	 
    	$this->load->view('frontend/masterdata/header', $userData);
    	$this->load->view('frontend/masterdata/inputHeader_v', $dataInputHeader);
    	$this->load->view('frontend/masterdata/'.$this->inputTypeName[$dataType] , $data);
    	$this->load->view('frontend/masterdata/inputFooter_v');
    	$this->load->view('frontend/masterdata/footer');
    }
    // -------------------------------------------------------- Initial view mode --------------------------------
    private function getDataToDisplayViewMode($dataType=1)
    {
    	$data['dataType'] = $dataType;
    	$data['dataTypeName'] = $this->dataTypeName[$dataType];
    	if($dataType == 1) {
    		$data['dsView'] = $this->getDsCustomer(0);
    	}
    	else if($dataType == 2) {
    		$data['dsView'] = $this->getDsLine(0);
    	}
    	else if($dataType == 3) {
    		$data['dsView'] = $this->getDsMachine([], 0);
    	}
    	else if($dataType == 4) {
    		$data['dsView'] = $this->getDsSubAssembly(0);
    	}
    	else if($dataType == 5) {
    		$data['dsView'] = $this->getDsDefect(0);
    	}
    	else if($dataType == 6) {
    		$data['dsView'] = $this->getDsFullRM([], 0);
    	}
    	else if($dataType == 7) {
    		$data['dsView'] = $this->getDsUnit(0);
    	}
		else if($dataType == 8) {
    		$data['dsView'] = $this->getDsJobType(0);
    	}
		else if($dataType == 9) {
    		$data['dsView'] = $this->getDsJobStatus(0);
    	}
    	else if($dataType == 0) {
    		$data['dsView'] = $this->getDsFullUser([]);
    	}
    	 
    	return $data;
    }
    // -------------------------------------------------------- Initial input mode -------------------------------
    private function getDataToDisplayInputMode($dataType=1, $rowID=0)
    {
    	if($dataType == 1) {
    		$this->load->model('customer_m');
    		$dataset = $this->customer_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->customer_m->get_template());
    	}
    	else if($dataType == 2) {
    		$this->load->model('line_m');
    		$dataset = $this->line_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->line_m->get_template());
		}
    	else if($dataType == 3) {
    		$result['dsLine'] = $this->getDsLine(0);

    		$this->load->model('machine_m');
    		$dataset = $this->machine_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->machine_m->get_template());
    	}
    	else if($dataType == 4) {
    		$this->load->model('subAssembly_m');
    		$dataset = $this->subAssembly_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->subAssembly_m->get_template());
    	}
    	else if($dataType == 5) {
    		$this->load->model('defect_m');
    		$dataset = $this->defect_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->defect_m->get_template());
    	}
    	else if($dataType == 6) {
    		$result['dsUnit'] = $this->getDsUnit(0);

    		$this->load->model('rm_m');
    		$dataset = $this->rm_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->rm_m->get_template());
    	}
    	else if($dataType == 7) {
    		$this->load->model('unit_m');
    		$dataset = $this->unit_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->unit_m->get_template());
    	}
		else if($dataType == 8) {
    		$this->load->model('jobType_m');
    		$dataset = $this->jobType_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->jobType_m->get_template());
		}
		else if($dataType == 9) {
    		$this->load->model('jobStatus_m');
    		$dataset = $this->jobStatus_m->get_row_by_id($rowID);
    	  $result['dsInput'] = ((count($dataset) > 0) ? $dataset[0] : $this->jobStatus_m->get_template());
		}
    	else if($dataType == 0) {
				$result['dsLine'] = $this->getDsLine(0);

    		$this->load->model('user_m');
				$dataset = $this->user_m->get_row_by_id($rowID);
				if(count($dataset) > 0) {
					$result['dsInput'] = $dataset[0];
					$result['dsInputLineId'] = explode(',', $dataset[0]['FK_ID_Line']);
				} else {
					$result['dsInput'] = $this->user_m->get_template();
					$result['dsInputLineId'] = array();
				}
    	}
    	 
    	return $result;
		}
    

    // --------------------------------------------------------- Save input mode ---------------------------------
    private function saveDataToDB($dsSave)
    {
    	$result = false;
    	
    	$dataType = $dsSave['dataType'];
    	unset($dsSave['dataType']);
    	
    	$rowID = $dsSave['rowID'];
    	unset($dsSave['rowID']);
		
    	
    	
    	
    	if($dataType == 1) {
    		$this->load->model('customer_m');
    		$result = $this->customer_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 2) {
    		$this->load->model('line_m');
    		$result = $this->line_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 3) {
    		$this->load->model('machine_m');
    		$result = $this->machine_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 4) {
    		$this->load->model('subAssembly_m');
    		$result = $this->subAssembly_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 5) {
    		$this->load->model('defect_m');
    		$result = $this->defect_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 6) {
    		$this->load->model('rm_m');
    		$result = $this->rm_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 7) {
    		$this->load->model('unit_m');
    		$result = $this->unit_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 8) {
    		$this->load->model('jobType_m');
    		$result = $this->jobType_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 9) {
    		$this->load->model('jobStatus_m');
    		$result = $this->jobStatus_m->save($rowID, $dsSave);
    	}
    	else if($dataType == 0) {
    		$this->load->model('user_m');
    		$result = $this->user_m->saveAllStatus($rowID, $dsSave);
    	}
    	
    	return $result;
    }
    
    
    
    
    // -------------------------------------------------------- Get DB to combobox ----------------------------
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Customer table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsCustomer($id=0)
    {
    	$this->load->model('customer_m');
    	$dataset = (($id == 0) ? ($this->customer_m->get_row()) : ($this->customer_m->get_row_by_id($id)));
    
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Line table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsLine($id=0)
    {
    	$this->load->model('line_m');
    	$dataset = (($id == 0) ? ($this->line_m->get_row()) : ($this->line_m->get_row_by_id($id)));
    
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Machine table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsMachine($arrID=0, $displayMode=0)
    {
    	$this->load->model('machine_m');
    	$dataset = $this->machine_m->get_full_machine($arrID);
    	
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Sub Assembly table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsSubAssembly($id=0)
    {
    	$this->load->model('subAssembly_m');
    	$dataset = (($id == 0) ? ($this->subAssembly_m->get_row()) : ($this->subAssembly_m->get_row_by_id($id)));
    	
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Defect table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsDefect($id=0)
    {
    	$this->load->model('defect_m');
    	$dataset = (($id == 0) ? ($this->defect_m->get_row()) : ($this->defect_m->get_row_by_id($id)));
    	
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Raw material table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsFullRM($arrID=0, $displayMode=0)
    {
    	$this->load->model('rm_m');
    	$dataset = $this->rm_m->get_full_rm($arrID);
    	 
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Unit table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsUnit($id=0)
    {
    	$this->load->model('unit_m');
    	$dataset = (($id == 0) ? ($this->unit_m->get_row()) : ($this->unit_m->get_row_by_id($id)));
    
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Job Type table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsJobType($id=0)
    {
    	$this->load->model('jobType_m');
    	$dataset = (($id == 0) ? ($this->jobType_m->get_row()) : ($this->jobType_m->get_row_by_id($id)));
    
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ Job Status table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsJobStatus($id=0)
    {
    	$this->load->model('jobStatus_m');
    	$dataset = (($id == 0) ? ($this->jobStatus_m->get_row()) : ($this->jobStatus_m->get_row_by_id($id)));
    
    	return $dataset;
    }
    // ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ User table ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    private function getDsFullUser($arrID=0)
    {
    	$this->load->model('user_m');
    	$dataset = $this->user_m->get_full_user($arrID);
    
    	return $dataset;
    }
    private function getDsUser($userID=0)
    {
    	$this->load->model('user_m');
    	$dataset = $this->user_m->get_row_by_id($userID);
    	
    	return $dataset;
    }

    
    
    
    
    
    

    // -------------------------------------------------------------- Helper -------------------------------------
    private function getPostArrayHelper($arrayData)
    {
    	return (((count($arrayData) == 1) && ($arrayData[0] == '')) ? $arrayData = [] : $arrayData);
    }
    




	// ********************************************************* Check logged *********************************
	private function is_logged()
	{
		if(!$this->session->userdata('id')){
			$this->logout();
			return false;
		} else {
			return true;
		}
	}
	private function logout()
	{
		$this->load->view('frontend/include/header');
		$this->load->view('frontend/logout');
		$this->load->view('frontend/include/footer');
	}
}	// ********************************************************** End logged **********************************