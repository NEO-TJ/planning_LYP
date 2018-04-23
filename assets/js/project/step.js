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
			'stepID': 			$(this).find('td:nth-child(1) input#firstStepFlag').val(),
			'firstStepFlag': 	(($(this).find('td:nth-child(1) input#firstStepFlag').prop('checked'))? 1: 0),
			'nextStepNumber': 	$(this).find('td:nth-child(2) input#nextStepNumber').val(),
			'stepNumber': 		$(this).find('td:nth-child(3) input#stepNumber').val(),
			'stepDesc': 		$(this).find('td:nth-child(4) input#stepDesc').val(),
			'lineID': 			$(this).find('td:nth-child(5) select#line :selected').val(),
			'machineID': 		$(this).find('td:nth-child(6) select#machine :selected').val(),
			'operationTime': 	( ($(this).find('td:nth-child(7) input#operationTime').val() ) / 60),
			'subAssemblyID': 	$(this).find('td:nth-child(8) select#subAssemble :selected').val(),
			'nbSub': 			$(this).find('td:nth-child(9) input#nbSub').val(),
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
	let resultOperationTime = true;
	let resultSubAssemblyID = true;
	let resultNBSub = true;
	
	// Check require field of step.
	$('table#step.table-components tbody tr').each(function(i, row) {
		// Check Step Number require has input?
		resultStepNumber &= validateFillInputElement($(this).find('td:nth-child(3) input#stepNumber'));
		// Check Line id selected?
		resultLineID &= validateFillSelectElement($(this).find('td:nth-child(5) select#line'));
		// Check Line id selected?
		resultMachineID &= validateFillSelectElement($(this).find('td:nth-child(6) select#machine'));
		// Check Step Number require has input?
		resultOperationTime &= validateFillInputElement($(this).find('td:nth-child(7) input#operationTime'));
		// Check Line id selected?
		resultSubAssemblyID &= validateFillSelectElement($(this).find('td:nth-child(8) select#subAssemble'));
		// Check Step Number require has input?
		resultNBSub &= validateFillInputElement($(this).find('td:nth-child(9) input#nbSub'));
	});
	
	
	result = (resultStepNumber && resultLineID && resultMachineID && resultOperationTime && resultSubAssemblyID && resultNBSub);
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
	
	currentTr.find('td:nth-child(1) input#firstStepFlag').val(dsFullStep[i].id);
	currentTr.find('td:nth-child(1) input#firstStepFlag').prop('checked', ((dsFullStep[i].First_Step_Flag == 1)? true: false));
	currentTr.find('td:nth-child(2) input#nextStepNumber').val(dsFullStep[i].Next_Step_Number);
	currentTr.find('td:nth-child(3) input#stepNumber').val(dsFullStep[i].Number);
	currentTr.find('td:nth-child(4) input#stepDesc').val(dsFullStep[i].DESC);
	currentTr.find('td:nth-child(5) select#line').val(dsFullStep[i].FK_ID_Line);
	currentTr.find('td:nth-child(6) select#machine').val(dsFullStep[i].FK_ID_Machine);
	currentTr.find('td:nth-child(7) input#operationTime').val(dsFullStep[i].Operation_Time * 60);
	currentTr.find('td:nth-child(8) select#subAssemble').val(dsFullStep[i].FK_ID_Sub_Assembly);
	currentTr.find('td:nth-child(9) input#nbSub').val(dsFullStep[i].NB_Sub);
}
//************************************** Reset Full Process input fill ***************************************
//----------------------------------------- Reset Step input fill --------------------------------------------
function resetStepLastRowTable(dsFullBom, i) {
	let currentTr = $('table#step.table-components tbody tr:last-child');
	
	currentTr.find('td:nth-child(1) input#firstStepFlag').val(0);
	currentTr.find('td:nth-child(1) input#firstStepFlag').prop('checked', false);
	currentTr.find('td:nth-child(2) input#nextStepNumber').val('');
	currentTr.find('td:nth-child(3) input#stepNumber').val('');
	currentTr.find('td:nth-child(4) input#stepDesc').val('');
	currentTr.find('td:nth-child(5) select#line').val(0);
	currentTr.find('td:nth-child(6) select#machine').val(0);
	currentTr.find('td:nth-child(7) input#operationTime').val('');
	currentTr.find('td:nth-child(8) select#subAssemble').val(0);
	currentTr.find('td:nth-child(9) input#nbSub').val('');

	currentTr.find('td:nth-child(1) input#firstStepFlag').removeClass('bg-error');
	currentTr.find('td:nth-child(2) select#rm').removeClass('bg-error');
	currentTr.find('td:nth-child(3) input#qty').removeClass('bg-error');
	currentTr.find('td:nth-child(4) input#stepDesc').removeClass('bg-error');
	currentTr.find('td:nth-child(5) select#line').removeClass('bg-error');
	currentTr.find('td:nth-child(6) select#machine').removeClass('bg-error');
	currentTr.find('td:nth-child(7) input#operationTime').removeClass('bg-error');
	currentTr.find('td:nth-child(8) select#subAssemble').removeClass('bg-error');
	currentTr.find('td:nth-child(9) input#nbSub').removeClass('bg-error');
}