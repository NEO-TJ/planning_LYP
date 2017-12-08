<center>
	<h1>LoyalPAC - Planning</h3>
</center>

<?php 
if ($this->session->flashdata ( 'msg' )) { 
?>

	<div class="alert alert-info alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert">x</button>
	    <?php echo $this->session->flashdata('msg'); ?>
	</div>

<?php
} // end if msg
?>

<div class="row center-box">
	<div class="col-md-3">
		<div class="panel panel-warning">
			<div class="panel-heading" align="center">Log in</div>
			<div class="panel-body">
                <?php echo form_open ( 'login/validate' ); ?>
                    <div class="form-group">
						<label for="text">User ID</label> 
						<input type="text" id="userID" name="userID">
					</div>					
					<!-- /.form-group -->
					
					<div class="form-group">
						<label for="password">Password</label>
						<input type="password" id="password" name="password" autocomplete="off">
					</div>
					<!-- /.form-group -->
					
					<div class="form-group">
						<input type="submit" class="btn btn-warning" value="Log in">
					</div>
					<!-- /.form-group -->
					
				<?php echo form_close(); ?>
			</div>
			<!-- /.panel-body -->
			
		</div>
		<!-- /.panel -->
		
	</div>
	<!-- /.col-md-3 -->
	
	<div class="col-md-8"></div>
	<!-- /.col-md-8 -->
	
</div>
<!-- /.row -->
