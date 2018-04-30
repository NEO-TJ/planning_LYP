<?php $this->load->view('frontend/process/input/header_v'); ?>
<?php $inputModeTheme = (($inputMode==3)?"DarkRed":(($inputMode==2)?"DarkGreen":"DarkOrange")) ?>
<!-- Open form-process -->
<?php echo form_open(base_url(), array("id" => "form-process")); ?>
<input type='hidden' id='rowID' name='rowID' value=<?=($dsProcess['id'])?>></input>

<!-- Panel of Process & Step -->
	<div class="panel panel-primary">
	<!-- Row of expand process -->
		<div class="row">
			<div class="col-md-12">
			<!-- Panel of expand process -->
				<div class="panel panel-info expand-input">
					<div class="panel-heading" style="text-align: center;">
						<h1 id="panel-caption-process">
							<b style="color:<?=$inputModeTheme?>">
								<?=$inputModeName?>
							</b>
						</h1>
					</div>
					<div class="row">
					<!-- Col of process name -->
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-primary disabled" type="button">* Process Name : </button>
								</span>								
								<input type="text" class="form-control" placeholder="Process name..." 
									name="processName" id="processName" value="<?php echo($dsProcess['Name']);?>">
							</div>
						</div>
					<!-- End col of process name -->

					<!-- Col of process Desc -->
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-primary disabled" type="button">Process Description : </button>
								</span>								
								<input type="text" class="form-control" placeholder="Process description..." 
									name="processDesc" id="processDesc" value="<?php echo($dsProcess['DESC']);?>">
							</div>
						</div>
					<!-- End col of process Desc -->
					</div>
					<div class="row">
					<!-- Col of process Desc Thai -->
						<div class="col-md-12">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-primary disabled" type="button">Process Desc Thai : </button>
								</span>								
								<input type="text" class="form-control" placeholder="Process description Thai..."
									name="processDescThai" id="processDescThai" value="<?php echo($dsProcess['DESC_Thai']);?>">
							</div>
						</div>
					<!-- End col of process Desc Thai -->
					</div>
				</div>
			<!-- End panel of expand process -->
			</div>
		</div>
	<!-- End row of expand process -->

		<hr>
	<!-- Row of Step -->
		<div class="row">
			<div class="col-md-12">
			<!-- Table of Step -->
				<table id="step" class="table table-bordered table-components "
				."table-condensed table-hover table-striped table-responsive">
					<thead class="bg-info">
						<tr>
							<th class="text-center table-caption bg-info" 
							id="step-caption" colspan="9">
								<div class="panel-heading" style="text-align: center;">
									<h1 id="panel-caption-process">
										<b style="color:<?=$inputModeTheme?>">STEP
										</b>
									</h1>
								</div>
							</th>
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
						<?php $this->load->view('frontend/process/input/bodyTableStep_v'); ?>
					</tbody>
				</table>
			<!-- End table of Step -->
			<br>
			<!-- Command button -->
				<div class="row">
					<div class="col-md-9">
					</div>
					<div class="col-md-3 pull-right">
						<button type="button" class="btn btn-danger btn-reset pull-left">Reset process</button>
						<button type="submit" class="btn btn-primary btn-submit pull-right" disabled>Update process</button>
					</div>
				</div>
			<!-- End command button -->
			<br>
			</div>
		</div>
	<!-- End row of Step -->
	</div>
<!-- End panel of Process & Step -->

<?php echo form_close(); ?>
<!-- Close form-process -->

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