		<input type='hidden' id='dataType' name='dataType' value=<?php echo($dataType); ?>></input>
		<input type='hidden' id='rowID' name='rowID' value=<?php echo($dsInput['id']); ?>></input>
		<input type='hidden' id='baseUrl' value="<?php echo(base_url()); ?>"></input>
		
		<div class="col-md-11 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Name : </button>
				</span>
				<input type="text" class="form-control input-require" autocomplete="off"
					placeholder="Name..." id="name" name="Name" value="<?php echo($dsInput['Name']); ?>">
			</div>
		</div>
		
		<div class="col-md-11 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Description : </button>
				</span>
				<input type="text" class="form-control" autocomplete="off"
					placeholder="Description..." id="desc" name="DESC" value="<?php echo($dsInput['DESC']); ?>">
			</div>
		</div>
		
		<div class="col-md-11 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Desc Thai : </button>
				</span>
				<input type="text" class="form-control" autocomplete="off"
					placeholder="Description Thai..." id="descThai" name="DESC_Thai" value="<?php echo($dsInput['DESC_Thai']); ?>">
			</div>
		</div>
		
		<div class="col-md-11 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Unit : </button>
				</span>
				<select class="form-control" id="unitID" name="FK_ID_Unit">
					<option value="0" selected>Please select unit</option>
					<?php 
						$i=1;
						foreach($dsUnit as $row) {
							$selected = (($dsInput['FK_ID_Unit'] == $i++) ? ' selected' : '');
							echo '<option value='.$row['id'].$selected.'>'.$row['Name'].'</option>';
						}
					?>
				</select>
			</div>
		</div>