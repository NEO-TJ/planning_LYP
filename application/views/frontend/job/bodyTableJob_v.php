<!-- Table body -->
<?php 
	$i = 1;
	foreach($dsView as $row) {
		echo ('<tr>');
			echo('<td class="text-center">' . ($pageCode + $i++) . '</td>');
			echo('<td class="text-left">' . $row['JobName'] . '</td>');
			echo('<td class="text-left">' . $row['JobTypeName'] . '</td>');
			echo('<td class="text-left">' . $row['JobStatusName'] . '</td>');
			
			echo('<td class="text-center">');
				echo('<button type="button" class="btn btn-danger" id="remove" value=' . $row['JobID'] . '>');
					echo('<i class="fa fa-minus"></i>');
				echo('</button>');
			echo('</td>');
		echo ('</tr>');
	}
?>
<!-- End Table body -->