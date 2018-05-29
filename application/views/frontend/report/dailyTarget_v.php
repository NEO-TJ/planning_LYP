<?php echo form_open(base_url("dailyTargetReport"), array("id" => "form-search")); ?>
<div class="row top">
	<div class="col-md-12 page-header users-header">
		<div class="col-md-10">
			<h1 id="headerPage">Daily Target Report</h1>
		</div>
		<div class="col-md-2">
			<h1><button type="button" class="btn btn-success pull-right" id="refresh">Refresh</button></h1>
		</div>
	</div>
</div>


<div class="row panel panel-primary">

	<div class="col-md-12">
		<div class="row">
			<div class="col-md-6 panel panel-info">
				<div class="col-md-1 pull-left">
					<input type="checkbox" class="pull-right" name="ckUseDataPlan" id="ckUseDataPlan">
				</div>
				<div class="col-md-11">
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-primary disabled" type="button">ช่วงเวลา : </button>
						</span>
						<input id="daterange" size="400">
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<div class="input-group">
					<span class="input-group-btn">
						<button class="btn btn-primary disabled" type="button">Line : </button>
					</span>
					<select class="form-control" id="lineID">
						<option value="0" selected>Please select line</option>
						<?php 
							foreach($dsLine as $row) {
								echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-md-1">
				<button type="button" class="btn btn-primary pull-right" id="search">Go</button>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<div class="row">
			<div class="col-md-6">
				<div class="input-group" id="jobID">
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
			<div class="col-md-6">
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
	</div>

</div>
<?php echo form_close(); ?><!-- Close form-search -->


<hr>
<div class="row">
<!-- Table -->
	<div class="col-md-12">
		<table class="table table-components table-condensed table-hover table-striped table-responsive" 
			id="dailyTargetReport" style="width: 100%;">
			<thead>
	<!-- Row header 0 -->
				<tr>
					<th class="header-border-report"><h4 class="text-left"><strong>Date</strong></h4></th>
					<th class="header-border-report"><h4 class="text-left"><strong>Job Number</strong></h4></th>
					<th class="header-border-report"><h4 class="text-right"><strong>Step</strong></h4></th>
					<th class="header-border-report"><h4 class="text-left"><strong>- Description</strong></h4></th>
					<th class="header-border-report" width="100"><h4 class="text-right"><strong>Plan Qty</strong></h4></th>
					<th class="header-border-report"><h4><strong></strong></h4></th>
					<th class="header-border-report"><h4 class="text-right"><strong>Next Step</strong></h4></th>
					<th class="header-border-report"><h4 class="text-right"><strong>Next Line</strong></h4></th>
					<th class="header-border-report"><h4 class="text-center"><strong>Barcode</strong></h4></th>
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