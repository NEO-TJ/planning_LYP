<!-- Table body -->
<?php 
	$i = 1;
	foreach($dsView as $row) {
		echo ('<tr>');
			echo('<td class="text-center">' . ($pageCode + $i++)
					. '<input type="text" class="hide" id="allID" value="'
					. $row['JobID'] . ',' . $row['StepID'] . ',' . $row['FirstStepFlag'] . '" />'
				. '</td>');
			echo('<td class="text-left">' . $row['JobName']
				. '</td>');
			echo('<td class="text-left">' . $row['NumberAndDesc'] . '</td>');
			echo('<td class="text-left">' . $row['SubAssemblyName'] . '</td>');
			echo('<td class="text-right" id="stockQty">' . number_format($row['StockQty'],0,',','.') . '</td>');
		echo ('</tr>');
	}
?>
<!-- End Table body -->