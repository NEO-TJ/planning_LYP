// ************************************************ Event **********************************************
// ------------------------------------------------ Step -----------------------------------------------
$('select#step').change(changeStep);

// *********************************************** Method **********************************************
// ------------------------------------------ Change step mode------------------------------------------
function changeStep(){
	let stepID = $('select#step :selected').val();
	
	if(stepID == 0){
		disStepNotChoose();
	} else {
		let data = { 'stepID': stepID };

		// Get process table one row by ajax.
		$.ajax({
			url: 'qtyInput/ajaxGetDsStepOneRow',
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
			success: function(dsSubAssembly) {
				if(dsSubAssembly.length === 0) {
					swal("Error", "ไม่พบข้อมูล Job และ Step ในฐานข้อมูล กรุณาแจ้งผู้ดูแลระบบ\n"
					+ "Not found Job and Step in database", "error");
				} else {
					disStepChoose(dsSubAssembly);
				}
			}
		});
	}
}




//************************************************** Tool ****************************************************
//******************************************** Set Display Flow **********************************************
function disStepNotChoose() {
	resetQtyInputFill();
	
	$('select#worker').prop('disabled', true);
	$('input#qtyOK').prop('disabled', true);
	$('table#ng').find("input,button,textarea,select").prop('disabled', true);
	
	$('button#saveQtyInput').prop('disabled', true);
	$('button#resetQtyInput').prop('disabled', true);

	$('input#qtyOK').val(0);
	$('input#totalQtyOK').val(0);
	$('input#qtyNG').val(0);
	$('input#totalQtyNG').val(0);
}
function disStepChoose(dsSubAssembly) {
	resetQtyInputFill();
	setSubAssemblySelectElement(dsSubAssembly);
	
	$('select#worker').prop('disabled', false);
	$('input#qtyOK').prop('disabled', false);
	$('table#ng').find("input,button,textarea,select").prop('disabled', false);
	
	$('button#saveQtyInput').prop('disabled', false);
	$('button#resetQtyInput').prop('disabled', false);

	$('input#qtyOK').val(0);
	$('input#totalQtyOK').val(0);
	$('input#qtyNG').val(0);
	$('input#totalQtyNG').val(0);
}

function setSubAssemblySelectElement(dataSet = []) {
	$('select#subAssembly').empty();
	$('select#subAssembly').append('<option value="0">Please select Sub Assembly' + '</option>');

	$.each(dataSet, function (key, value) {
		$('select#subAssembly').append('<option value="' + value.id + '">' + value.Name + '</option>');
	});
}




//************************************** Reset Full Process input fill ***************************************
function resetQtyInputFill(){
	clearWarningInputRequire();

	$('select#worker').val(0);
	$('input#qtyOK').val(0);
	$('input#totalQtyOK').val('');
	
	$('select#subAssembly').val(0);
	$('select#defect').val(0);
	$('input#qtyNG').val(0);
	
	deleteAllCloneNGRowTable();
}
function clearWarningInputRequire(){
	$('select#worker').removeClass('bg-error');
	$('input#qtyOK').removeClass('bg-error');
	
	$('select#subAssembly').removeClass('bg-error');
	$('select#defect').removeClass('bg-error');
	$('input#qtyNG').removeClass('bg-error');
}
