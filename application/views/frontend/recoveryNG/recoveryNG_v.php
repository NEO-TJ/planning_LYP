<!-- Header page. -->
	<?php $attributes = array("id" => "formRecoveryNG"); ?>
	<?php echo form_open(base_url(), $attributes); ?>	<!-- 'issue/ajax_save/?action=add' -->
	<div class="row top">
		<div class="col-md-12 page-header users-header">
			<h1>Recovery NG</h1>
		</div>
	</div>
<!-- End Header page. -->

<!-- Panel Header -->
	<div class="panel panel-primary">												<!-- Row of header -->
		<div class="row">
	<!-- **************************************************************************************** Job & DATE & Worker Panel ***** -->
			<div class="col-md-12">
				<div class="panel panel-default">								<!-- Panel of job & DATE & Worker -->
					<div class="row margin-input">							<!-- Row of job & DATE -->
						<div class="col-md-8">							<!-- Col of job number -->
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
						<div class="col-md-4">							<!-- Col of DATE -->
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
<!-- End Panel Header -->

<!-- Panel Quantity -->
	<!-- ***************************************************************************************** Recovery NG Panel ********** -->
	<div class="panel panel-primary">																	<!-- Row of Recovery -->
		<div class="row">
	<!-- ********************************************************************************* Source Panel *********************** -->
			<div class="col-md-12">																		<!-- Row of Source -->
				<div class="panel panel-primary">													<!-- Panel of source -->
					<div class="row">
						<div class="col-md-12">													<!-- Row of Source -->
							<div class="panel panel-default">

								<div class="row margin-input">								<!-- Row of Step-desc source -->
									<div class="col-md-12">
									<!-- *************************************************************** Step source Table ******************** -->
										<div class="row">												<!-- Row of Source -->
											<div class="col-md-12">
												<table class="table table-sm table-bordered table-condensed table-hover"
													. " table-components table-responsive" id="sourceStepTable">
												<caption>Source Step</caption>
													<thead class="bg-primary">
														<tr>
															<th class="text-center" width="65%" rowspan="1">Source Step</th>
															<th class="text-center" width="19%" rowspan="1">Sub assembly</th>
															<th class="text-center" width="8%" rowspan="1">Total Qty NG</th>
															<th class="text-center" width="8%" rowspan="1">Send NG</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td class="text-center td-group">
																<select class="form-control text-center textLeft" name="sourceStep" id="sourceStep">
																	<option value=0 selected>Please select source step-description...</option>
																</select>
															</td>
															<td class="text-center td-group">
																<input class="form-control text-center textLeft" id="sourceSubAssembly"
																type="text" name="sourceSubAssembly[]" disabled>
															</td>
															<td class="text-center td-group">
																<input class="form-control text-center textRight" id="sourceQtyNG"
																type="text" name="sourceQtyNG" disabled>
															</td>
															<td class="text-center td-group">
																<input class="form-control text-center textRight" id="qtyNGSend"
																type="number" name="qtyNGSend">

																<input class="form-control text-center textRight hidden" id="sourceNbSub"
																type="number" name="sourceNbSub">
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									<!-- *************************************************************** End Step source Table **************** -->
									</div>
								</div>

							</div>
						</div><!-- End row of Source -->
					</div>				
				</div>
			</div>
	<!-- ********************************************************************************* Button send Panel ****************** -->
			<div class="col-md-5">																<!-- Row of button send NG -->
			</div>
			<div class="col-md-2">
				<div class="panel panel-primary input-group">
					<i class="fa fa-arrow-down" aria-hidden="true"></i>
					<button type="submit" class="btn btn-success btn-submit" id="sendQtyNG">
						Send
					</button>
					<i class="fa fa-arrow-down" aria-hidden="true"></i>
				</div>
			</div>
			<div class="col-md-5">
			</div>
	<!-- ********************************************************************************* Destination Panel ****************** -->
			<div class="col-md-12">																		<!-- Row of destination -->
				<div class="panel panel-primary">													<!-- Panel of destination -->
					<div class="row">
						<div class="col-md-12">													<!-- Row of destination -->
							<div class="panel panel-default">

								<div class="row margin-input">							<!-- Row of Step-desc destination -->
									<div class="col-md-12">
									<!-- *************************************************************** Step destination Table ******************** -->
										<div class="row">												<!-- Row of Source -->
											<div class="col-md-12">
												<caption>Destination Step</caption>
												<select class="form-control text-center textLeft" name="destinationStep" id="destinationStep">
													<option value=0 selected>Please select destination step-description...</option>
												</select>
												<br>
												<table class="table table-sm table-bordered table-condensed"
													. " table-components table-responsive" id="destinationStepTable">
													<thead class="bg-primary">
														<tr>
															<th class="text-center" width="2%" rowspan="1"></th>
															<th class="text-center" width="63%" rowspan="1">Destination Stock</th>
															<th class="text-center" width="27%" rowspan="1">Sub assembly</th>
															<th class="text-center" width="8%" rowspan="1">Receive NG</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td class="text-center td-group">
																<input type="radio" class="form-control td-group" id="destinationCheck"
																type="text" name="destinationCheck[]" value=0  />
															</td>
															<td class="text-center td-group">
																<input class="form-control text-center textLeft" id="destinationStock"
																type="text" name="destinationStock[]" value="" disabled>
															</td>
															<td class="text-center td-group">
																<input class="form-control text-center textLeft" id="destinationSubAssembly"
																type="text" name="destinationSubAssembly[]" value="" disabled>
															</td>
															<td class="text-center td-group">
																<input class="form-control text-center textRight" id="receiveNgQty"
																type="number" name="receiveNgQty[]" value="" disabled>

																<input class="form-control text-center textRight hidden" id="destinationNbSub"
																type="number" name="destinationNbSub[]" value=1>
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									<!-- *************************************************************** End Step source Table **************** -->
									</div>
								</div>
								
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
<!-- End Panel Quantity -->