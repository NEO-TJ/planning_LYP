<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_m extends CI_Model {
	// Private Property
	var $table_name = "user";
	var $col_id = "id";
	var $col_name = "Name";
	var $col_user_id = "User_ID";
	var $col_password = "Password";
	var $col_line_id = "FK_ID_Line";
	var $col_level = "Level";
	var $col_status = "Status";
	
	public function __construct() {
        parent::__construct();
    }





    // **************************************************** Manual *************************************
    public function saveAllStatus($id, $data) {
		$result = false;

		asort($data['FK_ID_Line']);
		$sLineId = implode(',', $data['FK_ID_Line']);
		$data['FK_ID_Line'] = $sLineId;
    	// check in database
    	$exist = $this->get_row_by_id($id);
    	$ce = count($exist);
    
    	$result = (($ce > 0) ? $this->update_row($id, $data) : $this->insert_row($data));
    	
    	return $result;
    }
    


    // **************************************************** Join table function ***************************************
    public function get_full_user($arrUserID=[]) {
    	// Prepare Criteria.
		$criteria ='';
		$this->load->model('plan_m');
    	if(count($arrUserID) > 0) { $criteria = $this->plan_m->createCriteriaIN('u.id', $arrUserID, $criteria); }
    	if(strlen($criteria) > 4) {
    		$criteria = substr($criteria, 4, strlen($criteria) - 4);
    
    		$criteria = ' WHERE '.$criteria;
    	}
    
		$sqlStr = "SELECT u.id, u.Name, u.User_ID"
					.", CASE WHEN u.level=1 THEN 'Admin' WHEN u.level=2 THEN 'Superviser/Engineer' ELSE 'Staff' END as Level"
	    			.", CASE WHEN u.Status=0 THEN 'Active' ELSE 'Terminate' END as Status"
	    			." FROM user as u"
   					.$criteria
   					." ORDER BY u.Level, u.Status, u.Name";
	    	
		$query = $this->db->query($sqlStr);
		$result = $query->result_array();
    	
    	return $result;
    }
    public function get_template() {
		$result = [
				$this->col_id		=> 0,
				$this->col_name		=> '',
				$this->col_user_id	=> '',
				$this->col_password	=> '',
				$this->col_line_id	=> 0,
				$this->col_level	=> 3,
				$this->col_status	=> 0,
		];

    	return $result;
    }
    
    
    
    
    public function validate() {
        $query = $this->db->get_where('user'
	        				,array(
		                        'User_ID =' => $this->input->post('userID')
		        				,'Password =' => $this->input->post('password')
		        				,'Status !=' => '1'))
	        				->result();
        $iCount = count($query);
        if($iCount == 1)
        {
            return $query;
        }
    }
    
    public function findAll() {
        $query = $this->db->order_by('id','ASC')->get_where('user',array('Status !=' => '1'))->result();
        return $query;
    }
    
    public function save($data) {
        // check in database
        $exist = $this->db->get_where('user',array('id =' => $data['id'],'Status !=' => '1'))->result();
        
        $ce = count($exist);
        
        if($ce > 0) {
            // update bank to db
            $this->db->where('id', $data['id']);
            $this->db->update('user', $data);
            return true;
        } else {
            // save bank to db
            $this->db->insert('user', $data);
            return true;
        }
    }
    public function get_row_active_status($id=0, $arrWhere=[]) {
    	$this->db->select('*');
    	$this->db->from($this->table_name);
    	$this->db->where($this->col_status, 0);
    	if($id != 0) {
    		$this->db->where($this->col_id, $id);
    	}
    	if(count($arrWhere) > 0) {
    		$this->db->where($arrWhere);
    	}
    	$query = $this->db->get();
    
    	return $query->result_array();
    }
	
    
    // ****************************************************** Normal function *****************************************
    /**
     * Get product by his is
     * @param int $product_id
     * @return array
     */
    public function get_row_by_id($id=0, $arrWhere=[]) {
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
		, $limit_start=null, $limit_end=null) {
	    
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
    * Update customer
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
    * Delete customer
    * @param int $id - customer id
    * @return boolean
    */
	function delete_row($id) {
		$this->db->where($this->col_id, $id);
		$result = $this->db->delete($this->table_name);
		
		return $result;
	}
}
