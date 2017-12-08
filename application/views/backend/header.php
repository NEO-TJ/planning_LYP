<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Start project">
		<title>Backend Page</title>

		<!-- BOOTSTRAP CSS (REQUIRED ALL PAGE)-->
        <?php echo css_asset('bootstrap.css'); ?>

		<!-- PLUGINS CSS -->
        <?php echo css_asset('../plugins/magnific-popup/magnific-popup.min.css'); ?>
        <?php echo css_asset('../plugins/chosen/chosen.min.css'); ?>
        <?php echo css_asset('../plugins/summernote/summernote.min.css'); ?>
        <?php echo css_asset('../plugins/datatable/css/bootstrap.datatable.min.css'); ?>
        <?php echo css_asset('../plugins/toastr/toastr.css'); ?>

	</head>

	<body>
		<?php
		echo anchor('login/logout','Log out');
		?>
