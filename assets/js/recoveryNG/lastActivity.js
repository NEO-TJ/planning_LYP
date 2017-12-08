// ************************************************ Event **********************************************
//------------------------------------------------- Step -----------------------------------------------
$('table#lastActivity').on("click", ".delete-elements", deleteActivity);


// ********************************************** Method ***********************************************
//----------------------------------------------- Delete -----------------------------------------------
function deleteActivity(){
	var currentTr = $(this).closest("tr");
	var activityID = currentTr.find('button#activityID').val();
	var stockID = currentTr.find('input#stockID').val();
	var qtyNG = currentTr.find('td:nth-child(6)').html();
	qtyNG = (isEmpty(qtyNG) ? 0 : qtyNG);

	if(validateID(activityID, stockID)) {
		ajaxDeleteActivity(activityID, stockID, qtyNG);
	} else {
		showDialog(dltValidate);
	}
}
function ajaxDeleteActivity(activityID, stockID, qtyNG){
	if(validateID(activityID, stockID)) {
		var data = {
				'activityID': activityID,
				'stockID': stockID,
				'qtyNG': qtyNG,
				};

		// Get process table one row by ajax.
		$.ajax({
			url: 'recoveryNG/ajaxDeleteActivity',
			type: 'post',
			data: data,
			beforeSend: function(){
				swal({title:"Delete activity and modify stock...", 
					text: '<span class="text-info"><i class="fa fa-refresh fa-spin"></i> Delete activity and modify stock please wait...</span>', 
					showConfirmButton: false, 
				});
			},
			error: function(xhr, textStatus){
				swal("Error", textStatus + xhr.responseText, "error");
			},
			complete: function(){
			},
			success: function(result) {
				if(result == 0){
					swal({
						title: "Success",
						text: "Delete activity and modify stock to database has success",
						type: "success",
						showCancelButton: false,
						allowOutsideClick: false,
						confirmButtonText: "Done",
						confirmButtonClass: "btn btn-success",
					}).then(function(){
						window.location.href="recoveryNG"
					});
				}
				else if(result == 1) {
					swal({
						title: "Warning!",
						text: 'Can not delete,<span class="text-info"> Not enough Stock!</span><p>' 
								+ 'Please check<span class="text-info"> Stock in database. </span>',
						type: "error",
						confirmButtonColor: "#DD6B55"
					});
				}
				else if(result == 2) {
					swal({
						title: "Warning!",
						text: 'Modify<span class="text-info"> Stock </span> Not complete...!<p>' 
								+ 'Please check<span class="text-info"> Stock in database. </span>',
						type: "error",
						confirmButtonColor: "#DD6B55"
					});
				}
				else if(result == 3) {
					swal({
						title: "Warning!",
						text: 'Delete<span class="text-info"> Activity </span> Not complete...!<p>' 
								+ 'Please check<span class="text-info"> Activity in database. </span>',
						type: "error",
						confirmButtonColor: "#DD6B55"
					});
				}
				else{
					swal({
						title: "Warning!",
						text: 'Delete<span class="text-info"> activity and modify stock </span> Not complete...!<p>' 
								+ 'Please check<span class="text-info"> Stock in database. </span>',
						type: "error",
						confirmButtonColor: "#DD6B55"
					});
				}
			}
		});
	}
}


//********************************************** Validation *******************************************
function validateID(activityID, stockID){
	return ((activityID>0) && (stockID>0));
}