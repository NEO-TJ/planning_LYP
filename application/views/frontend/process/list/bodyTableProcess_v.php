<!-- Table body -->
<?php 
	$i = 1;
	foreach($dsView as $row) {
		echo ('<tr>');
			echo('<td class="text-center">' . ($pageCode + $i++) . '</td>');
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
<!-- End Table body -->