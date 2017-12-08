<?php
class RecoveryNG extends CI_Controller {

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
    	 
    	if ($this->input->server('REQUEST_METHOD') === 'POST')
    	{
    		// -------------------------- Save Step, Stock Part ----------------------------------------
    		$jobID = $this->input->post('jobID');
    		$dateTimeStamp = $this->input->post('dateTimeStamp');
    		$workerID = $this->input->post('workerID');
    		$sourceStepID = $this->input->post('sourceStepID');
    		$destinationStepID = $this->input->post('destinationStepID');
    		$qtyNGSend = $this->input->post('qtyNGSend');
    		
    		$result = $this->fullRecoveryNG($jobID, $dateTimeStamp, $workerID, $sourceStepID
    										, $destinationStepID, $qtyNGSend);
		}
    	
		echo $result;    	
    }
    
    // ------------------------------------------- Get data set ----------------------------------------
    public function ajaxGetDsStep()
    {
    	if(!($this->is_logged())) {exit(0);}
    	if ($this->input->server('REQUEST_METHOD') === 'POST')
    	{
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
    public function ajaxGetDsFullStock()
    {
    	if(!($this->is_logged())) {exit(0);}
    	if ($this->input->server('REQUEST_METHOD') === 'POST')
    	{
    		$dsFullStock = array();
    		$dsResult = array();
    
    		$jobID = $this->input->post('jobID');
    		$stepID = $this->input->post('stepID');
    		$onlyNG = ($this->input->post('onlyNG') == 0);
    		
    		$dsStep = $this->getDsStep($stepID);
    		if(count($dsStep) > 0) {
    			if($onlyNG){
    				// Only NG ====> Get current NG.
    				$dsFullStock = $this->getDsCurrentFullStock($jobID, $stepID);
    				if(count($dsFullStock) > 0) {
    					$dsResult = array(
    							'FK_ID_Step'	=> $stepID,
    							'Qty_NG'		=> $dsFullStock[0]['Qty_NG'],
    							'Qty_Stock'		=> 0,
    					);
    				}
				} else {
					// Only Stock ====> Get (Current or First) Stock.
					if($dsStep[0]['First_Step_Flag'] == 1) {
						$dsFullStock = $this->getDsCurrentFullStock($jobID, $stepID);
						if(count($dsFullStock) > 0) {
							$dsResult = array(
									'FK_ID_Step'	=> $stepID,
									'Qty_NG'		=> 0,
									'Qty_Stock'		=> $dsFullStock[0]['Qty_OK_First_Step'],
							);
						}
					}
					else {
						$dsFullStock = $this->getDsPreviousFullStock($jobID, $dsStep[0]['Number']);
						if(count($dsFullStock) > 0) {
							$stockQty = "";
							foreach($dsFullStock as $row){
								$stockQty .= $row['Qty_OK']." | ";
							}
							$dsResult = array(
									'FK_ID_Step'	=> $stepID,
									'Qty_NG'		=> 0,
									'Qty_Stock'		=> substr($stockQty, 0, -3),
							);
						}
					}
				}
    		}

    		$dsFullStock = [$dsResult];
    		echo json_encode($dsFullStock);
    	}
    }
    

    // -------------------------------- Modify stock and Delete Activity -------------------------------
    public function ajaxDeleteActivity(){
    	if(!($this->is_logged())) {exit(0);}
    	$result = 4;

    	if ($this->input->server('REQUEST_METHOD') === 'POST')
    	{
    		// -------------------------- Save Step, Stock Part ----------------------------------------
    		$activityID = $this->input->post('activityID');
    		$stockID = $this->input->post('stockID');
    		$qtyNGSend = (int)($this->input->post('qtyNG')) * -1;

    		$result = $this->undoFullRecoveryNG($activityID, $stockID, $qtyNGSend);
    	}

    	echo $result;
    }
    
    
    
    
    // ***************************************** Private function **************************************
    // ************************************* Receive input quantity ************************************
    private function fullRecoveryNG($jobID, $dateTimeStamp, $workerID, $sourceStepID, $destinationStepID, $qtyNGSend) {
    	$result = 4;
    
    	$resultEnoughStock = false;
    	$resultUpdateStock = false;

    	$dsSourceFullStock = $this->getDsCurrentFullStock($jobID, $sourceStepID);
    	if(count($dsSourceFullStock) > 0) {
    		// Check enough stock.
    		$resultEnoughStock = $this->checkEnoughStock($dsSourceFullStock[0], $qtyNGSend);
			
    		$dsDestinationStock = $this->getDsStock($jobID, $destinationStepID);
    		if(($resultEnoughStock) && (count($dsDestinationStock) > 0)) {
    			$resultUpdateStock = $this->updateStock($dsSourceFullStock[0], $dsDestinationStock[0]
    													, $qtyNGSend, $workerID, $dateTimeStamp);
    		}
    	}

    	// --------- Set return result 
    	$result = $this->setResult($resultEnoughStock, $resultUpdateStock);
    
    	return $result;
    }
    
    // -------------------------------------------- Modify stock ---------------------------------------
    private function updateStock($rowSourceFullStock, $rowDestinationStock, $qtyNGSend, $workerID, $dateTimeStamp)
    {
    	$result = false;
    	$this->load->model('stock_m');
    
    	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Prepare Data !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	// DateTimeStamp.
    	$strDateTimeStamp = DateTime::createFromFormat('j-M-Y H:i', $dateTimeStamp)->format('Y-m-d H:i:s');
    	 
		// Source : Decrease Total NG stock.
    	$sourceStockID = $rowSourceFullStock['id'];
    	$sourceStockData['Qty_NG'] = $rowSourceFullStock['Qty_NG'] - $qtyNGSend;
    	
    	// Destination : Increase OK stock(If first step => increase that, If not increase previous step).
    	$destinationStockData = array();
    	$dsDestinationStep = $this->getDsStep($rowDestinationStock['FK_ID_Step']);
		if(count($dsDestinationStep) > 0) {
			if($dsDestinationStep[0]['First_Step_Flag'] == 1) {			// Not write log for first step.
				$destinationStockData[0]['DestinationStockID'] = $rowDestinationStock['id'];
				$destinationStockData[0]['Qty_OK_First_Step'] = $rowDestinationStock['Qty_OK_First_Step'] + $qtyNGSend;
			}
			else {
				$dsDestinationStock = $this->getDsPreviousFullStock(
                                    $rowDestinationStock['FK_ID_Job'], $dsDestinationStep[0]['Number']);
				if(count($dsDestinationStock) > 0) {
					$i = 0;
					foreach($dsDestinationStock as $row){
						$destinationStockData[$i]['DestinationStockID'] = $row['id'];
						$destinationStockData[$i++]['Qty_OK'] = $row['Qty_OK']
                                        + (($qtyNGSend * $row['NB_Sub']) / $rowSourceFullStock['NB_Sub']);
					}
				}
			}
		}
    	
		
    	// Insert activity data for cut NG.
    	$i=0;
		$dataActivity[$i]['Datetime_Stamp'] = $strDateTimeStamp;
		$dataActivity[$i]['FK_ID_Stock'] = $sourceStockID;
		$dataActivity[$i]['FK_ID_Worker'] = $workerID;
		$dataActivity[$i]['FK_ID_User'] = $this->session->userdata('id');
		$dataActivity[$i]['Qty_NG'] = (0 - $qtyNGSend);

        $i++;
        // Insert activity data for know destination stepID.
        $dataActivity[$i]['Datetime_Stamp'] = $strDateTimeStamp;
        $dataActivity[$i]['FK_ID_Stock'] = $rowDestinationStock['id'];
        $dataActivity[$i]['FK_ID_Worker'] = $workerID;
        $dataActivity[$i]['FK_ID_User'] = $this->session->userdata('id');
    	// --------- Update Database
    	$result = $this->stock_m->transaction_update_NG_stock($sourceStockID, $sourceStockData
    														, $destinationStockData, $dataActivity);

    	return $result;
    }
    // ----------------------------------------- Check enough stock ------------------------------------
    private function checkEnoughStock($rowSourceFullStock, $qtyNGSend) {
    	$result = ( (($rowSourceFullStock['Qty_NG'] < $qtyNGSend) || ($qtyNGSend == 0)) ? false : true );
    
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

    
    
    // ********************************** Modify stock and delete activity *****************************
    private function undoFullRecoveryNG($activityID, $stockID, $qtyNGSend) {
    	$result = 4;
    	 
    	$this->load->model('stock_m');
    	$dsSourceStock = $this->stock_m->get_row_by_id($stockID);       // Get Source stock for recovery(+) NG.
		if(count($dsSourceStock) > 0) {                                 // Have stock in DB.
			$this->load->model('activity_m');
			$dsDestinationStock = $this->activity_m->get_full_recovery_NG_destination($activityID); // Get Destination stock (OK or First).

			$result = $this->updateUndoRecoveryNGStock($dsSourceStock[0], $dsDestinationStock, $qtyNGSend, $activityID);
		}
    	 
    	return $result;
    }
    private function updateUndoRecoveryNGStock($rowSourceStock, $dsDestinationActivityStock, $qtyNGSend, $activityID)
    {
    	$result = 4;
    	$this->load->model('stock_m');
    
    	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Prepare Data !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	// StockID.
    	$sourceStockID = $rowSourceStock['id'];
    	// Prepare source stock data.
    	$dataSourceStock['Qty_NG'] = $rowSourceStock['Qty_NG'] + $qtyNGSend;
    	
    	// Prepare destination stock data.
        $dataDestinationStock = [];
		if(count($dsDestinationActivityStock) > 0) {
            $rowDestinationActivityStock = $dsDestinationActivityStock[0];
            // Destination : Decrease OK stock(If first step => decrease that, If not decrease previous step).
            $dsDestinationStep = $this->getDsStep($rowDestinationActivityStock['stepID']);
            if (count($dsDestinationStep) > 0) {
                $rowDestinationStep = $dsDestinationStep[0];
                if ($rowDestinationStep['First_Step_Flag'] == 1) {            // Check for first step.
                    $dataDestinationStock[0]['id'] = $rowDestinationActivityStock['stockID'];
                    $dataDestinationStock[0]['Qty_OK_First_Step'] = $rowDestinationActivityStock['firstStepStock'] - $qtyNGSend;
                    $result = 1;
                } else {
                    $dsDestinationStock = $this->getDsPreviousFullStock(
                                            $rowDestinationActivityStock['jobID'], $rowDestinationStep['Number']);
                    if (count($dsDestinationStock) > 0) {
                        $i = 0;
                        foreach ($dsDestinationStock as $row) {
                            $dataDestinationStock[$i]['id'] = $row['id'];
                            $dataDestinationStock[$i++]['Qty_OK'] = $row['Qty_OK']
                                - (($qtyNGSend * $row['NB_Sub']) / $rowDestinationStep['NB_Sub']);
                        }
                        $result = 1;
                    }
                }
            }
        } else {
            // Old version : Decrease qty_ok_first_step_stock
            $dataSourceStock['Qty_OK_First_Step'] = $rowSourceStock['Qty_OK_First_Step'] - $qtyNGSend;
            $result = (($dataSourceStock['Qty_OK_First_Step'] < 0) ? 4 : 3);
        }

    	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Update Database !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	// Updata database.
    	if($result != 4) {
    		$result = (($this->stock_m->transaction_update_undo_NG_stock(
						$sourceStockID, $dataSourceStock, $dataDestinationStock, $activityID))
						? 0 : 4);
		}
    	
    	return $result;
    }
    
    
    
    
    
    
    
    
    
    // ----------------------------------------- Initial combobox --------------------------------------
    private function getInitialDataToDisplay()
    {
		$data['dsJob'] = $this->getDsJobStatusOpen(0);
    	$data['dsWorker'] = $this->getDsWorker(0);

    	$data['dsFullActivity'] = $this->getDsLastFullRecoveryNGActivity(1000);

    	return $data;
    }
    // ------------------------------------------ Get DB to combobox -----------------------------------
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Job Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    private function getDsJobStatusOpen($id)
    {
    	$this->load->model('job_m');
    	$dsJob = (($id == 0)
    			? $this->job_m->get_all_row_status_open()
    			: $this->job_m->get_row_status_open_by_id($id));

		return $dsJob;
    }
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Worker Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    private function getDsWorker($id)
    {
    	$this->load->model('user_m');
    	$dsWorker = $this->user_m->get_row_active_status($id);
    
    	return $dsWorker;
    }
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    private function getDsStepByProcessID($processID)
    {
    	$this->load->model('step_m');
    	$dsStep = $this->step_m->get_by_where_array(array('FK_ID_Process' => $processID));
    
    	return $dsStep;
    }

    private function getDsStep($id)
    {
    	$this->load->model('step_m');
    	$dsStep = $this->step_m->get_row_by_id($id);
    	
    	return $dsStep;
	}
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    private function getDsCurrentFullStock($jobID, $stepID)
    {
    	$this->load->model('step_m');
    	$dsFullStock = $this->step_m->get_full_stock($jobID, $stepID);
    
    	return $dsFullStock;
    }
	private function getDsPreviousFullStock($jobID, $stepNumber)
    {
    	$this->load->model('step_m');
    	$dsPreviousFullStock = $this->step_m->get_previous_full_stock($jobID, $stepNumber);
    	 
    	return $dsPreviousFullStock;
    }
	
    
    // ------------------------------------------ Get DB to drilldown ----------------------------------
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Stock Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    private function getDsStock($jobID, $stepID)
    {
    	$this->load->model('stock_m');
    	$dsStock = $this->stock_m->get_row_by_job_and_step_id($jobID, $stepID);
    
    	return $dsStock;
    }





    // -------------------------------------- Initial Last activity table ------------------------------
    // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Last activity Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    private function getDsLastFullRecoveryNGActivity($rowNumber)
    {
    	$this->load->model('activity_m');
    	$dsFullActivity = $this->activity_m->get_last_full_recovery_NG_activity($rowNumber);

    	return $dsFullActivity;
    }
    









    // ************************************************ Helper *****************************************
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