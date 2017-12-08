<!DOCTYPE html>
<html lang="en">
    <head>
        <?php 
	        // Meta data
        	$this->view('/frontend/headerMeta');
        ?>
        <title>LoyalPAC-Planning</title>
		<?php
			// BOOTSTRAP CSS (REQUIRED ALL PAGE)
			echo css_asset('bootstrap/bootstrap.min.css');
			echo css_asset('font-awesome.css');
			// My
    	    echo css_asset('frontend.css');
//			echo css_asset('menubar.css');
			echo css_asset('login/stylesheet.css');
        ?>
    </head>
    <body>
        <div class="wrapper">
        <br><br>
