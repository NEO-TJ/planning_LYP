// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Overall %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ********************************************** Variable *********************************************
const dltOK = 0;					// Success
const dltValidate = 1;				// Validate error


//************************************************ Method **********************************************
//************************************************* Tool ***********************************************
function showDialog($type){
	if($type == dltOK){

	}else if($type == dltValidate) {
		swal("Warning", "Please check your input key.","warning");
	}
}




//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Full process %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//************************************************ Submit & Reset **************************************
$('form#form-process').submit(function(e) {
	e.preventDefault();

	if(validateAll()) {
		saveAll();
	} else {
		showDialog(dltValidate);
	}
});

//************************************************ Method **********************************************
//------------------------------------------------- Save -----------------------------------------------
function saveAll(){
	let baseUrl = window.location.origin + "/" + window.location.pathname.split('/')[1] + "/";
	dataFullProcess = prepareProcessData();
	dataFullProcess['dsStep'] = prepareStepData();

	// Save full process and step by ajax.
	$.ajax({
		url: baseUrl + 'process/ajaxSaveFullProcess',
		type: 'post',
		data: dataFullProcess,
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
		success: function(result) {
			if(result == 0){
				swal({
					title: "Success",
					text: "Save process to database has success",
					type: "success",
					showCancelButton: false,
					allowOutsideClick: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				}).then(function(){
					window.location.href="process"
				});
			}else{
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> All Project </span> Not complete...!<p>' + result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}
//********************************************** Validation *******************************************
function validateAll(){
	let result = false;

	let resultProcess = false;
	let resultAllStep = false;

	// Check process id selected?
	resultProcess = validateProcess();
	// Check All step require has input?
	resultAllStep = validateStep();
	
	result = (resultProcess && resultAllStep);
	return result;
}

function validateProcess(){
	let result = false;
	
	// Check process name require has input?
	result = validateFillInputElement($('input#processName'));
	
	return result;
}


//********************************************* Prepare data ******************************************
function prepareProcessData(){
	let processId = $('input#processId').val();
	let processName = $('input#processName').val();
	let processDesc = $('input#processDesc').val();
	let processDescThai = $('input#processDescThai').val();

	let dataFullProcess = {
		'processId': processId,
		'processName': processName,
		'processDesc': processDesc,
		'processDescThai': processDescThai,
	};
	
	return dataFullProcess;
}