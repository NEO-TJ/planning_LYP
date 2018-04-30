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


<div style="overflow-x:auto; overflow-y:auto;">
<?php echo form_open(base_url("process/edit"), array("id" => "formChoose")); ?>
	<table class="table table-bordered table-components table-condensed table-hover table-striped table-responsive" 
	id="view">
		<thead class="table-header">
<!-- Row header 1 -->
			<tr>
				<th class="text-center" width="40">No.</th>
				<?php 
					if(count($dsView) > 0) {
						$i=0;
						foreach($dsView[0] as $col => $value) {
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
		
		<tbody>
			<?php 
				$i = 1;
				foreach($dsView as $row) {
					echo ('<tr>');
						echo('<td class="text-center">' .$i++. '</td>');
						$j=0;
						foreach($row as $value) {
							if($j++ > 0) {
								echo('<td class="text-left">' .$value. '</td>');
							}
						}
						echo('<td class="text-center">
								<button type="submit" class="btn btn-success" id="rowID" name="rowID" value='.$row['id'].'>
									<i class="fa fa-pencil-square-o"></i>
								</button>
							</td>');
						echo('<td class="text-center">
								<button type="submit" class="btn btn-danger" id="rowID" name="rowID" value='.(-1)*$row['id'].'>
									<i class="fa fa-files-o"></i>
								</button>
							</td>');
					echo ('</tr>');
				}
			?>
		</tbody>
	</table>
<?php echo form_close(); ?><!-- Close formChoose -->
</div>

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