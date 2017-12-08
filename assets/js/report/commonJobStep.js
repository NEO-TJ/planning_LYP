// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$('select#jobID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all job',
		close: function(event, ui) { changeJob(); }
	}).multiselectfilter();
	bindingMultiselectStep();
});
//--------------------------------------------- Event Method ------------------------------------------
function bindingMultiselectStep() {
	$('select#stepID').multiselect({
		header: true,
		enableFiltering: true,
		noneSelectedText: 'Default selected all step',
	}).multiselectfilter();
}



//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//******************************************** Change job mode ****************************************
function changeJob(){
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	
	var data = {'jobID': arrayJobID};
	
	// Get step table by jobID with ajax.
	$.ajax({
		url: 'achievementReport/ajaxGetDsStepByJobID',
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
			filterStep(dsStep);
		}
	});
}
function filterStep(dsStep) {
	var tableTagInputCaption = '<span class="input-group-btn">';
	tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Step number : </button>';
	tableTagInputCaption += '</span>';
	
	var tableTagInputSelecter = '<select class="form-control multi-select" id="stepID" name="stepID[]" multiple="multiple">';
	for(var i=0; i<dsStep.length; i++) {
		tableTagInputSelecter += '<option value=' + dsStep[i]['id'] + '>'
		tableTagInputSelecter += dsStep[i]['Number'] + ' - ' + dsStep[i]['DESC'] + '</option>';
	}
	tableTagInputSelecter += '</select>'
	
	$('div#stepID').html(tableTagInputCaption + tableTagInputSelecter);
	bindingMultiselectStep();
}