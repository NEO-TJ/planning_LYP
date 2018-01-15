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
					<button class="btn btn-primary disabled" type="button">User ID : </button>
				</span>
				<input type="text" class="form-control input-require" autocomplete="off"
					placeholder="User ID..." id="userID" name="User_ID" value="<?php echo($dsInput['User_ID']); ?>">
			</div>
		</div>
		
		<div class="col-md-11 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Password : </button>
				</span>
				<input type="text" class="form-control input-require" autocomplete="off" 
					readonly onfocus="this.removeAttribute('readonly');"
					placeholder="Password..." id="password" name="Password" value="<?php echo($dsInput['Password']); ?>">
			</div>
		</div>
		
		<div class="col-md-11 margin-input">
			<div class="input-group" id="userLineID">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Line : </button>
				</span>
				<select class="form-control multi-select input-require-multi-select"
				id="userLineID" name="FK_ID_Line[]" multiple="multiple">
					<?php 
						foreach($dsLine as $row) {
							$selected = (in_array($row['id'], $dsInputLineId) ? ' selected' : '');
							echo '<option value='.$row['id'].$selected.'>'.$row['Name'].'</option>';
						}
					?>
				</select>
			</div>
		</div>
		
		<div class="col-md-11 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Level : </button>
				</span>
				<select class="form-control" id="levelID" name="Level">
					<?php $i=1; ?>
					<option value="1"<?php echo(($dsInput['Level'] == $i++) ? ' selected' : ''); ?>>Admin</option>
					<option value="2"<?php echo(($dsInput['Level'] == $i++) ? ' selected' : ''); ?>>Supervisor/Engineer</option>
					<option value="3"<?php echo(($dsInput['Level'] == $i++) ? ' selected' : ''); ?>>Staff</option>
				</select>
			</div>
		</div>

		<div class="col-md-11 margin-input">
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary disabled" type="button">Status : </button>
				</span>
				<select class="form-control" id="statusID" name="Status">
					<?php $i=0; ?>
					<option value="0"<?php echo(($dsInput['Status'] == $i++) ? ' selected' : ''); ?>>Active</option>
					<option value="1"<?php echo(($dsInput['Status'] == $i++) ? ' selected' : ''); ?>>Terminate</option>
				</select>
			</div>
		</div>