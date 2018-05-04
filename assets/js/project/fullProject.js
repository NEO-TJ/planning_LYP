// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Overall %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ********************************************** Variable *********************************************
const dltOK = 0;					// Success
const dltValidate = 1;				// Validate error
// ************************************************ Event **********************************************
$(window).load(function() {
	resetProjectPage();
});
$('input[type="number"]').on('keydown', function(e) {
	numericFilter(e, this, true);
});
$(document).on('keydown', 'input[type="number"]', function(e) {
	numericFilter(e, this, true);
});
$(document).on('keydown', '#operationTime', function(e) {
	numericFilter(e, this, false);
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








//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Full project %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//************************************************ Submit & Reset **************************************
$('form#form-all').submit(function(e) {
	e.preventDefault();

//	let includeProcessDetail = $('form#form-process button.btn-submit').prop('disabled');
	if(validateAll()) {
		saveAll();
	} else {
		showDialog(dltValidate);
	}
});
$('form#form-all button.btn-reset').click(function(e) {
	resetProjectPage($('select#project :selected').val());
});

//************************************************ Method **********************************************
//------------------------------------------------- Save -----------------------------------------------
function saveAll(){
	let dataFullProject = {
		'processID'	: $('select#process :selected').val(),
		'jobID'			: $('select#job :selected').val(),
		'bomID'			: $('select#bom :selected').val(),
		'dsStep'		: prepareStepData(),
	};

	// Save full process and step by ajax.
	$.ajax({
		url: 'project/ajaxSaveAllProject',
		type: 'post',
		data: dataFullProject,
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
					text: "Save project to database has success",
					type: "success",
					showCancelButton: false,
					allowOutsideClick: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				}).then(function(){
					window.location.href="project"
				});
			}
			else if(result == 1) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> All Project </span> Not complete...!<p>' 
							+ 'Please check<span class="text-info"> Quantity Plan Product in job data. </span>',
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else{
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
	
	let resultProject = false;
	let resultJob = false;
	let resultQtyPlanProduct = false;
	let resultProcess = false;
	let resultAllStock = false;
	
	// Check project id selected?
	resultProject = validateFillSelectElement($('select#project'));
	// Check job id selected?
	resultJob = validateFillSelectElement($('select#job'));
	// Check process id selected?
	resultProcess = validateFillSelectElement($('select#process'));
	// Check All step require has input?
	resultAllStock = validateStock();
	
	result = (resultProject && resultJob && resultProcess && resultAllStock);
	return result;
}
//********************************************* Prepare data ******************************************
function prepareProcessID(){
	let dataProcessID = { 'processID': $('select#process :selected').val() };
	
	return dataProcessID;
}



//******************************************** Set Display Flow ****************************************
function resetProjectPage(){
	$('div#collapse-project').collapse('hide');
	$('div#collapse-job').collapse('hide');
	$('div#collapse-bom').collapse('hide');
	$('div#collapse-process').collapse('hide');

	deleteAllCloneStepRowTable();
	resetStepLastRowTable();
	$('select').val(0);
	$('input').val('');

	$('select#project').prop('disabled', false);
	$('select#job').prop('disabled', true);
	$('select#bom').prop('disabled', true);
	$('select#process').prop('disabled', true);
	$('table#step').find("input,button,textarea,select").prop('disabled', true);

	$('button#add-edit-job').prop('disabled', true);
	$('button#add-edit-bom').prop('disabled', true);
	$('button#add-edit-process').prop('disabled', true);
	$('form#form-all button.btn-submit').prop('disabled', true);

	$('select#project').removeClass('bg-error');
	$('select#job').removeClass('bg-error');
	$('select#bom').removeClass('bg-error');
	$('select#process').removeClass('bg-error');

	disStepAddMode();
}

function setSelectElement(dataSet, dataType) {
	$('select#' + dataType).empty();		// you might wanna empty it first with .empty()
	$('select#' + dataType).append('<option value="0">Please select ' + dataType + '</option>');

	for(let i=0; i < dataSet.length; i++) {
		$('select#' + dataType).append('<option value="' + dataSet[i].id + '">' + dataSet[i].Name + '</option>');
	}
}
