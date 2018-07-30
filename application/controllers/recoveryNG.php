<?php
class RecoveryNG extends CI_Controller {
	public function __construct() {
		parent::__construct();

		$this->is_logged();
	}


	public function index() {
		$data = $this->getInitialDataToDisplay();
		$userData['level'] = $this->session->userdata('level');
		
		$this->load->view('frontend/recoveryNG/header', $userData);
		$this->load->view('frontend/recoveryNG/recoveryNG_v', $data);
		$this->load->view('frontend/recoveryNG/footer');
	}


// ****************************************** AJAX function ****************************************
	// ----------------------------------------- Save data to DB ---------------------------------------
	public function ajaxSaveRecoveryNG(){
		if(!($this->is_logged())) {exit(0);}
		$result = 4;

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			// -------------------------- Save Step, Stock Part ----------------------------------------
			$jobID = $this->input->post('jobID');
			$dateTimeStamp = $this->input->post('dateTimeStamp');
			$workerID = $this->input->post('workerID');
			$sourceStepID = $this->input->post('sourceStepID');
			$qtyNGSend = $this->input->post('qtyNGSend');
			$dsDestinationStep = $this->input->post('dsDestinationStep');
			$firstStepStock = ($this->input->post('firstStepStock') == 1) ? true : false;

			$result = $this->recoveryNG($jobID, $dateTimeStamp, $workerID
				, $sourceStepID, $qtyNGSend, $dsDestinationStep, $firstStepStock);
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

	public function ajaxGetDsFullSourceStock() {
		if(!($this->is_logged())) {exit(0);}
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$jobID = $this->input->post('jobID');
			$stepID = $this->input->post('stepID');
			$dsSourceStepStock = $this->getDsCurrentStepDescSubAssStock($jobID, $stepID);

			echo json_encode($dsSourceStepStock);
		}
	}


	// -------------------------------- Modify stock and Delete Activity -------------------------------
	public function ajaxUndoReturnNg(){
		if(!($this->is_logged())) {exit(0);}
		$result = 4;

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			// -------------------------- Save Step, Stock Part ----------------------------------------
			$activityID = $this->input->post('activityID');
			$stockID = $this->input->post('stockID');
			$qtyNGSend = (int)($this->input->post('qtyNG')) * -1;

			$result = $this->undoFullRecoveryNG($activityID, $stockID, $qtyNGSend);
		}

		echo $result;
	}
// ****************************************** End AJAX function **************************************



// ***************************************** Retrive function ****************************************
////////////////////////////////////////////// Recovery NG ///////////////////////////////////////////
	private function recoveryNG($jobID, $dateTimeStamp, $workerID
	, $sourceStepID, $qtyNGSend, $dsDestinationStep, $firstStepStock) {
		$result = 4;

		$resultEnoughStock = false;
		$resultUpdateStock = false;

		$dsSourceFullStock = $this->getDsCurrentFullStock($jobID, $sourceStepID);
		if(count($dsSourceFullStock) > 0) {
			// Check enough stock.
			$resultEnoughStock = $this->checkEnoughStock($dsSourceFullStock[0], $qtyNGSend);

			// Prepare main data
			$dataMain = $this->prepareDataMain($dateTimeStamp, $workerID);
			// Prepare source data
			$dataSourceStock = $this->prepareDataSourceStock($dsSourceFullStock[0], $qtyNGSend);
			// Prepare destination stock data
			$dataDestinationStock = $this->prepareDataDestinationStock($dsDestinationStep, $jobID, $firstStepStock);

			if(($resultEnoughStock) && (count($dataDestinationStock) > 0)) {
				$resultUpdateStock = $this->updateStock($dataMain, $dataSourceStock, $dataDestinationStock);
			}
		}

		// --------- Set return result
		$result = $this->setResult($resultEnoughStock, $resultUpdateStock);
	
		return $result;
	}
	
	// ----------------------------------------- Check enough stock ------------------------------------
	private function checkEnoughStock($rowSourceFullStock, $qtyNGSend) {
		$result = ( (($rowSourceFullStock['Qty_NG'] < $qtyNGSend) || ($qtyNGSend == 0)) ? false : true );
	
		return $result;
	}
	
	// ____________________________________________ Prepare data _______________________________________
	// ----------------------------------------- Prepare main data -------------------------------------
	private function prepareDataMain($dateTimeStamp, $workerID) {
		$result = array(
			"datetimeStamp"	=> DateTime::createFromFormat('j-M-Y H:i', $dateTimeStamp)->format('Y-m-d H:i:s'),
			"workerId"			=> $workerID,
		);

		return $result;
	}
	// ------------------------------------- Prepare source stock data ---------------------------------
	private function prepareDataSourceStock($rowSourceFullStock, $qtyNGSend) {
		$result = array(
			"sourceStockID"		=> $rowSourceFullStock['id'],
			"qtyNGSend"				=> $qtyNGSend,
			"sourceStockData"	=> array("Qty_NG" => ($rowSourceFullStock['Qty_NG'] - $qtyNGSend)),
		);

		return $result;
	}
	// ------------------------------------ Prepare destination stock data -----------------------------
	private function prepareDataDestinationStock($dsDestinationStep, $jobID, $firstStepStock) {
		$rStepId = array_column($dsDestinationStep, 'stepId');
		$rReceiveNgQty = array_column($dsDestinationStep, 'receiveNgQty');
		$combineIdReceiveNgQty = array_combine($rStepId, $rReceiveNgQty);

		$this->load->model('stock_m');
		$dsDestinationStock = $this->stock_m->getRowByJobAndMultiStepId($jobID, $rStepId);
		//$stockType = (($firstStepStock) ? $this->stock_m->col_qty_ok_first_step : $this->stock_m->col_qty_ok);
		$i = 0;
		foreach ($dsDestinationStock as $value) {
			$dataDestinationStock[$i] = array(
				"DestinationStockID"				=> $value[$this->stock_m->col_id],
				$this->stock_m->col_qty_ok	=> $value[$this->stock_m->col_qty_ok] + $combineIdReceiveNgQty[$value[$this->stock_m->col_step_id]]
				//$stockType	=> $value[$stockType] + $combineIdReceiveNgQty[$value[$this->stock_m->col_step_id]],
			);

			$dataDestinationStockActivity[$i] = array(
				"DestinationStockID"	=> $value[$this->stock_m->col_id],
				"ReceiveNgQty"				=> $combineIdReceiveNgQty[$value[$this->stock_m->col_step_id]] * (($firstStepStock) ? -1 : 1),
			);

			$i++;
		}

		$rResult = array(
			"dataDestinationStock"					=> $dataDestinationStock,
			"dataDestinationStockActivity"	=> $dataDestinationStockActivity
		);

		return $rResult;
	}
	// __________________________________________ End Prepare data _____________________________________

	// -------------------------------------------- Modify stock ---------------------------------------
	private function updateStock($dataMain, $dataSourceStock, $dataDestinationStock) {
		$result = false;
		$this->load->model('stock_m');
	
		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Prepare Data !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// Insert activity data for cut NG.
		$i=0;
		$dataActivity[$i]['Datetime_Stamp'] = $dataMain["datetimeStamp"];
		$dataActivity[$i]['FK_ID_Stock'] = $dataSourceStock["sourceStockID"];
		$dataActivity[$i]['FK_ID_Worker'] = $dataMain["workerId"];
		$dataActivity[$i]['FK_ID_User'] = $this->session->userdata('id');
		$dataActivity[$i]['Qty_NG'] = (0 - $dataSourceStock["qtyNGSend"]);

		foreach ($dataDestinationStock["dataDestinationStockActivity"] as $value) {
			$i++;
			// Insert activity data for know destination stepID.
			$dataActivity[$i]['Datetime_Stamp'] = $dataMain["datetimeStamp"];
			$dataActivity[$i]['FK_ID_Stock'] = $value["DestinationStockID"];
			$dataActivity[$i]['FK_ID_Worker'] = $dataMain["workerId"];
			$dataActivity[$i]['FK_ID_User'] = $this->session->userdata('id');
			$dataActivity[$i]['Qty_Revoke_NG'] = $value["ReceiveNgQty"];
		}

		// --------- Update Database
		$result = $this->stock_m->transaction_update_NG_stock($dataSourceStock['sourceStockID']
			, $dataSourceStock['sourceStockData']
			, $dataDestinationStock["dataDestinationStock"]
			, $dataActivity);

		return $result;
	}
	
	// ------------------------------------- Set result receive input quantity -------------------------
	private function setResult($resultEnoughStock=false, $resultUpdateStock=false) {
		$result = 4;

		if($resultEnoughStock && $resultUpdateStock) {
			$result = 0;
		} else if(!$resultEnoughStock) {
			$result = 1;
		} else if(!$resultUpdateStock) {
			$result = 2;
		} else {
			$result = 4;
		}

		return $result;
	}
//////////////////////////////////////////// End Recovery NG /////////////////////////////////////////



///////////////////////////////////////////// Undo Recovery NG ///////////////////////////////////////
	private function undoFullRecoveryNG($activityID, $stockID, $qtyNGSend) {
		$result = 4;
			
		$this->load->model('stock_m');
		$dsSourceStock = $this->stock_m->get_row_by_id($stockID);       // Get Source stock for recovery(+) NG.
		if(count($dsSourceStock) > 0) {                                 // Have stock in DB.
			// Prepare undo source data
			$dataSourceStock = $this->prepareDataUndoSourceStock($dsSourceStock[0], $qtyNGSend);
			// Prepare destination stock data
			$rDataDestinationStock = $this->prepareDataUndoDestinationStock($activityID, $dsSourceStock[0], $qtyNGSend);
			$dataSourceStock = (($rDataDestinationStock["dataSourceStock"] == null) 
				? $dataSourceStock : $rDataDestinationStock["dataSourceStock"]);
			$dataDestinationStock = $rDataDestinationStock["dataDestinationStock"];

			$result = $this->updateUndoRecoveryNGStock($dataSourceStock, $dataDestinationStock, $activityID);
		}

		return $result;
	}
	
	// ____________________________________________ Prepare data _______________________________________
	// ------------------------------------- Prepare source stock data ---------------------------------
	private function prepareDataUndoSourceStock($rowSourceStock, $qtyNGSend) {
		$result = array(
			"sourceStockID"		=> $rowSourceStock['id'],
			"sourceStockData"	=> array("Qty_NG" => ($rowSourceStock['Qty_NG'] + $qtyNGSend)),
		);

		return $result;
	}
	// ------------------------------------ Prepare destination stock data -----------------------------
	private function prepareDataUndoDestinationStock($activityID, $rowSourceStock, $qtyNGSend) {
		$dataSourceStock = null;
		$this->load->model('activity_m');
		$dsDestinationStock = $this->activity_m->get_full_recovery_NG_destination($activityID); // Get Destination stock (OK or First).

		if(count($dsDestinationStock) > 0) {
			$i = 0;
			foreach ($dsDestinationStock as $value) {
				if($value["revokeQty"] > 0) {
					// Normal stock.
					$dataDestinationStock[$i] = array(
						$this->stock_m->col_id			=> $value["stockID"],
						$this->stock_m->col_qty_ok	=> $value["stockOK"] - $value["revokeQty"],
					);
				} else if ($value["revokeQty"] < 0) {
					// First stock.
					$dataDestinationStock[$i] = array(
						$this->stock_m->col_id								=> $value["stockID"],
						$this->stock_m->col_qty_ok_first_step	=> $value["firstStepStock"] + $value["revokeQty"],
					);
				} else {
					// Old version of activity return ng.
					$dataDestinationStockTmp = $this->prepareDataUndoDestinationStockOldversion2($dsDestinationStock, $qtyNGSend, $i);
					if(count($dataDestinationStockTmp) > 0) {
						foreach($dataDestinationStockTmp as $valueTmp) {
							$dataDestinationStock[$i++] = $valueTmp;
						}
						$i--;
					}
				}

				$i++;
			}
		} else {
			// Old version 1 : Decrease qty_ok_first_step_stock
			$dataSourceStock = $this->prepareDataUndoDestinationStockOldversion1($rowSourceStock, $qtyNGSend);
			$result = (($dataSourceStock < 0) ? 4 : 3);
		}

		$rResult = array(
			"dataDestinationStock"					=> $dataDestinationStock,
			"dataSourceStock"								=> $dataSourceStock,
		);

		return $rResult;
	}
	private function prepareDataUndoDestinationStockOldversion1($rowSourceStock, $qtyNGSend) {
		$dataSourceStock['Qty_OK_First_Step'] = $rowSourceStock['Qty_OK_First_Step'] - $qtyNGSend;

		return $dataSourceStock;
	}
	private function prepareDataUndoDestinationStockOldversion2($dsDestinationActivityStock, $qtyNGSend, $i) {
		$rowDestinationActivityStock = $dsDestinationActivityStock[0];
		// Destination : Decrease OK stock(If first step => decrease that, If not decrease previous step).
		$dsDestinationStep = $this->getDsStep($rowDestinationActivityStock['stepID']);
		if (count($dsDestinationStep) > 0) {
			$rowDestinationStep = $dsDestinationStep[0];
			if ($rowDestinationStep['First_Step_Flag'] == 1) {            // Check for first step.
				$dataDestinationStock[$i]['id'] = $rowDestinationActivityStock['stockID'];
				$dataDestinationStock[$i]['Qty_OK_First_Step'] = $rowDestinationActivityStock['firstStepStock'] - $qtyNGSend;
			} else {
				$dsDestinationStock = $this->getDsPreviousFullStock(
					$rowDestinationActivityStock['jobID']
					, $rowDestinationStep['Number']);
				if (count($dsDestinationStock) > 0) {
					foreach ($dsDestinationStock as $row) {
						$dataDestinationStock[$i]['id'] = $row['id'];
						$dataDestinationStock[$i++]['Qty_OK'] = $row['Qty_OK'] - (($qtyNGSend * $row['NB_Sub']) / $rowDestinationStep['NB_Sub']);
					}
				}
			}
		}

		return $dataDestinationStock;
	}
	// __________________________________________ End Prepare data _____________________________________

	private function updateUndoRecoveryNGStock($dataSourceStock, $dataDestinationStock, $activityID) {
		$result = (($this->stock_m->transaction_update_undo_NG_stock(
			$dataSourceStock["sourceStockID"], $dataSourceStock["sourceStockData"]
			, $dataDestinationStock, $activityID))
			? 0 : 4);

		return $result;
	}
/////////////////////////////////////////// End Undo Recovery NG /////////////////////////////////////
// *************************************** End Retrive function **************************************







// ***************************************** Get function **************************************
	// ------------------------------------- Initial combobox ------------------------------------
	private function getInitialDataToDisplay() {
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
		$data['dsWorker'] = $this->getDsWorker(0);

		return $data;
	}
	// ------------------------------------------ Get DB to combobox -----------------------------------
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Job Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsJobStatusOpen($id) {
		$this->load->model('job_m');
		$dsJob = (($id == 0)
				? $this->job_m->get_all_row_status_open()
				: $this->job_m->get_row_status_open_by_id($id));

		return $dsJob;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Worker Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsWorker($id) {
		$this->load->model('user_m');
		$dsWorker = $this->user_m->get_row_active_status($id);

		return $dsWorker;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsStepByProcessID($processID) {
		$this->load->model('step_m');
		$dsStep = $this->step_m->get_by_where_array(array('FK_ID_Process' => $processID));

		return $dsStep;
	}
	
	private function getDsStep($id) {
		$this->load->model('step_m');
		$dsStep = $this->step_m->get_row_by_id($id);

		return $dsStep;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step Stock Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsCurrentFullStock($jobID, $stepID) {
		$this->load->model('step_m');
		$dsFullStock = $this->step_m->get_full_stock($jobID, $stepID);

		return $dsFullStock;
	}
	private function getDsPreviousFullStock($jobID, $stepNumber) {
		$this->load->model('step_m');
		$dsPreviousFullStock = $this->step_m->get_previous_full_stock($jobID, $stepNumber);

		return $dsPreviousFullStock;
	}
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step-Desc Stock Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsCurrentStepDescSubAssStock($jobID, $stepID) {
		$this->load->model('step_m');
		$dsFullStock = $this->step_m->getFullStockNumberDescSubAss($jobID, $stepID);

		return $dsFullStock;
	}
// ***************************************** End Get function **************************************







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
			if($this->session->userdata('level') == 1) {
				return true;
			} else {
				$this->logout();
				return false;
			}
		}
	}
	private function logout() {
		$this->load->view('frontend/include/header');
		$this->load->view('frontend/logout');
		$this->load->view('frontend/include/footer');
	}
// ********************************************************** End logged **********************************
}
