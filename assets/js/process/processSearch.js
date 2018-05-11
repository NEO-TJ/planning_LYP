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
$('button#search').click(function(e) { displayProcessList(); });




//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//__________________________________________ Get process data _________________________________________
function displayProcessList() {
	let baseUrl = window.location.origin + "/" + window.location.pathname.split('/')[1] + "/";
	let arrayProcessID = $('select#processID').multiselect("getChecked").map(function() { return this.value; }).get();
	//let arrayProcessStatus = $('select#processStatus').multiselect("getChecked").map(function() { return this.value; }).get();

	let data = {
		'processID'			: arrayProcessID,
		//'processStatus'	: arrayProcessStatus,
	};

	// Get top reject report by ajax.
	$.ajax({
		url: baseUrl + 'process/ajaxGetProcessList',
		type: 'post',
		data: data,
		beforeSend: function() {},
		error: function(xhr, textStatus) {
				swal("Error", textStatus + xhr.responseText, "error");
		},
		complete: function() {},
		success: function(htmlTableBody) {
			$('table#view tbody').html(htmlTableBody);
		}
	});
}