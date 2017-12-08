// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$('select#jobTypeID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all job type',
		close: function(event, ui) { changeJobTypeJobStatus(); }
	}).multiselectfilter();
	$('select#jobStatusID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all job status',
		close: function(event, ui) { changeJobTypeJobStatus(); }
	}).multiselectfilter();

	bindingMultiselectJob();
});
//--------------------------------------------- Event Method ------------------------------------------
function bindingMultiselectJob() {
	$('select#jobID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all job',
	}).multiselectfilter();
}



//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//***************************************** Change customer mode ***************************************
function changeJobTypeJobStatus(){
	var arrayJobTypeID = $('select#jobTypeID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayJobStatusID = $('select#jobStatusID').multiselect("getChecked").map(function() { return this.value; } ).get();
	
	var data = {
			'jobTypeID'		: arrayJobTypeID,
			'jobStatusID'	: arrayJobStatusID,
	};
	
	// Get job table by JobTypeID, JobStatusID with ajax.
	$.ajax({
		url: 'jobRemove/ajaxGetDsJobByJobTypeJobStatusID',
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
	
	$('div#jobID').html(tableTagInputCaption + tableTagInputSelecter);
	bindingMultiselectJob();
}
