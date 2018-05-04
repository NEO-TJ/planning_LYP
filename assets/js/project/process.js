let permanentProcess = false;
// ************************************************ Event ***********************************************
// ----------------------------------------------- Process ----------------------------------------------
$('select#process').change(changeProcess);
$('div#collapse-process').on("show.bs.collapse", function(e){
	disCollapseProcessMode();
});


//******************************************** Submit & Reset ********************************************
$('form#form-process').on('submit', function(e) {
	e.preventDefault();
	
	if(validateProcess()) {
		saveProcess();
	} else {
		showDialog(dltValidate);
	}
});
$('form#form-process button.btn-reset').on('click',function(){
	$('select#process').removeClass('bg-error');

	disCollapseProcessMode();
	changeProcess();
});



//************************************************ Method *************************************************
//------------------------------------------------- Save -----------------------------------------------
function saveProcess(){
	let dataFullProcess = prepareProcessData();
	
	// Get process table one row by ajax.
	$.ajax({
		url: 'project/ajaxSaveProcess',
		type: 'post',
		data: dataFullProcess,
		dataType: 'json',
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
		success: function(arrResult) {
			if(arrResult['result'] == 0){
				swal({
					title: "Success",
					text: "Save process to database has success",
					type: "success",
					showCancelButton: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				});
				
				setSelectElement(arrResult['dsProcess'], 'process');
				$('select#process').val(arrResult['processID']);
				$('select#process').trigger('change');
				$('div#collapse-process').collapse('hide');
			}else{
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Process </span> Not complete...!',
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}
//********************************************** Validation *******************************************
function validateProcess(){
	let result = false;
	
	// Check process name require has input?
	result = validateFillInputElement($('input#processName'));
	
	return result;
}
//********************************************* Prepare data ******************************************
function prepareProcessData(){
	let processID = $('select#process :selected').val();
	let processName = $('input#processName').val();
	let processDesc = $('input#processDesc').val();
	let processDescThai = $('input#processDescThai').val();

	let dataFullProcess = {
		'processID': processID, 
		'processName': processName, 
		'processDesc': processDesc,
		'processDescThai': processDescThai,
	};
	
	return dataFullProcess;
}

//------------------------------------------------- Mode ----------------------------------------------
//****************************************** Change process mode **************************************
function changeProcess(){
	resetFullProcessInputFill();
	
	let jobID = $('select#job :selected').val();
	let processID = $('select#process :selected').val();
	
	if((processID != 0) && (jobID == 0)) {
		swal({
			title: "Warning!",
			text: 'Please select<span class="text-info"> Job </span>...!',
			type: "warning",
			confirmButtonColor: "#DD6B55"
		});
	}
	
	if(processID == 0){
		disProcessNotChoose();
	} else {
		disProcessChoose();

		let data = {
			'jobID': jobID,
			'processID': processID
		};

		// Get process table one row by ajax.
		$.ajax({
			url: 'project/ajaxGetDsFullProcess',
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function(){
			},
			error: function(xhr, textStatus){
				swal("Error", textStatus + xhr.responseText, "error");
			},
			complete: function(){
			},
			success: function(dsData) {
				let dsProcess = dsData['dsProcess'];
				let dsFullStep = dsData['dsFullStep'];

				// Process Part.
				if((dsProcess.length) > 0){
					$('input#processName').val(dsProcess[0].Name);
					$('input#processDesc').val(dsProcess[0].DESC);
					$('input#processDescThai').val(dsProcess[0].DESC_Thai);
				}
				
				// Step Part.
				if(dsFullStep.length > 0) {
					// Set Step.
					for(let i=0; i < dsFullStep.length; i++){
						if(i != 0){
							cloneStepRowTable();
						}
						setStepLastRowTable(dsFullStep, i);
					}
				}
			}
		});
	}
}




//************************************************** Tool ****************************************************
//************************************** Reset Full Process input fill ***************************************
function resetFullProcessInputFill(){
	resetProcessInputFill();
	deleteAllCloneStepRowTable();
}
//----------------------------------------- Reset Process input fill -----------------------------------------
function resetProcessInputFill(){
	$('input#processName').val('');
	$('input#processDesc').val('');
	$('input#processDescThai').val('');
	
	$('input#processName').removeClass('bg-error');
	$('input#processDesc').removeClass('bg-error');
	$('input#processDescThai').removeClass('bg-error');
}


//************************* Set display process and step of [New-Edit] or [Clone] mode ***********************
function disCollapseProcessMode(){
	$('div#panel-expand-process').removeClass('panel-danger');
	$('div#panel-expand-process').addClass('panel-success');

	$('select#process').prop('disabled', permanentProcess);
	$('button#add-edit-process').prop('disabled', false);
	$('form#form-process button.btn-submit').prop('disabled', false);
}

//******************************************** Set Display Flow **********************************************
function disProcessNotChoose() {
	$('table#step').find("input,button,textarea,select").prop('disabled', true);
	$('form#form-all button.btn-submit').prop('disabled', true);

	$('button#print-process').prop('disabled', true);
}
function disProcessChoose() {
	$('table#step').find("input,button,textarea,select").prop('disabled', true);
	$('table#step').find("input#operationTime").prop('disabled', false);
	$('form#form-all button.btn-submit').prop('disabled', false);

	$('button#print-process').prop('disabled', false);
}
