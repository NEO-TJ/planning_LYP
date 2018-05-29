<!-- Header page. -->
	<div class="row top">
		<div class="col-md-12 page-header users-header">
			<h1>Activity Recovery NG</h1>
		</div>
	</div>
<!-- End Header page. -->


<!-- Filter. -->
	<?php echo form_open(base_url("activityRevoke"), array("id" => "form-search")); ?>
	<div class="row panel panel-primary">
	<!-- Daterange -->
		<div class="col-xs-12 col-md-5 col-lg-5 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">ช่วงเวลา : </button>
				</span>
					<input id="daterange" size="40">
			</div>
		</div>
	<!-- Line -->
		<div class="col-xs-12 col-md-6 col-lg-6 margin-input">
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
	<!-- Button -->
		<div class="col-xs-12 col-md-1 col-lg-1 margin-input pull-left">
			<button type="button" class="btn btn-primary pull-right" id="search">Go</button>
		</div>
	<!-- Job -->
		<div class="col-xs-12 col-md-5 col-lg-5 margin-input">
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
	<!-- Step -->
		<div class="col-xs-12 col-md-7 col-lg-7 margin-input">
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
<!-- End Filter. -->

<hr>
<!-- Activity table result -->
	<div class="panel panel-primary">												<!-- Panel of Last activity Recovery NG -->
		<div class="row">													<!-- Row of Last activity Recovery NG -->
			<div class="col-md-12">
				<?php echo form_open(base_url(), array("id" => "formLastActivityRecoveryNG")); ?>
				<table id="actRecoveryNG"
				class="table table-bordered table-components table-condensed table-hover table-striped table-responsive">
				<!-- table head -->
					<thead class="bg-info">
						<tr>
							<th class="text-center table-caption bg-info" id="activityCaption" colspan="10">
								<h3><b>Last activity Recovery NG</b></h3>
							</th>
						</tr>
						<tr>
							<th class="text-center" width="40">No</th>
							<th class="text-center" width="100">DateTime</th>
							<th class="text-center" width="400">Job</th>
							<th class="text-center" width="600">Step</th>
							<th class="text-center" width="100">Line</th>
							<th class="text-center" width="150">Worker</th>
							<th class="text-center" width="50">Qty NG</th>
							<th class="text-center" width="150">User</th>
							<th class="text-center" width="20">#</th>
						</tr>
					</thead>
				<!-- table body -->
					<tbody class="bg-warning">
					</tbody>
				</table>
			<!-- pagination link -->
				<div class="pagination pull-right" id="paginationLinks"> 
					<p><?php echo $paginationLinks; ?></p> 
				</div>
			<!-- end pagination link -->
				<?php echo form_close(); ?><!-- Close formLastActivityRecoveryNG -->
				
			</div>
		</div><!-- End row of Last activity Recovery NG -->
	</div><!-- End panel of Last activity Recovery NG -->
<!-- End Activity table result -->