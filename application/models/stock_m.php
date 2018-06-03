<?php
class Stock_m extends CI_Model {
// Public Property
	public $firstStepFlag = false;
// Public Property

// Private Property
	var $table_name = "stock";
	var $col_id = "id";
	var $col_job_id = "FK_ID_Job";
	var $col_step_id = "FK_ID_Step";
	var $col_qty_ok_first_step = "Qty_OK_First_Step";
	var $col_qty_ok = "Qty_OK";
	var $col_qty_ng = "Qty_NG";
	var $col_operation_time = "Operation_Time";
// End Private Property

	public function __construct() {
		parent::__construct();
	}


	// ******************************************* Custome function ************************************
	public function getStepStockByMultiJobAndStepId($arrayJobID=[], $arrayStepID=[], $limit=null, $offset=null) {
		// Create criteria query.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('k.FK_ID_Job', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->plan_m->createCriteriaIN('k.FK_ID_Step', $arrayStepID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' AND '.$criteria;
		}


		$sqlStr = "SELECT j.Name JobName, CONCAT(s.Number, ' - ', s.`DESC`) NumberAndDesc"
					.", IF(s.First_Step_Flag=0, pb.Name, b.Name) SubAssemblyName"
					.", IF(s.First_Step_Flag=0, pk.Qty_OK, k.Qty_OK_First_Step) StockQty"

					.", s.First_Step_Flag FirstStepFlag"
					.", k.FK_ID_Job JobID"
					.", IF(s.First_Step_Flag=0, pk.FK_ID_Step, k.FK_ID_Step) StepID"
			." FROM stock k"
				." INNER JOIN job j ON k.FK_ID_Job = j.id"
				." INNER JOIN step s ON k.FK_ID_Step = s.id"
				." LEFT JOIN sub_assembly b ON s.FK_ID_Sub_Assembly = b.id"

				." LEFT JOIN step ps ON ((s.FK_ID_Process = ps.FK_ID_Process) && (s.Number = ps.Next_Step_Number))"
				." LEFT JOIN sub_assembly pb ON ps.FK_ID_Sub_Assembly = pb.id"
				." LEFT JOIN stock pk ON ((j.id = pk.FK_ID_Job) && (ps.id = pk.FK_ID_Step))"
			." WHERE j.FK_ID_Job_Status = 1 AND j.Delete_Flag=0" .$criteria
			." ORDER BY k.FK_ID_Job, k.FK_ID_Step, ps.Number"
			.createSqlLimitOffset($limit, $offset);

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
    
    
  public function get_row_by_job_and_step_id($jobID=0, $stepID=0) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_job_id, $jobID);
		$this->db->where($this->col_step_id, $stepID);

		$query = $this->db->get();
		return $query->result_array(); 
	}

  public function getRowByJobAndMultiStepId($jobID, $rStepId=[]) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_job_id, $jobID);
		$this->db->where_in($this->col_step_id, $rStepId);

		$query = $this->db->get();
		return $query->result_array();
	}



	public function count_row_by_job_and_step_id($jobID=0, $stepID=0) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_job_id, $jobID);
		$this->db->where($this->col_step_id, $stepID);

		$query = $this->db->get();
		return $query->num_rows();
	}

	function update_row_by_step_id($stepID, $data) {
		$this->db->where($this->col_step_id, $stepID);
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


	function update_row_by_job_id_and_step_id($jobID, $stepID, $data) {
		$this->db->where($this->col_job_id, $jobID);
		$this->db->where($this->col_step_id, $stepID);
		$affectedRows = $this->db->update($this->table_name, $data);

		$report = array();
		$report['error'] = $this->db->_error_number();
		$report['message'] = $this->db->_error_message();
		if($report !== 0){
			return true;
		}else{
			return false;
		}
	}



	// ---------------------------------------- Update stock by qty input ------------------------------
	function transaction_update_stock($currentStockID, $dataPreviousStock, $dataCurrentStock, $dataActivity) {
		$result = false;
		// Start transcation.
		$this->db->trans_begin();

		if(count($dataPreviousStock) > 0) {									// In case : Not first step.
			// Modify previous stock.
			foreach($dataPreviousStock as $row) {
				$PreviousStockID = $row['PreviousStockID'];
				
				$PreviousStockUpdate = $row;
				unset($PreviousStockUpdate['PreviousStockID']);

				$this->update_row($PreviousStockID, $PreviousStockUpdate);
			}
		}
		// Modify current stock (Or include modify current first stock).
		$this->update_row($currentStockID, $dataCurrentStock);

		// Insert activity.
		if(count($dataActivity) > 0) {
			foreach($dataActivity as $row) {
				$this->db->insert("activity", $row);
			}
		}

		// Check status of transaction progress.
		if($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$result = false;
		} else {
			$this->db->trans_commit();
			$result = true;
		}

		return $result;
	}

	// --------------------------------------- Update stock by recovery NG -----------------------------
	function transaction_update_NG_stock($sourceStockID, $sourceStockData, $destinationStockData, $dataActivity) {
		$result = false;
		// Start transcation.
		$this->db->trans_begin();

		if($this->update_row($sourceStockID, $sourceStockData)) {					// Manipulate Source stock.
			if(count($destinationStockData) > 0) {									// In case : Not first step.
				// Increase previous stock qty OK.
				foreach($destinationStockData as $row) {
					$destinationStockID = $row['DestinationStockID'];

					$PreviousStockUpdate = $row;
					unset($PreviousStockUpdate['DestinationStockID']);

					$this->update_row($destinationStockID, $PreviousStockUpdate);	// Manipulate Destination stock.
				}
			}
			// Insert activity.
			if(count($dataActivity) > 0) {
				$activitySourceID = 0;
				$activityCurrentID = 0;
				$i=0;
				foreach($dataActivity as $row) {
					$this->db->insert("activity", $row);
					$activityCurrentID = $this->db->insert_id();

					if($i == 0) {
						$activitySourceID = $activityCurrentID;
						$i++;
					}
					$data['FK_ID_Activity_Source'] = $activitySourceID;
					$data['Datetime_Stamp'] = $row['Datetime_Stamp'];

					$this->db->where("id", $activityCurrentID);
					$this->db->update("activity", $data);
				}
			}
		}

		// Check status of transaction progress.
		if($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$result = false;
		} else {
			$this->db->trans_commit();
			$result = true;
		}

		return $result;
	}

	// -------------------------------------- Update stock by undo qty input ---------------------------
	function transaction_update_undo_stock($currentStockID, $dataPreviousStock, $dataCurrentStock, $activityID) {
		$result = false;
		// Start transcation.
		$this->db->trans_begin();

		if(count($dataPreviousStock) > 0) {									// In case : Not first step.
			// Modify previous stock.
			foreach($dataPreviousStock as $row) {
				$PreviousStockID = $row['PreviousStockID'];

				$PreviousStockUpdate = $row;
				unset($PreviousStockUpdate['PreviousStockID']);

				$this->update_row($PreviousStockID, $PreviousStockUpdate);
			}
		}
		// Modify current stock (Or include modify current first stock).
		$this->update_row($currentStockID, $dataCurrentStock);

		// Delete record of activity table.
		if(count($activityID) > 0) {
			$this->db->where("id", $activityID);
			$this->db->delete("activity");
		}

		// Check status of transaction progress.
		if($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$result = false;
		} else {
			$this->db->trans_commit();
			$result = true;
		}

		return $result;
	}

	// ------------------------------------- Update stock by undo recovery NG --------------------------
	function transaction_update_undo_NG_stock($sourceStockID, $dataSourceStock, $dataDestinationStock, $activityID) {
		$result = false;
		// Start transcation.
		$this->db->trans_begin();

		if(count($dataDestinationStock) > 0) {									// In case : Not first step.
			// Modify destination stock.
			foreach($dataDestinationStock as $row) {
				$DestinationStockID = $row['id'];
	
				$DestinationStockUpdate = $row;
				unset($DestinationStockUpdate['id']);
	
				$this->update_row($DestinationStockID, $DestinationStockUpdate);
			}
		}
		// Modify source stock (Or include modify source first stock).
		$this->update_row($sourceStockID, $dataSourceStock);

		// Delete all record of activitySourceID table.
		if(count($activityID) > 0) {
			$this->db->where("FK_ID_Activity_Source", $activityID);
			$this->db->delete("activity");
		}

		// Check status of transaction progress.
		if($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$result = false;
		} else {
			$this->db->trans_commit();
			$result = true;
		}

		return $result;
	}




	// ************************************************* Normal function *******************************
	public function get_row_by_id($id=0, $arrWhere=[]) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_id, $id);
		if(count($arrWhere) > 0) {
			$this->db->where($arrWhere);
		}
		$query = $this->db->get();

		return $query->result_array(); 
	}

	public function get_row($search_string=null, $order=null, $order_type='Asc', $limit_start=null, $limit_end=null) {
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

  function count_row($search_string=null, $order=null) {
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

	function insert_row($data) {
		$insert = $this->db->insert($this->table_name, $data);
		return $insert;
	}

	function update_row($id, $data) {
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

	function delete_row($id) {
		$this->db->where($this->col_id, $id);
		$result = $this->db->delete($this->table_name);
		
		return $result;
	}


	function delete_not_in($jobID, $arrStepID) {
		$this->db->where($this->col_job_id, $jobID);
		$this->db->where_not_in($this->col_step_id, $arrStepID);

		$result = $this->db->delete($this->table_name);

		return $result;
	}
}
?>