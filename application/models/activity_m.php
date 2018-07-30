<?php
class Activity_m extends CI_Model {
// Private Property
	var $table_name = "activity";
	var $col_id = "id";
	var $col_datetime_stamp = "Datetime_Stamp";
	var $col_stock_id = "FK_ID_Stock";
	var $col_worker_id = "FK_ID_Worker";
	var $col_qty_ok = "Qty_OK";
	var $col_qty_ng = "Qty_NG";
	var $col_defect_id = "FK_ID_Defect";
	var $col_user_id = "FK_ID_User";
	var $col_activity_source = "FK_ID_Activity_Source";
	var $col_qty_revoke_ng = "Qty_Revoke_NG";
// End Private Property	

	public function __construct() {
		parent::__construct();
	}



	// **************************************************** Join table function ***************************************
	public function getDsFullActivity($limit=null, $offset=null,
	$strDateStart, $strDateEnd, $arrayJobID=[], $arrayStepID=[], $arrayLineID=[]) {
		// Create criteria query.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.id', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->plan_m->createCriteriaIN('t.id', $arrayStepID, $criteria); }
		if(count($arrayLineID) > 0) { $criteria = $this->plan_m->createCriteriaIN('l.id', $arrayLineID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' AND '.$criteria;
		}


		$sqlStr = "SELECT a.id activityID, s.id stockID"
				.", a.Datetime_Stamp, j.Name JobName, CONCAT(t.Number,' - ',t.`DESC`) 'StepNumber-Desc'"
				.", l.Name LineName, uw.Name WorkerName, a.Qty_OK, a.Qty_NG, d.Name DefectName , u.Name UserName"
			." FROM activity a"
				." INNER JOIN stock s ON a.FK_ID_Stock=s.id"
				." INNER JOIN job j ON s.FK_ID_Job=j.id"
				." INNER JOIN step t ON s.FK_ID_Step=t.id"
				." LEFT JOIN line l ON t.FK_ID_Line=l.id"
				." LEFT JOIN defect d ON a.FK_ID_Defect=d.id"
				." LEFT JOIN user uw ON a.FK_ID_Worker=uw.id"
				." LEFT JOIN user u ON a.FK_ID_User=u.id"
			." WHERE a.FK_ID_Activity_Source is NULL AND j.Delete_Flag=0"
			." AND a." . $this->col_datetime_stamp
			." BETWEEN '" . $strDateStart . "%' AND '" . $strDateEnd . "%'"
			.$criteria
			." ORDER BY a.id DESC"
			. createSqlLimitOffset($limit, $offset);
		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	public function getDsFullRecoveryNgActivity($limit=null, $offset=null,
	$strDateStart, $strDateEnd, $arrayJobID=[], $arrayStepID=[], $arrayLineID=[]) {
		// Create criteria query.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.id', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->plan_m->createCriteriaIN('t.id', $arrayStepID, $criteria); }
		if(count($arrayLineID) > 0) { $criteria = $this->plan_m->createCriteriaIN('l.id', $arrayLineID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' AND '.$criteria;
		}


		$sqlStr = "SELECT a.id activityID, s.id stockID"
				.", a.Datetime_Stamp, j.Name JobName, CONCAT(t.Number,' - ',t.`DESC`) 'StepNumber-Desc'"
				.", l.Name LineName, uw.Name WorkerName, a.Qty_OK, a.Qty_NG, d.Name DefectName , u.Name UserName"
			." FROM activity a"
				." INNER JOIN stock s ON a.FK_ID_Stock=s.id"
				." INNER JOIN job j ON s.FK_ID_Job=j.id"
				." INNER JOIN step t ON s.FK_ID_Step=t.id"
				." LEFT JOIN line l ON t.FK_ID_Line=l.id"
				." LEFT JOIN defect d ON a.FK_ID_Defect=d.id"
				." LEFT JOIN user uw ON a.FK_ID_Worker=uw.id"
				." LEFT JOIN user u ON a.FK_ID_User=u.id"
			." WHERE (a.FK_ID_Activity_Source is NOT NULL) && (a.Qty_NG is NOT NULL) AND j.Delete_Flag=0"
			.$criteria
			." ORDER BY a.id DESC"
			. createSqlLimitOffset($limit, $offset);

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}


	public function get_full_recovery_NG_destination($activitySourceID) {
		$sqlStr = "SELECT s.id stockID, s.Qty_OK stockOK, a.Qty_OK activityOK, a.Qty_Revoke_NG revokeQty"
				.", s.Qty_OK_First_Step firstStepStock, s.FK_ID_Job jobID, s.FK_ID_Step stepID"
			." FROM activity a"
				." INNER JOIN stock s ON a.FK_ID_Stock=s.id"
			." WHERE (a.Qty_NG is NULL) && (a.FK_ID_Activity_Source = ".$activitySourceID.")"
			." ORDER BY a.id DESC";
		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}




	// ****************************************************** Report function *****************************************
	// ------------------------------------------------------- Percent of NG ------------------------------------------
	public function get_ngPercent($strDateStart, $strDateEnd, $arrayLineID=[]
	, $arrayJobID=[], $arrayStepID=[]) {
		// Create criteria query.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayLineID) > 0) { $criteria = $this->plan_m->createCriteriaIN('s.FK_ID_Line', $arrayLineID, $criteria); }
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('k.FK_ID_Job', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->plan_m->createCriteriaIN('k.FK_ID_Step', $arrayStepID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' AND '.$criteria;
		}

		$sqlStr = "SELECT q.dateStamp, q.jobName, q.Number, q.`DESC`, l.Name lineName"
				.", IF(ISNULL(q.sumQtyOK), 0, q.sumQtyOK) qtyOK"
				.", IF(ISNULL(q.sumQtyNG), 0, q.sumQtyNG) qtyNG"
				.", (IF(ISNULL(q.sumQtyNG), 0, q.sumQtyNG)"
					." / ("
						."IF(((IF(ISNULL(q.sumQtyOK) , 0, q.sumQtyOK)) + (IF(ISNULL(q.sumQtyNG), 0, q.sumQtyNG))) = 0, 1"
						.",(IF(ISNULL(q.sumQtyOK) , 0, q.sumQtyOK)) + (IF(ISNULL(q.sumQtyNG), 0, q.sumQtyNG)))"
					.")"
					." * 100) ngPercent"
			." FROM "
				."(SELECT DATE(a.Datetime_Stamp) dateStamp, j.Name jobName, s.Number, s.`DESC`, s.FK_ID_Line lineID"
					.", SUM(a.Qty_OK) sumQtyOK, SUM(a.Qty_NG) sumQtyNG"
				." FROM activity a"
					." INNER JOIN stock k ON a.FK_ID_Stock = k.id"
					." INNER JOIN job j ON k.FK_ID_Job = j.id"
					." INNER JOIN step s ON k.FK_ID_Step = s.id"
				." WHERE a.FK_ID_Activity_Source IS NULL AND j.Delete_Flag=0"
					." AND a.Datetime_Stamp BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'" .$criteria
				." GROUP BY dateStamp"
				.") q"
				." LEFT JOIN line l ON q.lineID = l.id"
			." ORDER BY". ((count($arrayLineID) > 0) ? " l.id," : "")." dateStamp";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}


	// -------------------------------------------------------- Top Reject --------------------------------------------
	public function get_ngPercentByStep($strDateStart, $strDateEnd, $arrayJobID=[], $arrayStepID=[]) {
		// Create criteria query.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('k.FK_ID_Job', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->plan_m->createCriteriaIN('k.FK_ID_Step', $arrayStepID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' AND '.$criteria;
		}

		$sqlStr = "SELECT q.Number, q.`DESC`"
				.", IF(ISNULL(q.sumQtyOK), 0, q.sumQtyOK) qtyOK"
				.", IF(ISNULL(q.sumQtyNG), 0, q.sumQtyNG) qtyNG"
				.", (IF(ISNULL(q.sumQtyNG), 0, q.sumQtyNG)"
					." / ("
						."IF(((IF(ISNULL(q.sumQtyOK) , 0, q.sumQtyOK)) + (IF(ISNULL(q.sumQtyNG), 0, q.sumQtyNG))) = 0, 1"
						.",(IF(ISNULL(q.sumQtyOK) , 0, q.sumQtyOK)) + (IF(ISNULL(q.sumQtyNG), 0, q.sumQtyNG)))"
					.")"
					." * 100) ngPercent"
			." FROM"
				."(SELECT s.Number, s.`DESC`, SUM(a.Qty_OK) sumQtyOK, SUM(a.Qty_NG) sumQtyNG"
				." FROM activity a"
					." INNER JOIN stock k ON a.FK_ID_Stock = k.id"
					." INNER JOIN job j ON k.FK_ID_Job = j.id"
					." INNER JOIN step s ON k.FK_ID_Step = s.id"
				." WHERE a.FK_ID_Activity_Source IS NULL AND j.Delete_Flag=0"
					." AND a.Datetime_Stamp BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'" .$criteria
				." GROUP BY s.Number, s.`DESC`"
				.") q"
			." ORDER BY q.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	public function get_topReject($strDateStart, $strDateEnd, $arrayJobID=[], $arrayStepID=[]) {
		// Create criteria query.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('k.FK_ID_Job', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->plan_m->createCriteriaIN('k.FK_ID_Step', $arrayStepID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			$criteria = ' AND '.$criteria;
		}

		$sqlStr = "SELECT a.FK_ID_Defect"
				.", IF(!ISNULL(a.FK_ID_Defect) && ISNULL(d.Name), '', d.Name) defectName"
				.", IF(!ISNULL(s.FK_ID_Sub_Assembly) && ISNULL(b.Name), '', b.Name) subAssemblyName"
				.", SUM(a.Qty_NG) rejectQty"
			." FROM activity a"
				." INNER JOIN stock k ON a.FK_ID_Stock = k.id"
				." INNER JOIN job j ON k.FK_ID_Job = j.id"
				." INNER JOIN step s ON k.FK_ID_Step = s.id"
				." LEFT JOIN defect d ON a.FK_ID_Defect = d.id"
				." LEFT JOIN sub_assembly b ON s.FK_ID_Sub_Assembly = b.id"
			." WHERE a.FK_ID_Activity_Source IS NULL AND a.Qty_NG IS NOT NULL AND j.Delete_Flag=0"
				." AND a.Datetime_Stamp BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'" .$criteria
			." GROUP BY a.FK_ID_Defect, s.FK_ID_Sub_Assembly"
			." ORDER BY rejectQty DESC, a.FK_ID_Defect"
			." LIMIT 5";

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

	function insert_row($data) {
		$insert = $this->db->insert($this->table_name, $data);
		return $this->db->insert_id();
		//return $insert;
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


	function delete_not_in($processID, $arrStepID){
		$this->db->where($this->col_process_id, $processID);
		$this->db->where_not_in($this->col_id, $arrStepID);

		$result = $this->db->delete($this->table_name);

		return $result;
	}
}
?>	
