// ************************************************ Event ***********************************************
// ------------------------------------------------- Job -----------------------------------------------
$('select#job').change(function() {
	changeJob();
});



//************************************************ Method **********************************************
//------------------------------------------------- Mode ----------------------------------------------
//******************************************** Change job mode ****************************************
function changeJob(){
	var jobID = $('select#job :selected').val();
	
	if(jobID == 0){
		disJobNotChoose();
	}
	else {
		var data = {'jobID': jobID};

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
			}
		});
	}
}






//************************************************** Tool ****************************************************
//******************************************** Set Display Flow ****************************************
//------------------------------------------------- Job ------------------------------------------------
function disJobNotChoose() {
	disStepFix([], 0)
}
//------------------------------------------------- step -----------------------------------------------
function disStepFix(dataSet, id) {
	bindingStepSelectElement('#sourceStep', dataSet);
	bindingStepSelectElement('#destinationStep', dataSet);
	
	setSelectElementDisplayMode('#sourceStep', true, id);
	setSelectElementDisplayMode('#destinationStep', true, id);
}

function disStepFree(dataSet) {
	bindingStepSelectElement('#sourceStep', dataSet);
	bindingStepSelectElement('#destinationStep', dataSet);
	
	setSelectElementDisplayMode('#sourceStep', false, 0);
	setSelectElementDisplayMode('#destinationStep', false, 0);
}


function bindingStepSelectElement(element, dataSet) {
	$('select'+element).empty();
	$('select'+element).append('<option value="0">Please select step' + '</option>');

	for(var i=0; i < dataSet.length; i++) {
		$('select'+element).append('<option value="' + dataSet[i].id + '">' + dataSet[i].Number + ' - ' + dataSet[i].DESC + '</option>');
	}
}
function setSelectElementDisplayMode(element, mode, id) {
	$('select'+element).prop('disabled', mode);
	$('select'+element).val(id);
	$('select'+element).trigger('change');
}
