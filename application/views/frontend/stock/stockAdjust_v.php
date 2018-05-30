<!-- Header page. -->
	<?php echo form_open(base_url("stockAdjust"), array("id" => "form-search")); ?>
	<div class="row top">
		<div class="col-md-12 page-header users-header">
			<div class="col-md-10">
				<h1 id="headerPage">Adjust Stock</h1>
			</div>
			<div class="col-md-2">
				<h1><button type="button" class="btn btn-success pull-right" id="refresh">Refresh</button></h1>
			</div>
		</div>
	</div>
<!-- End Header page. -->


<!-- Filter. -->
	<div class="row panel panel-primary">
	<!-- Job -->
		<div class="col-md-5 margin-input">
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
	<!-- Step -->
		<div class="col-md-6 margin-input">
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
	<!-- Button -->
		<div class="col-md-1 margin-input pull-left">
			<button type="button" class="btn btn-primary pull-right" id="search">Go</button>
		</div>
	<!-- End Button -->
	</div>
	<?php echo form_close(); ?><!-- Close form-search -->
<!-- End Filter. -->


<hr>
<!-- Stock adjust table result -->
	<div class="row">
	<!-- Table -->
		<div class="col-md-12">
			<table class="table table-bordered table-components table-condensed table-hover table-striped table-responsive" 
				id="stockAdjust" style="width: 100%;">
			<!-- table head -->
				<thead>
					<tr class="bg-primary">
						<th class="text-center"><strong>No.</strong></th>
						<th class="text-center"><strong>Job</strong></th>
						<th class="text-center"><strong>Step-Description</strong></th>
						<th class="text-center"><strong>Sub Assembly</strong></th>
						<th class="text-center"><strong>Stock Quantity</strong></th>
					</tr>
				</thead>
			<!-- table body -->
				<tbody>
				</tbody>
			</table>
		<!-- pagination link -->
			<div class="pagination pull-right" id="paginationLinks"> 
				<p><?php echo $paginationLinks; ?></p> 
			</div>
		<!-- end pagination link -->
		</div>
	</div>
<!-- End Stock adjust table result -->
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