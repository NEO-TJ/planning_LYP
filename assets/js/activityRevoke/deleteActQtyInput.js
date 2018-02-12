// ************************************************ Event **********************************************
//------------------------------------------------- Step -----------------------------------------------
$('table#actQtyInput').on("click", ".delete-elements", deleteActivity);


// ********************************************** Method ***********************************************
//----------------------------------------------- Delete -----------------------------------------------
function deleteActivity(){
	let currentTr = $(this).closest("tr");
	let activityID = currentTr.find('button#activityID').val();
	let stockID = currentTr.find('input#stockID').val();
	let qtyOK = currentTr.find('td:nth-child(7)').html();
	let qtyNG = currentTr.find('td:nth-child(8)').html();
	qtyOK = (isEmpty(qtyOK) ? 0 : qtyOK);
	qtyNG = (isEmpty(qtyNG) ? 0 : qtyNG);

	if(validateID(activityID, stockID)) {
		ajaxDeleteActivity(activityID, stockID, qtyOK, qtyNG);
	} else {
		showDialog(dltValidate);
	}
}

function ajaxDeleteActivity(activityID, stockID, qtyOK, qtyNG){
	let baseUrl = window.location.origin + "/" + window.location.pathname.split('/')[1] + "/";
	if(validateID(activityID, stockID)) {
		let data = {
			'activityID': activityID,
			'stockID': stockID,
			'qtyOK': qtyOK,
			'qtyNG': qtyNG,
		};

		// Get process table one row by ajax.
		$.ajax({
			url: baseUrl + 'qtyInput/ajaxDeleteActivity',
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
						window.location.href="activityQtyInput"
					});
				} else if(result == 1) {
					swal({
						title: "Warning!",
						text: 'Can not delete,<span class="text-info"> Not enough Stock!</span><p>' 
								+ 'Please check<span class="text-info"> Stock in database. </span>',
						type: "error",
						confirmButtonColor: "#DD6B55"
					});
				} else if(result == 2) {
					swal({
						title: "Warning!",
						text: 'Modify<span class="text-info"> Stock </span> Not complete...!<p>' 
								+ 'Please check<span class="text-info"> Stock in database. </span>',
						type: "error",
						confirmButtonColor: "#DD6B55"
					});
				} else if(result == 3) {
					swal({
						title: "Warning!",
						text: 'Delete<span class="text-info"> Activity </span> Not complete...!<p>' 
								+ 'Please check<span class="text-info"> Activity in database. </span>',
						type: "error",
						confirmButtonColor: "#DD6B55"
					});
				} else{
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
