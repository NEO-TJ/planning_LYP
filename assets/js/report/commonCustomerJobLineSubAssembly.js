// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$('select#customerID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all customer',
		close: function(event, ui) { changeCustomer(); }
	}).multiselectfilter();
	bindingMultiselectJob();
	bindingMultiselectLine();
	bindingMultiselectSubAssembly();
});
//--------------------------------------------- Event Method ------------------------------------------
function bindingMultiselectJob() {
	$('select#jobID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all job',
		close: function(event, ui) { changeJob(); }
	}).multiselectfilter();
}
function bindingMultiselectLine() {
	$('select#lineID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all line',
	}).multiselectfilter();
}
function bindingMultiselectSubAssembly() {
	$('select#subAssemblyID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all subAssembly',
	}).multiselectfilter();
}



//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//***************************************** Change customer mode ***************************************
function changeCustomer(){
	var arrayCustomerID = $('select#customerID').multiselect("getChecked").map(function() { return this.value; } ).get();
	
	var data = {'customerID': arrayCustomerID};
	
	// Get job table by customerID with ajax.
	$.ajax({
		url: 'workingCapacityReport/ajaxGetDsJobByCustomerID',
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
		success: function(dsJob) {
			filterJob(dsJob);
		}
	});
}
function filterJob(dsJob) {
	var tableTagInputCaption = '<span class="input-group-btn">';
	tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Job : </button>';
	tableTagInputCaption += '</span>';
	
	var tableTagInputSelecter = '<select class="form-control multi-select" id="jobID" name="jobID[]" multiple="multiple">';
	for(var i=0; i<dsJob.length; i++) {
		tableTagInputSelecter += '<option value=' + dsJob[i]['id'] + '>' + dsJob[i]['Name'] + '</option>';
	}
	tableTagInputSelecter += '</select>'
	
	$('div#jobID').html(tableTagInputCaption + tableTagInputSelecter);
	bindingMultiselectJob();
	
	changeJob();
}




//******************************************** Change job mode *****************************************
function changeJob(){
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	if(arrayJobID.length == 0){
		arrayJobID = $("select#jobID option").map(function() { return this.value; } ).get();
	}

	var data = {'jobID': arrayJobID};
	
	// Get project table one row by ajax.
	$.ajax({
		url: 'workingCapacityReport/ajaxGetDsLineDsSubAssemblyByJobID',
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
		success: function(result) {
			var dsLine = result['dsLine'];
			var dsSubAssembly = result['dsSubAssembly'];

			filterLine(dsLine);
			filterSubAssembly(dsSubAssembly);
		}
	});
}
function filterLine(dsLine) {
	var tableTagInputCaption = '<span class="input-group-btn">';
	tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Line : </button>';
	tableTagInputCaption += '</span>';
	
	var tableTagInputSelecter = '<select class="form-control multi-select" id="lineID" name="lineID[]" multiple="multiple">';
	for(var i=0; i<dsLine.length; i++) {
		tableTagInputSelecter += '<option value=' + dsLine[i]['id'] + '>' + dsLine[i]['Name'] + '</option>';
	}
	tableTagInputSelecter += '</select>'
	
	$('div#lineID').html(tableTagInputCaption + tableTagInputSelecter);
	bindingMultiselectLine();
}
function filterSubAssembly(dsSubAssembly) {
	var tableTagInputCaption = '<span class="input-group-btn">';
	tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Sub Assembly : </button>';
	tableTagInputCaption += '</span>';
	
	var tableTagInputSelecter = '<select class="form-control multi-select"';
	tableTagInputSelecter += 'id="subAssemblyID" name="subAssemblyID[]" multiple="multiple">';
	for(var i=0; i<dsSubAssembly.length; i++) {
		tableTagInputSelecter += '<option value=' + dsSubAssembly[i]['id'] + '>' + dsSubAssembly[i]['Name'] + '</option>';
	}
	tableTagInputSelecter += '</select>'
	
	$('div#subAssemblyID').html(tableTagInputCaption + tableTagInputSelecter);
	bindingMultiselectSubAssembly();
}