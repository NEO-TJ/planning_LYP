// ************************************************ Event ***********************************************
// ------------------------------------------------- Job -----------------------------------------------
$('select#job').change(function() {
	changeJob();
});



//************************************************ Method **********************************************
//------------------------------------------------- Mode ----------------------------------------------
//******************************************** Change job mode ****************************************
function changeJob(){
	let jobID = $('select#job :selected').val();
	
	if(jobID == 0){
		disJobNotChoose();
	}
	else {
		let data = {'jobID': jobID};

		// Get project table one row by ajax.
		$.ajax({
			url: 'recoveryNG/ajaxGetDsStep',
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
			success: function(dsStep) {
				if((dsStep.length) == 1) {
					disStepFix(dsStep, dsStep[0].id);
				} else {
					disStepFree(dsStep);
				}
				$('select#destinationStep').prop('disabled', true);
			}
		});
	}
}






//************************************************** Tool ****************************************************
//******************************************** Set Display Flow ****************************************
//------------------------------------------------- Job ------------------------------------------------
function disJobNotChoose() {
	disStepFix([], 0);
	$('select#destinationStep').prop('disabled', true);
}
//------------------------------------------------- step -----------------------------------------------
function disStepFix(dataSet, id) {
	// Source step.
	bindingStepSelectElement('#sourceStep', dataSet);
	setSelectElementDisplayMode('#sourceStep', true, id);
	// Destination step.
	bindingStepSelectElement('#destinationStep', dataSet);
	setSelectElementDisplayMode('#destinationStep', true, id);
}

function disStepFree(dataSet) {
	// Source step.
	bindingStepSelectElement('#sourceStep', dataSet);
	setSelectElementDisplayMode('#sourceStep', false, 0);
	// Destination step.
	bindingStepSelectElement('#destinationStep', dataSet);
	setSelectElementDisplayMode('#destinationStep', false, 0);
}


function bindingStepSelectElement(element, dataSet) {
	$('select'+element).empty();
	$('select'+element).append('<option value="0">Please select step' + '</option>');

	for(let i=0; i < dataSet.length; i++) {
		$('select'+element).append('<option value="' + dataSet[i].id + '">'
		+ dataSet[i].Number + ' - ' + dataSet[i].DESC + '</option>');
	}
}
function setSelectElementDisplayMode(element, mode, id) {
	$('select'+element).prop('disabled', mode);
	$('select'+element).val(id);
	$('select'+element).trigger('change');
}
