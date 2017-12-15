<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_m extends CI_Model {
	// Private Property
	var $table_name = "job";
	var $col_id = "id";
	var $col_name = "Name";
	var $col_datetime_create = "Datetime_Create";
	var $col_project_id = "FK_ID_Project";
	var $col_process_id = "FK_ID_Process";
	var $col_bom_id = "FK_ID_BOM";
	var $col_qty_order = "Qty_Order";
	var $col_qty_plan_product = "Qty_Plan_Product";
	var $col_job_type_id = "FK_ID_Job_Type";
	var $col_job_status_id = "FK_ID_Job_Status";
	var $col_delete_flag = "Delete_Flag";
	
	/**
    * Responsable for auto load the database
    * @return void
	*/
	public function __construct() {
		parent::__construct();
	}


    
    
	// ******************************************* Report function *************************************
	// --------------------------------------------- Daily Target --------------------------------------
	public function get_print_process_barcode($lineID, $arrayJobID=[], $arrayStepID=[]) {
		$criteria ='';
		// Create job criteria.
    	$this->load->model('plan_m');
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.id', $arrayJobID, $criteria); }
		if(count($arrayStepID) > 0) { $criteria = $this->createCriteriaIN('s.id', $arrayStepID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
		
			$criteria = ' AND '.$criteria;
		}
		// Create line criteria.
		if($lineID > 0) {
			$criteria = $criteria.' AND s.FK_ID_Line='.$lineID;
		}
		
		$sqlStr = "SELECT s.FK_ID_Line, l.Name lineCurrent"	//, p.Date_Stamp"
					.", j.Name `Job Number`, s.Number, s.`DESC`, '-' AS Plan_Qty_OK"
					.", s.Next_Step_Number nextStepNumber, nl.Name lineNext, CONCAT(j.id,'-',s.id) jsBarcode"
					." FROM stock k"
					." INNER JOIN job j ON k.FK_ID_Job = j.id"
					." INNER JOIN step s ON k.FK_ID_Step = s.id"
					." LEFT JOIN line l ON s.FK_ID_Line = l.id"
					." LEFT JOIN step ns ON s.Next_Step_Number = ns.Number AND j.FK_ID_Process = ns.FK_ID_Process"
					." LEFT JOIN line nl ON ns.FK_ID_Line = nl.id"
					." WHERE j.Delete_Flag=0" .$criteria
					." ORDER BY s.FK_ID_Line, j.Name, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();
		
		return $result;
	}




    
	
	

	// ******************************************* Custome function ************************************	
	/**
		* Get job by his is
		* @param int $arrayJobID, $arrayjobTypeID, $arrayjobStatusID
		* @return array
	*/
	public function get_row_by_id_type_status($arrayJobID=[], $arrayjobTypeID=[], $arrayjobStatusID=[]) {
		// Create criteria query.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayJobID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.ID', $arrayJobID, $criteria); }
		if(count($arrayjobTypeID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.FK_ID_Job_Type', $arrayjobTypeID, $criteria); }
		if(count($arrayjobStatusID) > 0) { $criteria = $this->plan_m->createCriteriaIN('j.FK_ID_Job_Status', $arrayjobStatusID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);

			$criteria = ' AND '.$criteria;
		}

		$sqlStr = "SELECT j.id JobID, j.Name JobName, t.Name JobTypeName, u.Name JobStatusName"
			." FROM job j"
				." LEFT JOIN job_type t ON j.FK_ID_Job_Type = t.id"
				." LEFT JOIN job_status u ON j.FK_ID_Job_Status = u.id"
			." WHERE j.Delete_Flag=0" .$criteria
			." ORDER BY j.Name";
		
		$query = $this->db->query($sqlStr);
		$result = $query->result_array();
			
		return $result;
	}
    
    
	/**
     * Get job by his is
     * @param int $arrayjobTypeID, $arrayjobStatusID
     * @return array
	*/
	public function get_row_by_type_status($arrayjobTypeID=[], $arrayjobStatusID=[]) {
		// Create criteria query.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayjobTypeID) > 0) { $criteria = $this->plan_m->createCriteriaIN('FK_ID_Job_Type', $arrayjobTypeID, $criteria); }
		if(count($arrayjobStatusID) > 0) { $criteria = $this->plan_m->createCriteriaIN('FK_ID_Job_Status', $arrayjobStatusID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
	
			$criteria = ' AND '.$criteria;
		}
	
		$sqlStr = "SELECT id, Name"." FROM job WHERE Delete_Flag=0".$criteria." ORDER BY Name";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}

    // **************************************************** Join table function ***************************************
	public function getJob_Customer_ID($arrayCustomerID=[]) {
		// Prepare Criteria.
		$this->load->model('plan_m');
		$criteria ='';
		if(count($arrayCustomerID) > 0){
			$criteria = $this->plan_m->createCriteriaIN('p.FK_ID_Customer', $arrayCustomerID, $criteria);
		}
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
	
			$criteria = ' AND '.$criteria;
		}

		$sqlStr = "SELECT DISTINCT(j.id), j.Name"
				." FROM job j"
					." INNER JOIN project p ON (j.FK_ID_Project = p.id)"
				." WHERE j.Delete_Flag=0" .$criteria
				." ORDER BY j.id";
	
		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}

	public function getJobByLineID($lineID=0) {
		$this->load->model('step_m');
		// Prepare Criteria.
		$criteria ='';
		if($lineID > 0){
			$criteria = ' AND t.'.$this->step_m->col_line_id.'='.$lineID;
		}
	
		$sqlStr = "SELECT DISTINCT(j.id), j.Name"
				." FROM job j"
					." INNER JOIN step t ON (j.FK_ID_Process = t.FK_ID_Process)"
				." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1 " .$criteria
				." ORDER BY j.id";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}





	// **************************************************** Manual *************************************
	// Advance.
	public function get_row_by_projectID($projectID) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_project_id, $projectID);
		$this->db->where($this->col_delete_flag, 0);
		$this->db->order_by($this->col_name, 'Asc');
		$query = $this->db->get();
	
		return $query->result_array();
	}
	public function get_row_by_projectAvailable() {
		$sqlStr = "SELECT * FROM job"
			." WHERE Delete_Flag = 0"
				." AND (FK_ID_Project IS NULL OR FK_ID_Project = 0)"
			." ORDER BY Name Asc";
		
		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	public function get_all_row_status_open() {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_job_status_id, 1);
		$this->db->where($this->col_delete_flag, 0);
		$this->db->order_by($this->col_name, 'Asc');
		$query = $this->db->get();
	
		return $query->result_array();
	}
	public function get_row_status_open_by_id($id, $arrWhere=[]) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_id, $id);
		$this->db->where($this->col_job_status_id, 1);
		$this->db->where($this->col_delete_flag, 0);
		if(count($arrWhere) > 0) {
			$this->db->where($arrWhere);
		}
		$this->db->order_by($this->col_name, 'Asc');
		$query = $this->db->get();

		return $query->result_array();
	}
	
	
	
	public function save($id, $data) {
		$result = false;
	
		if($id > 0) {
			// check in database
			$exist = $this->get_row_by_id($id);
			$ce = count($exist);
			
			if($ce > 0) {
				$data[$this->col_datetime_create] = $exist[0][$this->col_datetime_create];
				$result = $this->update_row($id, $data);
			}
			else {
				$data[$this->col_datetime_create] = date('Y-m-d H:i:s');
				$result = $this->insert_row($data);
			}
		}
		else {
			$data[$this->col_datetime_create] = date('Y-m-d H:i:s');
			$result = $this->insert_row($data);
		}
			
		return $result;
	}
	public function save_return_id($id, $data) {
		$result = -1;
	
		if($id > 0) {
			// check in database
			$exist = $this->get_row_by_id($id);
			$ce = count($exist);
	
			if($ce > 0) {
				$data[$this->col_datetime_create] = $exist[0][$this->col_datetime_create];
				if($this->update_row($id, $data)) { $result = $id; }
			}
			else {
				$data[$this->col_datetime_create] = date('Y-m-d H:i:s');
				if($this->insert_row($data)) { $result = $this->db->insert_id(); }
			}
		}
		else {
			$data[$this->col_datetime_create] = date('Y-m-d H:i:s');
			if($this->insert_row($data)) { $result = $this->db->insert_id(); }
		}
	
		return $result;
	}
	

	public function removeJob($id) {
		$result = false;
	
		$this->db->where($this->col_id, $id);
		$this->db->set($this->col_delete_flag, 1);
		$affectedRows = $this->db->update($this->table_name);
		
		$report = array();
		$report['error'] = $this->db->_error_number();
		$report['message'] = $this->db->_error_message();
		if(($report !== 0) && ($affectedRows > 0)){
			$result = true;
		}else{
			$result = false;
		}

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
		$this->db->where($this->col_delete_flag, 0);
		if(count($arrWhere) > 0) {
			$this->db->where($arrWhere);
		}
		$this->db->order_by($this->col_name, 'Asc');
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
	public function get_row($search_string=null, $order='Name', $order_type='Asc'
	, $limit_start=null, $limit_end=null) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($this->col_delete_flag, 0);

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
	function delete_row($id) {
		$this->db->where($this->col_id, $id);
		$result = $this->db->delete($this->table_name);
		
		return $result;
	}
}
?>	
