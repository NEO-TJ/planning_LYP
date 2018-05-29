		</div><!-- .wrapper -->
		<!--
		===========================================================
		END PAGE masterdata
		===========================================================
		-->
		
 		<?php 
			echo js_asset('jquery.min.js');
			echo js_asset('jquery-ui.min.js');
			echo js_asset('jquery.blockUI.js');
			echo js_asset('bootstrap/bootstrap.min.js');
			echo js_asset('sweetalert2.min.js');

			echo js_asset('moment.min.js');
			echo my_js_asset("plugins/jquery-daterangepicker/js/jquery.daterangepicker.min.js");
			echo js_asset('jquery.multiselect.js');
			echo js_asset('jquery.multiselect.filter.js');
			echo js_asset('bootstrap/prettify.min.js');
			echo js_asset('bootstrap/base.js');

			echo js_asset('frontend.js');
			echo js_asset('my.helper.js');
			echo js_asset('extent/initialDaterange.js');
			echo js_asset('extent/commonPagination.js');

			echo js_asset('activityRevoke/commonJobStepLine.js');
			echo js_asset('activityRevoke/filterRenderActRecoveryNG.js');
			echo js_asset('activityRevoke/deleteActRecoveryNG.js');
		?>
	</body>
</html>