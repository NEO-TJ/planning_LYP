<?php echo form_open(base_url("workingCapacityReport"), array("id" => "form-search")); ?>
<div class="row top">
	<div class="col-md-12 page-header users-header">
		<div class="col-md-10">
			<h1 id="headerPage">Working Capacity Report</h1>
		</div>
		<div class="col-md-2">
			<h1><button type="button" class="btn btn-success pull-right" id="refresh">Refresh</button></h1>
		</div>
	</div>
</div>




<div class="row panel panel-primary">
    <div class="col-md-6 margin-input">
		<div class="input-group">
			<span class="input-group-btn">
				<button class="btn btn-primary disabled" type="button">Customer : </button>
			</span>
			<select class="form-control multi-select" id="customerID" name="customerID[]" multiple="multiple">
				<?php 
					foreach($dsCustomer as $row) {
						echo '<option value='.$row['id'].'>'.$row['Name'].'</option>';
					}
				?>
			</select>
		</div>
	</div>
    <div class="col-md-6 margin-input">
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
	<div class="col-md-5 margin-input">
		<div class="input-group" id="subAssemblyID">
			<span class="input-group-btn">
				<button class="btn btn-primary disabled" type="button">Sub Assembly : </button>
			</span>
			<select class="form-control multi-select" id="subAssemblyID" name="subAssemblyID[]" multiple="multiple">
				<?php 
					foreach($dsSubAssembly as $row) {
						echo '<option value='.$row['id'].'>'.$row['Name'].'</option>';
					}
				?>
			</select>
		</div>
	</div>
	<div class="col-md-1 margin-input pull-left">
		<button type="button" class="btn btn-primary pull-right" id="search">Go</button>
	</div>
	
</div>
<?php echo form_close(); ?><!-- Close form-search -->


<hr>
<div class="row">
<!-- Table -->
	<div class="col-md-12">
		<table class="table table-bordered table-components table-condensed table-hover table-striped table-responsive" 
			id="workingCapacityReport" style="width: 100%;">
			<thead>
<!-- Row header 0 -->
				<tr>
					<th class="header-border-report" rowspan="2"><h4><strong>Customer</strong></h4></th>
					<th class="header-border-report" rowspan="2"><h4><strong>Job</strong></h4></th>
					<th class="header-border-report" rowspan="2"><h4><strong>Line</strong></h4></th>
					<th class="header-border-report" rowspan="2"><h4><strong>Sub Assembly</strong></h4></th>
					<th class="header-border-report" rowspan="2"><h4><strong>Time (SEC)</strong></h4></th>
					
					<th class="header-border-report" rowspan="1" colspan="2"><h4><strong>Week 1</strong></h4></th>
					<th class="header-border-report" rowspan="1" colspan="2"><h4><strong>Week 2</strong></h4></th>
					<th class="header-border-report" rowspan="1" colspan="2"><h4><strong>Week 3</strong></h4></th>
					<th class="header-border-report" rowspan="1" colspan="2"><h4><strong>Week 4</strong></h4></th>
					<th class="header-border-report" rowspan="1" colspan="2"><h4><strong>Week 5</strong></h4></th>
					<th class="header-border-report" rowspan="1" colspan="2"><h4><strong>Week 6</strong></h4></th>
					<th class="header-border-report" rowspan="1" colspan="2"><h4><strong>Week 7</strong></h4></th>
				</tr>
				<tr>
					<th class="header-border-report"><h4><strong>Qty</strong></h4></th>
					<th class="header-border-report"><h4><strong>hr</strong></h4></th>

					<th class="header-border-report"><h4><strong>Qty</strong></h4></th>
					<th class="header-border-report"><h4><strong>hr</strong></h4></th>

					<th class="header-border-report"><h4><strong>Qty</strong></h4></th>
					<th class="header-border-report"><h4><strong>hr</strong></h4></th>

					<th class="header-border-report"><h4><strong>Qty</strong></h4></th>
					<th class="header-border-report"><h4><strong>hr</strong></h4></th>

					<th class="header-border-report"><h4><strong>Qty</strong></h4></th>
					<th class="header-border-report"><h4><strong>hr</strong></h4></th>

					<th class="header-border-report"><h4><strong>Qty</strong></h4></th>
					<th class="header-border-report"><h4><strong>hr</strong></h4></th>

					<th class="header-border-report"><h4><strong>Qty</strong></h4></th>
					<th class="header-border-report"><h4><strong>hr</strong></h4></th>
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