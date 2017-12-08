<?php echo form_open(base_url("planning/view"), array("id" => "form-search")); ?>
<div class="row top">
	<div class="col-md-12 page-header users-header">
		<div class="col-md-12">
			<h1 id="headerPage" title="">Planning</h1>
			<?php echo "<input type='hidden' id='diffStartCurrentDate' name='diffStartCurrentDate' value="
			. $diffStartCurrentDate . " />"; ?>
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
		<button type="submit" class="btn btn-primary pull-right" id="search">Go</button>
	</div>
</div>

<div class="row">
	<div class="col-md-9"></div>
	<div class="col-md-3">
		<div class="input-group">
			<span class="input-group-btn">
				<button class="btn btn-primary disabled small-input-group" type="button">Day of plan : </button>
			</span>
			<select class="form-control small-input-group" id="dayOfPlan" name="totalSlotDate">
			<?php
                for ($i = 5; $i < 41; $i += 5) {
					$selected = (($totalSlotDate == $i) ? ' selected' : '');
					echo '<option value=' . $i . $selected.'>' . $i . '</option>';
				}
			?>
			</select>
		</div>
	</div>
</div>


<div class="row">
	<div id="divPlanning" class="col-md-12">
		<table class="table table-bordered table-components table-condensed table-hover table-striped table-responsive"
			id="planning" style="width: 100%;">

		<?php
			// Header Saction.
			$totalSpanSlotDate = (($diffStartCurrentDate > 0) ? 0 :
				($diffStartCurrentDate < (($totalSlotDate - 1) * (-1))) ? $totalSlotDate :
				(1 - $diffStartCurrentDate));
			$totalCol = ($totalSpanSlotDate * 3) + $totalSlotDate + 11;
			// Header row 0
			echo '<thead class="table-header">';
			echo '<tr>';
			echo '<th id="tableHeader" class="text-center" colspan="' . $totalCol
				. '"><h4><b>Planning</b></h4></th>';
			echo '</tr>';

			// Header row 1
			echo '<tr>';
			echo '<th class="text-center" colspan="10">';
			echo '<button type="button" class="btn btn-warning text-left pull-right"'
				. 'id="previous-date">Previous</button>';
			echo '</th>';
			for ($i = 0; $i < $totalSlotDate; $i++) {
				echo '<th id="slot-' . ($i + 1) . '" class="text-center" colspan="'
					. ((($diffStartCurrentDate + $i) > 0) ? 1 : 4) . '">'
					. ($i + 1) . '</th>';
			}
			echo '<th class="text-right">';
			echo '<button type="button" class="btn btn-warning pull-right" id="next-date">Next</button>';
			echo '</th>';
			echo '</tr>';

			// Header row 2
			echo '<tr>';
			echo '<th class="text-center" rowspan="2">Job</th>';
			echo '<th class="text-center" rowspan="2">Next step</th>';
			echo '<th class="text-center" rowspan="2">Step-Description</th>';
			echo '<th class="text-center" rowspan="2">Line</th>';
			echo '<th class="text-center" rowspan="2">Machine</th>';
			echo '<th class="text-center" rowspan="2">(Sec)</th>';
			echo '<th class="text-center" colspan="4">Total</th>';
			for ($i = 0; $i < $totalSlotDate; $i++) {
				echo '<th class="text-center" colspan="'
					. ((($diffStartCurrentDate + $i) > 0) ? 1 : 4)
					. '" id="date-slot-' . ($i + 1) . '">';															// Date value
				$dateCaption = (($diffStartCurrentDate + $i) == 0) ? 'Today' : 
					(new DateTime('+' . ($diffStartCurrentDate + $i) . ' day'))->format('j-M-Y  (D)');		// Date Caption
				echo $dateCaption;
				echo '</th>';
			}
			echo '<th class="text-center" rowspan="2">Delay</th>';
			echo '</tr>';

			// Header row 3
			echo '<tr>';
			echo '<th class="text-center">Sub Assembly</th>';
			echo '<th class="text-center">Stock</th>';
			echo '<th class="text-center">OK</th>';
			echo '<th class="text-center">NG</th>';
			$elementHidden = "";
			for ($i = 0; $i < $totalSlotDate; $i++) {
				$elementHidden = ((($diffStartCurrentDate + $i) > 0) ? ' hidden' : '');
				echo '<th class="text-center">...Plan...</th>';
				echo '<th class="text-center' . $elementHidden . '" id="ngQtySlotH' . ($i + 1) . '">NG</th>';
				echo '<th class="text-center' . $elementHidden . '" id="workerQtySlotH' . ($i + 1) . '">Worker No.</th>';
				echo '<th class="text-center' . $elementHidden . '" id="totalTimeSlotH' . ($i + 1) . '">Time</th>';
			}
		echo '</tr>';

		echo '</thead>';
		?>



		<?php
		// Body Saction.
		echo '<tbody>';
		$rowSpanSub = 0;
		$dupNo = 0;
		$row;
		$elementDisabled = "";
		$elementDisplayDisabled = "";
		$elementHidden = "";
		$elementStriped = "";
		$striped = false;
	
		$iSlotDate = 0;
		$bgColor = "";
	
		for ($limit=count($dsFullPlanning), $i = 0; $i < $limit; $i++) {
			$row = $dsFullPlanning[$i];
			if ($dupNo == $rowSpanSub) {
				// Set start
				$rowSpanSub = $row['duplicatePStock'];
				$dupNo = 0;
			}
	
			echo '<tr>';
			if ($dupNo == 0) {
				echo '<td class="text-left" rowspan=' . $rowSpanSub . '>' . $row['JobName'] . '</td>';
				echo '<td class="text-left" rowspan=' . $rowSpanSub . '>' . $row['Next_Step_Number'] . '</td>';
				echo '<td class="text-left" rowspan=' . $rowSpanSub . '>' . $row['NumberAndDESC'] . '</td>';
				echo '<td class="text-left" rowspan=' . $rowSpanSub . '>' . $row['LineName'] . '</td>';
				echo '<td class="text-left" rowspan=' . $rowSpanSub . '>' . $row['MachineName'] . '</td>';
				echo '<td class="text-right" rowspan=' . $rowSpanSub . '>' . ($row['Operation_Time'] * 60) . '</td>';
			}
	
			echo '<td class="text-left bg-success">' . $row['SubAssemblyName'] . '</td>';
			echo '<td class="text-right bg-success">' . $row['stock'] . '</td>';
	
			if ($dupNo == 0) {
				echo '<td class="text-right bg-success" rowspan=' . $rowSpanSub . '>' . $row['activity_Qty_OK'] . '</td>';
				echo '<td class="text-right bg-success" rowspan=' . $rowSpanSub . '>' . $row['Qty_NG'] . '</td>';
	
				//<!-- Date Slot -->
				$iSlotDate = 0;
				$striped = false;
				for ($d = 1; $d < ($totalSlotDate + 1); $d++) {
					// $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Set planning mode & striped $$$$$$$$$$$$$$$$$
					if (($diffStartCurrentDate + $d) > 1) {
						$elementDisabled = '';
						$elementDisplayDisabled = '';
						$elementHidden = ' hidden';
						$elementStriped = ($striped ? " warning" : "");
						$attrHidden = ' style="display: none !important; overflow: hidden;"';	// Check?
						$striped = !$striped;
					} else {
						$elementDisabled = ' readonly';
						$elementDisplayDisabled = ' bg-error';
						$elementHidden = '';
						$elementStriped = '';
						$attrHidden = '';														// Check?
					}
	
					// $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Set holiday on planning mode $$$$$$$$$$$$$$$$$
					if (date('w',
					strtotime((new DateTime('+' . ($diffStartCurrentDate + $iSlotDate) . ' day'))->format('Y-m-d'))) == 1) {
						$bgColor = " bg-primary";
						$elementStriped = "";
					} else {
						$bgColor = "";
					}
					$iSlotDate++;
	
					echo '<td class="text-center' . $elementDisplayDisabled . $elementStriped . $bgColor . '"';
					echo ' rowspan="' . $rowSpanSub . '"';
					echo ' id="okQtySlot' . $d . '">';
					echo '<input type="text" class="form-control text-right" autocomplete="off"';
					echo ' id="okQtySlot' . $d . '"';
					echo ' name="okQtySlot[' . $d . ']";';
					echo ' style="font-size: 15px; font-family: monospace;"';
					echo ' placeholder="Plan..." value="' . $row['OKQtySlot' . $d] . '"' . $elementDisabled . ' />';
					echo '</td>';
	
					echo '<td class="text-center' . $elementDisplayDisabled . $elementHidden . $bgColor . '"';
					echo ' rowspan="' . $rowSpanSub . '"';
					echo ' style="font-size: 15px; font-family: monospace;"';
					echo ' id="ngQtySlot' . $d . '">';
					echo $row['NGQtySlot' . $d];
					echo '</td>';
	
					echo '<td class="text-center' . $elementDisplayDisabled . $elementHidden . $bgColor . '"';
					echo ' rowspan="' . $rowSpanSub . '"';
					echo ' id="workerQtySlot' . $d . '">';
					echo '<input type="text" class="form-control text-right" autocomplete="off"';
					echo ' id="workerQtySlot' . $d . '"';
					echo ' name="workerQtySlot[' . $d . ']";';
					echo ' style="font-size: 15px; font-family: monospace;"';
					echo ' placeholder="Machine..." value="' . $row['WorkerQtySlot' . $d] . '"' . $elementDisabled . ' />';
					echo '</td>';
	
					echo '<td class="text-center' . $elementDisplayDisabled . $elementHidden . $bgColor . '"';
					echo ' rowspan="' . $rowSpanSub . '"';
					echo ' style="font-size: 15px; font-family: monospace;"';
					echo ' id="totalTimeSlot' . $d . '">';
					echo $row['TotalTimeSlot' . $d];
					echo '</td>';
				}
	
				//<!-- Delay button -->
				echo '<td class="text-center" rowspan="' . $rowSpanSub . '">';
				echo '<button type="button" class="btn btn-danger" id="delay" value=' . $row['StockID'] . '>';
				echo '<i class="fa fa-plus"></i>';
				echo '</button>';
				echo '</td>';
			}
			echo '</tr>';
			$dupNo++;
		}
		echo '</tbody>';
		?>

		</table>
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