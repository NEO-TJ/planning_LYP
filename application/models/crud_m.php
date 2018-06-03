<?php
class Crud_m extends CI_Model {
	// Private Property
	var $table_name;
	var $col_id = "id";
	var $col_name = "Name";

	
	public function __construct(){
		parent::__construct();
	}

	// ******************************************* Custome function ************************************
	// -------------------------------------------------- Get ------------------------------------------
	public function find($rWhere=[]) {
		$this->db->select('*');
		$this->db->from($this->table_name);
		if(count($arrWhere) > 0) {
			$this->db->where($arrWhere);
		}

		$query = $this->db->get();
		$result = count($query->result_array() > 0);

		return $result; 	
	}

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
