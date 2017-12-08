<?php
class Machine_m extends CI_Model {
	// Private Property
	var $table_name = "machine";
	var $col_id = "id";
	var $col_name = "Name";
	var $col_desc = "DESC";
	var $col_line_id = "FK_ID_Line";
	
	/**
    * Responsable for auto load the database
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }





    // **************************************************** Manual *************************************
    public function save($id, $data)
    {
    	$result = false;
    
    	// check in database
    	$exist = $this->get_row_by_id($id);
    	$ce = count($exist);
    
    	$result = (($ce > 0) ? $this->update_row($id, $data) : $this->insert_row($data));
    
    	return $result;
    }
    public function get_template()
    {
    	$result = [
    			$this->col_id		=> 0,
    			$this->col_name		=> '',
    			$this->col_desc		=> '',
    			$this->col_line_id	=> 0,
    	];
    
    	return $result;
    }
    
    
    
    // **************************************************** Join table function ***************************************
    public function get_full_machine($arrMachineID)
    {
        	// Prepare Criteria.
    	$criteria ='';
		if(count($arrMachineID) > 0) { $criteria = $this->createCriteriaIN('m.id', $arrMachineID, $criteria); }
		if(strlen($criteria) > 4) {
			$criteria = substr($criteria, 4, strlen($criteria) - 4);
		
			$criteria = ' WHERE '.$criteria;
		}
    	
    	$sqlStr = "SELECT m.id, m.Name, m.DESC Description, l.Name Line"
    			." FROM machine as m"
    			." LEFT JOIN line as l ON (m.FK_ID_Line = l.id)"
    			.$criteria
    			." ORDER BY m.Name";
    															 
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
    		, $limit_start=null, $limit_end=null)
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
	    return $insert;
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





    // ********************************************************** Helper **********************************************
	private function createCriteriaIN($columnName, $arrayDataIN, $criteria){
		$criteria = $criteria.' && '.$columnName.' IN (';
	
		for($i=0 ; $i < count($arrayDataIN) ; $i++) {
			$criteria = $criteria.$arrayDataIN[$i].',';
		}
		$criteria = substr($criteria, 0, strlen($criteria) - 1);
		$criteria = $criteria.')';
	
		return $criteria;
	}
}
?>
