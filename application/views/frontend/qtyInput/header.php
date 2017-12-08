<!DOCTYPE html>
<html>
    <head>
        <?php 
	        // Meta data
        	$this->view('/frontend/headerMeta');
        ?>
        <title>LoyalPAC-Quantity Input</title>
		<?php
			// BOOTSTRAP CSS (REQUIRED ALL PAGE)
        	echo css_asset('jquery-ui.css');
			echo css_asset('bootstrap/bootstrap.min.css');
			echo css_asset('base.css');
			echo css_asset('bootstrap/prettify.css');
			echo css_asset('bootstrap/bootstrap-datetimepicker.min.css');
			echo css_asset('font-awesome.css');
			echo css_asset('sweetalert2.css');
			// My
			echo css_asset('frontend.css');
			echo css_asset('menubar.css');
			echo css_asset('bootstrap.min.my.css');
			echo css_asset('qtyInput/stylesheet.css');
 		?>
    </head>
    <body>
        <div class="wrapper">
 			<?php $this->load->view('frontend/include/menubar'); ?>