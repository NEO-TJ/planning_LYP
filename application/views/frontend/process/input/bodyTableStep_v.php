<!-- Table body -->
<?php 
	$rBtnAttr = [
		"Master"	=> ["btnClass" => "add-elements btn-default", "iClass" => "fa-plus"],
		"Child"		=> ["btnClass" => "delete-elements btn-danger", "iClass" => "fa-minus"],
	];
	$i=0;
?>
<?php foreach($dsStep as $row) { ?>
	<tr>
		<td class="text-center td-group">
			<input type="checkbox" class="form-control td-group" name="firstStepFlag[]" 
			id="firstStepFlag" value="<?php echo($row['id']);?>" 
			<?php echo(($row['First_Step_Flag']) ? " checked" : "");?> />
		</td>
		<td class="text-center td-group">
			<input type="text" class="form-control td-group" placeholder="Next Step Number..." 
			name="nextStepNumber[]" id="nextStepNumber" value="<?php echo($row['Next_Step_Number']);?>" />
		</td>
		<td class="text-center td-group">
			<input class="form-control" type="text" placeholder="Step Number..."
			name="stepNumber[]" id="stepNumber" value="<?php echo($row['Number']);?>" />
		</td>
		<td class="text-center td-group">
			<input class="form-control" type="text" placeholder="Description..." 
			name="stepDesc[]" id="stepDesc" value="<?php echo($row['DESC']);?>" />
		</td>
		<td class="text-center td-group">
			<select class="form-control text-center" name="line[]" id="line">
				<option value="0" selected>Please select line</option>
				<?php 
					foreach($dsLine as $rowLine) {
						$selected = (($rowLine['id'] == $row['FK_ID_Line']) ? ' selected' : '');
						echo '<option value='.$rowLine['id'].$selected.'>'.$rowLine['Name'].'</option>';
					}
				?>
			</select>
		</td>
		<td class="text-center td-group">
			<select class="form-control text-center" name="machine[]" id="machine">
				<option value="0" selected>Please select machine</option>
				<?php 
					foreach($dsMachine as $rowMachine) {
						$selected = (($rowMachine['id'] == $row['FK_ID_Machine']) ? ' selected' : '');
						echo '<option value='.$rowMachine['id'].$selected.'>'.$rowMachine['Name'].'</option>';
					}
				?>
			</select>
		</td>
		<td class="text-center td-group">
			<select class="form-control text-center" name="subAssemble[]" id="subAssemble">
				<option value="0" selected>Please select sub assemble</option>
				<?php 
					foreach($dsSubAssembly as $rowSubAssembly) {
						$selected = (($rowSubAssembly['id'] == $row['FK_ID_Sub_Assembly']) ? ' selected' : '');
						echo '<option value='.$rowSubAssembly['id'].$selected.'>'.$rowSubAssembly['Name'].'</option>';
					}
				?>
			</select>
		</td>
		<td class="text-center td-group">
			<input class="form-control text-center" type="number" name="nbSub[]" id="nbSub"
				title="Quantity of Sub_Assembly to make one step" placeholder="NB sub..." step="0.1"
				value="<?php echo($row['NB_Sub']);?>" />
		</td>
		<td class="text-center">
			<button type="button" class="btn <?= (($i==0) ? $rBtnAttr["Master"]["btnClass"] : $rBtnAttr["Child"]["btnClass"]) ?>">
				<i class="fa <?= (($i==0) ? $rBtnAttr["Master"]["iClass"] : $rBtnAttr["Child"]["iClass"]) ?>"></i>
			</button>
		</td>
	</tr>
<?php $i++; ?>
<?php } ?>
<!-- End Table body -->