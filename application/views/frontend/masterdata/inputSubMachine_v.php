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
					<button class="btn btn-primary disabled" type="button">Line : </button>
				</span>
				<select class="form-control" id="lineID" name="FK_ID_Line">
					<option value="0" selected>Please select line</option>
					<?php 
						$i=1;
						foreach($dsLine as $row) {
							$selected = (($dsInput['FK_ID_Line'] == $i++) ? ' selected' : '');
							echo '<option value='.$row['id'].$selected.'>'.$row['Name'].'</option>';
						}
					?>
				</select>
			</div>
		</div>