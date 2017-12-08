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
	public function get_full_plan_row($diffStartCurrentDate=0, $arrayJobID=[], $arrayJobTypeID=[]
		, $arrayStepID=[], $totalSlotDate=20, $arrayJobStatusID=[1]) {
		// Prepare Date Slot.
		$startDate = $this->gen_date_by_diff($diffStartCurrentDate);
		
		// Prepare Criteria.
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->createCriteriaIN('j.id', $arrayJobID, $criteria); }
		if(count($arrayJobTypeID) > 0) { $criteria = $this->createCriteriaIN('j.FK_ID_Job_Type', $arrayJobTypeID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->createCriteriaIN('t.id', $arrayStepID, $criteria); }
		if(count($arrayJobStatusID) > 0) { $criteria = $this->createCriteriaIN('j.FK_ID_Job_Status', $arrayJobStatusID, $criteria); }

		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			
			$criteria = ' HAVING '.$criteria;
		}

		$sqlStr = "SELECT j.id JobID, t.id StepID, s.id StockID, t.FK_ID_Line, t.FK_ID_Machine"
			.", j.FK_ID_Job_Type, j.FK_ID_Job_Status"
			.", j.Name JobName"
			.", IF((ISNULL(t.Next_Step_Number)) || (t.Next_Step_Number=0),'-',t.Next_Step_Number) Next_Step_Number"
			.", CONCAT(t.Number, ' - ', t.`DESC`) NumberAndDESC"
			.", l.Name LineName, m.Name MachineName, s.Operation_Time Operation_Time"
			.", IF(t.First_Step_Flag=0,z.Name,b.Name) SubAssemblyName"
			.", IF(t.First_Step_Flag=0,y.Qty_OK,s.Qty_OK_First_Step) stock"
			.", IF(ISNULL(SUM(a.Qty_OK)),0,SUM(a.Qty_OK)) activity_Qty_OK, s.Qty_NG"
			.", (SELECT COUNT(subS.id)"
				." FROM job subJ"
					." INNER JOIN step subT"
						." ON subJ.FK_ID_Process = subT.FK_ID_Process" 
					." LEFT JOIN step subX"
						." ON ((subT.FK_ID_Process = subX.FK_ID_Process) && (subT.Number = subX.Next_Step_Number))" 
			
					." INNER JOIN stock subS"
						." ON ((subJ.id = subS.FK_ID_Job) && (subT.id = subS.FK_ID_Step))" 
				." WHERE subJ.Delete_Flag=0"
				." GROUP BY subS.id"
				." HAVING subS.id = s.id"
				." ORDER BY subJ.id, subT.id"
			.") duplicatePStock"
			.$this->genSqlSlotDate($diffStartCurrentDate, $totalSlotDate)
			
			." FROM job j"
			." INNER JOIN step t" 
			." ON j.FK_ID_Process = t.FK_ID_Process" 
			." INNER JOIN stock s"
			." ON ((j.id = s.FK_ID_Job) && (t.id = s.FK_ID_Step))" 

			." LEFT JOIN step x"
			." ON ((t.FK_ID_Process = x.FK_ID_Process) && (t.Number = x.Next_Step_Number))" 
			
			." LEFT JOIN line l"
			." ON t.FK_ID_Line = l.id" 
			." LEFT JOIN machine m"
			." ON t.FK_ID_Machine = m.id"
			." LEFT JOIN sub_assembly b"
			." ON t.FK_ID_Sub_Assembly = b.id"
			." LEFT JOIN activity a"
			." ON s.id = a.FK_ID_Stock"
			
			." LEFT JOIN stock y"
			." ON ((j.id = y.FK_ID_Job) && (x.id = y.FK_ID_Step))"
			." LEFT JOIN sub_assembly z"
			." ON x.FK_ID_Sub_Assembly = z.id"

			.$this->genSqlSourceSlotDate($startDate, $totalSlotDate)
			
			." WHERE j.Delete_Flag=0"
			." GROUP BY j.id, t.id, s.id, y.id"
			.$criteria
			." ORDER BY JobName, t.Number";

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
					." WHERE j.Delete_Flag=0 AND p.Plan_Qty_OK > 0" .$criteria
					." AND Date_Stamp BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'"
					." ORDER BY s.FK_ID_Line, j.Name, s.Number, p.Date_Stamp";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();
		
		return $result;
	}

	// -------------------------------------------------------- Achievement -------------------------------------------
	public function get_achievement($strDateStart, $strDateEnd, $arrayLineID=[]
		, $arrayJobID=[], $arrayStepID=[]) {
		// Create criteria query.
		$criteria ='';
		if(count($arrayLineID) > 0) { $criteria = $this->createCriteriaIN('sl.FK_ID_Line', $arrayLineID, $criteria); }
		if(count($arrayJobID) > 0) { $criteria = $this->createCriteriaIN('kl.FK_ID_Job', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->createCriteriaIN('kl.FK_ID_Step', $arrayStepID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
		
			$criteria = ' AND '.$criteria;
		}

		$sqlStr = "SELECT ac.DateStamp dateStamp, ac.LineID, l.Name lineName"
					.", IF(ISNULL(ac.ActualOKQty), 0, ac.ActualOKQty) actualQtyOK"
					.", IF(ISNULL(ac.PlanOKQty), 0, ac.PlanOKQty) planQtyOK"
					.", (IF(ISNULL(ac.ActualOKQty), 0, ac.ActualOKQty)"
						." / IF( (ISNULL(ac.PlanOKQty) || (ac.PlanOKQty=0)) && (ac.ActualOKQty > 0)"
							.", 100"
							.", ac.PlanOKQty )"
						." * 100) achievementQtyOK"
				." FROM job j"
					." INNER JOIN step s ON j.FK_ID_Process = s.FK_ID_Process"
					." INNER JOIN stock k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
					." LEFT JOIN line l ON s.FK_ID_Line = l.id"
					." LEFT JOIN ("
						." SELECT sl.FK_ID_Line lineID, DATE(al.Datetime_Stamp) dateStamp"
							.", SUM(al.Qty_OK) actualOKQty"
							.", 0 planOKQty"
						." FROM stock kl"
							." INNER JOIN job jl ON kl.FK_ID_Job = jl.id"
							." INNER JOIN step sl ON kl.FK_ID_Step = sl.id"
							." LEFT JOIN activity al ON (kl.id = al.FK_ID_Stock)"
						." WHERE DATE(al.Datetime_Stamp) IS NOT NULL"
							." AND jl.Delete_Flag=0" .$criteria
						." GROUP BY sl.FK_ID_Line"
						." UNION"
						." SELECT sl.FK_ID_Line lineID, pl.Date_Stamp dateStamp"
							.", 0 actualOKQty"
							.", SUM(pl.Plan_Qty_OK) planOKQty"
						." FROM stock kl"
							." INNER JOIN job jl ON kl.FK_ID_Job = jl.id"
							." INNER JOIN step sl ON kl.FK_ID_Step = sl.id"
							." LEFT JOIN plan pl ON (kl.id = pl.FK_ID_Stock)"
						." WHERE pl.Date_Stamp IS NOT NULL"
							." AND jl.Delete_Flag=0" .$criteria
						." GROUP BY sl.FK_ID_Line"
					.") ac ON (s.FK_ID_Line = ac.lineID)"
				." WHERE ac.dateStamp IS NOT NULL AND j.Delete_Flag=0"
				." GROUP BY ac.lineID, ac.dateStamp"
				." HAVING ac.dateStamp BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'"
				." ORDER BY ac.lineID, ac.dateStamp";

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
						." WHERE j.Delete_Flag=0"
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
						." WHERE j.Delete_Flag=0"
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
						." WHERE j.Delete_Flag=0"
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
						." WHERE j.Delete_Flag=0"
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
						." WHERE j.Delete_Flag=0"
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
						." WHERE j.Delete_Flag=0"
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
						." WHERE j.Delete_Flag=0"
							." AND p.Date_Stamp BETWEEN '"
							.$oStartDate->modify('+2 day')->format('Y-m-d')."%' AND '"
							.$oStartDate->modify('+5 day')->format('Y-m-d')."%'"
						." GROUP BY p.FK_ID_Stock"
					.") w7 ON k.id = w7.FK_ID_Stock"
				." WHERE j.Delete_Flag=0" .$criteria
				." ORDER BY c.Name, j.Name, l.Name, sm.Name";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}





	// ****************************************************** Normal function *****************************************
	/**
		* Get project by his is
		* @param int $project_id 
		* @return array
	*/
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

	/**
		* Fetch project data from the database
		* possibility to mix search, filter and order
		* @param string $search_string 
		* @param strong $order
		* @param string $order_type 
		* @param int $limit_start
		* @param int $limit_end
		* @return array
	*/
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

	/**
		* Count the number of rows
		* @param int $search_string
		* @param int $order
		* @return int
	*/
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
	/**
		* Store the new item into the database
		* @param array $data - associative array with data to store
		* @return boolean 
	*/
	function insert_row($data) {
		$insert = $this->db->insert($this->table_name, $data);
		return $insert;
	}

	/**
    * Update project
    * @param array $data - associative array with data to store
    * @return boolean
	*/
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

	/**
    * Delete project
    * @param int $id - project id
    * @return boolean
	*/
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
	// ________________________________________________________ Slot Date _____________________________________________
	private function genSqlSlotDate($diffStartCurrentDate=0, $totalSlotDate=20) {
		$sqlStr = "";
		for($i = 1 ; $i < ($totalSlotDate+1) ; $i++) {
			$sqlStr .= $this->genSqlOneSlotDate($diffStartCurrentDate, $i);
		}

		return $sqlStr;
	}
	private function genSqlOneSlotDate($diffStartCurrentDate=0, $d=1) {
		$sqlStr = "";
		$diffStartCurrentDate += ($d - 1);
		
		if($diffStartCurrentDate > 0) {
			$sqlStr =
				", IF(ISNULL(DateSlot".$d.".PlanOKQty),'',DateSlot".$d.".PlanOKQty) OKQtySlot".$d
				.", '-' NGQtySlot".$d
				.", IF(ISNULL(DateSlot".$d.".PlanWorkerQty),'',DateSlot".$d.".PlanWorkerQty) WorkerQtySlot".$d
				.", IF(ISNULL(DateSlot".$d.".PlanWorkerQty),'-',(DateSlot".$d.".PlanWorkerQty*s.Operation_Time)) TotalTimeSlot".$d;
		}
		else {
			$sqlStr =
				", CONCAT(IF(ISNULL(DateSlot".$d.".ActualOKQty),'-',DateSlot".$d.".ActualOKQty),'/'"
					.",IF(ISNULL(DateSlot".$d.".PlanOKQty),'-',DateSlot".$d.".PlanOKQty)) OKQtySlot".$d
				.", IF(ISNULL(DateSlot".$d.".ActualNGQty),'-',DateSlot".$d.".ActualNGQty) NGQtySlot".$d
				.", CONCAT(IF(ISNULL(DateSlot".$d.".ActualWorkerQty),'-',DateSlot".$d.".ActualWorkerQty),'/'"
					.",IF(ISNULL(DateSlot".$d.".PlanWorkerQty),'-',DateSlot".$d.".PlanWorkerQty)) WorkerQtySlot".$d
				.", IF((ISNULL(DateSlot".$d.".ActualWorkerQty))"
					.",(IF(ISNULL(DateSlot".$d.".PlanWorkerQty),'-',(DateSlot".$d.".PlanWorkerQty*s.Operation_Time)))"
				.",(DateSlot".$d.".ActualWorkerQty*s.Operation_Time)) TotalTimeSlot".$d;
		}

		return $sqlStr;
	}
	
	// ______________________________________________________ Source Slot Date ________________________________________
	private function genSqlSourceSlotDate($startDate, $totalSlotDate=20) {
		$sqlStr = "";
		for($i = 1 ; $i < ($totalSlotDate+1) ; $i++) {
			$sqlStr .= ($this->genSqlSourceOneSlotDate($startDate, $i));
			$startDate->modify('+1 day');
		}
		$startDate->modify('-1 day');

		return $sqlStr;
	}
	private function genSqlSourceOneSlotDate($startDate, $d=1) {
		$strDateSlot = $startDate->format('Y-m-d');

		$sqlStr = " LEFT JOIN ("
					." SELECT sl.id StockID , DATE(al.Datetime_Stamp) DateStamp"
						.", SUM(al.Qty_OK) ActualOKQty"
						.", SUM(al.Qty_NG) ActualNGQty"
						.", COUNT(DISTINCT al.FK_ID_Worker) ActualWorkerQty"
						.", pl.Plan_Qty_OK PlanOKQty"
						.", pl.Plan_Qty_Worker PlanWorkerQty"
					." FROM stock sl"
						." INNER JOIN job jl ON sl.FK_ID_Job = jl.id"
						." LEFT JOIN activity al ON (sl.id = al.FK_ID_Stock)"
						." LEFT JOIN plan pl ON (sl.id = pl.FK_ID_Stock) && (pl.Date_Stamp = DATE(al.Datetime_Stamp))"
					." WHERE jl.Delete_Flag=0"
					." GROUP BY StockID, DateStamp"
					." HAVING DateStamp LIKE '".$strDateSlot."%'"
					." UNION"
					." SELECT sl.id StockID, pl.Date_Stamp DateStamp"
						.", SUM(al.Qty_OK) ActualOKQty"
						.", SUM(al.Qty_NG) ActualNGQty"
						.", COUNT(DISTINCT al.FK_ID_Worker) ActualWorkerQty"
						.", pl.Plan_Qty_OK PlanOKQty"
						.", pl.Plan_Qty_Worker PlanWorkerQty"
					." FROM stock sl"
						." INNER JOIN job jl ON sl.FK_ID_Job = jl.id"
						." LEFT JOIN plan pl ON (sl.id = pl.FK_ID_Stock) "
						." LEFT JOIN activity al ON (sl.id = al.FK_ID_Stock) && (pl.Date_Stamp = DATE(al.Datetime_Stamp))"
					." WHERE jl.Delete_Flag=0"
					." GROUP BY StockID,DateStamp"
					." HAVING DateStamp LIKE '".$strDateSlot."%'"
				.") DateSlot".$d." ON (s.id = DateSlot".$d.".StockID)";

		return $sqlStr;
	}


	
	
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
