<!-- Header page. -->
	<div class="row top">
		<div class="col-md-12 page-header users-header">
			<div class="col-md-10">
				<h1>
					<label class="pull-left">Process</label>
				</h1>
			</div>
			<?php echo form_open(base_url("process/addNew"), array("id" => "formAddNew")); ?>
				<div class="col-md-2">
					<h1>
						<button type="submit" class="btn btn-warning pull-right" id="dataType" name="dataType">
							Add a new
						</button>
					</h1>
				</div>
			<?php echo form_close(); ?><!-- Close formAddNew -->
		</div>
	</div>
<!-- End Header page. -->


<!-- Filter. -->
	<?php echo form_open(base_url("process"), array("id" => "form-search")); ?>
	<div class="row panel panel-primary">
		<!-- Process -->
		<div class="col-md-7 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Process : </button>
				</span>
				<select class="form-control multi-select" id="processID" name="processID[]" multiple="multiple">
					<?php 
						foreach($dsProcess as $row) {
							echo '<option value='.$row['id'].'>'.$row['Name'].'</option>';
						}
					?>
				</select>
			</div>
		</div>
		<!-- Process Status -->
		<div class="col-md-4 margin-input"><!--
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Status : </button>
				</span>
				<select class="form-control multi-select" id="processStatus" name="processStatus[]" multiple="multiple">
					<option value='1'>Enable</option>
					<option value='0'>Disable</option>
				</select>
			</div>-->
		</div>
		<!-- Search button -->
		<div class="col-md-1 margin-input pull-left">
			<button type="button" class="btn btn-primary pull-right" id="search">Go</button>
		</div>
	</div>
	<?php echo form_close(); ?><!-- Close formSearch -->
<!-- End Filter. -->


<!-- Process table result -->
	<div style="overflow-x:auto; overflow-y:auto;">
	<?php echo form_open(base_url("process/edit"), array("id" => "formChoose")); ?>
		<table class="table table-bordered table-components table-condensed table-hover table-striped table-responsive" 
		id="view">
		<!-- table head -->
			<thead class="table-header">
				<tr class="bg-primary">
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
					<th class="text-center" width="40">edit</th>
					<th class="text-center" width="40">copy</th>
				</tr>
			</thead>
		<!-- table body -->
			<tbody>
				<?php /* $this->load->view('frontend/process/list/bodyTableProcess_v'); */?>
			</tbody>
		</table>
	<!-- pagination link -->
		<div class="pagination pull-right" id="paginationLinks"> 
			<p><?php echo $paginationLinks; ?></p> 
		</div>
	<!-- end pagination link -->
	<?php echo form_close(); ?><!-- Close formChoose -->
	</div>
<!-- End Process table result -->

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