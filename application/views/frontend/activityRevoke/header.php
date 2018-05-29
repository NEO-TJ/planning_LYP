<!DOCTYPE html>
<html>
    <head>
        <?php 
	        // Meta data
        	$this->view('/frontend/headerMeta');
        ?>
        <title>LoyalPAC-Quantity Input</title>
		<?php
			// Plugin.
			echo css_asset('jquery-ui.css');
			echo css_asset('bootstrap/bootstrap.min.css');
			echo css_asset('base.css');
			echo css_asset('bootstrap/prettify.css');
			echo my_css_asset("plugins/jquery-daterangepicker/css/daterangepicker.min.css");
			echo css_asset('jquery.multiselect.css');
			echo css_asset('jquery.multiselect.filter.css');
			echo css_asset('font-awesome.css');
			echo css_asset('sweetalert2.css');
			// My
			echo css_asset('frontend.css');
			echo css_asset('menubar.css');
			echo css_asset('bootstrap.min.my.css');
			echo css_asset('qtyInput/stylesheet.css');
		    echo css_asset("extent/my-daterangepicker.css");
 		?>
    </head>
    <body>
        <div class="wrapper" id="docTopBody">
 			<?php $this->load->view('frontend/include/menubar'); ?>