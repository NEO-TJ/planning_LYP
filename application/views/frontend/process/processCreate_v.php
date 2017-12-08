<div class="row top">
	<div class="col-md-12 page-header users-header">
		<div class="col-md-10">
			<h1 id="headerPage">Create New Process</h1>
		</div>
		<div class="col-md-2">
		</div>
	</div>
</div>

<div class="panel panel-primary">
	<button data-toggle="collapse" data-target="#collapse-process" class="btn btn-primary">All process</button>

	<div class="row collapse" id="collapse-process">				<!-- Row of expand process -->
		<div class="col-md-12 margin-input">
			<table class="table table-bordered table-components table-condensed table-hover table-striped table-responsive" id="view">
				<thead class="table-header">
					<tr>
						<th class="text-center" width="40">No.</th>
						<?php 
							if(count($dsProcess) > 0) {
								$i=0;
								foreach($dsProcess[0] as $col => $value) {
									if($i++ > 0) {
										echo ('<th class="text-center">'. $col .'</th>');
									}
								}
							}
						?>
					</tr>
				</thead>
				
				<tbody>
					<?php 
						$i = 1;
						foreach($dsProcess as $row) {
							echo ('<tr>');
								echo('<td class="text-center">' .$i++. '</td>');
								$j=0;
								foreach($row as $value) {
									if($j++ > 0) {
										echo('<td class="text-left">' .$value. '</td>');
									}
								}
							echo ('</tr>');
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<hr>
<?php echo form_open(base_url(), array("id" => "form-process")); ?>

<div class="panel panel-primary">									<!-- Panel of Process & Step -->
	<div class="row">										<!-- Row of expand process -->
		<div class="col-md-12">
			<div class="panel panel-success expand-input">
				<div class="panel-heading" style="text-align: center;">
					<h1 id="panel-caption-process">Process</h1>
				</div>
				<div class="row">
					<div class="col-md-6">						<!-- Col of process name -->
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">* Process Name : </button>
							</span>								
							<input type="text" class="form-control"
								placeholder="Process name..." name="processName" id="processName">
						</div>
					</div><!-- End col of process name -->

					<div class="col-md-6">						<!-- Col of process Desc -->
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">Process Description : </button>
							</span>								
							<input type="text" class="form-control"
								placeholder="Process description..." name="processDesc" id="processDesc">
						</div>
					</div><!-- End col of process Desc -->
				</div>

				<div class="row">
					<div class="col-md-12">						<!-- Col of process Desc Thai -->
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">Process Desc Thai : </button>
							</span>								
							<input type="text" class="form-control"
								placeholder="Process description Thai..." name="processDescThai" id="processDescThai">
						</div>
					</div><!-- End col of process Desc Thai -->
				</div>

			</div><!-- End panel of expand process -->
		</div>
	</div><!-- End row of expand process -->

	<hr>
	<div class="row">													<!-- Row of Step -->
		<div class="col-md-12">
			<table id="step"
			class="table table-bordered table-components table-condensed table-hover table-striped table-responsive">
				<thead class="bg-info">
					<tr>
						<th class="text-center table-caption bg-info" id="step-caption" colspan="9">STEP</th>
					</tr>
					<tr>
						<th class="text-center" width="40">First Step</th>
						<th class="text-center" width="100">Next Step</th>
						<th class="text-center" width="100">* Step</th>
						<th class="text-center" width="420">Description</th>
						<th class="text-center" width="230">* Line</th>
						<th class="text-center" width="230">* Machine</th>
						<th class="text-center" width="230">* Sub assembly</th>
						<th class="text-center" width="110">* NB sub</th>
						<th class="text-center" width="36">#</th>
					</tr>
				</thead>
				<tbody class="bg-warning">
					<tr>
						<td class="text-center td-group">
							<input type="checkbox" class="form-control td-group" name="firstStepFlag[]" id="firstStepFlag" value="0" />
						</td>
						<td class="text-center td-group">
							<input type="text" class="form-control td-group" name="nextStepNumber[]" id="nextStepNumber"
								placeholder="Next Step Number..." />
						</td>
						<td class="text-center td-group">
							<input class="form-control" type="text" name="stepNumber[]" id="stepNumber"
								placeholder="Step Number...">
						</td>
						<td class="text-center td-group">
							<input class="form-control" type="text" name="stepDesc[]" id="stepDesc"
								placeholder="Description...">
						</td>
						<td class="text-center td-group">
							<select class="form-control text-center" name="line[]" id="line">
								<option value="0" selected>Please select line</option>
								<?php 
									foreach($dsLine as $row) {
										echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
									}
								?>
							</select>
						</td>
						<td class="text-center td-group">
							<select class="form-control text-center" name="machine[]" id="machine">
								<option value="0" selected>Please select machine</option>
								<?php 
									foreach($dsMachine as $row) {
										echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
									}
								?>
							</select>
						</td>
						<td class="text-center td-group">
							<select class="form-control text-center" name="subAssemble[]" id="subAssemble">
								<option value="0" selected>Please select sub assemble</option>
								<?php 
									foreach($dsSubAssembly as $row) {
										echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
									}
								?>
							</select>
						</td>
						<td class="text-center td-group">
							<input class="form-control text-center" type="number" name="nbSub[]" id="nbSub"
								title="Quantity of Sub_Assembly to make one step" placeholder="NB sub...">
						</td>
						<td class="text-center">
							<button type="button" class="btn btn-default add-elements">
								<i class="fa fa-plus"></i>
							</button>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="row">
				<div class="col-md-9">
				</div>
				<div class="col-md-3 pull-right">
					<button type="button" class="btn btn-danger btn-reset pull-left">Reset process</button>
					<button type="submit" class="btn btn-primary btn-submit pull-right">Update process</button>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12"></div>
			</div>
		</div>
	</div><!-- End row of Step -->
</div><!-- End panel of Process & Step -->
<?php echo form_close(); ?><!-- Close form-process -->

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