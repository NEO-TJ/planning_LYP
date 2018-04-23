<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <a class="brand">LoyalPAC</a>
      <ul class="nav">
        <li><h4>||||||</h4></li>

        <?php if(($level == 1) || ($level == 2)){ ?>
        <!-- planning menu -->
          <li <?php if($this->uri->segment(1) == 'planning'){echo 'class="active"';}?>>
            <a href="<?php echo base_url(); ?>planning">Planning</a>
          </li>
        <!-- end planning menu -->
          <li><h4>||||||</h4></li>
        <!-- project menu -->
          <li <?php 
        		if((($this->uri->segment(1) == 'project') 
        				|| ($this->uri->segment(1) == 'processCreate'))
        				|| ($this->uri->segment(1) == 'jobRemove')){
        			echo 'class="active dropdown"';
        		}
        	?>>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Project<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li <?php if($this->uri->segment(1) == 'project'){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>project">(Create/Edit) Project</a>
              </li>
              
              <li <?php if($this->uri->segment(1) == 'processCreate'){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>processCreate">Create Process</a>
              </li>            
              <hr>
              <li <?php if($this->uri->segment(1) == 'jobRemove'){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>jobRemove">Remove Job</a>
              </li>
            </ul>
          </li>
        <!-- end project menu -->
          <li><h4>||||||</h4></li>
        <?php } ?>
        
        <!-- qty input menu -->
          <li <?php 
        		if(($this->uri->segment(1) == 'qtyInput') || ($this->uri->segment(1) == 'recoveryNG')) {
        			echo 'class="active dropdown"';
        		}
        	?>>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Input Mode<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li <?php if($this->uri->segment(1) == 'qtyInput'){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>qtyInput">Quantity Input</a>
              </li>
              <li <?php if($this->uri->segment(1) == 'recoveryNG'){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>recoveryNG">Recovery NG</a>
              </li>

              <?php if($level == 1){ ?>
                <hr>
                <li <?php if($this->uri->segment(2) == 'activityQtyInput'){echo 'class="active"';}?>>
                  <a href="<?php echo base_url(); ?>activityRevoke\activityQtyInput">Activity-Quantity Input</a>
                </li>
                <li <?php if($this->uri->segment(2) == 'activityRecoveryNG'){echo 'class="active"';}?>>
                  <a href="<?php echo base_url(); ?>activityRevoke\activityRecoveryNG">Activity-Recovery NG</a>
                </li>
              <?php } ?>
            </ul>
          </li>
        <!-- end qty input menu -->
        
        <?php if(($level == 1) || ($level == 2)){ ?>
          <li><h4>||||||</h4></li>
        <!-- adjust stock menu -->
          <li <?php 
            if($this->uri->segment(1) == 'stockAdjust'){ echo 'class="active dropdown"'; }
        	?>>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Stock<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li <?php if($this->uri->segment(1) == 'stockAdjust'){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>stockAdjust">Adjust Stock</a>
              </li>
            </ul>
          </li>
        <!-- end adjust stock menu -->
          <li><h4>||||||</h4></li>
          <li><h4>||||||</h4></li>
        <!-- report menu -->
          <li class="dropdownm">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Report<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="<?php echo base_url(); ?>dailyTargetReport">Daily Target</a></li>
              <li><a href="<?php echo base_url(); ?>achievementReport">Achievement</a></li>
              <hr>
              <li><a href="<?php echo base_url(); ?>ngPercentReport">NG Percent</a></li>
              <li><a href="<?php echo base_url(); ?>topRejectReport">Top Reject</a></li>
              <hr>
              <li><a href="<?php echo base_url(); ?>workingCapacityReport">Working Capacity</a></li>
            </ul>
          </li>
        <!-- end report menu -->
        <?php } ?>
        
        <?php if($level == 1){ ?>
          <li><h4>||||||</h4></li>
          <li><h4>||||||</h4></li>
        <!-- master data menu -->
          <li <?php 
        		if($level < 3){
        			if($this->uri->segment(1) == 'masterdata'){
        				echo 'class="active dropdown"';
        			}
        		}
        		else{
        			echo 'class=hide';
        		}
        	?>>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Master data<b class="caret"></b></a>
            <ul class="dropdown-menu">
	            <li <?php if($this->uri->segment(3) == 1){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/1">Customer</a>
	            </li>
              <hr>
              <li <?php if($this->uri->segment(3) == 2){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/2">Line</a>
              </li>
              <li <?php if($this->uri->segment(3) == 3){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/3">Machine</a>
              </li>
              <li <?php if($this->uri->segment(3) == 4){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/4">Sub Assembly</a>
              </li>
              <li <?php if($this->uri->segment(3) == 5){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/5">Defect</a>
                <hr>
              </li>
              <li <?php if($this->uri->segment(3) == 6){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/6">Raw Material</a>
              </li>
              <li <?php if($this->uri->segment(3) == 7){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/7">Unit</a>
                <hr>
              </li>
              <li <?php if($this->uri->segment(3) == 8){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/8">Job Type</a>
              </li>
              <li <?php if($this->uri->segment(3) == 9){echo 'class="active"';}?>>
                <a href="<?php echo base_url(); ?>masterdata/view/9">Job Status</a>
              </li>
              <hr>
              <li <?php 
                if(($this->uri->segment(3) == 0) && ($this->uri->segment(3) != NULL) && ($this->uri->segment(3) != '')) {
                  echo 'class="active"';
                }
              ?>>
                <a href="<?php echo base_url(); ?>masterdata/view/0">User</a>
              </li>
            </ul>
          </li>
        <!-- end master data menu -->
        <?php } ?>

        <!-- logout menu -->
          <li><h4>||||||</h4></li>
          <li <?php if($this->uri->segment(1) == 'index'){echo 'class="active"';}?>>
            <a href="<?php echo base_url(); ?>index">Logout</a>
          </li>
        <!-- end logout menu -->
      </ul>
    </div>
  </div>
</div>