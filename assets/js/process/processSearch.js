// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Overall %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ********************************************** Variable *********************************************
const dltOK = 0;					// Success
const dltValidate = 1;				// Validate error
// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	// Multiselect.
	$('select#processID').multiselect({
		header: true,
		noneSelectedText: 'Default selected all process',
	}).multiselectfilter();

	$('select#processStatus').multiselect({
		header: true,
		noneSelectedText: 'Default selected all process status',
	}).multiselectfilter();

	// UI block style.
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
});
//------------------------------------------------- Search ---------------------------------------------
$('button#search').click(function(e) { displayProcessList(0); });


//----------------------------------------------- Delegation -------------------------------------------
function paginationChange(pageCode) {
	displayProcessList(pageCode);
}





//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//__________________________________________ Get process data _________________________________________
function displayProcessList(pageCode) {
	let baseUrl = window.location.origin + "/" + window.location.pathname.split('/')[1] + "/";
	let rProcessID = $('select#processID').multiselect("getChecked").map(function() { return this.value; }).get();
	//let arrayProcessStatus = $('select#processStatus').multiselect("getChecked").map(function() { return this.value; }).get();

	let data = {
		'rProcessID'		: rProcessID,
		//'processStatus'	: arrayProcessStatus,
		"pageCode" 			: pageCode
	};

	// Get top reject report by ajax.
	$.ajax({
		url: baseUrl + 'process/ajaxGetProcessList',
		type: 'post',
		data: data,
		dataType: 'json',
		beforeSend: function() {},
		error: function(xhr, textStatus) {
				swal("Error", textStatus + xhr.responseText, "error");
		},
		complete: function() {},
		success: function(rDataResult) {
			// pagination.
			$('div#paginationLinks').html(rDataResult.paginationLinks);

			// datatable.
			$('table#view tbody').html(rDataResult.htmlTableBody);
		}
	});
}