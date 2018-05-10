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
	
	public function __construct() {
		parent::__construct();
	}

    
    
	// **************************************************** Join table function ***************************************
	// ------------------------------------------------------ Filter combobox -----------------------------------------
	public function getStep_Job_Open() {
		$sqlStr = "SELECT DISTINCT(s.id), s.Number, s.DESC"
			." FROM step s"
			." INNER JOIN job j ON (s.FK_ID_Process = j.FK_ID_Process)"
			." WHERE j.FK_ID_Job_Status = 1 AND j.Delete_Flag=0"
			." ORDER BY s.FK_ID_Process, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	public function getStep_Job_Open_Job_ID($arrayJobID=[]) {
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
	public function getStep_Job_ID($arrayJobID=[]) {
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
			." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1" .$criteria
			." ORDER BY s.FK_ID_Process, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	public function getLine_Step_ID($arrayStepID=[]) {
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
			." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1" .$criteria
			." ORDER BY l.Name";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}



	public function getLine_Job_ID($arrayJobID=[]) {
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
	public function getSubAssembly_Job_ID($arrayJobID=[]) {
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



	public function getFullStepStock($jobID=0, $processID=0) {
		$sqlStr = "SELECT DISTINCT(s.id) stepID, s.First_Step_Flag, s.Next_Step_Number, s.Number, s.DESC"
			.", l.Name lineName, m.Name machineName, b.Name subAssemblyName"
			.", k.Operation_Time, k.id stockID, s.NB_Sub"
			." FROM step as s"
				." LEFT JOIN stock as k ON s.id=k.FK_ID_Step AND k.FK_ID_Job = ".$jobID
				." LEFT JOIN line as l ON s.FK_ID_Line=l.id"
				." LEFT JOIN machine as m ON s.FK_ID_Machine=m.id"
				." LEFT JOIN sub_assembly b ON s.FK_ID_Sub_Assembly=b.id"
			." WHERE s.FK_ID_Process = ".$processID
			." ORDER BY s.Number";
		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}


    
	public function get_all() {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->order_by($this->col_process_id, 'Asc');
		$this->db->order_by($this->col_number, 'Asc');
		$query = $this->db->get();

		return $query->result_array(); 	
	}
    
	public function get_by_where_array($arrWhere = []) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->where($arrWhere);
		$this->db->order_by($this->col_process_id, 'Asc');
		$this->db->order_by($this->col_number, 'Asc');
		$query = $this->db->get();

		return $query->result_array();
	}
	// --------------------------------------------------------- minimal data stock.
	public function get_full_stock($jobID=0, $stepID=0) {
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
	public function get_previous_full_stock($jobID=0, $stepNumber=0) {
		$sqlStr = "SELECT k.id, s.Number, s.Next_Step_Number, s.First_Step_Flag, s.NB_Sub"
			.", k.Qty_OK_First_Step, k.Qty_OK, k.Qty_NG"
			." FROM job as j"
			." INNER JOIN step as s ON (j.FK_ID_Process = s.FK_ID_Process)"
			." INNER JOIN stock as k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
			." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
			." AND ((j.id = ".$jobID.") && (s.Next_Step_Number = ".$stepNumber."))"
			." ORDER BY j.id, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	// --------------------------------------------------------- medium data stock.
	public function getFullStockNumberDescSubAss($jobID=0, $stepID=0) {
		$sqlStr = "SELECT s.id as stepId, sb.Name as subAssamblyName, k.Qty_NG, s.NB_Sub as nbSub"
			." FROM job as j"
			." INNER JOIN step as s ON (j.FK_ID_Process = s.FK_ID_Process)"
			." INNER JOIN stock as k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
			." INNER JOIN sub_assembly as sb ON (s.FK_ID_Sub_Assembly = sb.id)"
			." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
			." AND ((j.id = ".$jobID.") && (s.id = ".$stepID."))"
			." ORDER BY j.id, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	public function getFullStockNumberDesc($jobID=0, $stepID=0) {
		$sqlStr = "SELECT k.id as stockId, s.id as stepId"
			.", CONCAT(s.Number, ' - ', s.`" . $this->col_desc . "`) as stepDesc"
			.", sb.Name as subAssamblyName, s.NB_Sub as nbSub"
			.", k.Qty_OK_First_Step as qtyStock"
			." FROM job as j"
			." INNER JOIN step as s ON (j.FK_ID_Process = s.FK_ID_Process)"
			." INNER JOIN stock as k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
			." INNER JOIN sub_assembly as sb ON (s.FK_ID_Sub_Assembly = sb.id)"
			." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
			." AND ((j.id = ".$jobID.") && (s.id = ".$stepID."))"
			." ORDER BY j.id, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}
	public function getPreviousFullStockNumberDesc($jobID=0, $stepNumber=0) {
		$sqlStr = "SELECT k.id as stockId, s.id as stepId"
			.", CONCAT(s.Number, ' - ', s.`" . $this->col_desc . "`) as stepDesc"
			.", sb.Name as subAssamblyName, s.NB_Sub as nbSub"
			.", k.Qty_OK as qtyStock"
			." FROM job as j"
			." INNER JOIN step as s ON (j.FK_ID_Process = s.FK_ID_Process)"
			." INNER JOIN stock as k ON ((j.id = k.FK_ID_Job) && (s.id = k.FK_ID_Step))"
			." INNER JOIN sub_assembly as sb ON (s.FK_ID_Sub_Assembly = sb.id)"
			." WHERE j.Delete_Flag=0 AND j.FK_ID_Job_Status=1"
			." AND ((j.id = ".$jobID.") && (s.Next_Step_Number = ".$stepNumber."))"
			." ORDER BY j.id, s.Number";

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}


	// --------------------------------------------- Get template --------------------------------------
	public function get_template(){
		$result[0] = [
			$this->col_id								=> 0,
			$this->col_number						=> '',
			$this->col_desc							=> '',
			$this->col_process_id				=> 0,
			$this->col_line_id					=> 0,
			$this->col_machine_id				=> 0,
			$this->col_sub_assembly_id	=> 0,
			$this->col_nb_sub						=> 0,
			$this->col_next_step_number	=> '',
			$this->col_first_step_flag	=> 0,
		];

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
		$this->db->order_by($this->col_process_id, 'Asc');
		$this->db->order_by($this->col_number, 'Asc');
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
		if($search_string) {
			$this->db->like($this->col_name, $search_string);
		}
		if($order) {
			$this->db->order_by($order, 'Asc');
		} else {
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
