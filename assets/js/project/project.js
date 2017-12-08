// ************************************************ Event ***********************************************
// ----------------------------------------------- Project ---------------------------------------------
$('select#project').change(changeProject);
$('div#collapse-project').on("show.bs.collapse", setProjectCaptionPanelMode);

//------------------------------------------------ Button ------------------------------------------------
//******************************************** Submit & Reset ********************************************
$('form#form-project').on('submit', function(e) {
	e.preventDefault();
	
	if(validateProject()) {
		saveProject();
	} else {
		showDialog(dltValidate);
	}
});
$('form#form-project button.btn-reset').click(changeProject);



// ************************************************ Method *************************************************
//-------------------------------------------------- Save ----------------------------------------------
function saveProject(){
	var projectID = $('select#project :selected').val();
	var projectName = $('input#projectName').val();
	var customerID = $('select#customer :selected').val();

	var data = {
				'projectID': projectID, 
				'projectName': projectName, 
				'customerID': customerID
				};
	// Get project table one row by ajax.
	$.ajax({
		url: 'project/ajaxSaveProject',
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
					text: "Save project to database has success",
					type: "success",
					showCancelButton: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				});

				setSelectElement(arrResult['dsProject'], 'project');
				$('select#project').val(arrResult['projectID']);
				$('select#project').trigger('change');
				$('div#collapse-project').collapse('hide');
			}
			else{
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Project </span> Not complete...!' + arrResult,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	})
}
//********************************************** Validation *******************************************
function validateProject(){
	var result = false;
	
	var resultProjectName = false;
	var resultCustomerID = false;
	
	// Check project name require has input?
	resultProjectName = validateFillInputElement($('input#projectName'));
	// Check customer id selected?
	resultCustomerID = validateFillSelectElement($('select#customer'));
	

	result = (resultProjectName && resultCustomerID);
	return result;
}



//------------------------------------------------- Mode ----------------------------------------------
//****************************************** Change project mode **************************************
function changeProject(){
	setProjectCaptionPanelMode();
	resetProjectInputFill();
	var projectID = $('select#project :selected').val();
	
	if(projectID == 0){
		disProjectNotChoose();
	} else {
		disProjectChoose();
		
		var data = {'projectID': projectID};
		
		// Get project table one row by ajax.
		$.ajax({
			url: 'project/ajaxGetDsProjectListJob',
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
//				alert(dsData);
				var dsProject = dsData['dsProject'];
				var dsJob = dsData['dsJob'];
				
				// Project Part.
				if((dsProject.length) > 0) {
					$('input#projectName').val(dsProject[0].Name);
					$('select#customer').val(dsProject[0].FK_ID_Customer);
				}
				
				// Job Part.
				if((dsJob.length) == 1) {
					disJobFix(dsJob, 'job', dsJob[0].id);
				} else {
					disJobFree(dsJob, 'job');
				}
			}
		});
	}
}






//************************************************** Tool ****************************************************
//******************************************** Reset input fill **********************************************
function resetProjectInputFill(){
	$('input#projectName').val('');
	$('select#customer').val(0);
	
	$('input#projectName').removeClass('bg-error');
	$('select#customer').removeClass('bg-error');
}
//***************************************** Set caption panel mode *******************************************
function setProjectCaptionPanelMode(){
	var caption = '';
	
	caption = ($('select#project :selected').val() == 0)? 'New project' : 'Edit project';
	$('#panel-caption-project').html('<span class="text-info"><h1>' + caption + '</h1></span>');
}



//******************************************** Set Display Flow **********************************************
function disProjectNotChoose() {
	$('select#job').val(0);
	$('div#collapse-job').collapse('hide');
	
	$('select#job').prop('disabled', true);
	$('button#add-edit-job').prop('disabled', true);
	
	$('select#job').trigger('change');
}

function disProjectChoose() {
	$('select#job').prop('disabled', false);
	$('button#add-edit-job').prop('disabled', false);
}

//******************************************** Set Display Flow **********************************************
function disJobFix(dataSet, dataType, id) {
	setSelectElement(dataSet, dataType);
	
	$('select#' + dataType).val(id);
	$('select#' + dataType).trigger('change');
}

function disJobFree(dataSet, dataType) {
	setSelectElement(dataSet, dataType);
	
	$('select#' + dataType).val(0);
	$('select#' + dataType).trigger('change');
}