// ************************************************ Event **********************************************
//-------------------------------------------------- NG ------------------------------------------------
$('table#ng.table-components .add-elements').on('click', addNewNGRowTable);
$('table#ng.table-components').on("click", ".delete-elements", deleteNGRowTableAutoIncrementNo);
$('input#qtyOK').change(function(e) {
	$('input#totalQtyOK').val($('input#qtyOK').val());
});
$(document).on("change", "input#qtyNG", function() {
	var total = 0;
	$("input[id='qtyNG']").each(function() {
		total += parseInt($(this).val());
	});
	
	$('input#totalQtyNG').val(total);
});



// ********************************************** Method ***********************************************
//********************************************** Validation ********************************************
function validateAllNG() {
	var result = false;
	
	var resultSubAssemblyID = true;
	var resultDefectID = true;
	var resultQtyNG = true;
	
	// Check require field of step.
	$('table#ng.table-components tbody tr').each(function(i, row) {
		var objSubAssemblyID = $(this).find('td:nth-child(2) select#subAssembly :selected');
		var objDefectID = $(this).find('td:nth-child(3) select#defect :selected');
		var objQtyNG = $(this).find('td:nth-child(4) input#qtyNG');
		
		// Check Qty NG has input?
		if( isEmpty(objQtyNG.val()) ) {
			$(this).find('td:nth-child(4) input#qtyNG').addClass('bg-error');
			resultQtyNG &= false;
		}
		else if( ($('table#ng.table-components tbody tr').length == 1) && (i == 0) && (objQtyNG.val() == 0)
				&& (objSubAssemblyID.val() < 1) && (objDefectID.val() < 1)) {
			$(this).find('td:nth-child(2) select#subAssembly').removeClass('bg-error');
			$(this).find('td:nth-child(3) select#defect').removeClass('bg-error');
			$(this).find('td:nth-child(4) input#qtyNG').removeClass('bg-error');
			return true;
		}
		else if((objSubAssemblyID.val() > 0) || (objDefectID.val() > 0) || !(isEmpty(objQtyNG.val()))) {
			if(!((objSubAssemblyID.val() > 0) && (objDefectID.val() > 0) 
					&& !(isEmpty(objQtyNG.val())) && (objQtyNG.val() > 0))) {
				$(this).find('td:nth-child(2) select#subAssembly').addClass('bg-error');
				$(this).find('td:nth-child(3) select#defect').addClass('bg-error');
				$(this).find('td:nth-child(4) input#qtyNG').addClass('bg-error');
				resultQtyNG &= false;
			}
		}
		else {
			$(this).find('td:nth-child(4) input#qtyNG').removeClass('bg-error');
		}

		// Check Sub Assembly id?
		if(objSubAssemblyID.val() == 0) {
			$(this).find('td:nth-child(2) select#subAssembly').addClass('bg-error');
			resultSubAssemblyID &= false;
		}
		else {
			$(this).find('td:nth-child(2) select#subAssembly').removeClass('bg-error');
		}
		
		// Check Defect id selected?
		if(objDefectID.val() == 0) {
			$(this).find('td:nth-child(3) select#defect').addClass('bg-error');
			resultDefectID &= false;
		}
		else {
			$(this).find('td:nth-child(3) select#defect').removeClass('bg-error');
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
	var $clone = $("table#ng.table-components tbody tr:first-child");
	
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
	
	var n = 0;
	$('table#ng.table-components tbody tr').each(function(){
		n++;
		$(this).find('td:first-child').html(n);
	});
}
//******************************************** Reset NG input fill *************************************
function resetNGLastRowTable(dsFullBom, i) {
	var currentTr = $('table#ng.table-components tbody tr:last-child');
	
	currentTr.find('td:nth-child(2) select#subAssemble').val(0);
	currentTr.find('td:nth-child(3) select#defect').val(0);
	currentTr.find('td:nth-child(4) input#qtyNG').val('');

	currentTr.find('td:nth-child(2) select#subAssemble').removeClass('bg-error');
	currentTr.find('td:nth-child(3) select#defect').removeClass('bg-error');
	currentTr.find('td:nth-child(4) input#qtyNG').removeClass('bg-error');
}