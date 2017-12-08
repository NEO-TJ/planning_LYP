<?php echo form_open(base_url("topRejectReport"), array("id" => "form-search")); ?>
<div class="row top">
	<div class="col-md-12 page-header users-header">
		<div class="col-md-10">
			<h1 id="headerPage">Top Reject Report</h1>
		</div>
		<div class="col-md-2">
			<h1><button type="button" class="btn btn-success pull-right" id="refresh">Refresh</button></h1>
		</div>
	</div>
</div>




<div class="row panel panel-primary">
    <div class="col-md-5 margin-input">
		<div class="input-group input-daterange" id="datepicker">
		    <input type="text" class="form-control" placeholder="Start Date" id="dateStart">
		    <span class="input-group-addon">to</span>
		    <input type="text" class="form-control" placeholder="End Date" id="dateEnd">
		</div>
	</div>
	<div class="col-md-6 margin-input">
	</div>
	<div class="col-md-1 margin-input pull-left">
		<button type="button" class="btn btn-primary pull-right" id="search">Go</button>
	</div>


	<div class="col-md-5 margin-input">
		<div class="input-group">
			<span class="input-group-btn">
				<button class="btn btn-primary disabled" type="button">Job : </button>
			</span>
			<select class="form-control multi-select" id="jobID" name="jobID[]" multiple="multiple">
				<?php 
					foreach($dsJob as $row) {
						echo '<option value='.$row['id'].'>'.$row['Name'].'</option>';
					}
				?>
			</select>
		</div>
	</div>
	<div class="col-md-7 margin-input">
		<div class="input-group" id="stepID">
			<span class="input-group-btn">
				<button class="btn btn-primary disabled" type="button">Step number : </button>
			</span>
			<select class="form-control multi-select" id="stepID" name="stepID[]" multiple="multiple">
				<?php 
					foreach($dsStep as $row) {
						echo '<option value='.$row['id'].'>'.$row['Number'].' - '.$row['DESC'].'</option>';
					}
				?>
			</select>
		</div>
	</div>
</div>
<?php echo form_close(); ?><!-- Close form-search -->


<hr>
<div class="row">
<!-- Table -->
	<div class="col-md-12">
		<table class="table table-components table-condensed table-hover table-striped table-responsive" 
			id="ngPercentByStepReport" style="width: 100%;">
			<thead>
<!-- Row header 0 -->
				<tr>
					<th class="text-center header-border-report"><h4><strong>Step</strong></h4></th>
					<th class="text-center header-border-report"><h4><strong>Description</strong></h4></th>
					<th class="text-center header-border-report"><h4><strong>Done Qty</strong></h4></th>
					<th class="text-center header-border-report"><h4><strong>NG Qty</strong></h4></th>
					<th class="text-center header-border-report"><h4><strong>% of NG</strong></h4></th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
	</div>
</div>
<div class="clear"></div>
<hr>

<div class="row">
<!-- Table -->
	<div class="col-md-12">
		<table class="table table-components table-condensed table-hover table-striped table-responsive" 
			id="topRejectReport" style="width: 100%;">
			<thead>
<!-- Row header 0 -->
				<tr>
					<th class="text-center header-border-report"><h4><strong>No</strong></h4></th>
					<th class="text-center header-border-report"><h4><strong>Defect Name</strong></h4></th>
					<th class="text-center header-border-report"><h4><strong>Reject Quantity</strong></h4></th>
				</tr>
			</thead>
			
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<div class="clear"></div>



<div id="footer">
	<hr>
	<div class="inner">
		<div class="container">
			<div class="row">
				<div class="col-md-10">
				</div>
				<div class="col-md-2">
					<a href="#">Back to top</a>
				</div>
			</div>
		</div>
	</div>
</div>