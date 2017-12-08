// ************************************************ Event ***********************************************
// ------------------------------------------------- Job -----------------------------------------------
$('select#job').change(changeJob);
$('div#collapse-job').on("show.bs.collapse", setJobCaptionPanelMode);

//------------------------------------------------ Button ------------------------------------------------
//******************************************** Submit & Reset ********************************************
$('form#form-job').on('submit', function(e) {
	e.preventDefault();
	
	if(validateJob()) {
		saveJob();
	} else {
		showDialog(dltValidate);
	}
});
$('form#form-job button.btn-reset').click(changeJob);



// ************************************************* Method *************************************************
//--------------------------------------------------- Save ----------------------------------------------
function saveJob(){
	var jobID = $('select#job :selected').val();
	var jobName = $('input#jobName').val();
	var projectID = $('select#project :selected').val();
	var bomID = $('select#bom :selected').val();
	var qtyOrder = $('input#qtyOrder').val();
	var qtyPlanProduct = $('input#qtyPlanProduct').val();
	var jobTypeID = $('select#jobType :selected').val();
	var jobStatusID = $('select#jobStatus :selected').val();

	var data = {
				'jobID': jobID, 
				'jobName': jobName, 
				'projectID': projectID,
				'bomID': bomID,
				'qtyOrder': qtyOrder, 
				'qtyPlanProduct': qtyPlanProduct, 
				'jobTypeID': jobTypeID,
				'jobStatusID': jobStatusID
				};
	
	// Get job table one row by ajax.
	$.ajax({
		url: 'project/ajaxSaveJob',
		type: 'post',
		data: data,
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
					text: "Save job to database has success",
					type: "success",
					showCancelButton: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				});
				
				setSelectElement(arrResult['dsJob'], 'job');
				$('select#job').val(arrResult['jobID']);
				$('select#job').trigger('change');
				$('div#collapse-job').collapse('hide');
			}
			else{
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Job </span> Not complete...!' + arrResult,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}
//********************************************** Validation *******************************************
function validateJob(){
	var result = false;
	
	var resultJobName = false;
	var resultProjectID = false;
	var resultQtyOrder = false;
	var resultQtyPlanProduct = false;
	
	// Check job name require has input?
	resultJobName = validateFillInputElement($('input#jobName'));
	// Check project id selected?
	resultProjectID = validateFillSelectElement($('select#project'));
	// Check quantity order require has input?
	resultQtyOrder = validateFillInputElement($('input#qtyOrder'));
	// Check quantity plan product require has input?
	resultQtyPlanProduct = validateFillInputElement($('input#qtyPlanProduct'));
	

	result = (resultJobName && resultProjectID && resultQtyOrder && resultQtyPlanProduct);
	return result;
}

//------------------------------------------------- Mode ----------------------------------------------
//******************************************** Change job mode ****************************************
function changeJob(){
	setJobCaptionPanelMode();
	resetJobInputFill();
	var jobID = $('select#job :selected').val();
	
	if(jobID == 0){
		disJobNotChoose();
	} else {
		disJobChoose();

		var data = {'jobID': jobID};
		
		// Get project table one row by ajax.
		$.ajax({
			url: 'project/ajaxGetDsJobListBomAndProcess',
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
				var dsJob = dsData['dsJob'];
				var dsBom = dsData['dsBom'];
				var dsProcess = dsData['dsProcess'];
				
				// Job Part.
				if((dsJob.length) > 0){
					$('input#jobName').val(dsJob[0].Name);
					$('select#jobType').val(dsJob[0].FK_ID_Job_Type);
					$('input#qtyOrder').val(dsJob[0].Qty_Order);
					$('input#qtyPlanProduct').val(dsJob[0].Qty_Plan_Product);
					$('select#jobStatus').val(dsJob[0].FK_ID_Job_Status);
				}
			
			
				// BOM Part.
				if((dsBom.length) == 1) {
					disFix(dsBom, 'bom', dsBom[0].id);
				} else {
					disFree(dsBom, 'bom');
				}
				
				// Process Part.
				if((dsProcess.length) == 1) {
					disFix(dsProcess, 'process', dsProcess[0].id);
					permanentProcess = true;
				} else {
					disFree(dsProcess, 'process');
					permanentProcess = false;
				}
			}
		});
	}
}






//************************************************** Tool ****************************************************
//******************************************** Reset input fill **********************************************
function resetJobInputFill(){
	$('input#jobName').val('');
	$('select#jobType').val(0);
	$('input#qtyOrder').val('');
	$('input#qtyPlanProduct').val('');
	$('select#jobStatus').val(0);
	
	$('input#jobName').removeClass('bg-error');
	$('select#jobType').removeClass('bg-error');
	$('input#qtyOrder').removeClass('bg-error');
	$('input#qtyPlanProduct').removeClass('bg-error');
	$('select#jobStatus').removeClass('bg-error');
}
//***************************************** Set caption panel mode *******************************************
function setJobCaptionPanelMode(){
	var caption = '';
	
	caption = ($('select#job :selected').val() == 0)? 'New job' : 'Edit job';
	$('#panel-caption-job').html('<span class="text-info"><h1>' + caption + '</h1></span>');
}


//******************************************** Set Display Flow **********************************************
function disJobNotChoose() {
	$('select#bom').prop('disabled', true);
	$('button#add-edit-bom').prop('disabled', true);
	$('div#collapse-bom').collapse('hide');
	$('select#bom').val(0);
	$('select#bom').trigger('change');

	$('select#process').prop('disabled', true);
	$('button#add-edit-process').prop('disabled', true);
	$('div#collapse-process').collapse('hide');
	$('select#process').val(0);
	$('select#process').trigger('change');
}

function disJobChoose() {
	$('div#collapse-bom').collapse('hide');
	$('div#collapse-process').collapse('hide');
}

//******************************************** Set Display Flow **********************************************
function disFix(dataSet, dataType, id) {
	setSelectElement(dataSet, dataType);
	
	$('select#' + dataType).prop('disabled', true);
	$('button#add-edit-' + dataType).prop('disabled', false);
	$('button#add-edit-' + dataType).text('[Edit] : ' + dataType);
	
	$('select#' + dataType).val(id);
	$('select#' + dataType).trigger('change');
}

function disFree(dataSet, dataType) {
	setSelectElement(dataSet, dataType);
	
	$('select#' + dataType).prop('disabled', false);
	$('button#add-edit-' + dataType).prop('disabled', false);
	$('button#add-edit-' + dataType).text('[New-Edit] : ' + dataType);
	
	$('select#' + dataType).val(0);
	$('select#' + dataType).trigger('change');
}