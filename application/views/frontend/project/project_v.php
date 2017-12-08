<div class="row top">
	<div class="col-md-12 page-header users-header">
		<h1>Project</h1>
		<?php echo "<input type='hidden' name='arrBomRmID' id='arrBomRmID' value='None' />"; ?>
	</div>
</div>

 <div class="panel panel-primary">												<!-- Panel of header -->
	<div class="row">
		<div class="col-md-12">
<!-- ************************************************************************************** Project Panel ***************** -->
			<div class="panel panel-default">								<!-- Panel of project -->
				<div class="row margin-input">							<!-- Row of project -->
					<div class="col-md-7">							<!-- Col of project -->
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">* Project : </button>
							</span>
							<select class="form-control" id="project">
								<option value="0" selected>Please select project</option>
								<?php 
									foreach($dsProject as $row) {
										echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
									}
								?>
							</select>
						</div>
					</div><!-- End col of project -->
					
					<div class="col-md-2 pull-left">
						<button type="button" class="btn btn-success pull-left" id="add-edit-project"
							data-toggle="collapse" data-target="#collapse-project">[New-Edit] : project</button>
					</div>
				</div><!-- End row of project -->
<!-- ** Expand Project Panel ********************************************************************* -->
				<div class="row collapse" id="collapse-project">		<!-- Row of expand project -->
					<div class="col-md-12">
						<?php echo form_open(base_url(), array("id" => "form-project")); ?>
						<div class="panel panel-success expand-input">
							<div class="panel-heading" style="text-align: center;">
								<h3 id="panel-caption-project"></h3>
							</div>
							<div class="row">
								<div class="col-md-6">				<!-- Col of project name -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* Project Name : </button>
										</span>								
										<input type="text" class="form-control"
											placeholder="Project name..." id="projectName" name="projectName">
									</div>
								</div><!-- End col of project name -->
								
								<div class="col-md-6">				<!-- Col of customer -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* Customer : </button>
										</span>
										<select class="form-control" id="customer" name="customer">
											<option value="0" selected>Please select customer</option>
											<?php 
												foreach($dsCustomer as $row) {
													echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
												}
											?>
										</select>
									</div>
								</div><!-- End col of customer -->
							</div>
							
							<div class="row">
								<div class="col-md-10">
								</div>
								<div class="col-md-1 pull-left">
									<button type="button" class="btn btn-danger btn-reset pull-right">Reset project</button>
								</div>
								<div class="col-md-1 pull-right">
									<button type="submit" class="btn btn-primary btn-submit pull-right">Save project</button>
								</div>
							</div>
						</div><!-- End panel of expand project -->
						<?php echo form_close(); ?><!-- Close form-project -->
					</div>
				</div><!-- End row of expand project -->
			</div><!-- End panel of project -->
			
			
<!-- ************************************************************************************** Job Panel ***************** -->
			<div class="panel panel-default">								<!-- Panel of job -->
				<div class="row margin-input">							<!-- Row of job -->
					<div class="col-md-7">							<!-- Col of job -->
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">* Job : </button>
							</span>
							<select class="form-control" id="job">
								<option value="0" selected>Please select Job</option>
								<?php 
									foreach($dsJob as $row) {
										echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
									}
								?>
							</select>
						</div>
					</div><!-- End col of job -->
					
					<div class="col-md-2 pull-left">
						<button type="button" class="btn btn-success pull-left" id="add-edit-job"
							data-toggle="collapse" data-target="#collapse-job">[New-Edit] : Job</button>
					</div>
				</div><!-- End row of Job -->
<!-- ** Expand job Panel ********************************************************************* -->
				<div class="row collapse" id="collapse-job">			<!-- Row of expand job -->
					<div class="col-md-12">
						<?php echo form_open(base_url(), array("id" => "form-job")); ?>
						<div class="panel panel-success expand-input">
							<div class="panel-heading" style="text-align: center;">
								<h3 id="panel-caption-job"></h3>
							</div>
							<div class="row">
								<div class="col-md-6">				<!-- Col of job name -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* Job Name : </button>
										</span>								
										<input type="text" class="form-control"
											placeholder="Job name..." name="jobName" id="jobName">
									</div>
								</div><!-- End col of job name -->
								
								<div class="col-md-6">				<!-- Col of job type -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">Job Type : </button>
										</span>
										<select class="form-control" id="jobType">
											<option value="0" selected>Please select job type</option>
											<?php 
												foreach($dsJobType as $row) {
													echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
												}
											?>
										</select>
									</div>
								</div><!-- End col of job type -->
							</div>

							<div class="row">
								<div class="col-md-4">				<!-- Col of qty order -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* Quantity order : </button>
										</span>								
										<input type="number" class="form-control"
											placeholder="Quantity order..." name="qtyOrder" id="qtyOrder">
									</div>
								</div><!-- End col of qty order -->
								
								<div class="col-md-4">				<!-- Col of qty plan -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* Quantity plan : </button>
										</span>								
										<input type="number" class="form-control"
											placeholder="Quantity plan..." name="qtyPlanProduct" id="qtyPlanProduct">
									</div>
								</div><!-- End col of qty plan -->
								
								<div class="col-md-4">				<!-- Col of status -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">Status : </button>
										</span>								
										<select class="form-control" id="jobStatus">
											<option value="0" selected>Please select job status</option>
											<?php 
												foreach($dsJobStatus as $row) {
													echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
												}
											?>
										</select>
									</div>
								</div><!-- End col of status -->
							</div>
							
							<div class="row">
								<div class="col-md-10">
								</div>
								<div class="col-md-1 pull-left">
									<button type="button" class="btn btn-danger btn-reset pull-right">Reset job</button>
								</div>
								<div class="col-md-1 pull-right">
									<button type="submit" class="btn btn-primary btn-submit pull-right">Save job</button>
								</div>
							</div>
						</div><!-- End panel of expand job -->
						<?php echo form_close(); ?><!-- Close form-job -->
					</div>
				</div><!-- End row of expand job -->


				<div class="row margin-input">							<!-- Row of BOM -->
					<div class="col-md-7">							<!-- Col of BOM -->
						<div class="input-group">
							<span class="input-group-btn">
								<button class="btn btn-primary disabled" type="button">* BOM : </button>
							</span>
 							<select class="form-control" id="bom">
 								<option value="0" selected>Please select BOM</option>
								<?php 
									foreach($dsBOM as $row) {
										echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
									}
								?>
 							</select>
						</div>
					</div><!-- End col of BOM -->
						
						<div class="col-md-2 pull-left">
 							<button type="button" class="btn btn-success pull-left" id="add-edit-bom"
								data-toggle="collapse" data-target="#collapse-bom">[New-Edit] : BOM</button>
						</div>
					</div><!-- End row of BOM -->
<!-- ** Expand BOM Panel ********************************************************************* -->
				<div class="row collapse" id="collapse-bom">			<!-- Row of expand BOM -->
					<div class="col-md-12">
						<?php echo form_open(base_url(), array("id" => "form-bom")); ?>
						<div class="panel panel-success expand-input">
							<div class="panel-heading" style="text-align: center;">
								<h3 id="panel-caption-bom"></h3>
							</div>
							<div class="row">
								<div class="col-md-6">				<!-- Col of BOM name -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">* BOM Name : </button>
										</span>								
										<input type="text" class="form-control"
											placeholder="BOM name..." name="bomName" id="bomName">
									</div>
								</div><!-- End col of BOM name -->

								<div class="col-md-6">				<!-- Col BOM Desc -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">BOM Description : </button>
										</span>								
										<input type="text" class="form-control"
											placeholder="BOM description..." name="bomDesc" id="bomDesc">
									</div>
								</div><!-- End col of BOM Desc -->
							</div>
							
							<div class="row">
								<div class="col-md-12">				<!-- Col of BOM Desc Thai -->
									<div class="input-group">
										<span class="input-group-btn">
											<button class="btn btn-primary disabled" type="button">BOM Desc Thai : </button>
										</span>								
										<input type="text" class="form-control"
											placeholder="BOM description Thai..." name="bomDescThai" id="bomDescThai">
									</div>
								</div><!-- End col of BOM Desc Thai -->
							</div>
							
							<div class="row">
								<div class="col-md-12">				<!-- Col of BOM_RM -->
									<table class="table table-bordered table-condensed table-hover table-components" id="bom_rm">
										<thead>
											<tr class="active">
												<th class="text-center" width="40">No.</th>
												<th class="text-center" width="500">* Raw material</th>
												<th class="text-center" width="100">* Qty /1000</th>
												<th class="text-center" width="100">Unit</th>
												<th class="text-center" width="36">#</th>
											</tr>
										</thead>
										<tbody>
											<tr class="danger">
												<td class="text-center td-group">1</td>
												<td class="text-center td-group">
													<select class="form-control text-center" name="rm[]" id="rm">
						 								<option value="0" selected>Please select Raw material</option>
														<?php 
															foreach($dsRM as $row) {
																echo '<option value="'.$row['id'].'">'
																		.$row['Name'] ." - " .$row['DESC']
																	.'</option>';
															}
														?>
													</select>
												</td>
												<td class="text-center td-group">
													<input class="form-control text-center" type="number" name="qty[]" id="qty"
														placeholder="Quantity / 1000..." title="Quantity per 1000">
												</td>
												<td class="text-center td-group"> - </td>
												<td class="text-center">
													<button type="button" class="btn btn-default add-elements">
														<i class="fa fa-plus"></i>
													</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!-- End col of BOM_RM -->
							</div>

							<div class="row">
								<div class="col-md-10">
								</div>
								<div class="col-md-1 pull-left">
									<button type="button" class="btn btn-danger btn-reset pull-right">Reset BOM</button>
								</div>
								<div class="col-md-1 pull-right">
									<button type="submit" class="btn btn-primary btn-submit pull-right">Save BOM</button>
								</div>
							</div>
						</div><!-- End panel of expand bom -->
						<?php echo form_close(); ?><!-- Close form-bom -->
					</div>
				</div><!-- End row of expand bom -->
			</div><!-- End panel of Job -->
			
		</div>
	</div>
</div><!-- End panel of Header -->




<!-- ********************************************************************************* Process & Step Panel ************ -->
<div class="panel panel-primary PrintArea">										<!-- Panel of Process & Step -->


<!-- ************************************************************************************** Process Panel ***************** -->
	<div class="panel panel-default">										<!-- Panel of process -->
		<div class="row margin-input">									<!-- Row of process -->
			<div class="col-md-7">									<!-- Col of process -->
				<div class="input-group">
					<span class="input-group-btn">
						<button class="btn btn-primary disabled" type="button">* Process : </button>
					</span>
					<select class="form-control" id="process">
						<option value="0" selected>Please select process</option>
						<?php 
							foreach($dsProcess as $row) {
								echo '<option value="'.$row['id'].'">'.$row['Name'].'</option>';
							}
						?>
					</select>
				</div>
			</div><!-- End col of process -->
			
			<div class="col-md-1 pull-left">
				<button type="button" class="btn btn-success pull-left" id="add-edit-process"
					data-toggle="collapse" data-target="#collapse-process">[New-Edit] : Process</button>
			</div>
			<div class="col-md-1 pull-left"></div>
			<div class="col-md-3 pull-left">
				<button type="button" class="btn btn-info pull-right" id="print-process">Print Process</button>
				<button type="button" class="btn btn-warning pull-right" id="clone-process"
					data-toggle="collapse" data-target="#collapse-process">[Clone] - Process</button>
			</div>
		</div><!-- End row of process -->
<!-- ** Expand process Panel ********************************************************************* -->
		<div class="row collapse" id="collapse-process">				<!-- Row of expand process -->
			<div class="col-md-12">
				<?php echo form_open(base_url(), array("id" => "form-process")); ?>
				<div class="panel panel-success expand-input" id="panel-expand-process">
					<div class="panel-heading" style="text-align: center;">
						<h3 id="panel-caption-process"></h3>
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

					<div class="row">
						<div class="col-md-9">
						</div>
						<div class="col-md-3 pull-right">
							<button type="button" class="btn btn-danger btn-reset pull-left">Reset process</button>
							<button type="submit" class="btn btn-primary btn-submit pull-right">Update process</button>
						</div>
					</div>
				</div><!-- End panel of expand process -->
				<?php echo form_close(); ?><!-- Close form-process -->
			</div>
		</div><!-- End row of expand process -->
	</div><!-- End panel of process -->
	
<!-- %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Step Table %%%%%%%%%%%%%%%%% -->
	<div class="row">													<!-- Row of Step -->
		<div class="col-md-12">
			<?php echo form_open(base_url(), array("id" => "form-step")); ?>
			<table id="step"
			class="table table-bordered table-components table-condensed table-hover table-striped table-responsive">
				<thead class="bg-info">
					<tr>
						<th class="text-center table-caption bg-info" id="step-caption" colspan="10">STEP</th>
					</tr>
					<tr>
						<th class="text-center" width="40">First Step</th>
						<th class="text-center" width="100">Next Step</th>
						<th class="text-center" width="100">* Step</th>
						<th class="text-center" width="420">Description</th>
						<th class="text-center" width="200">* Line</th>
						<th class="text-center" width="200">* Machine</th>
						<th class="text-center" width="100">* (Sec)</th>
						<th class="text-center" width="200">* Sub assembly</th>
						<th class="text-center" width="100">* NB sub</th>
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
							<input class="form-control text-center" type="number" name="operationTime[]" id="operationTime"
								placeholder="Time operation...">
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
			<?php echo form_close(); ?><!-- Close form-step -->
			
		</div>
	</div><!-- End row of Step -->
</div><!-- End panel of Process & Step -->





<?php echo form_open(base_url(), array("id" => "form-all")); ?>
<div class="row">
	<div class="col-md-10">
	</div>
	<div class="col-md-1 pull-left">
		<button type="button" class="btn btn-danger btn-reset pull-right">Reset ALL</button>
	</div>
	<div class="col-md-1 pull-right">
		<button type="submit" class="btn btn-primary btn-submit pull-right">Save ALL</button>
	</div>
</div>
<?php echo form_close(); ?><!-- Close form-all -->