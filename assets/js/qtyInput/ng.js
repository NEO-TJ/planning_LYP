// ************************************************ Event **********************************************
//-------------------------------------------------- NG ------------------------------------------------
$('table#ng.table-components .add-elements').on('click', addNewNGRowTable);
$('table#ng.table-components').on("click", ".delete-elements", deleteNGRowTableAutoIncrementNo);
$('input#qtyOK').change(function(e) {
	$('input#totalQtyOK').val($('input#qtyOK').val());
});
$(document).on("change", "input#qtyNG", function() {
	let total = 0;
	$("input[id='qtyNG']").each(function() {
		total += parseInt($(this).val());
	});
	
	$('input#totalQtyNG').val(total);
});



// ********************************************** Method ***********************************************
//********************************************** Validation ********************************************
function validateAllNG() {
	let result = false;
	
	let resultSubAssemblyID = true;
	let resultDefectID = true;
	let resultQtyNG = true;
	
	// Check require field of step.
	$('table#ng.table-components tbody tr').each(function(i, row) {
		let objSubAssemblyID = $(this).find('select#subAssembly :selected');
		let objDefectID = $(this).find('select#defect :selected');
		let objQtyNG = $(this).find('input#qtyNG');
		
		// Check Qty NG has input?
		if( isEmpty(objQtyNG.val()) ) {
			$(this).find('input#qtyNG').addClass('bg-error');
			resultQtyNG &= false;
		}
		else if( ($('table#ng.table-components tbody tr').length == 1) && (i == 0) && (objQtyNG.val() == 0)
				&& (objSubAssemblyID.val() < 1) && (objDefectID.val() < 1)) {
			$(this).find('select#subAssembly').removeClass('bg-error');
			$(this).find('select#defect').removeClass('bg-error');
			$(this).find('input#qtyNG').removeClass('bg-error');
			return true;
		}
		else if((objSubAssemblyID.val() > 0) || (objDefectID.val() > 0) || !(isEmpty(objQtyNG.val()))) {
			if(!((objSubAssemblyID.val() > 0) && (objDefectID.val() > 0) 
					&& !(isEmpty(objQtyNG.val())) && (objQtyNG.val() > 0))) {
				$(this).find('select#subAssembly').addClass('bg-error');
				$(this).find('select#defect').addClass('bg-error');
				$(this).find('input#qtyNG').addClass('bg-error');
				resultQtyNG &= false;
			}
		}
		else {
			$(this).find('input#qtyNG').removeClass('bg-error');
		}

		// Check Sub Assembly id?
		if(objSubAssemblyID.val() == 0) {
			$(this).find('select#subAssembly').addClass('bg-error');
			resultSubAssemblyID &= false;
		}
		else {
			$(this).find('select#subAssembly').removeClass('bg-error');
		}
		
		// Check Defect id selected?
		if(objDefectID.val() == 0) {
			$(this).find('select#defect').addClass('bg-error');
			resultDefectID &= false;
		}
		else {
			$(this).find('select#defect').removeClass('bg-error');
		}
	});
	
	result = (resultSubAssemblyID && resultDefectID && resultQtyNG);
	return result;
}



//*********************************************** Tool *************************************************
//------------------------------------------------ NG --------------------------------------------------
//*************************************** Add new row of table *****************************************
function addNewNGRowTable() {
	cloneNGRowTableAutoIncrementNo();
	resetNGLastRowTable();
}
//******************************** Clone row table with auto increment no ******************************
function cloneNGRowTableAutoIncrementNo() {
	let $clone = $("table#ng.table-components tbody tr:first-child");
	
	$clone.find('.btn').removeClass('add-elements btn-default').addClass('delete-elements btn-danger')
		.html('<i class="fa fa-minus"></i>');
	$clone.find('td:first-child').html($('table#ng.table-components tbody tr').length + 1);
	
	$clone.clone().appendTo('table#ng.table-components tbody');
	
	$clone.find('td:first-child').html(1);
	$clone.find('.btn').removeClass('delete-elements btn-danger').addClass('add-elements btn-default')
		.html('<i class="fa fa-plus"></i>');
}
// *********************************** Delete row table and reset auto increment no ********************
function deleteAllCloneNGRowTable() {
	$('table#ng.table-components tbody > tr:not(:first-child)').remove();
	$('table#ng.table-components tbody > tr').removeClass('bg-error');
	
	resetNGLastRowTable();
	$('input#totalQtyNG').val('');
}
function deleteNGRowTableAutoIncrementNo(){
	$(this).closest("tr").remove();
	
	let n = 0;
	$('table#ng.table-components tbody tr').each(function(){
		n++;
		$(this).find('td:first-child').html(n);
	});
}
//******************************************** Reset NG input fill *************************************
function resetNGLastRowTable() {
	let currentTr = $('table#ng.table-components tbody tr:last-child');
	
	currentTr.find('select#subAssembly').val(0);
	currentTr.find('select#defect').val(0);
	currentTr.find('input#qtyNG').val(0);

	currentTr.find('select#subAssembly').removeClass('bg-error');
	currentTr.find('select#defect').removeClass('bg-error');
	currentTr.find('input#qtyNG').removeClass('bg-error');
}