// ************************************************ Event **********************************************
// ------------------------------------------------ Step -----------------------------------------------
$('select#sourceStep').change(function() {
	changeStep(0);
});
$('select#destinationStep').change(function() {
	changeStep(1);
});

// *********************************************** Method **********************************************
// ------------------------------------------ Change step mode------------------------------------------
function changeStep(stepSelector){
	var jobID = $('select#job').val();
	var stepID = ((stepSelector == 0) ? $('select#sourceStep :selected').val() : $('select#destinationStep :selected').val());
	
	if(stepID == 0){
		disStepNotChoose(stepSelector);
	}
	else {
		var data = { 
					'jobID'		: jobID,
					'stepID'	: stepID,
					'onlyNG'	: stepSelector,
					};

		// Get process table one row by ajax.
		$.ajax({
			url: 'recoveryNG/ajaxGetDsFullStock',
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
			success: function(dsFullStock) {
				if(true){
				if(dsFullStock.length > 0) {
					disStepChoose(stepSelector, dsFullStock);
				}
				else {
					swal("Error", "ไม่พบข้อมูล Job, Step และ  Stock ในฐานข้อมูล กรุณาแจ้งผู้ดูแลระบบ\n" + "Not found Job, Step and Stock in database", "error");
				}
				}
			}
		});
	}
}




//************************************************** Tool ****************************************************
//******************************************** Set Display Flow **********************************************
function disStepNotChoose(stepSelector) {
	fullResetStep(stepSelector);
}
function disStepChoose(stepSelector, dsFullStock) {
	clearWarningInputRequire(stepSelector);
	if(stepSelector == 0) {
		$('select#sourceStep').val(dsFullStock[0].FK_ID_Step);
		$('input#sourceQtyNG').val(dsFullStock[0].Qty_NG);
	}
	else {
		$('select#destinationStep').val(dsFullStock[0].FK_ID_Step);
		$('input#destinationQtyOK').val(dsFullStock[0].Qty_Stock);
	}
}




//************************************** Reset Full Process input fill ***************************************
function fullResetAllStep(){
	fullResetStep(0);
	fullResetStep(1);
}
function fullResetStep(stepSelector){
	clearWarningInputRequire(stepSelector);

	if(stepSelector == 0) {
		$('select#sourceStep').val(0);
		$('input#sourceQtyNG').val('');
		$('input#qtyNGSend').val('');
	}
	else {
		$('select#destinationStep').val(0);
		$('input#destinationQtyOK').val('');
	}
}
function clearWarningInputRequire(stepSelector){
	$('select#job').removeClass('bg-error');

	if(stepSelector == 0) {
		$('select#sourceStep').removeClass('bg-error');
		$('input#qtyNGSend').removeClass('bg-error');
	}
	else {
		$('select#destinationStep').removeClass('bg-error');		
	}
}
