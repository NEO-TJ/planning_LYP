<?php echo form_open(base_url("jobRemove"), array("id" => "form-search")); ?>
<div class="row top">
	<div class="col-md-12 page-header users-header">
		<div class="col-md-10">
			<h1 id="headerPage">Remove Job</h1>
		</div>
		<div class="col-md-2">
			<h1><button type="button" class="btn btn-success pull-right" id="refresh">Refresh</button></h1>
		</div>
	</div>
</div>




<div class="row panel panel-primary">
    <div class="col-md-12 margin-input">
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
		<div class="input-group">
			<span class="input-group-btn">
				<button class="btn btn-primary disabled" type="button">Job Type : </button>
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
	<div class="col-md-5 margin-input">
		<div class="input-group">
			<span class="input-group-btn">
				<button class="btn btn-primary disabled" type="button">Job Status : </button>
			</span>
			<select class="form-control multi-select" id="jobStatusID" name="jobStatusID[]" multiple="multiple">
				<?php 
					foreach($dsJobStatus as $row) {
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
			id="jobRemove" style="width: 100%;">
			<thead>
				<tr>
					<th class="text-center"><h4><strong>Job Name</strong></h4></th>
					<th class="text-center"><h4><strong>Job Type</strong></h4></th>
					<th class="text-center"><h4><strong>Job Status</strong></h4></th>
					<th class="text-center" width="80"><h4><strong>Remove</strong></h4></th>
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