// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Overall %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ********************************************** Variable *********************************************
const dltOK = 0;					// Success
const dltValidate = 1;				// Validate error
// ************************************************ Event **********************************************
$(document).ready(function() {
	$('select#userLineID').multiselect({
		header: true,
		noneSelectedText: 'Please selected line',
	}).multiselectfilter();
});
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
	let baseUrl = $('input#baseUrl').val();
	let dataType = $('input#dataType').val();
	let data = $('form#formInputData').serializeArray();

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
	let result = false;
	let resultFillInput = true;
	let resultSelectInput = true;
	let resultMultiSelectInput = true;
	
	$('input.input-require').each(function(i, obj) {
		// Check input data require has key?
		resultFillInput = validateFillInputElement($(this), true);
	});
	$('select.input-require').each(function(i, obj) {
		// Check input data require has selected?
		resultSelectInput = validateFillSelectElement($(this));
	});

	$('select.input-require-multi-select').each(function(i, obj) {
		// Check input data require has multi selected?
		resultMultiSelectInput = validateFillMultiSelectElement($(this));
	});

	result = (resultFillInput && resultSelectInput && resultMultiSelectInput);

	return result;
}