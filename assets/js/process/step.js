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
			'stepID': 			$(this).find('td input#firstStepFlag').val(),
			'firstStepFlag': 	(($(this).find('td input#firstStepFlag').prop('checked'))? 1: 0),
			'nextStepNumber': 	$(this).find('td input#nextStepNumber').val(),
			'stepNumber': 		$(this).find('td input#stepNumber').val(),
			'stepDesc': 		$(this).find('td input#stepDesc').val(),
			'lineID': 			$(this).find('td select#line :selected').val(),
			'machineID': 		$(this).find('td select#machine :selected').val(),
			'subAssemblyID': 	$(this).find('td select#subAssembly :selected').val(),
			'nbSub': 			$(this).find('td input#nbSub').val(),
		};
		dsStep.push(dictStep);
	});
	
	return dsStep;
}
//********************************************** Validation ********************************************
function validateStep() {
	let result = false;
	
	let resultStepNumber = true;
	let resultLineID = true;
	let resultMachineID = true;
	let resultSubAssemblyID = true;
	let resultNBSub = true;
	
	// Check require field of step.
	$('table#step.table-components tbody tr').each(function(i, row) {
		// Check Step Number require has input?
		resultStepNumber &= validateFillInputElement($(this).find('td input#stepNumber'));
		// Check Line id selected?
		resultLineID &= validateFillSelectElement($(this).find('td select#line'));
		// Check Line id selected?
		resultMachineID &= validateFillSelectElement($(this).find('td select#machine'));
		// Check Line id selected?
		resultSubAssemblyID &= validateFillSelectElement($(this).find('td select#subAssembly'));
		// Check Step Number require has input?
		resultNBSub &= validateFillInputElement($(this).find('td input#nbSub'));
	});
	
	
	result = (resultStepNumber && resultLineID && resultMachineID && resultSubAssemblyID && resultNBSub);
	return result;
}



//*********************************************** Tool *************************************************
//----------------------------------------------- Step -------------------------------------------------
//*************************************** Add new row of table *****************************************
function addNewStepRowTable() {
	cloneStepRowTable();
	resetStepLastRowTable();

	let $lastTr = $('table#step.table-components tbody tr:last-child');
	$lastTr.find('input#firstStepFlag').focus();
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
	
	currentTr.find('td input#firstStepFlag').val(dsFullStep[i].id);
	currentTr.find('td input#firstStepFlag').prop('checked', ((dsFullStep[i].First_Step_Flag == 1)? true: false));
	currentTr.find('td input#nextStepNumber').val(dsFullStep[i].Next_Step_Number);
	currentTr.find('td input#stepNumber').val(dsFullStep[i].Number);
	currentTr.find('td input#stepDesc').val(dsFullStep[i].DESC);
	currentTr.find('td select#line').val(dsFullStep[i].FK_ID_Line);
	currentTr.find('td select#machine').val(dsFullStep[i].FK_ID_Machine);
	currentTr.find('td select#subAssembly').val(dsFullStep[i].FK_ID_Sub_Assembly);
	currentTr.find('td input#nbSub').val(dsFullStep[i].NB_Sub);
}
//************************************** Reset Full Process input fill ***************************************
//----------------------------------------- Reset Step input fill --------------------------------------------
function resetStepLastRowTable(dsFullBom, i) {
	let currentTr = $('table#step.table-components tbody tr:last-child');
	
	currentTr.find('td input#firstStepFlag').val(0);
	currentTr.find('td input#firstStepFlag').prop('checked', false);
	currentTr.find('td input#nextStepNumber').val('');
	currentTr.find('td input#stepNumber').val('');
	currentTr.find('td input#stepDesc').val('');
	currentTr.find('td select#line').val(0);
	currentTr.find('td select#machine').val(0);
	currentTr.find('td select#subAssembly').val(0);
	currentTr.find('td input#nbSub').val('');

	currentTr.find('td input#firstStepFlag').removeClass('bg-error');
	currentTr.find('td select#nextStepNumber').removeClass('bg-error');
	currentTr.find('td input#stepNumber').removeClass('bg-error');
	currentTr.find('td input#stepDesc').removeClass('bg-error');
	currentTr.find('td select#line').removeClass('bg-error');
	currentTr.find('td select#machine').removeClass('bg-error');
	currentTr.find('td select#subAssembly').removeClass('bg-error');
	currentTr.find('td input#nbSub').removeClass('bg-error');
}