var cloneMode = false;
var permanentProcess = false;
// ************************************************ Event ***********************************************
// ----------------------------------------------- Process ----------------------------------------------
$('select#process').change(changeProcess);
$('div#collapse-process').on("show.bs.collapse", function(e){
	disCollapseProcessMode();
});

//------------------------------------------------ Button ------------------------------------------------
$('button#add-edit-process').on('click', function(){
	cloneMode = false;
});
$('button#clone-process').on('click', function(){
	cloneMode = true;
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
	cloneMode = false;
	$('select#process').removeClass('bg-error');

	disCollapseProcessMode();
	changeProcess();
});



//************************************************ Method *************************************************
//------------------------------------------------- Save -----------------------------------------------
function saveProcess(){
	var dataFullProcess = prepareProcessData();
	
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
			}
			else{
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
	var result = false;
	
	// Check process name require has input?
	result = validateFillInputElement($('input#processName'));
	
	return result;
}
//********************************************* Prepare data ******************************************
function prepareProcessData(){
	var processID = $('select#process :selected').val();
	var processName = $('input#processName').val();
	var processDesc = $('input#processDesc').val();
	var processDescThai = $('input#processDescThai').val();

	var dataFullProcess = {
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
	setProcessCaptionPanelMode();
	resetFullProcessInputFill();
	
	var jobID = $('select#job :selected').val();
	var processID = $('select#process :selected').val();
	
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

		var data = {
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
				var dsProcess = dsData['dsProcess'];
				var dsFullStep = dsData['dsFullStep'];

				// Process Part.
				if((dsProcess.length) > 0){
					$('input#processName').val(dsProcess[0].Name);
					$('input#processDesc').val(dsProcess[0].DESC);
					$('input#processDescThai').val(dsProcess[0].DESC_Thai);
				}
				
				// Step Part.
				if(dsFullStep.length > 0) {
					if(permanentProcess){
						disStepEditMode();
					} else {
						disStepAddMode();
					}

					// Set Step.
					for(var i=0; i < dsFullStep.length; i++){
						if(i != 0){
							cloneStepRowTable();
						}
						setStepLastRowTable(dsFullStep, i);
					}
				} else {
					disStepAddMode();
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
//***************************************** Set caption panel mode *******************************************
function setProcessCaptionPanelMode(){
	var panelCaption = (($('select#process :selected').val() == 0) ? 'New' : 'Edit');
	$('#panel-caption-process').html('<span class="text-info"><h1>' + panelCaption + ' process</h1></span>');
}


//************************* Set display process and step of [New-Edit] or [Clone] mode ***********************
function disCollapseProcessMode(){
	if(cloneMode) {
		$('select#process').prop('disabled', true);
		$('button#add-edit-process').prop('disabled', true);
		$('button#clone-process').prop('disabled', true);
		$('form#form-process button.btn-submit').prop('disabled', true);

		$('#panel-caption-process').html('<span class="text-info"><h1>Clone process</h1></span>');
		$('div#panel-expand-process').removeClass('panel-success');
		$('div#panel-expand-process').addClass('panel-danger');
		
		$('input#processName').val('');

		disStepCloneMode();
	}
	else {
		var btnCaption = '';
		if(permanentProcess){
			disStepEditMode();
		} else {
			disStepAddMode();
			btnCaption = 'New-';
		}
		$('button#add-edit-process').text('[' + btnCaption + 'Edit] : process');
		setProcessCaptionPanelMode();
		$('div#panel-expand-process').removeClass('panel-danger');
		$('div#panel-expand-process').addClass('panel-success');

		$('select#process').prop('disabled', permanentProcess);
		$('button#add-edit-process').prop('disabled', false);
		$('button#clone-process').prop('disabled', permanentProcess);
		$('form#form-process button.btn-submit').prop('disabled', false);

	}
}

//******************************************** Set Display Flow **********************************************
function disProcessNotChoose() {
	$('button#clone-process').prop('disabled', true);
	$('table#step').find("input,button,textarea,select").prop('disabled', true);
	$('form#form-all button.btn-submit').prop('disabled', true);

	disStepAddMode();

	$('button#print-process').prop('disabled', true);
}
function disProcessChoose() {
	$('button#clone-process').prop('disabled', permanentProcess);
	$('table#step').find("input,button,textarea,select").prop('disabled', false);
	$('form#form-all button.btn-submit').prop('disabled', false);

	$('button#print-process').prop('disabled', false);
}

//******************************************** Set Display Flow **********************************************
function disStepAddMode() {
	$('th#step-caption').text('STEP : [New]');

	$('th#step-caption').removeClass('bg-info');
	$('th#step-caption').removeClass('bg-danger');
	$('th#step-caption').addClass('bg-primary');
}
function disStepEditMode() {
	$('th#step-caption').text('STEP : [Edit]');
	
	$('th#step-caption').removeClass('bg-primary');
	$('th#step-caption').removeClass('bg-danger');
	$('th#step-caption').addClass('bg-info');
}
function disStepCloneMode() {
	$('th#step-caption').text('STEP : [Clone]');
	
	$('th#step-caption').removeClass('bg-primary');
	$('th#step-caption').removeClass('bg-info');
	$('th#step-caption').addClass('bg-danger');
}