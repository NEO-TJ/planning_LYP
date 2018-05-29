// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
});

//------------------------------------------------ Component -------------------------------------------
$('button#refresh').click(function () { displayJobList(0); });
$('button#search').click(function () { displayJobList(0); });


//----------------------------------------------- Delegation -------------------------------------------
function paginationChange(pageCode) {
	displayJobList(pageCode);
}






//************************************************ Method **********************************************
//------------------------------------------------ AJAX -----------------------------------------------
function displayJobList(pageCode) {
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayJobTypeID = $('select#jobTypeID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayJobStatusID = $('select#jobStatusID').multiselect("getChecked").map(function() { return this.value; } ).get();

	var data = {
			'jobID'				: arrayJobID,
			'jobTypeID'		: arrayJobTypeID,
			'jobStatusID'	: arrayJobStatusID,
			"pageCode"		: pageCode
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
		success: function(rDataResult) {
			// pagination.
			$('div#paginationLinks').html(rDataResult.paginationLinks);
			
			// datatable.
			$('table#jobRemove tbody').html(rDataResult.htmlTableBody);
		}
	});
}