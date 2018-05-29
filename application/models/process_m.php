<?php
class Process_m extends CI_Model {
	// Private Property
	var $table_name = "process";
	var $col_id = "id";
	var $col_name = "Name";
	var $col_desc = "DESC";
	var $col_desc_thai = "DESC_Thai";

	
	public function __construct(){
		parent::__construct();
	}

	// ******************************************* Custome function ************************************
	// ------------------------------------------- Save full project -----------------------------------
	function transactionSaveFullProject($jobID, $bomID, $processID, $dsStep, $qtyPlanProduct){
		$result = false;
		$resultJob = false;
		$resultStock = true;

		$this->load->model('job_m');
		$this->load->model('step_m');
		$this->load->model('stock_m');

		// Start transcation.
		$this->db->trans_begin();

		// ---------------- Save Stock Part ------------------------------------------
		$i=0;$j=0;
			foreach($dsStep as $row){
				if($row['stockID'] > 0) {
					// Update op-time in Stock.
					$dataStockToStore = array('Operation_Time'	=> $row['operationTime']);
					$resultStock &= $this->updateRowFreeRole($this->stock_m->table_name, $this->stock_m->col_id
					, $row['stockID'], $dataStockToStore);
				} else {
					// Insert Stock.
					$dataStockToStore = array(
						'FK_ID_Job'					=> $jobID,
						'FK_ID_Step'				=> $row['stepID'],
						'Qty_OK_First_Step'	=> $row['firstStepFlag'] * ($qtyPlanProduct * $row['nbSub']),
						'Qty_OK'						=> 0,
						'Qty_NG'						=> 0,
						'Operation_Time'		=> $row['operationTime'],
					);
					$stockID = $this->insertRowOtherTable($this->stock_m->table_name, $dataStockToStore);
					$resultStock &= (($stockID > 0) ? true : false);
				}
			}
		// ---------------- End Save Stock Part --------------------------------------

		// ------------- Update foreign key(Relation table) to Job table ---------------------------------
			$dataJobToStore[$this->job_m->col_process_id] = $processID;
			if($bomID > 0) { $dataJobToStore[$this->job_m->col_bom_id] = $bomID; }

			$resultJob = $this->updateRowFreeRole($this->job_m->table_name
				, $this->job_m->col_id, $jobID, $dataJobToStore);
		// ------------- End Update foreign key(Relation table) to Job table -----------------------------

		// Check status of transaction progress.
			if($this->db->trans_status() && ($resultJob && $resultStock)) {
				$this->db->trans_commit();
				$result = true;
			} else {
				$this->db->trans_rollback();
				$result = false;
			}
		// End Check status of transaction progress.
	
		return $result;
	}


	function transaction_save_full_project($jobID, $bomID, $processID, $cloneMode
	, $dataProcess_to_store, $dsStep, $qtyPlanProduct){

		$result = false;
		$resultJob = false;
		$resultProcess = false;
		$resultStep = true;
		$resultStock = true;

		$this->load->model('job_m');
		$this->load->model('step_m');
		$this->load->model('stock_m');

		// Start transcation.
		$this->db->trans_begin();

		// -------------- Save Process Part ------------------------------------------
		if($cloneMode){							// Check Clone mode of Process? (For insert process data)
			$processID = $this->insert_row($dataProcess_to_store);
			$resultProcess = (($processID == 0) ? false : true);
		} else {$resultProcess = true;}

		if($resultProcess) {
			$arrStepID = [];
			foreach($dsStep as $row){
				// ---------------- Save Step Part -------------------------------------------
				// Prepare data for save Step
				$dataStep_to_store = array(
					'Number'			=> $row['stepNumber'],
					'DESC'				=> $row['stepDesc'],
					'FK_ID_Process'		=> $processID,
					'FK_ID_Line'		=> $row['lineID'],
					'FK_ID_Machine'		=> $row['machineID'],
					'FK_ID_Sub_Assembly'=> $row['subAssemblyID'],
					'NB_Sub'			=> $row['nbSub'],
					'Next_Step_Number'	=> $row['nextStepNumber'],
					'First_Step_Flag'	=> $row['firstStepFlag'],
				);
				// Step part.
				$stepID = ($cloneMode ? 0 : $row['stepID']);		// Check Clone mode? (For insert insert new step)
				if($stepID == 0) {
					// Insert Step.
					$stepID = $this->insertRowOtherTable($this->step_m->table_name, $dataStep_to_store);
					$resultStep &= (($stepID == 0) ? false : true);
				} else {
					// Update Step.
					$resultStep &= $this->updateRowFreeRole($this->step_m->table_name
						, $this->step_m->col_id, $stepID, $dataStep_to_store);
				}

				// ---------------- Save Stock Part ------------------------------------------
				if($resultStep){
					// Stock part.
					if(($this->count_row_by_any_data($this->stock_m->table_name, $this->stock_m->col_job_id
					, $this->stock_m->col_step_id, $jobID, $stepID)) > 0) {
						// Prepare data for update op-time in Stock.
						$dataStock_to_store = array('Operation_Time'	=> $row['operationTime']);
						// Update op-time in Stock.
						$resultStock &= $this->updateRowFreeRole($this->stock_m->table_name, $this->stock_m->col_step_id
						, $stepID, $dataStock_to_store);
					} else {
						// Insert Stock.
						$dataStock_to_store = array(
							'FK_ID_Job'			=> $jobID,
							'FK_ID_Step'		=> $stepID,
							'Qty_OK_First_Step'	=> $row['firstStepFlag'] * ($qtyPlanProduct * $row['nbSub']),
							'Qty_OK'			=> 0,
							'Qty_NG'			=> 0,
							'Operation_Time'	=> $row['operationTime'],
						);

						$stockID = $this->insertRowOtherTable($this->stock_m->table_name, $dataStock_to_store);
						$resultStock &= (($stockID == 0) ? false : true);
					}

					array_push($arrStepID, $stepID);
				}
			}

			// ------------ Delete step in DB by exclude step id.
			$resultStep &= $this->delete_in_not_in_any_table($this->step_m->table_name, $this->step_m->col_process_id
				, $this->step_m->col_id, $processID, $arrStepID);
			$resultStock &= $this->delete_in_not_in_any_table($this->stock_m->table_name, $this->stock_m->col_job_id
				, $this->stock_m->col_step_id, $jobID, $arrStepID);

			// ------------- Update foreign key job --------------------------------------
			$dataJob_to_store[$this->job_m->col_process_id] = $processID;
			if($bomID > 0) { $dataJob_to_store[$this->job_m->col_bom_id] = $bomID; }

			$resultJob = $this->updateRowFreeRole($this->job_m->table_name
				, $this->job_m->col_id, $jobID, $dataJob_to_store);
		}

		// Check status of transaction progress.
		if($this->db->trans_status() && ($resultJob && $resultProcess && $resultStep && $resultStock)) {
			$this->db->trans_commit();
			$result = true;
		}
		else {
			$this->db->trans_rollback();
			$result = false;
		}
	
		return $result;
	}


	// ------------------------------------------- Save full process -----------------------------------
	function transactionSaveFullProcess($processID=0, $dataProcessToStore, $dsStep){
		$result = false;
		$resultProcess = false;
		$resultStep = true;
		$resultStock = false;

		$AddNewMode = (($processID <= 0) ? true : false);
		$this->load->model('step_m');
		$this->load->model('stock_m');

		// Start transcation.
		$this->db->trans_begin();

		// -------------- Save Process Part ------------------------------------------
		if($processID <= 0) {
			// Case Add new or Clone mode of Process? (For insert process data)
			$processID = $this->insert_row($dataProcessToStore);
			$resultProcess = (($processID <= 0) ? false : true);
		} else {
			// Case edit mode of Process? (For update process data)
			$resultProcess = $this->update_row($processID, $dataProcessToStore);
		}
		// -------------- End Save Process Part --------------------------------------

		// ---------------- Save Step Part -------------------------------------------
		if($resultProcess) {
			$arrStepID = [];
			foreach($dsStep as $row){
				// Prepare data for save Step
				$dataStep_to_store = array(
					'Number'			=> $row['stepNumber'],
					'DESC'				=> $row['stepDesc'],
					'FK_ID_Process'		=> $processID,
					'FK_ID_Line'		=> $row['lineID'],
					'FK_ID_Machine'		=> $row['machineID'],
					'FK_ID_Sub_Assembly'=> $row['subAssemblyID'],
					'NB_Sub'			=> $row['nbSub'],
					'Next_Step_Number'	=> $row['nextStepNumber'],
					'First_Step_Flag'	=> $row['firstStepFlag'],
				);
				// Step part.
				$stepID = ($AddNewMode ? 0 : $row['stepID']);		// Check Clone mode? (For insert insert new step)
				if($stepID <= 0) {
					// Insert Step.
					$stepID = $this->insertRowOtherTable($this->step_m->table_name, $dataStep_to_store);
					$resultStep &= (($stepID == 0) ? false : true);
				} else {
					// Update Step.
					$resultStep &= $this->updateRowFreeRole($this->step_m->table_name
						, $this->step_m->col_id, $stepID, $dataStep_to_store);
				}

				array_push($arrStepID, $stepID);
			}

			// ------------ Delete step in DB by exclude step id.
			$dsStepIdExclude = $this->getStepExcludeProcess($processID, $arrStepID);
			$rStepIdExclude = array_column($dsStepIdExclude, "id");
			if(count($rStepIdExclude) > 0) {
				$resultStep &= $this->deleteRowArrayId($rStepIdExclude
				, $this->step_m->col_id, $this->step_m->table_name);
				$resultStock = $this->deleteRowArrayId($rStepIdExclude
				, $this->stock_m->col_step_id, $this->stock_m->table_name);
			}
		}
		// ---------------- End Save Step Part ---------------------------------------

		// Check status of transaction progress.
		if($this->db->trans_status() && ($resultProcess && $resultStep)) {
			$this->db->trans_commit();
			$result = true;
		} else {
			$this->db->trans_rollback();
			$result = false;
		}
		// End Check status of transaction progress.

		return $result;
	}


	// --------------------------------------------- Get template --------------------------------------
	public function get_template(){
		$result = [
			$this->col_id					=> 0,
			$this->col_name				=> '',
			$this->col_desc				=> '',
			$this->col_desc_thai	=> '',
		];

		return $result;
	}






	// -------------------------------------------------- Get ------------------------------------------
	public function getRowByArrayID($arrayProcessID=[], $limit=null, $offset=null) {
		// Prepare Criteria.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayProcessID) > 0) { $criteria = $this->plan_m->createCriteriaIN('p.id', $arrayProcessID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' WHERE '.$criteria;
		}


		$sqlStr = "SELECT *"
			. " FROM process p"
			. $criteria
			. " ORDER BY " . $this->col_name . " ASC"
			. createSqlLimitOffset($limit, $offset);

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}

	public function get_row_by_id($id=0, $arrWhere=[]){
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_id, $id);
		if(count($arrWhere) > 0) {
			$this->db->where($arrWhere);
		}
		$this->db->order_by($this->col_name, 'Asc');
		$query = $this->db->get();

		return $query->result_array(); 
	}    

	public function get_row($search_string=null, $order='Name', $order_type='Asc'
	, $limit_start=null, $limit_end=null){

		$this->db->select('*');
		$this->db->from($this->table_name);

		if($search_string){
			$this->db->like($this->col_name, $search_string);
		}
		$this->db->group_by($this->col_id);

		if($order){
			$this->db->order_by($order, $order_type);
		}else{
				$this->db->order_by($this->col_id, $order_type);
		}

		if($limit_start && $limit_end){
			$this->db->limit($limit_start, $limit_end);	
		}

		if($limit_start != null){
			$this->db->limit($limit_start, $limit_end);    
		}

		$query = $this->db->get();

		return $query->result_array(); 	
	}

	function getStepExcludeProcess($processId=0, $rStepId=[]){
		$this->load->model('step_m');
		
		$this->db->select($this->step_m->col_id);
		$this->db->from($this->step_m->table_name);
		if($processId > 0) {
			$this->db->where($this->step_m->col_process_id, $processId);
		}
		if(count($rStepId) > 0) {
			$this->db->where_not_in($this->step_m->col_id, $rStepId);
		}

		$query = $this->db->get();
		$result = $query->result_array();

		return $result;
	}

	// ------------------------------------------------- Count -----------------------------------------
	function count_row($search_string=null, $order=null){
		$this->db->select('*');
		$this->db->from($this->table_name);
		if($search_string){
			$this->db->like($this->col_name, $search_string);
		}
		if($order){
			$this->db->order_by($order, 'Asc');
		}else{
				$this->db->order_by($this->col_id, 'Asc');
		}
		$query = $this->db->get();
		return $query->num_rows();        
	}

	public function count_row_by_any_data($tableName, $idName1, $idName2, $id1=0, $id2=0){
		$this->db->select('*');
		$this->db->from($tableName);
		$this->db->where($idName1, $id1);
		$this->db->where($idName2, $id2);
	
		$query = $this->db->get();
		return $query->num_rows();
	}


	// ------------------------------------------------- Insert ----------------------------------------
	function insert_row($data){
		$insert = $this->db->insert($this->table_name, $data);
		return $this->db->insert_id();
		//return $insert;
	}

	function insertRowOtherTable($table_name, $data){
		$insert = $this->db->insert($table_name, $data);
		return $this->db->insert_id();
		//return $insert;
	}


	// ------------------------------------------------- Update ----------------------------------------
	function update_row($id, $data){
		$this->db->where($this->col_id, $id);
		$this->db->update($this->table_name, $data);
		$report = array();
		$report['error'] = $this->db->_error_number();
		$report['message'] = $this->db->_error_message();
		if($report !== 0){
			return true;
		}else{
			return false;
		}
	}

	function updateRowFreeRole($tableName, $idName, $id, $data){
		$this->db->where($idName, $id);
		$this->db->update($tableName, $data);
		$report = array();
		$report['error'] = $this->db->_error_number();
		$report['message'] = $this->db->_error_message();
		if($report !== 0){
			return true;
		}else{
			return false;
		}
	}


	// ------------------------------------------------- Delete ----------------------------------------
	function delete_row($id){
		$this->db->where($this->col_id, $id);
		$result = $this->db->delete($this->table_name);

		return $result;
	}

	function deleteRowArrayId($rId=[], $columnWhere, $tableName){
		$result = false;
		if(count($rId) > 0) {
			$this->db->where_in($columnWhere, $rId);
			$result = $this->db->delete($tableName);
		}

		return $result;
	}
}
?>	
