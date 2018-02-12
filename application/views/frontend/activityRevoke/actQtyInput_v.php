<!-- Header page. -->
	<div class="row top">
		<div class="col-md-12 page-header users-header">
			<h1>Activity Quantity Input</h1>
		</div>
	</div>
<!-- End Header page. -->


<!-- Filter. -->
	<?php echo form_open(base_url("activityRevoke"), array("id" => "form-search")); ?>
	<div class="row panel panel-primary">
	<!-- Job -->
		<div class="col-md-6 margin-input">
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
	<!-- Line -->
		<div class="col-md-6 margin-input">
			<div class="input-group" id="lineID">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Line : </button>
				</span>
				<select class="form-control multi-select" id="lineID" name="lineID[]" multiple="multiple">
					<?php 
						foreach($dsLine as $row) {
							echo '<option value='.$row['id'].'>'.$row['Name'].'</option>';
						}
					?>
				</select>
			</div>
		</div>

	<!-- Step -->
		<div class="col-md-11 margin-input">
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
	<!-- Button -->
		<div class="col-md-1 margin-input pull-left">
			<button type="button" class="btn btn-primary pull-right" id="search">Go</button>
		</div>
	<!-- End Button -->
	</div>
	<?php echo form_close(); ?><!-- Close form-search -->
<!-- End Filter. -->

<hr>
<!-- Activity table result -->
	<div class="panel panel-primary">												<!-- Panel of Last activity qty input -->
		<div class="row">													<!-- Row of Last activity qty input -->
			<div class="col-md-12">
				<?php echo form_open(base_url(), array("id" => "formLastActivityQtyInput")); ?>
				<table id="actQtyInput"
				class="table table-bordered table-components table-condensed table-hover table-striped table-responsive">
					<thead class="bg-info">
						<tr>
							<th class="text-center table-caption bg-info" id="activityCaption" colspan="11">
								<h3><b>Last activity quantity input</b></h3>
							</th>
						</tr>
						<tr>
							<th class="text-center" width="40">No</th>
							<th class="text-center" width="100">DateTime</th>
							<th class="text-center" width="400">Job</th>
							<th class="text-center" width="400">Step</th>
							<th class="text-center" width="100">Line</th>
							<th class="text-center" width="100">Worker</th>
							<th class="text-center" width="50">Qty OK</th>
							<th class="text-center" width="50">Qty NG</th>
							<th class="text-center" width="200">Defect</th>
							<th class="text-center" width="100">User</th>
							<th class="text-center" width="20">#</th>
						</tr>
					</thead>
					<tbody class="bg-warning">
					</tbody>
				</table>
				<?php echo form_close(); ?><!-- Close formLastActivityQtyInput -->
				
			</div>
		</div><!-- End row of Last activity qty input -->
	</div><!-- End panel of Last activity qty input -->
<!-- End Activity table result -->