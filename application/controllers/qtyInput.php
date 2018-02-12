<?php
class QtyInput extends CI_Controller {

	/**
    * Responsable for auto load the model
    * @return void
	*/
	public function __construct() {
		parent::__construct();

		$this->is_logged();
	}


	public function index() {
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');
		
		$this->load->view('frontend/qtyInput/header', $userData);
		$this->load->view('frontend/qtyInput/qtyInput_v', $data);
		$this->load->view('frontend/qtyInput/footer');
	}


// ****************************************** AJAX function ****************************************
	// ----------------------------------------- Save data to DB ---------------------------------------
	public function ajaxSaveQtyInput() {
		if(!($this->is_logged())) {exit(0);}
		$result = 4;

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			// -------------------------- Save Step, Stock Part ----------------------------------------
			$jobID = $this->input->post('jobID');
			$dateTimeStamp = $this->input->post('dateTimeStamp');
			$stepID = $this->input->post('stepID');
			$workerID = $this->input->post('workerID');
			$qtyOK = $this->input->post('qtyOK');
			$totalQtyNG = $this->input->post('totalQtyNG');
			$dsNG = $this->getPostArrayHelper($this->input->post('dsNG'));

			$result = $this->fullQtyInput($jobID, $dateTimeStamp, $stepID, $workerID, $qtyOK, $totalQtyNG, $dsNG);
		}
		
		echo $result;    	
	}

	// ------------------------------------------- Get data set ----------------------------------------
	public function ajaxGetDsStep() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$dsStep = [];
	
			$jobID = $this->input->post('jobID');
			$dsJob = $this->getDsJobStatusOpen($jobID);
	
			if(count($dsJob > 0)) {
				if(!empty($dsJob[0]['FK_ID_Process'])) {
					$dsStep = $this->getDsStepByProcessID($dsJob[0]['FK_ID_Process']);
				}
			}

			echo json_encode($dsStep);
		}
	}
	public function ajaxGetDsStepOneRow() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
	
			$stepID = $this->input->post('stepID');
			$data['dsStep'] = $this->getDsStep($stepID);
			//$data['dsSubAssembly'] = $this->getDsSubAssemblyByStep($stepID);		// Koravit.
			$data['dsSubAssembly'] = $this->getDsSubAssemblyByStep(0);

			echo json_encode($data);
		}
	}


	// -------------------------------- Modify stock and Delete Activity -------------------------------
	public function ajaxDeleteActivity() {
		if(!($this->is_logged())) {exit(0);}
		$result = 4;
	
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			// -------------------------- Save Step, Stock Part ----------------------------------------
			$activityID = $this->input->post('activityID');
			$stockID = $this->input->post('stockID');
			$qtyOK = $this->input->post('qtyOK');
			$qtyNG = $this->input->post('qtyNG');

			$result = $this->undoFullQtyInput($activityID, $stockID, $qtyOK, $qtyNG);
		}

		echo $result;
	}
// ****************************************** End AJAX function ****************************************




// ***************************************** Get function **************************************
	// ----------------------------------------- Initial combobox --------------------------------------
	private function getInitialDataToDisplay() {
		$data['dsJob'] = $this->getDsJobStatusOpen(0);

		$data['dsWorker'] = $this->getDsWorker(0);
		//$data['dsSubAssembly'] = $this->getDsSubAssembly(0);
		$data['dsDefect'] = $this->getDsDefect(0);

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
	private function getDsStepByProcessID($processID) {
		$this->load->model('step_m');
		$dsStep = $this->step_m->get_by_where_array(array('FK_ID_Process' => $processID));
	
		return $dsStep;
	}
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


	// --------------------------------------- Initial NG Input combobox -------------------------------
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Worker Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsWorker($id) {
		$this->load->model('user_m');
		$dsWorker = $this->user_m->get_row_active_status($id);
	
		return $dsWorker;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Sub Assembly Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsSubAssemblyByStep($stepId) {
		$this->load->model('subAssembly_m');
		$dsSubAssembly = (($stepId == 0) 
			? $this->subAssembly_m->get_row() 
			: $this->subAssembly_m->getRowByStep($stepId));

		return $dsSubAssembly;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Defect Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsDefect($id) {
		$this->load->model('defect_m');
		$dsDefect = (($id == 0) ? $this->defect_m->get_row() : $this->defect_m->get_row_by_id($id));

		return $dsDefect;
	}
// ***************************************** End Get function **************************************





// ***************************************** Retrive function **************************************
	// ************************************* Receive input quantity ************************************
	private function fullQtyInput($jobID, $dateTimeStamp, $stepID, $workerID, $qtyOK, $totalQtyNG, $dsNG) {
		$result = 4;
	
		$resultEnoughStock = false;
		$resultUpdateStock = false;
			
		$dsFullStock = $this->getDsFullStock($jobID, $stepID);
		if(count($dsFullStock) > 0) {
			// Check enough stock.
			$resultEnoughStock = $this->checkEnoughStock($jobID, $stepID, $qtyOK, $totalQtyNG, $dsFullStock);
			if($resultEnoughStock) {
				$resultUpdateStock = $this->updateStock($jobID, $dateTimeStamp, $stepID, $qtyOK, $totalQtyNG
														, $dsFullStock, $dsNG, $workerID);
			}
		}
			
		// ---------------------------- Set return result ------------------------------------------
		$result = $this->setResult($resultEnoughStock, $resultUpdateStock);
			
		return $result;
	}
    
	// -------------------------------------------- Modify stock ---------------------------------------
	private function updateStock($jobID, $dateTimeStamp, $stepID
	, $qtyOK, $totalQtyNG, $dsFullStock, $dsNG, $workerID) {
		$result = false;
		$this->load->model('stock_m');
	
		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Prepare Data !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// DateTimeStamp.
		$strDateTimeStamp = DateTime::createFromFormat('j-M-Y H:i', $dateTimeStamp)->format('Y-m-d H:i:s');
		
		// StockID.
		$currentStockID = $dsFullStock[0]['id'];
		// Decrease stock data.
		$dataPreviousStock = [];
	
		if($dsFullStock[0]['First_Step_Flag'] == 1) {							// Update self stock.
			// Cut first stock data.
			$dataCurrentStock['Qty_OK_First_Step'] = $dsFullStock[0]['Qty_OK_First_Step'] - ($qtyOK + $totalQtyNG);
		}
		else {																	// Update previous and self stock.
			// Cut previous stock data.
			$dsPreviousFullStock = $this->getDsPreviousFullStock($jobID, $dsFullStock[0]['Number']);		// Get all previous step.
			if(count($dsPreviousFullStock) > 0) {
				$i = 0;
				foreach($dsPreviousFullStock as $row){
					$dataPreviousStock[$i]['PreviousStockID'] = $row['id'];
					$dataPreviousStock[$i++]['Qty_OK'] = $row['Qty_OK'] -
					((($qtyOK + $totalQtyNG) * $row['NB_Sub']) / $dsFullStock[0]['NB_Sub']);
				}
			}
		}


		// Increase current stock data (OK|NG).
		if($qtyOK > 0) {
			$dataCurrentStock['Qty_OK'] = $dsFullStock[0]['Qty_OK'] + $qtyOK;
		}
		if($totalQtyNG > 0) {
			$dataCurrentStock['Qty_NG'] = $dsFullStock[0]['Qty_NG'] + $totalQtyNG;
		}
	
			
		// Insert activity data.
		$dataActivity = [];
		$i = 0;
		if($qtyOK > 0) {
			$dataActivity[$i]['Datetime_Stamp'] = $strDateTimeStamp;
			$dataActivity[$i]['FK_ID_Stock'] = $currentStockID;
			$dataActivity[$i]['FK_ID_Worker'] = $workerID;
			$dataActivity[$i]['FK_ID_User'] = $this->session->userdata('id');
			$dataActivity[$i]['Qty_OK'] = $qtyOK;
			$i++;
		}
		if(count($dsNG) > 0) {
			foreach($dsNG as $row){
				if($row['qtyNG'] > 0) {
					$dataActivity[$i]['Datetime_Stamp'] = $strDateTimeStamp;
					$dataActivity[$i]['FK_ID_Stock'] = $currentStockID;
					$dataActivity[$i]['FK_ID_Worker'] = $workerID;
					$dataActivity[$i]['FK_ID_User'] = $this->session->userdata('id');
					$dataActivity[$i]['Qty_NG'] = $row['qtyNG'];
					$dataActivity[$i]['FK_ID_Defect'] = $row['defectID'];
					$i++;
				}
			}
		}


		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Update Database !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// Updata database.
		$result = $this->stock_m->transaction_update_stock($currentStockID, $dataPreviousStock, $dataCurrentStock, $dataActivity);

		return $result;
	}
	// ----------------------------------------- Check enough stock ------------------------------------
	private function checkEnoughStock($jobID, $stepID, $qtyOK, $totalQtyNG, $dsFullStock) {
		$result = false;
			
		if($dsFullStock[0]['First_Step_Flag'] == 1) {
			// Check "First step qty".
			if(($dsFullStock[0]['Qty_OK_First_Step']) >= ($qtyOK + $totalQtyNG)) {
				$result = true;
			}
		}
		else {
			// Get all previous step.
			$dsPreviousFullStock = $this->getDsPreviousFullStock($jobID, $dsFullStock[0]['Number']);
			if(count($dsPreviousFullStock) > 0) {
				$result = true;
	
				foreach($dsPreviousFullStock as $row){
					if($row['Qty_OK'] < ( (($qtyOK + $totalQtyNG) * $row['NB_Sub']) / ($dsFullStock[0]['NB_Sub']) ) ) {
						$result = false;
						break;
					}
				}
			}
		}
			
		return $result;
	}
	// ------------------------------------- Set result receive input quantity -------------------------
	private function setResult($resultEnoughStock=false, $resultUpdateStock=false) {
		$result = 4;
	
		if($resultEnoughStock && $resultUpdateStock) {
			$result = 0;
		}
		else if(!$resultEnoughStock) {
			$result = 1;
		}
		else if(!$resultUpdateStock) {
			$result = 2;
		}
		else {
			$result = 4;
		}
	
		return $result;
	}
	
	
	// -------------------------------------- Check and update stock -----------------------------------
	private function getDsFullStock($jobID, $stepID) {
		$this->load->model('step_m');
		$dsFullStock = $this->step_m->get_full_stock($jobID, $stepID);
	
		return $dsFullStock;
	}
	private function getDsPreviousFullStock($jobID, $stepNumber) {
		$this->load->model('step_m');
		$dsPreviousFullStock = $this->step_m->get_previous_full_stock($jobID, $stepNumber);
			
		return $dsPreviousFullStock;
	}





	// ********************************** Modify stock and delete activity *****************************
	private function undoFullQtyInput($activityID, $stockID, $qtyOK, $qtyNG) {
		$result = 4;
		
		$this->load->model('stock_m');
		$dsStock = $this->stock_m->get_row_by_id($stockID);
		if(count($dsStock) > 0) {
			$jobID = $dsStock[0]['FK_ID_Job'];
			$stepID = $dsStock[0]['FK_ID_Step'];
				
			$dsFullStock = $this->getDsFullStock($jobID, $stepID);
			if(count($dsFullStock) > 0) {
				$result = $this->updateUndoStock($jobID, $stepID, $qtyOK, $qtyNG, $dsFullStock, $activityID);
			}
		}
		
		return $result;
	}

	private function updateUndoStock($jobID, $stepID, $qtyOK, $qtyNG, $dsFullStock, $activityID) {
		$result = 4;
		$this->load->model('stock_m');

		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Prepare Data !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// StockID.
		$currentStockID = $dsFullStock[0]['id'];
		// Prepare previous stock data.
		$dataPreviousStock = [];

		// Prepare previous stock data (or current First step stock data)
		if($dsFullStock[0]['First_Step_Flag'] == 1) {							// Update self stock.
			// Prepare current first step stock data.
			$dataCurrentStock['Qty_OK_First_Step'] = $dsFullStock[0]['Qty_OK_First_Step'] + ($qtyOK + $qtyNG);
		}
		else {																	// Update previous and self stock.
			// Prepare previous stock data.
			$dsPreviousFullStock = $this->getDsPreviousFullStock($jobID, $dsFullStock[0]['Number']);		// Get all previous step.
			if(count($dsPreviousFullStock) > 0) {
				$i = 0;
				foreach($dsPreviousFullStock as $row){
					$dataPreviousStock[$i]['PreviousStockID'] = $row['id'];
					$dataPreviousStock[$i++]['Qty_OK'] = $row['Qty_OK'] + ((($qtyOK + $qtyNG) * $row['NB_Sub']) / $dsFullStock[0]['NB_Sub']);
				}
			}
		}


		// Prepare current stock data.
		if($qtyOK > 0) {
			$dataCurrentStock['Qty_OK'] = $dsFullStock[0]['Qty_OK'] - $qtyOK;
			$result = (($dataCurrentStock['Qty_OK'] < 0) ? 1 : 4);
		}
		if($qtyNG > 0) {
			$dataCurrentStock['Qty_NG'] = $dsFullStock[0]['Qty_NG'] - $qtyNG;
			$result = (($dataCurrentStock['Qty_NG'] < 0) ? 1 : 4);
		}


		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Update Database !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// Updata database.
		if($result != 1) {
			$result = (($this->stock_m->transaction_update_undo_stock(
						$currentStockID, $dataPreviousStock, $dataCurrentStock, $activityID))
						? 0 : 4);
		}

		return $result;
	}
// *************************************** End Retrive function **************************************





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