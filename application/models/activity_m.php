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
	
	/**
    * Responsable for auto load the database
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }

    
    
    // **************************************************** Join table function ***************************************
    /**
     * Get Full step include job & time operate by his is
     * @param int $jobID, $projectID
     * @return array
     */
    public function get_last_full_activity($rowNumber=10)
    {
    	$sqlStr = "SELECT a.id activityID, s.id stockID"
    				.", a.Datetime_Stamp, j.Name JobName, CONCAT(t.Number,' - ',t.`DESC`) 'StepNumber-Desc'"
					.", uw.Name WorkerName, a.Qty_OK, a.Qty_NG, d.Name DefectName , u.Name UserName"
				." FROM activity a"
					." INNER JOIN stock s ON a.FK_ID_Stock=s.id"
					." INNER JOIN job j ON s.FK_ID_Job=j.id"
					." INNER JOIN step t ON s.FK_ID_Step=t.id"
					." LEFT JOIN defect d ON a.FK_ID_Defect=d.id"
					." LEFT JOIN user uw ON a.FK_ID_Worker=uw.id"
					." LEFT JOIN user u ON a.FK_ID_User=u.id"
				." WHERE a.FK_ID_Activity_Source is NULL AND j.Delete_Flag=0"
				." ORDER BY a.id DESC LIMIT ".$rowNumber;
    	$query = $this->db->query($sqlStr);
    	$result = $query->result_array();
    	
    	return $result;
    }
    public function get_last_full_recovery_NG_activity($rowNumber=10)
    {
    	$sqlStr = "SELECT a.id activityID, s.id stockID"
    				.", a.Datetime_Stamp, j.Name JobName, CONCAT(t.Number,' - ',t.`DESC`) 'StepNumber-Desc'"
    				.", uw.Name WorkerName, a.Qty_OK, a.Qty_NG, d.Name DefectName , u.Name UserName"
				." FROM activity a"
    				." INNER JOIN stock s ON a.FK_ID_Stock=s.id"
    				." INNER JOIN job j ON s.FK_ID_Job=j.id"
    				." INNER JOIN step t ON s.FK_ID_Step=t.id"
    				." LEFT JOIN defect d ON a.FK_ID_Defect=d.id"
    				." LEFT JOIN user uw ON a.FK_ID_Worker=uw.id"
    				." LEFT JOIN user u ON a.FK_ID_User=u.id"
    			." WHERE (a.FK_ID_Activity_Source is NOT NULL) && (a.Qty_NG is NOT NULL) AND j.Delete_Flag=0"
    			." ORDER BY a.id DESC LIMIT ".$rowNumber;
		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }

    
    public function get_full_recovery_NG_destination($activitySourceID)
    {
    	$sqlStr = "SELECT s.id stockID, s.Qty_OK stockOK, a.Qty_OK activityOK"
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
    								, $arrayJobID=[], $arrayStepID=[])
    {
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
    public function get_ngPercentByStep($strDateStart, $strDateEnd, $arrayJobID=[], $arrayStepID=[])
    {
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
    public function get_topReject($strDateStart, $strDateEnd, $arrayJobID=[], $arrayStepID=[])
    {
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
    				.", SUM(a.Qty_NG) rejectQty"
				." FROM activity a"
					." INNER JOIN stock k ON a.FK_ID_Stock = k.id"
					." INNER JOIN job j ON k.FK_ID_Job = j.id"
					." LEFT JOIN defect d ON a.FK_ID_Defect = d.id"
				." WHERE a.FK_ID_Activity_Source IS NULL AND a.Qty_NG IS NOT NULL AND j.Delete_Flag=0"
					." AND a.Datetime_Stamp BETWEEN '".$strDateStart."%' AND '".$strDateEnd."%'" .$criteria
				." GROUP BY a.FK_ID_Defect"
				." ORDER BY rejectQty DESC, a.FK_ID_Defect"
				." LIMIT 5";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }

    
    
    
    // ****************************************************** Normal function *****************************************
    /**
    * Get product by his is
    * @param int $product_id 
    * @return array
    */
    public function get_row_by_id($id=0, $arrWhere=[])
    {
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
    public function get_row($search_string=null, $order=null, $order_type='Asc', $limit_start=null, $limit_end=null)
    {
	    
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
    function count_row($search_string=null, $order=null)
    {
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

    /**
    * Store the new item into the database
    * @param array $data - associative array with data to store
    * @return boolean 
    */
    function insert_row($data)
    {
		$insert = $this->db->insert($this->table_name, $data);
		return $this->db->insert_id();
	    //return $insert;
    }

    /**
    * Update project
    * @param array $data - associative array with data to store
    * @return boolean
    */
    function update_row($id, $data)
    {
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
	
	
    /**
    * Delete step
    * @param int $processID, $arrStepID - process id and array step id
    * @return boolean
    */
	function delete_not_in($processID, $arrStepID){
		$this->db->where($this->col_process_id, $processID);
		$this->db->where_not_in($this->col_id, $arrStepID);
		
		$result = $this->db->delete($this->table_name);
		
		return $result;
	}
}
?>	
