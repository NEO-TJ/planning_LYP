// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
});

//------------------------------------------------ Component -------------------------------------------
$('button#refresh').click(displayFullJobTable);
$('button#search').click(displayFullJobTable);






//************************************************ Method **********************************************
//------------------------------------------------ Search ----------------------------------------------
function displayFullJobTable() {
	getFullJob();
}



//************************************************ Method **********************************************
//------------------------------------------------ AJAX -----------------------------------------------
function getFullJob() {
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayJobTypeID = $('select#jobTypeID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayJobStatusID = $('select#jobStatusID').multiselect("getChecked").map(function() { return this.value; } ).get();

	var data = {
			'jobID'			: arrayJobID,
			'jobTypeID'		: arrayJobTypeID,
			'jobStatusID'	: arrayJobStatusID,
	};

	// Get job for remove by ajax.
	$.ajax({
		url: 'jobRemove/ajaxGetDsFullJob',
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
		success: function(dsFullJob) {
			$('table#jobRemove > tbody').html(genBody(dsFullJob));
		}
	});
}




//--------------------------------------------- Generate Html ------------------------------------------
function genBody(dsFullJob) {
	var htmlBody = "";
	
	for(var i=0; i<dsFullJob.length; i++)
	{
		//Data.
		htmlBody += genOneRow(dsFullJob[i]);
	}
	
	$('#headerPage').prop('title', "Total Record : " + dsFullJob.length);
	return htmlBody;
}
function genOneRow(row) {
	var htmlBody;
	
	htmlBody +='<tr>';

	htmlBody +='<td class="text-left">' + row['JobName'] + '</td>';
	htmlBody +='<td class="text-left">' + row['JobTypeName'] + '</td>';
	htmlBody +='<td class="text-left">' + row['JobStatusName'] + '</td>';
	
	htmlBody +='<td class="text-center">';
	htmlBody +='<button type="button" class="btn btn-danger" id="remove" value=' + row['JobID'] + '>';
	htmlBody +='<i class="fa fa-minus"></i>';
	htmlBody +='</button>';
	htmlBody +='</td>';

	htmlBody +='</tr>';
	
	return htmlBody;
}
