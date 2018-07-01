<!-- Header page. -->
	<?php $attributes = array("id" => "formQtyInput"); ?>
	<?php echo form_open(base_url(), $attributes); ?>	<!-- 'issue/ajax_save/?action=add' -->
	<div class="row top">
		<div class="col-md-12 page-header users-header">
			<h1>Quantity Input</h1>
		</div>
	</div>
<!-- End Header page. -->

<!-- Panel Header -->
	<div class="panel panel-primary">												<!-- Row of header -->
		<div class="row">
	<!-- **************************************************************************************** Job & DATE & Step Panel ***** -->
			<div class="col-md-12">
				<div class="panel panel-default">								<!-- Panel of job & DATE & step -->
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
						</div><!-- End col of DATE -->
					</div><!-- End row of job & DATE -->

					<div class="row margin-input">							<!-- Row of step-desc -->
						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-primary disabled" type="button">* Step-Description : </button>
								</span>
								<select class="form-control" id="step">
									<option value=0 selected>Please select step-description</option>
								</select>
							</div>
						</div>
					</div><!-- End row of step-desc -->
				</div><!-- End panel of job & DATE & step -->
			</div>
		</div>
	</div><!-- End Form Header -->
<!-- End Panel Header -->

<!-- Panel Quantity -->
	<!-- ***************************************************************************************** Qty Panel ****************** -->
	<div class="panel panel-primary" id="qtyInput">								<!-- Panel of Qty -->
	<!-- ********************************************************************************* Worker & OK Qty Panel ************** -->
		<div class="panel panel-default">										<!-- Panel of Worker & OK Qty -->
			<div class="row margin-input">									<!-- Row of Worker -->
				<div class="col-md-6">
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-primary disabled" type="button">Worker : </button>
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

			<div class="row margin-input">									<!-- Row of OK Qty -->
				<div class="col-md-4">
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-primary disabled" type="button">Qty OK : </button>
						</span>
						<input type="Number" class="form-control text-center"
						placeholder="Qty OK..." name="qtyOK" id="qtyOK">
					</div>
				</div>
			</div><!-- End row of Qty OK -->
	<!-- **************************************************************************************** NG Table ******************** -->
			<div class="row">												<!-- Row of NG Qty -->
				<div class="col-md-12">
					<table class="table-bordered table-condensed table-hover table-components" id="ng">
						<thead>
							<tr class="bg-primary">
								<th class="text-center" width="40" rowspan="1">No</th>
								<th class="text-center" width="400" rowspan="1">Sub assembly</th>
								<th class="text-center" width="200" rowspan="1">Defect</th>
								<th class="text-center" width="80" rowspan="1">Quantity</th>
								<th class="text-center" width="25" rowspan="1">#</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-center td-group">1</td>
							<!-- Sub assembly. -->
								<td class="text-center td-group">
									<select class="form-control text-left" name="subAssembly[]" id="subAssembly">
										<option value=0 selected>Please select Sub Assembly</option>
										<?php 
											foreach($dsSubAssembly as $row) {
												echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
											}
										?>
									</select>
								</td>
							<!-- Defect. -->
								<td class="text-center td-group">
									<select class="form-control text-left" name="defect" id="defect">
										<option value=0 selected>Please select Defect</option>
										<?php 
											foreach($dsDefect as $row) {
												echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
											}
										?>
									</select>
								</td>
							<!-- Quantity NG. -->
								<td class="text-center td-group">
									<input class="form-control text-right text-uppercase" id="qtyNG"
									type="number" name="qtyNG[]" placeholder="Quantity NG..." value=0>
								</td>
							<!-- Add Row button. -->
								<td class="text-center">
									<button type="button" class="btn btn-default add-elements">
										<i class="fa fa-plus"></i>
									</button>
								</td>
							<!-- End Add Row button. -->
							</tr>
						</tbody>
					</table>
				</div>
			</div><!-- End row of Qty NG -->
		</div><!-- End panel of Worker & Qty -->

	<!-- ************************************************************************************ Total Qty Panel ***************** -->
		<div class="panel panel-default">										<!-- Panel of total Qty -->
			<div class="row margin-input">												<!-- Row of total Qty -->
				<div class="col-md-4">									<!-- Col of total OK Qty -->
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-primary disabled" type="button">Total OK Qty : </button>
						</span>
						<input type="text" class="form-control number-input-right"
							name="totalQtyOK" id="totalQtyOK" disabled>
					</div>
				</div>
				
				<div class="col-md-4"></div>

				<div class="col-md-4">									<!-- Col of total NG Qty -->
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-primary disabled" type="button">Total NG Qty : </button>
						</span>
						<input type="text" class="form-control number-input-right"
							name="totalQtyNG" id="totalQtyNG" disabled>
					</div>
				</div>
			</div><!-- End Row of total Qty -->
		</div><!-- End panel of total Qty -->


		<div class="row">													<!-- Row of submit & reset -->
			<div class="col-md-10">
			</div>
			<div class="col-md-2" style="margin-bottom: 4px;">
				<div class="input-group" style="margin-bottom: 4px;">
					<button type="submit" name="submit" class="btn btn-primary btn-submit" id="saveQtyInput"
					style="margin-right: 6px;">Save</button>
					<button type="button" class="btn btn-danger btn-reset" id="resetQtyInput">Reset</button>
				</div>
			</div>
		</div>

	</div><!-- End panel of qty -->
	<?php echo form_close(); ?>
<!-- End Panel Quantity -->