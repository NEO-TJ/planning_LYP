<?php echo form_open(base_url("planning"), array("id" => "form-search")); ?>
<div class="row top">
	<div class="col-md-12 page-header users-header">
		<div class="col-md-12">
			<h1 id="headerPage" title="">Planning</h1>
			<?php echo "<input type='hidden' id='diffStartCurrentDate' value=0 />"; ?>
		</div>
	</div>
</div>

<div class="row panel panel-primary">
	<div class="col-md-7 margin-input">
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

	<div class="col-md-5 margin-input">
		<div class="input-group">
			<span class="input-group-btn">
				<button class="btn btn-primary disabled" type="button">Job type : </button>
			</span>
			<select class="form-control multi-select" id="jobTypeID" name="jobTypeID[]" multiple="multiple">
				<?php 
					foreach($dsJobType as $row) {
						echo '<option value='.$row['id'].'>'.$row['Name'].'</option>';
					}
				?>
			</select>
		</div>
	</div>



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

	<div class="col-md-1 margin-input pull-left">
		<button type="button" class="btn btn-primary pull-right" id="search">Go</button>
	</div>
</div>

<div class="row">
	<div class="col-md-7"></div>

	<div class="col-md-5">
		<div class="row panel panel-primary">
			<div class="col-md-7 margin-input">							<!-- Col of DATE -->
				<div class="input-group">
					<span class="input-group-btn">
						<button class="btn btn-primary disabled small-input-group" type="button">Date-Time : </button>
					</span>
					<div class="input-group date" id='dtsStart'>
						<input data-date-format="DD-MMM-YYYY" type="text"
						class="small-input-group" name="dtsStart"></input>
						<span class="input-group-addon small-input-group">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div><!-- End col of DATE -->

			<div class="col-md-5 margin-input">
				<div class="input-group">
					<span class="input-group-btn">
						<button class="btn btn-primary disabled small-input-group" type="button">Day of plan : </button>
					</span>
					<select class="form-control small-input-group" id="dayOfPlan">
						<option value=5>5</option>
						<option value=10 selected>10</option>
						<option value=15>15</option>
						<option value=20>20</option>
						<option value=25>25</option>
						<option value=30>30</option>
						<option value=35>35</option>
						<option value=40>40</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>



<div class="row">
	<div id="divPlanning" class="col-md-12">
	</div>
</div>
<?php echo form_close(); ?><!-- Close formSearch -->
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