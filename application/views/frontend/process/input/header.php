<!DOCTYPE html>
<html>
    <head>
        <?php 
	        // Meta data
        	$this->view('/frontend/headerMeta');
        ?>
        <title>LoyalPAC-Process</title>
		<?php
			// BOOTSTRAP CSS (REQUIRED ALL PAGE)
        	echo css_asset('jquery-ui.css');
        	echo css_asset('bootstrap/bootstrap.min.css');
        	echo css_asset('font-awesome.css');
			echo css_asset('sweetalert2.css');
        	echo css_asset('jquery.multiselect.css');
        	echo css_asset('jquery.multiselect.filter.css');
        	echo css_asset('prettify.css');
        	// My
        	echo css_asset('frontend.css');
        	echo css_asset('menubar.css');
        	echo css_asset('bootstrap.min.my.css');
        	echo css_asset('process/stylesheet.css');
        ?>
    </head>
    <body>
    	<?php $this->load->view('frontend/include/menubar'); ?>
        <div class="wrapper">