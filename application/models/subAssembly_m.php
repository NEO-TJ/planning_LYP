<?php
class SubAssembly_m extends CI_Model {
	// Private Property
	var $table_name = "sub_assembly";
	var $col_id = "id";
	var $col_name = "Name";
	
    public function __construct() {
        parent::__construct();
    }





    // **************************************************** Manual *************************************
	// --------------------------------------------------------- Sub assembly of previous step.
	public function getSubAssemblyOfPreviousStep($stepId=0) {
		$this->load->model('step_m');
		$processId = 0;
		$stepNumber = 0;
		$firstStepFlag = 1;
		
		$dsCurrentStep = $this->step_m->get_row_by_id($stepId);
		if(count($dsCurrentStep) > 0 ) {
			$processId = $dsCurrentStep[0][$this->step_m->col_process_id];
			$stepNumber = $dsCurrentStep[0][$this->step_m->col_number];
			$firstStepFlag = $dsCurrentStep[0][$this->step_m->col_first_step_flag];
		}

		if($firstStepFlag == 0) {
			$sqlStr = "SELECT sa.id, sa.Name"
				." FROM step cs"
					." LEFT JOIN step ps ON (cs.FK_ID_Process = ps.FK_ID_Process)"
					." AND (cs.number = ps.Next_Step_Number)"
					." LEFT JOIN sub_assembly sa ON (ps.FK_ID_Sub_Assembly = sa.id)"
				." WHERE cs.FK_ID_Process = " . $processId
					." AND cs.Number = " . $stepNumber
				." ORDER BY sa.Name";
		} else {
			$sqlStr = "SELECT sa.id, sa.Name"
				." FROM step cs"
					." LEFT JOIN sub_assembly sa ON (cs.FK_ID_Sub_Assembly = sa.id)"
				." WHERE cs.id = " . $stepId
				." ORDER BY sa.Name";
		}

		$query = $this->db->query($sqlStr);
		$result = $query->result_array();

		return $result;
	}

	// --------------------------------------------------------- Manipulate.
	public function save($id, $data) {
    	$result = false;
    
    	// check in database
    	$exist = $this->get_row_by_id($id);
    	$ce = count($exist);
    
    	$result = (($ce > 0) ? $this->update_row($id, $data) : $this->insert_row($data));
    
    	return $result;
    }
    public function get_template() {
    	$result = [
            $this->col_id		=> 0,
            $this->col_name		=> '',
    	];
    
    	return $result;
    }
    
    public function getRowByStep($stepId) {
		$sqlStr = "SELECT s.FK_ID_Sub_Assembly id, a.Name"
            ." FROM step s"
            ." INNER JOIN sub_assembly a on s.FK_ID_Sub_Assembly=a.id"
            ." WHERE s.id = " . $stepId
            ." ORDER BY a.Name";

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
		$this->db->order_by($this->col_name, 'Asc');
		$query = $this->db->get();
		
		return $query->result_array(); 
    }    

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
}
?>	
