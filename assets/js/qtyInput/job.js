// ************************************************ Event ***********************************************
// ------------------------------------------------- Job -----------------------------------------------
$(window).load(function() {
	resetQtyInputPage();
});
$('select#job').change(changeJob);



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
			url: 'qtyInput/ajaxGetDsStep',
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
//******************************************** Reset input fill **********************************************
function resetQtyInputPage(jobID, stepID){
	if(isEmpty(jobID)) { jobID = 0; }
	if(isEmpty(stepID)) { stepID = 0; }

	$('select').val(0);
	
	$('select#job').val(jobID);
	$('select#job').trigger('change');

	if(stepID != 0) {
		$('select#step').val(stepID);
		$('select#step').trigger('change');
	}
	
	$('input#totalQtyNG').val(0);
}

//******************************************** Set Display Flow ****************************************
//------------------------------------------------- Job ------------------------------------------------
function disJobNotChoose() {
	disStepFix([], 0)
}
//------------------------------------------------- step -----------------------------------------------
function disStepFix(dataSet = [], id) {
	setStepSelectElement(dataSet);
	
	$('select#step').prop('disabled', true);
	$('select#step').val(id);
	$('select#step').trigger('change');
}

function disStepFree(dataSet = []) {
	setStepSelectElement(dataSet);
	
	$('select#step').prop('disabled', false);
	$('select#step').val(0);
	$('select#step').trigger('change');
}
function setStepSelectElement(dataSet = []) {
	$('select#step').empty();		// you might wanna empty it first with .empty()
	$('select#step').append('<option value="0">Please select step' + '</option>');

	for(var i=0; i < dataSet.length; i++) {
		$('select#step').append('<option value="' + dataSet[i].id + '">' + dataSet[i].Number + ' - ' + dataSet[i].DESC + '</option>');
	}
}
