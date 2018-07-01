<?php
class AjaxService extends CI_Controller {
  public function __construct() {
		parent::__construct();

		$this->is_logged();
	}

  public function index() {
    $this->is_logged();
  }

  
// ****************************************** AJAX function ****************************************
  public function ajaxGetDsFullNgStock() {
    if(!($this->is_logged())) {exit(0);}
    if ($this->input->server('REQUEST_METHOD') === 'POST') {
      $jobID = $this->input->post('jobID');
      $stepID = $this->input->post('stepID');

      $dsDestinationStepStock = array();
      $dsStep = $this->getDsStep($stepID);
      if(count($dsStep) > 0) {
        // Destination Step.
        if($dsStep[0]['First_Step_Flag'] == 1) {
          // ====> Get (Current or First) Stock.
          $dsDestinationStepStock = $this->getDsCurrentStepDescStock($jobID, $stepID);
        } else {
          // ====> Get (Previous Step) Stock.
          $dsDestinationStepStock = $this->getDsPreviousStepDescStock($jobID, $dsStep[0]['Number']);
        }
      }
      $rData['dsDestinationStepStock'] = $dsDestinationStepStock;
      $rData['dsDefect'] = $this->getDsDefect(0);

      echo json_encode($rData);
    }
  }
  public function ajaxGetDsFullDestinationStock() {
    if(!($this->is_logged())) {exit(0);}
    if ($this->input->server('REQUEST_METHOD') === 'POST') {
      $jobID = $this->input->post('jobID');
      $stepID = $this->input->post('stepID');
      
      $dsDestinationStepStock = array();
      $dsStep = $this->getDsStep($stepID);
      if(count($dsStep) > 0) {
        // Destination Step.
        if($dsStep[0]['First_Step_Flag'] == 1) {
          // ====> Get (Current or First) Stock.
          $dsDestinationStepStock = $this->getDsCurrentStepDescStock($jobID, $stepID);
        } else {
          // ====> Get (Previous Step) Stock.
          $dsDestinationStepStock = $this->getDsPreviousStepDescStock($jobID, $dsStep[0]['Number']);
        }
      }

      echo json_encode($dsDestinationStepStock);
    }
  }
// ****************************************** End AJAX function **************************************



// ***************************************** Get function **************************************
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step-Desc Stock Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsCurrentStepDescSubAssStock($jobID, $stepID) {
		$this->load->model('step_m');
		$dsFullStock = $this->step_m->getFullStockNumberDescSubAss($jobID, $stepID);

		return $dsFullStock;
	}

	private function getDsCurrentStepDescStock($jobID, $stepID) {
		$this->load->model('step_m');
		$dsFullStock = $this->step_m->getFullStockNumberDesc($jobID, $stepID);

		return $dsFullStock;
	}
	private function getDsPreviousStepDescStock($jobID, $stepNumber) {
		$this->load->model('step_m');
		$dsPreviousFullStock = $this->step_m->getPreviousFullStockNumberDesc($jobID, $stepNumber);

		return $dsPreviousFullStock;
  }
  // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step-Desc Stock Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%
  
	private function getDsStep($id) {
		$this->load->model('step_m');
		$dsStep = $this->step_m->get_row_by_id($id);

		return $dsStep;
  }
  
  // %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Defect Get DB %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	private function getDsDefect($id) {
		$this->load->model('defect_m');
		$dsDefect = (($id == 0) ? $this->defect_m->get_row() : $this->defect_m->get_row_by_id($id));

		return $dsDefect;
	}

// ***************************************** End Get function **************************************


// ********************************************************* Check logged *********************************
  private function is_logged() {
    if(!$this->session->userdata('id')){
      $this->logout();
      return false;
    } else {
      return true;
    }
  }
  private function logout() {
    $this->load->view('frontend/include/header');
    $this->load->view('frontend/logout');
    $this->load->view('frontend/include/footer');
  }
// ********************************************************** End logged **********************************
}
