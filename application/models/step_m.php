<?php
class Step_m extends CI_Model {
	// Private Property
	var $table_name = "step";
	var $col_id = "id";
	var $col_number = "Number";
	var $col_desc = "DESC";
	var $col_process_id = "FK_ID_Process";
	var $col_line_id = "FK_ID_Line";
	var $col_machine_id = "FK_ID_Machine";
	var $col_sub_assembly_id = "FK_ID_Sub_Assembly";
	var $col_nb_sub = "NB_Sub";
	var $col_next_step_number = "Next_Step_Number";
	var $col_first_step_flag = "First_Step_Flag";
	
	/**
    * Responsable for auto load the database
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }

    
    
    // **************************************************** Join table function ***************************************
    // ------------------------------------------------------ Filter combobox -----------------------------------------
    public function getStep_Job_Open()
    {
    	$sqlStr = "SELECT DISTINCT(s.id), s.Number, s.DESC"
    				." FROM step s"
					." INNER JOIN job j ON (s.FK_ID_Process = j.FK_ID_Process)"
					." WHERE j.FK_ID_Job_Status = 1 AND j.Delete_Flag=0"
					." ORDER BY s.FK_ID_Process, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }
    public function getStep_Job_Open_Job_ID($arrayJobID=[])
    {
    	// Prepare Criteria.
    	$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.id', $arrayJobID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
			 
			$criteria = ' AND '.$criteria;
		}

		$sqlStr = "SELECT DISTINCT(s.id), s.Number, s.DESC"
    			." FROM step s"
    			." INNER JOIN job j ON (s.FK_ID_Process = j.FK_ID_Process)"
    			." WHERE j.FK_ID_Job_Status = 1 AND j.Delete_Flag=0".$criteria
    			." ORDER BY s.FK_ID_Process, s.Number";

    	$query = $this->db->query($sqlStr);
    	$result = $query->result_array();

		return $result;
    }
    public function getStep_Job_ID($arrayJobID=[])
    {
    	// Prepare Criteria.
    	$this->load->model('plan_m');
    	$criteria ='';
    	if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.id', $arrayJobID, $criteria); }
    	if(strlen($criteria) > 4) {
    		$criteria = substr($criteria, 4, strlen($criteria) - 4);
    
    		$criteria = ' AND '.$criteria;
    	}
    
    	$sqlStr = "SELECT DISTINCT(s.id), s.Number, s.DESC"
    			." FROM step s"
					." INNER JOIN job j ON (s.FK_ID_Process = j.FK_ID_Process)"
				." WHERE j.Delete_Flag=0" .$criteria
				." ORDER BY s.FK_ID_Process, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }
    public function getLine_Step_ID($arrayStepID=[])
    {
    	// Prepare Criteria.
    	$this->load->model('plan_m');
    	$criteria ='';
    	if(count($arrayStepID) > 0) { $criteria = $this->plan_m->createCriteriaIN('s.id', $arrayStepID, $criteria); }
    	if(strlen($criteria) > 4) {
    		$criteria = substr($criteria, 4, strlen($criteria) - 4);
    
    		$criteria = ' AND '.$criteria;
    	}
    
    	$sqlStr = "SELECT DISTINCT(l.id), l.Name"
    			." FROM step s"
    				." INNER JOIN job j ON (s.FK_ID_Process = j.FK_ID_Process)"
    				." INNER JOIN line l ON (s.FK_ID_Line = l.id)"
    			." WHERE j.Delete_Flag=0" .$criteria
				." ORDER BY l.Name";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }
    
    
    
    public function getLine_Job_ID($arrayJobID=[])
    {
    	// Prepare Criteria.
    	$this->load->model('plan_m');
    	$criteria ='';
    	if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.id', $arrayJobID, $criteria); }
    	if(strlen($criteria) > 4) {
    		$criteria = substr($criteria, 4, strlen($criteria) - 4);
    
    		$criteria = ' AND '.$criteria;
    	}
    
    	$sqlStr = "SELECT DISTINCT(l.id), l.Name"
    			." FROM step s"
    				." INNER JOIN job j ON (s.FK_ID_Process = j.FK_ID_Process)"
    				." INNER JOIN line l ON (s.FK_ID_Line = l.id)"
    			." WHERE j.Delete_Flag=0" .$criteria
				." ORDER BY l.Name";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }
    public function getSubAssembly_Job_ID($arrayJobID=[])
    {
    	// Prepare Criteria.
    	$this->load->model('plan_m');
    	$criteria ='';
    	if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.id', $arrayJobID, $criteria); }
    	if(strlen($criteria) > 4) {
    		$criteria = substr($criteria, 4, strlen($criteria) - 4);
    
    		$criteria = ' AND '.$criteria;
    	}
    
    	$sqlStr = "SELECT DISTINCT(a.id), a.Name"
    			." FROM step s"
	    			." INNER JOIN job j ON (s.FK_ID_Process = j.FK_ID_Process)"
					." INNER JOIN sub_assembly a ON (s.FK_ID_Sub_Assembly = a.id)"
				." WHERE j.Delete_Flag=0" .$criteria
				." ORDER BY a.Name";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }
    
    

    
    
    
    
    
    
    
    /**
     * Get Full step include job & time operate by his is
     * @param int $jobID, $projectID
     * @return array
     */
    public function getFullStep_have_stock($jobID=0, $processID=0)
    {
    	$sqlStr = "SELECT s.id, s.First_Step_Flag, s.Next_Step_Number, s.Number, s.DESC" 
					.", s.FK_ID_Line, s.FK_ID_Machine, s.FK_ID_Sub_Assembly, s.NB_Sub, k.Operation_Time"
					." FROM job as j"
						." INNER JOIN step as s ON (j.FK_ID_Process = s.FK_ID_Process)" 
						." INNER JOIN stock as k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
					." WHERE j.Delete_Flag=0 AND ((j.id = ".$jobID.") && (s.FK_ID_Process = ".$processID."))"
					." ORDER BY j.id, s.Number";
    	$query = $this->db->query($sqlStr);
    	$result = $query->result_array();

    	return $result;
    }
    public function getFullStep_have_not_stock($jobID=0, $processID=0)
    {
    	$sqlStr = "SELECT DISTINCT(s.id), s.First_Step_Flag, s.Next_Step_Number, s.Number, s.DESC"
    				.", s.FK_ID_Line, s.FK_ID_Machine, s.FK_ID_Sub_Assembly, s.NB_Sub, NULL Operation_Time"
	    			." FROM step as s"
	    				." INNER JOIN job j ON (s.FK_ID_Process = j.FK_ID_Process)"
    				." WHERE j.Delete_Flag=0 AND s.FK_ID_Process = ".$processID
    				." ORDER BY s.Number";
    	$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }
    public function getFullStep_have_not_stock_r1($processID=0)
    {
    	$sqlStr = "SELECT DISTINCT(s.id), s.First_Step_Flag, s.Next_Step_Number, s.Number, s.DESC"
    				.", s.FK_ID_Line, s.FK_ID_Machine, s.FK_ID_Sub_Assembly, s.NB_Sub, NULL Operation_Time"
	    			." FROM step as s"
    				." WHERE s.FK_ID_Process = ".$processID
    				." ORDER BY s.Number";
    	$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
    }


    
    public function get_all()
    {
    	$this->db->select('*');
    	$this->db->from($this->table_name);
    	$this->db->order_by($this->col_process_id, 'Asc');
    	$this->db->order_by($this->col_number, 'Asc');
		$query = $this->db->get();
		
		return $query->result_array(); 	
    }
    
    public function get_by_where_array($arrWhere = [])
    {
    	$this->db->select('*');
    	$this->db->from($this->table_name);
		$this->db->where($arrWhere);
		$this->db->order_by($this->col_process_id, 'Asc');
		$this->db->order_by($this->col_number, 'Asc');
		$query = $this->db->get();
    
    	return $query->result_array();
    }
    public function get_full_stock($jobID=0, $stepID=0)
    {
    	$sqlStr = "SELECT k.id, s.Number, s.Next_Step_Number, s.First_Step_Flag, s.NB_Sub"
    				.", k.Qty_OK_First_Step, k.Qty_OK, k.Qty_NG"
    				." FROM job as j"
    					." INNER JOIN step as s ON (j.FK_ID_Process = s.FK_ID_Process)"
    					." INNER JOIN stock as k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
    				." WHERE j.Delete_Flag=0 AND ((j.id = ".$jobID.") && (s.id = ".$stepID."))"
					." ORDER BY j.id, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();
    															 
		return $result;
	}
	public function get_previous_full_stock($jobID=0, $stepNumber=0)
	{
		$sqlStr = "SELECT k.id, s.Number, s.Next_Step_Number, s.First_Step_Flag, s.NB_Sub"
					.", k.Qty_OK_First_Step, k.Qty_OK, k.Qty_NG"
					." FROM job as j"
						." INNER JOIN step as s ON (j.FK_ID_Process = s.FK_ID_Process)"
						." INNER JOIN stock as k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
					." WHERE j.Delete_Flag=0 AND ((j.id = ".$jobID.") && (s.Next_Step_Number = ".$stepNumber."))"
					." ORDER BY j.id, s.Number";

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
		$this->db->order_by($this->col_process_id, 'Asc');
		$this->db->order_by($this->col_number, 'Asc');
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
