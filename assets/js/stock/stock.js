// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
});

//------------------------------------------------ Component -------------------------------------------
$('button#refresh').click(function () { displayStockList(0); });
$('button#search').click(function () { displayStockList(0); });


//----------------------------------------------- Delegation -------------------------------------------
function paginationChange(pageCode) {
	displayStockList(pageCode);
}






//************************************************ Method **********************************************
//------------------------------------------------ AJAX -----------------------------------------------
function displayStockList(pageCode) {
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; } ).get();

	var data = {
			'jobID'			: arrayJobID,
			'stepID'		: arrayStepID,
			"pageCode"	: pageCode
	};

	// Get workingCapacity report by ajax.
	$.ajax({
		url: 'stockAdjust/ajaxGetDsFullStock',
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
			$('table#stockAdjust tbody').html(rDataResult.htmlTableBody);
		}
	});
}