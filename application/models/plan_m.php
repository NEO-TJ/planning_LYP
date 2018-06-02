<?php
class Plan_m extends CI_Model {
// Private Property
	var $table_name = "plan";
	var $col_id = "id";
	var $col_stock_id = "FK_ID_Stock";
	var $col_date_stamp = "Date_Stamp";
	var $col_plan_qty_ok = "Plan_Qty_OK";
	var $col_plan_qty_worker = "Plan_Qty_Worker";
// End Private Property

	public function __construct() {
		parent::__construct();
	}



	// **************************************************** Join table function ***************************************
	public function getFullPlanRow($diffStartCurrentDate=0, $arrayJobID=[], $arrayStepID=[]
	, $arrayLineID=[], $arrayJobTypeID=[], $totalSlotDate=20) {
		// Prepare Criteria.
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->createCriteriaIN('j.id', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->createCriteriaIN('k.FK_ID_Step', $arrayStepID, $criteria); }
		if(count($arrayLineID) > 0) { $criteria = $this->createCriteriaIN('s.FK_ID_Line', $arrayLineID, $criteria); }
		if(count($arrayJobTypeID) > 0) { $criteria = $this->createCriteriaIN('j.FK_ID_Job_Type', $arrayJobTypeID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' AND '.$criteria;
		}
		$sqlWhere = " WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1" . $criteria;
		// End Prepare Criteria.

		$dsData['dsMain'] = $this->getFullPlanRowMain($sqlWhere);
		$dsData['dsSlotDate'] = $this->getFullPlanRowSlotDate($sqlWhere, $diffStartCurrentDate, $totalSlotDate);

		return $dsData;
	}

	private function getFullPlanRowMain($sqlWhere) {
		$sqlStr = "SELECT j.id JobID, s.id StepID, k.id StockId, s.FK_ID_Line"
			. ", j.FK_ID_Job_Type, j.FK_ID_Job_Status, j.Name JobName"
			. ", IF((ISNULL(s.Next_Step_Number)) || (s.Next_Step_Number=0),'-',s.Next_Step_Number) Next_Step_Number"
			. ", CONCAT(s.Number, ' - ', s.`DESC`) NumberAndDESC"
			. ", l.Name LineName"
			. ", IF(s.First_Step_Flag=1, b.Name, pb.Name) SubAssemblyName"
			. ", IF(s.First_Step_Flag=1, k.Qty_OK_First_Step, pk.Qty_OK) StockQty"
			. ", IF(ISNULL(SUM(a.Qty_OK)),0,SUM(a.Qty_OK)) activity_Qty_OK"
			. ", k.Qty_NG"

			. " FROM job j"
			. " INNER JOIN step s ON j.FK_ID_Process = s.FK_ID_Process"
			. " INNER JOIN stock k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
			. " LEFT JOIN sub_assembly b ON s.FK_ID_Sub_Assembly = b.id"
			. " LEFT JOIN line l ON s.FK_ID_Line = l.id"
			. " LEFT JOIN activity a ON k.id = a.FK_ID_Stock"

			. " LEFT JOIN step ps ON ((s.FK_ID_Process = ps.FK_ID_Process) && (s.Number = ps.Next_Step_Number))"
			. " LEFT JOIN sub_assembly pb ON ps.FK_ID_Sub_Assembly = pb.id"
			. " LEFT JOIN stock pk ON ((j.id = pk.FK_ID_Job) && (ps.id = pk.FK_ID_Step))"

			. $sqlWhere
			. " GROUP BY j.id, s.id, k.id, pk.id"
			. " ORDER BY JobName, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}

	private function getFullPlanRowSlotDate($sqlWhere, $diffStartCurrentDate=0, $totalSlotDate=20) {
		// Prepare Date Slot.
		$startDate = $this->gen_date_by_diff($diffStartCurrentDate);
		$strDateStart = $startDate->format('Y-m-d');
		$startDate->modify('+' . $totalSlotDate . ' day');
		$strDateEnd = $startDate->format('Y-m-d');

		$sqlStr = "SELECT k.id StockId , DATE(a.Datetime_Stamp) DateStamp"
				.", SUM(a.Qty_OK) ActualOKQty"
				.", SUM(a.Qty_NG) ActualNGQty"
				.", COUNT(DISTINCT a.FK_ID_Worker) ActualWorkerQty"
				.", p.Plan_Qty_OK PlanOKQty"
				.", p.Plan_Qty_Worker PlanWorkerQty"
				.", k.Operation_Time OperationTime"
			." FROM stock k"
				." INNER JOIN job j ON k.FK_ID_Job = j.id"
				." INNER JOIN step s ON k.FK_ID_Step = s.id"
				." LEFT JOIN activity a ON (k.id = a.FK_ID_Stock)"
				." LEFT JOIN plan p ON (k.id = p.FK_ID_Stock) && (p.Date_Stamp = DATE(a.Datetime_Stamp))"
			. $sqlWhere
			." AND DATE(a.Datetime_Stamp) BETWEEN '" . $strDateStart . "%' AND '" . $strDateEnd . "%'"
			." GROUP BY StockId, DateStamp"
		." UNION"
			." SELECT k.id StockId, p.Date_Stamp DateStamp"
				.", SUM(a.Qty_OK) ActualOKQty"
				.", SUM(a.Qty_NG) ActualNGQty"
				.", COUNT(DISTINCT a.FK_ID_Worker) ActualWorkerQty"
				.", p.Plan_Qty_OK PlanOKQty"
				.", p.Plan_Qty_Worker PlanWorkerQty"
				.", k.Operation_Time OperationTime"
			." FROM stock k"
				." INNER JOIN job j ON k.FK_ID_Job = j.id"
				." INNER JOIN step s ON k.FK_ID_Step = s.id"
				." LEFT JOIN plan p ON (k.id = p.FK_ID_Stock) "
				." LEFT JOIN activity a ON (k.id = a.FK_ID_Stock) && (p.Date_Stamp = DATE(a.Datetime_Stamp))"
			.$sqlWhere
			." AND p.Date_Stamp BETWEEN '" . $strDateStart . "%' AND '" . $strDateEnd . "%'"
			." GROUP BY StockId,DateStamp";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}


	// ********************************************************* Save data ********************************************
	public function saveOKQtyPlan($stockID, $diffStartCurrentDate, $okQtyPlan) {
		$result = 1;
		$strDateStamp = $this->gen_date_by_diff($diffStartCurrentDate)->format('Y-m-d');
		
		$sqlStr = "SELECT * FROM plan WHERE ((FK_ID_Stock = ".$stockID.") && (Date_Stamp LIKE '".$strDateStamp."'))";
		$query = $this->db->query($sqlStr);
		$dsPlan = $query->result_array();
		
		if(count($dsPlan) > 0) {
			$id = $dsPlan[0]['id'];
			$data_to_store = array("Plan_Qty_OK" => $okQtyPlan);

			$result = (($this->update_row($id, $data_to_store) == TRUE)? 0 : 1);
		}
		else {
    		$data_to_store = array(
									'FK_ID_Stock' 		=> $stockID,
									'Date_Stamp'		=> $strDateStamp,
									'Plan_Qty_OK'		=> $okQtyPlan,
									'Plan_Qty_Worker'	=> 0,
			);
    		$result = (($this->insert_row($data_to_store) == TRUE)? 0 : 1);
		}
		
		return $result;
	}
	public function saveWorkerQtyPlan($stockID, $diffStartCurrentDate, $workerQtyPlan) {
		$result = 1;
		$strDateStamp = $this->gen_date_by_diff($diffStartCurrentDate)->format('Y-m-d');
	
		$sqlStr = "SELECT * FROM plan WHERE ((FK_ID_Stock = ".$stockID.") && (Date_Stamp LIKE '".$strDateStamp."'))";
		$query = $this->db->query($sqlStr);
		$dsPlan = $query->result_array();
	
		if(count($dsPlan) > 0) {
			$id = $dsPlan[0]['id'];
			$data_to_store = array("Plan_Qty_Worker" => $workerQtyPlan);
	
			($this->update_row($id, $data_to_store) == TRUE)? $result = 0 : $result = 1;
		}
		else {
			$data_to_store = array(
					'FK_ID_Stock' 		=> $stockID,
					'Date_Stamp'		=> $strDateStamp,
					'Plan_Qty_OK'		=> 0,
					'Plan_Qty_Worker'	=> $workerQtyPlan,
			);
			($this->insert_row($data_to_store) == TRUE) ? $result = 0 : $result = 1;
		}
	
		return $result;
	}

	public function shiftDatePlanDelayWithOffsetSun($stockID, $shiftDate) {
		$afftectedRows = -1;
		
		$dataToStore = $this->genSqlDataToStoreShiftDaywithOffsetSun($stockID, $shiftDate);
		if(($dataToStore != '') && ($dataToStore != NULL)) {
			$sqlStr = "INSERT INTO plan (".$this->col_id.", ".$this->col_date_stamp.")"
					." VALUES ".$dataToStore
					." ON DUPLICATE KEY UPDATE "
					.$this->col_date_stamp." = VALUES(".$this->col_date_stamp.")";
			
			$query = $this->db->query($sqlStr);

			$report = array();
			$report['error'] = $this->db->_error_number();
			$report['message'] = $this->db->_error_message();
			if($report !== 0) {
					$afftectedRows  = $this->db->affected_rows();
			}
			$afftectedRows = (($afftectedRows > 0) ? $afftectedRows/2 : $afftectedRows);
		}

		return $afftectedRows;
								
	}





	// ****************************************************** Report function *****************************************
	// -------------------------------------------------------- Daily Target ------------------------------------------
	public function get_daily_target($strDateStart, $strDateEnd, $lineID, $arrayJobID=[], $arrayStepID=[]) {
		$criteria ='';
		// Create job criteria.
		if(count($arrayJobID) > 0) { $criteria = $this->createCriteriaIN('j.id', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->createCriteriaIN('s.id', $arrayStepID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
		
			$criteria = ' AND '.$criteria;
		}
		// Create line criteria.
		if($lineID > 0) {
			$criteria = $criteria.' AND s.FK_ID_Line='.$lineID;
		}
		
		$sqlStr = "SELECT s.FK_ID_Line, l.Name lineCurrent, p.Date_Stamp"
					.", j.Name `Job Number`, s.Number, s.`DESC`, p.Plan_Qty_OK"
					.", s.Next_Step_Number nextStepNumber, nl.Name lineNext, CONCAT(j.id,'-',s.id) jsBarcode"
					." FROM plan p"
					." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
					." INNER JOIN job j ON k.FK_ID_Job = j.id"
					." INNER JOIN step s ON k.FK_ID_Step = s.id"
					." LEFT JOIN line l ON s.FK_ID_Line = l.id"
					." LEFT JOIN step ns ON s.Next_Step_Number = ns.Number AND j.FK_ID_Process = ns.FK_ID_Process"
					." LEFT JOIN line nl ON ns.FK_ID_Line = nl.id"
					." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
					." AND p.Plan_Qty_OK > 0" .$criteria
					." AND Date_Stamp BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'"
					." ORDER BY s.FK_ID_Line, j.Name, s.Number, p.Date_Stamp";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}

	// -------------------------------------------------------- Achievement -------------------------------------------
	public function getDsAchievement($strDateStart, $strDateEnd, $arrayLineID=[], $arrayJobID=[], $arrayStepID=[]) {
		// Create criteria query.
		$criteria ='';
		if(count($arrayLineID) > 0) { $criteria = $this->createCriteriaIN('s.FK_ID_Line', $arrayLineID, $criteria); }
		if(count($arrayJobID) > 0) { $criteria = $this->createCriteriaIN('k.FK_ID_Job', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->createCriteriaIN('k.FK_ID_Step', $arrayStepID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' AND '.$criteria;
		}

		$dsActivityOk = $this->getDsActivityQtyOk($strDateStart, $strDateEnd, $criteria);
		$dsPlanOk = $this->getDsPlanQtyOk($strDateStart, $strDateEnd, $criteria);
		$result = array("dsActivityOk" => $dsActivityOk, "dsPlanOk" => $dsPlanOk);

		return $result;
	}
	private function getDsActivityQtyOk($strDateStart, $strDateEnd, $criteria) {
		$sqlStr = "SELECT CONCAT(s.id, '-', DATE(a.Datetime_Stamp)) myId"
				.", l.Name lineName"
				.", j.Name jobName"
				.", s.Number, s.DESC"
				.", DATE(a.Datetime_Stamp) dateStamp"
				.", s.id"
				.", SUM(a.Qty_OK) actualOkQty"
				.", 0 planOkQty"
				.", SUM(a.Qty_OK) achievementOkQty"
			." FROM activity a"
				." INNER JOIN stock k ON a.FK_ID_Stock = k.id"
				." INNER JOIN job j ON k.FK_ID_Job = j.id"
				." INNER JOIN step s ON k.FK_ID_Step = s.id"
				." LEFT JOIN line l ON s.FK_ID_Line = l.id"
			." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
				." AND a.FK_ID_Activity_Source IS NULL AND a.Qty_OK > 0"
				." AND DATE(a.Datetime_Stamp) BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'"
				.$criteria
			." GROUP BY s.id, DATE(a.Datetime_Stamp)"
			." ORDER BY lineName, j.Name, s.Number, dateStamp";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	private function getDsPlanQtyOk($strDateStart, $strDateEnd, $criteria) {
		$sqlStr = "SELECT CONCAT(s.id, '-', DATE(p.Date_Stamp)) myId"
				.", l.Name lineName"
				.", j.Name jobName"
				.", s.Number, s.DESC"
				.", DATE(p.Date_Stamp) dateStamp"
				.", s.id"
				.", 0 actualOkQty"
				.", SUM(p.Plan_Qty_OK) planOkQty"
				.", 0 achievementOkQty"
			." FROM plan p"
				." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
				." INNER JOIN job j ON k.FK_ID_Job = j.id"
				." INNER JOIN step s ON k.FK_ID_Step = s.id"
				." LEFT JOIN line l ON s.FK_ID_Line = l.id"
			." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
				." AND p.Plan_Qty_OK > 0"
				." AND DATE(p.Date_Stamp) BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'"
				.$criteria
			." GROUP BY s.id, DATE(p.Date_Stamp)"
			." ORDER BY lineName, j.Name, s.Number, dateStamp";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}

	// ------------------------------------------------------ Working Capacity ----------------------------------------
	public function get_workingCapacity($arrayCustomerID=[], $arrayJobID=[]
		, $arrayLineID=[], $arraySubAssemblyID=[]) {
		// Set first week start date and interval.
		$oStartDate = new DateTime();
		if(date("w", strtotime($oStartDate->format('Y-m-d'))) == 0) {
			$oStartDate->modify('+1 day');
		}
		$firstWeekInterval = 6 - date("w", strtotime($oStartDate->format('Y-m-d')));
		
		// Create criteria query.
		$criteria ='';
		if(count($arrayCustomerID) > 0) { $criteria = $this->createCriteriaIN('pj.FK_ID_Customer', $arrayCustomerID, $criteria); }
		if(count($arrayJobID) > 0) { $criteria = $this->createCriteriaIN('k.FK_ID_Job', $arrayJobID, $criteria); }
		if(count($arrayLineID) > 0) { $criteria = $this->createCriteriaIN('s.FK_ID_Line', $arrayLineID, $criteria); }
		if(count($arraySubAssemblyID) > 0) { $criteria = $this->createCriteriaIN('s.FK_ID_Sub_Assembly', $arraySubAssemblyID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
	
			$criteria = ' AND '.$criteria;
		}
	
		$sqlStr = "SELECT c.Name customerName, j.Name jobName, l.Name lineName, sm.Name sub_assemblyName"
					.", (IF(ISNULL(k.Operation_Time),0,k.Operation_Time) * 60) Operation_Time"
					.", IF(ISNULL(w1.planQty),'-',w1.planQty) planQtyOK1"
					.", IF(ISNULL((k.Operation_Time * w1.planQty)/60),'-',(k.Operation_Time * w1.planQty)/60) hours1"
					.", IF(ISNULL(w2.planQty),'-',w2.planQty) planQtyOK2"
					.", IF(ISNULL((k.Operation_Time * w2.planQty)/60),'-',(k.Operation_Time * w2.planQty)/60) hours2"
					.", IF(ISNULL(w3.planQty),'-',w3.planQty) planQtyOK3"
					.", IF(ISNULL((k.Operation_Time * w3.planQty)/60),'-',(k.Operation_Time * w3.planQty)/60) hours3"
					.", IF(ISNULL(w4.planQty),'-',w4.planQty) planQtyOK4"
					.", IF(ISNULL((k.Operation_Time * w4.planQty)/60),'-',(k.Operation_Time * w4.planQty)/60) hours4"
					.", IF(ISNULL(w5.planQty),'-',w5.planQty) planQtyOK5"
					.", IF(ISNULL((k.Operation_Time * w5.planQty)/60),'-',(k.Operation_Time * w5.planQty)/60) hours5"
					.", IF(ISNULL(w6.planQty),'-',w6.planQty) planQtyOK6"
					.", IF(ISNULL((k.Operation_Time * w6.planQty)/60),'-',(k.Operation_Time * w6.planQty)/60) hours6"
					.", IF(ISNULL(w7.planQty),'-',w7.planQty) planQtyOK7"
					.", IF(ISNULL((k.Operation_Time * w7.planQty)/60),'-',(k.Operation_Time * w7.planQty)/60) hours7"
				." FROM stock k"
					." INNER JOIN job j ON k.FK_ID_Job = j.id"
					." INNER JOIN step s ON k.FK_ID_Step = s.id"
					." INNER JOIN project pj ON j.FK_ID_Project = pj.id"
					." LEFT JOIN customer c ON pj.FK_ID_Customer = c.id"
					." LEFT JOIN line l ON s.FK_ID_Line = l.id"
					." LEFT JOIN sub_assembly sm ON s.FK_ID_Sub_Assembly = sm.id"

					." LEFT JOIN ("
						." SELECT p.FK_ID_Stock, SUM(p.Plan_Qty_OK) planQty"
						." FROM plan p"
							." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
							." INNER JOIN job j ON k.FK_ID_Job = j.id"
						." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
							." AND p.Date_Stamp BETWEEN '"
							.$oStartDate->format('Y-m-d')."%' AND '"
							.$oStartDate->modify('+'.$firstWeekInterval.' day')->format('Y-m-d')."%'"
						." GROUP BY p.FK_ID_Stock"
					.") w1 ON k.id = w1.FK_ID_Stock"

					." LEFT JOIN ("
						." SELECT p.FK_ID_Stock, SUM(p.Plan_Qty_OK) planQty"
						." FROM plan p"
							." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
							." INNER JOIN job j ON k.FK_ID_Job = j.id"
						." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
							." AND p.Date_Stamp BETWEEN '"
							.$oStartDate->modify('+2 day')->format('Y-m-d')."%' AND '"
							.$oStartDate->modify('+5 day')->format('Y-m-d')."%'"
						." GROUP BY p.FK_ID_Stock"
					.") w2 ON k.id = w2.FK_ID_Stock"

					." LEFT JOIN ("
						." SELECT p.FK_ID_Stock, SUM(p.Plan_Qty_OK) planQty"
						." FROM plan p"
							." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
							." INNER JOIN job j ON k.FK_ID_Job = j.id"
						." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
							." AND p.Date_Stamp BETWEEN '"
							.$oStartDate->modify('+2 day')->format('Y-m-d')."%' AND '"
							.$oStartDate->modify('+5 day')->format('Y-m-d')."%'"
						." GROUP BY p.FK_ID_Stock"
					.") w3 ON k.id = w3.FK_ID_Stock"

					." LEFT JOIN ("
						." SELECT p.FK_ID_Stock, SUM(p.Plan_Qty_OK) planQty"
						." FROM plan p"
							." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
							." INNER JOIN job j ON k.FK_ID_Job = j.id"
						." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
							." AND p.Date_Stamp BETWEEN '"
							.$oStartDate->modify('+2 day')->format('Y-m-d')."%' AND '"
							.$oStartDate->modify('+5 day')->format('Y-m-d')."%'"
						." GROUP BY p.FK_ID_Stock"
					.") w4 ON k.id = w4.FK_ID_Stock"

					." LEFT JOIN ("
						." SELECT p.FK_ID_Stock, SUM(p.Plan_Qty_OK) planQty"
						." FROM plan p"
							." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
							." INNER JOIN job j ON k.FK_ID_Job = j.id"
						." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
							." AND p.Date_Stamp BETWEEN '"
							.$oStartDate->modify('+2 day')->format('Y-m-d')."%' AND '"
							.$oStartDate->modify('+5 day')->format('Y-m-d')."%'"
						." GROUP BY p.FK_ID_Stock"
					.") w5 ON k.id = w5.FK_ID_Stock"

					." LEFT JOIN ("
						." SELECT p.FK_ID_Stock, SUM(p.Plan_Qty_OK) planQty"
						." FROM plan p"
							." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
							." INNER JOIN job j ON k.FK_ID_Job = j.id"
						." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
							." AND p.Date_Stamp BETWEEN '"
							.$oStartDate->modify('+2 day')->format('Y-m-d')."%' AND '"
							.$oStartDate->modify('+5 day')->format('Y-m-d')."%'"
						." GROUP BY p.FK_ID_Stock"
					.") w6 ON k.id = w6.FK_ID_Stock"

					." LEFT JOIN ("
						." SELECT p.FK_ID_Stock, SUM(p.Plan_Qty_OK) planQty"
						." FROM plan p"
							." INNER JOIN stock k ON p.FK_ID_Stock = k.id"
							." INNER JOIN job j ON k.FK_ID_Job = j.id"
						." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
							." AND p.Date_Stamp BETWEEN '"
							.$oStartDate->modify('+2 day')->format('Y-m-d')."%' AND '"
							.$oStartDate->modify('+5 day')->format('Y-m-d')."%'"
						." GROUP BY p.FK_ID_Stock"
					.") w7 ON k.id = w7.FK_ID_Stock"
				." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1" .$criteria
				." ORDER BY c.Name, j.Name, l.Name, sm.Name";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}





	// ****************************************************** Normal function *****************************************
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


	// -------------------------------------------------------- Manipulate --------------------------------------------
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

	function delete_row($id){
		$this->db->where($this->col_id, $id);
		$result = $this->db->delete($this->table_name);
		
		return $result;
	}



	
	
	// ********************************************************** Helper **********************************************
	// _________________________________________________________ DateTime _____________________________________________
	private function gen_date_by_diff($diffStartCurrentDate) {
		// Prepare Date Slot.
		$startDate = new DateTime();
		$startDate->modify($diffStartCurrentDate.' day');

		return $startDate;
	}
	// ________________________________________________________ Criteria ______________________________________________
	public function createCriteriaIN($columnName, $arrayDataIN, $criteria){
		$criteria = $criteria.' && '.$columnName.' IN (';
			
		for($i=0 ; $i < count($arrayDataIN) ; $i++) {
			$criteria = $criteria.$arrayDataIN[$i].',';
		}
		$criteria = substr($criteria, 0, strlen($criteria) - 1);
		$criteria = $criteria.')';
			
		return $criteria;
	}

	// ******************************************************* Generate Sql *******************************************
	// ________________________________________________________ Shift Date ____________________________________________
	private function genSqlDataToStoreShiftDaywithOffsetSun($stockID, $shiftDate) {
		$dataToStore = '';
	
		$sundayCounter = 0;
		$dsPlan = $this->get_future_plan($stockID);
		foreach($dsPlan as $row) {
			$oDateStamp = new DateTime($row[$this->col_date_stamp]);
			$oDateStamp->modify(($shiftDate + $sundayCounter).' day');

			if(date("w", strtotime($oDateStamp->format('Y-m-d'))) == 0) {
				$sundayCounter++;
				$oDateStamp->modify('+1 day');
			}
	
			$dataToStore = $dataToStore."(".$row[$this->col_id]
										.", '".$oDateStamp->format('Y-m-d')
										."'),";
		}

		$dataToStore = ((($dataToStore == '') || ($dataToStore == NULL)) ? '' : substr($dataToStore, 0, -1));
	
		return $dataToStore;
	}
	private function get_future_plan($stockID) {
		$sqlStr = "SELECT ".$this->col_id.','.$this->col_date_stamp
				." FROM ".$this->table_name
				." WHERE ".$this->col_stock_id."=".$stockID." && ".$this->col_date_stamp." > CURDATE()"
				." ORDER BY ".$this->col_date_stamp;

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
}
?>
