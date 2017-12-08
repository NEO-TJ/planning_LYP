<div class="row top">
	<div class="col-md-12 page-header users-header">
		<div class="col-md-10">
			<h1>
				<label class="pull-left"><?php echo($dataTypeName); ?></label>
			</h1>
		</div>
		
		<div class="col-md-2">
			<h1>
				<button type="button" class="btn btn-danger btn-reset pull-right"
						onclick="location.href='<?php echo(base_url());?>masterdata/view/<?php echo($dataType);?>'">
					<<--- back   
				</button>
			</h1>
		</div>
	</div>
</div>

<div class="row">															<!-- Row of input data -->
	<div class="col-md-12">
		<?php echo form_open(base_url("masterdata/save"), array("id" => "formInputData")); ?>
		<div class="panel panel-success">
			<div class="panel-heading" style="text-align: center;">
				<h3 id="panel-caption"><?php echo($inputModeName);?></h3>
			</div>
			<div class="row">