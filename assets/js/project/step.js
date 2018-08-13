// ************************************************ Event **********************************************
//------------------------------------------------- Step -----------------------------------------------
$('table#step.table-components .add-elements').on('click', addNewStepRowTable);
$('table#step.table-components').on("click", ".delete-elements", deleteStepRowTable);


// ********************************************** Method ***********************************************
//********************************************* Prepare data ******************************************
function prepareStepData(){
	let dsStep = new Array();
	$('table#step.table-components tbody tr').each(function(i, row){
		let dictStep = {
			'stepID'				: $(this).find('input#firstStepFlag').val(),
			'stockID'				:	(isEmpty($(this).find('input#stockID').val()) 
												? 0 : $(this).find('input#stockID').val()),
			'operationTime'	:	$(this).find('input#operationTime').val(),
			'firstStepFlag'	:	(($(this).find('input#firstStepFlag').prop('checked'))? 1: 0),
			'nbSub'					:	$(this).find('td#nbSub').text(),
		};
		dsStep.push(dictStep);
	});

	return dsStep;
}
//********************************************** Validation ********************************************
function validateStock() {
	let result = false;
	let resultOperationTime = true;
	
	// Check require field of step.
	$('table#step.table-components tbody tr').each(function(i, row) {
		// Check Step Number require has input?
		resultOperationTime &= validateFillInputElement($(this).find('input#operationTime'));
	});

	result = resultOperationTime;
	return result;
}



//*********************************************** Tool *************************************************
//----------------------------------------------- Step -------------------------------------------------
//*************************************** Add new row of table *****************************************
function addNewStepRowTable() {
	cloneStepRowTable();
	resetStepLastRowTable();
}
//******************************** Clone row table with auto increment no ******************************
function cloneStepRowTable() {
	let $clone = $("table#step.table-components tbody tr:first-child");
	
	$clone.find('.btn').removeClass('add-elements btn-default').addClass('delete-elements btn-danger')
		.html('<i class="fa fa-minus"></i>');
	
	$clone.clone().appendTo('table#step.table-components tbody');
	
	$clone.find('.btn').removeClass('delete-elements btn-danger').addClass('add-elements btn-default')
		.html('<i class="fa fa-plus"></i>');
}
// *********************************** Delete row table and reset auto increment no *************************
function deleteAllCloneStepRowTable() {
	$('table#step.table-components tbody > tr:not(:first-child)').remove();
	$('table#step.table-components tbody > tr').removeClass('bg-error');
	
	resetStepLastRowTable();
}
function deleteStepRowTable(){
	$(this).closest("tr").remove();
}
//***************************************** Set Step input fill **********************************************
function setStepLastRowTable(dsFullStep, i) {
	let currentTr = $('table#step.table-components tbody tr:last-child');

	currentTr.find('input#firstStepFlag').val(dsFullStep[i].stepID);
	currentTr.find('input#firstStepFlag').prop('checked'
	, ((dsFullStep[i].First_Step_Flag == 1)? true: false));
	currentTr.find('td#nextStepNumber').text(dsFullStep[i].Next_Step_Number);
	currentTr.find('td#stepNumber').text(dsFullStep[i].Number);
	currentTr.find('td#stepDesc').text(dsFullStep[i].DESC);
	currentTr.find('td#lineName').text(dsFullStep[i].lineName);
	currentTr.find('td#machineName').text(dsFullStep[i].machineName);

	currentTr.find('input#stockID').val(dsFullStep[i].stockID);
	currentTr.find('input#operationTime').val(dsFullStep[i].Operation_Time);

	currentTr.find('td#subAssemblyName').text(dsFullStep[i].subAssemblyName);
	currentTr.find('td#nbSub').text(dsFullStep[i].NB_Sub);
}
//************************************** Reset Full Process input fill ***************************************
//----------------------------------------- Reset Step input fill --------------------------------------------
function resetStepLastRowTable(dsFullBom, i) {
	let currentTr = $('table#step.table-components tbody tr:last-child');
	
	currentTr.find('input#firstStepFlag').val(0);
	currentTr.find('input#firstStepFlag').prop('checked', false);
	currentTr.find('td#nextStepNumber').text('');
	currentTr.find('td#stepNumber').text('');

	currentTr.find('td#stepDesc').text('');
	currentTr.find('td#lineName').text('');
	currentTr.find('td#machineName').text('');
	currentTr.find('input#operationTime').val(0);

	currentTr.find('input#stockID').val('');
	currentTr.find('input#operationTime').val('');

	currentTr.find('td#subAssemblyName').text('');
	currentTr.find('td#nbSub').text('');


	// remove class bg-error.
	currentTr.find('input#operationTime').removeClass('bg-error');
}