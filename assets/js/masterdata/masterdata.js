// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Overall %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ********************************************** Variable *********************************************
const dltOK = 0;					// Success
const dltValidate = 1;				// Validate error
// ************************************************ Event **********************************************
$('input[type="number"]').on('keydown', function(e) {
	numericFilter(e, this, true);
});
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
//************************************************ Method **********************************************
//************************************************* Tool ***********************************************
function showDialog($type){
	if($type == dltOK){
		
	}else if($type == dltValidate) {
		swal("Warning", "Please check your input key.","warning");
	}
}





// ************************************************ Event **********************************************
//------------------------------------------------ Button ----------------------------------------------
//******************************************** Submit & Reset ******************************************
$('form#formInputData').on('submit', function(e) {
	e.preventDefault();
	
	if(validateInputRequire()) {
		saveInputData();
	} else {
		showDialog(dltValidate);
	}
});






//************************************************ Method **********************************************
//------------------------------------------------- Save -----------------------------------------------
function saveInputData(){
	var baseUrl = $('input#baseUrl').val();
	var dataType = $('input#dataType').val();
	var data = $('form#formInputData').serializeArray();

	// Ajax add or edit record.
	$.ajax({
		url: baseUrl + 'masterdata/ajaxSaveInputData',
		type: 'post',
		data: data,
		beforeSend: function(){
			swal({title:"Saving...", 
				text: '<span class="text-info"><i class="fa fa-refresh fa-spin"></i> Saving please wait...</span>', 
				showConfirmButton: false, 
			});
		},
		error: function(xhr, textStatus){
			swal("Error", textStatus + xhr.responseText, "error");
		},
		complete: function(){
		},
		success: function(result){
			if(result == 0){
				swal({
					title: "Success",
					text: "Save data to database has success",
					type: "success",
					showCancelButton: false,
					allowOutsideClick: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				}).then(function(){
					window.location.href = baseUrl + "masterdata/view/" + dataType
				});
			}
			else{
				swal({
					title: "Unsuccess!",
					text: "Can't save<span class='text-info'> data </span>to database" + result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}
//********************************************** Validation *******************************************
function validateInputRequire(){
	var result = false;
	var resultKeyInput = true;
	var resultSelectInput = true;
	
	$('input.input-require').each(function(i, obj) {
		// Check input data require has key?
		if(isEmpty($(this).val())) {
			$(this).addClass('bg-error');
			resultKeyInput = false;
		}
		else{
			$(this).removeClass('bg-error');
		}
	});
	$('select.input-require').each(function(i, obj) {
		// Check input data require has selected?
		if($(this).val() == 0) {
			$(this).addClass('bg-error');
			resultSelectInput = false;
		}
		else{
			$(this).removeClass('bg-error');
		}
	});

	result = (resultKeyInput && resultSelectInput);
	return result;
}