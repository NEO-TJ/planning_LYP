// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$('select#jobID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all job',
		close: function(event, ui) { changeJob(); }
	}).multiselectfilter();
	bindingMultiselectStep();
	bindingMultiselectLine();
});
//--------------------------------------------- Event Method ------------------------------------------
function bindingMultiselectStep() {
	$('select#stepID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all step',
		close: function(event, ui) { changeStep(); }
	}).multiselectfilter();
}
function bindingMultiselectLine() {
	$('select#lineID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all line',
	}).multiselectfilter();
}



//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//******************************************** Change job mode ****************************************
function changeJob(){
	let baseUrl = window.location.origin + "/" + window.location.pathname.split('/')[1] + "/";
	let arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	let data = {'jobID': arrayJobID};

	// Get step table by jobID with ajax.
	$.ajax({
		url: baseUrl + 'achievementReport/ajaxGetDsStepByJobID',
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
	let tableTagInputCaption = '<span class="input-group-btn">';
	tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Step number : </button>';
	tableTagInputCaption += '</span>';
	
	let tableTagInputSelecter = '<select class="form-control multi-select" id="stepID" name="stepID[]" multiple="multiple">';
	for(let i=0; i<dsStep.length; i++) {
		tableTagInputSelecter += '<option value=' + dsStep[i]['id'] + '>'
		tableTagInputSelecter += dsStep[i]['Number'] + ' - ' + dsStep[i]['DESC'] + '</option>';
	}
	tableTagInputSelecter += '</select>'
	
	$('div#stepID').html(tableTagInputCaption + tableTagInputSelecter);
	bindingMultiselectStep();

	changeStep();
}


//******************************************* Change step mode ****************************************
function changeStep(){
	let baseUrl = window.location.origin + "/" + window.location.pathname.split('/')[1] + "/";
	let arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; } ).get();
	if(arrayStepID.length == 0){
		arrayStepID = $("select#stepID option").map(function() { return this.value; } ).get();
	}

	let data = {'stepID': arrayStepID};
	
	// Get line table by stepID with ajax.
	$.ajax({
		url: baseUrl + 'achievementReport/ajaxGetDsLineByStepID',
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
		success: function(dsLine) {
			filterLine(dsLine);
		}
	});
}
function filterLine(dsLine) {
	let tableTagInputCaption = '<span class="input-group-btn">';
	tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Line : </button>';
	tableTagInputCaption += '</span>';
	
	let tableTagInputSelecter = '<select class="form-control multi-select" id="lineID" name="lineID[]" multiple="multiple">';
	for(let i=0; i<dsLine.length; i++) {
		tableTagInputSelecter += '<option value=' + dsLine[i]['id'] + '>' + dsLine[i]['Name'] + '</option>';
	}
	tableTagInputSelecter += '</select>'
	
	$('div#lineID').html(tableTagInputCaption + tableTagInputSelecter);
	bindingMultiselectLine();
}