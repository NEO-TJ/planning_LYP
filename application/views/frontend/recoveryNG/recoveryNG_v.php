<?php $attributes = array("id" => "formRecoveryNG"); ?>
<?php echo form_open(base_url(), $attributes); ?>	<!-- 'issue/ajax_save/?action=add' -->
<div class="row top">
	<div class="col-md-12 page-header users-header">
		<h1>Recovery NG</h1>
	</div>
</div>

<div class="panel panel-primary">												<!-- Row of header -->
	<div class="row">
<!-- **************************************************************************************** Job & DATE & Worker Panel ***** -->
		<div class="col-md-12">
			<div class="panel panel-default">								<!-- Panel of job & DATE & Worker -->
				<div class="row margin-input">							<!-- Row of job & DATE -->
					<div class="col-md-6">							<!-- Col of job number -->
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">* Job : </button>
							</span>
							<select class="form-control" id="job">
								<option value="0" selected>Please select job</option>
								<?php 
									foreach($dsJob as $row) {
										echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-md-6">							<!-- Col of DATE -->
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">Date-Time : </button>
							</span>
							<div class="input-group date" id='dateTimeStamp'>
								<input data-date-format="DD-MMM-YYYY HH:mm" type="text" id='dateTimeStamp'></input>
								<span class="input-group-addon" id='dateTimeStamp'>
									<span class="glyphicon glyphicon-calendar" id='dateTimeStamp'></span>
								</span>
							</div>
						</div>
					</div>
				</div><!-- End row of job & DATE -->

				<div class="row margin-input">							<!-- Row of Worker -->
					<div class="col-md-12">
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">* Worker : </button>
							</span>
							<select class="form-control" id="worker">
								<option value=0 selected>Please select Worker</option>
								<?php 
									foreach($dsWorker as $row) {
										echo '<option value="'.$row['id'].'">'.$row['User_ID'].' - '.$row['Name'].'</option>';
									}
								?>
							</select>
						</div>
					</div>
				</div><!-- End row of Worker -->
			</div><!-- End panel of job & DATE & Worker -->
		</div>
	</div>
</div><!-- End Form Header -->


<!-- ***************************************************************************************** Recovery NG Panel ********** -->
<div class="panel panel-primary">																	<!-- Row of Recovery -->
	<div class="row">
<!-- ********************************************************************************* Source Panel *********************** -->
		<div class="col-md-6">																		<!-- Row of Source -->
			<div class="panel panel-primary">													<!-- Panel of source -->
				<div class="row">
					<div class="col-md-12">													<!-- Row of Source -->
						<div class="panel panel-default">
						
							<div class="row margin-input">								<!-- Row of Step-desc source -->
								<div class="col-md-12">
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* Source Step : </button>
										</span>
										<select class="form-control" id="sourceStep">
											<option value=0 selected>Please select source step-description...</option>
										</select>
									</div>
								</div>
							</div><!-- End row of step-desc source -->
							
							<div class="row margin-input">								<!-- Row of Qty Source -->
								<div class="col-md-12">								<!-- Col of Total Quantity NG -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">Total Qty NG : </button>
										</span>
										<input type="text" class="form-control text-center" id="sourceQtyNG" disabled>
									</div>
								</div><!-- End col of NG Qty -->
							</div><!-- End row of Qty Source -->
							
							<div class="row margin-input">								<!-- Row of Qty NG Source to send -->
								<div class="col-md-12">								<!-- Col of Qty NG to send -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* Qty NG to send : </button>
										</span>
										<input type="number" class="form-control text-center"
												placeholder="Qty NG for send..." id="qtyNGSend">
									</div>
								</div><!-- End col of OK Qty -->
							</div><!-- End row of NG Qty Source for send -->

						</div>
					</div><!-- End row of Source -->
				</div>				
			</div>
		</div>
<!-- ********************************************************************************* Button send Panel ****************** -->
		<div class="col-md-1">																<!-- Row of button send NG -->
			<div class="panel panel-primary">
				<div class="row">
					<div class="col-md-12">
						<div class="input-group container">
							<button type="submit" class="btn btn-success btn-submit" id="sendQtyNG">Send</button>
						</div>
					</div>
				</div>
			</div>	
		</div>
<!-- ********************************************************************************* Destination Panel ****************** -->
		<div class="col-md-5">																		<!-- Row of destination -->
			<div class="panel panel-primary">													<!-- Panel of destination -->
				<div class="row">
					<div class="col-md-12">													<!-- Row of destination -->
						<div class="panel panel-default">
							
							<div class="row margin-input">							<!-- Row of Step-desc destination -->
								<div class="col-md-12">
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* Destination Step : </button>
										</span>
										<select class="form-control" id="destinationStep">
											<option value=0 selected>Please select destination step-description...</option>
										</select>
									</div>
								</div>
							</div><!-- End row of step-desc destination -->
							
							<div class="row margin-input">							<!-- Row of Qty Source -->
								<div class="col-md-12">							<!-- Col of OK Qty -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">Stock Qty OK : </button>
										</span>
										<input type="text" class="form-control text-center" id="destinationQtyOK" disabled>
									</div>
								</div><!-- End col of OK Qty -->
							</div><!-- End row of Qty Source -->
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">													<!-- Row of submit & reset -->
	<div class="col-md-11">
	</div>
	
	<div class="col-md-1 pull-right">
		<div class="input-group">
			<button type="button" class="btn btn-danger btn-reset pull-right" id="resetAllStep">Reset</button>
		</div>
	</div>
</div>
<?php echo form_close(); ?>


<br><br><br><br><br><br>
<div class="panel panel-primary">												<!-- Panel of Last activity Recovery NG -->
	<div class="row">													<!-- Row of Last activity Recovery NG -->
		<div class="col-md-12">
			<?php echo form_open(base_url(), array("id" => "formLastActivityRecoveryNG")); ?>
			<table id="lastActivity"
			class="table table-bordered table-components table-condensed table-hover table-striped table-responsive">
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
						<th class="text-center" width="150">Worker</th>
						<th class="text-center" width="50">Qty NG</th>
						<th class="text-center" width="150">User</th>
						<th class="text-center" width="20">#</th>
					</tr>
				</thead>
				<tbody class="bg-warning">
					<?php 
					$i = count($dsFullActivity);
					foreach($dsFullActivity as $row)
					{
						echo ('<tr>');
							echo('<td class="text-center td-group">'.$i--.'</td>');
							echo('<td class="text-text-left td-group">'.$row['Datetime_Stamp'].'</td>');
							echo('<td class="text-left td-group">'.$row['JobName'].'</td>');
							echo('<td class="text-left td-group">'.$row['StepNumber-Desc'].'</td>');
							echo('<td class="text-left td-group">'.$row['WorkerName'].'</td>');
							echo('<td class="text-right td-group">'.$row['Qty_NG'].'</td>');
							echo('<td class="text-left td-group">'.$row['UserName'].'</td>');
							
							echo('<td class="text-center">');
								echo('<button type="button" class="btn btn-danger delete-elements"');
								echo(' id="activityID" value="'.$row['activityID'].'">');
									echo('<i class="fa fa-minus"></i>');
								echo('</button>');
								echo('<input type="hidden" id="stockID" value="'.$row['stockID'].'">');
							echo('</td>');
						echo('</tr>');
					}
					?>
				</tbody>
			</table>
			<?php echo form_close(); ?><!-- Close formLastActivityRecoveryNG -->
			
		</div>
	</div><!-- End row of Last activity Recovery NG -->
</div><!-- End panel of Last activity Recovery NG -->