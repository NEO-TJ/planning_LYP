// ************************************************ Event **********************************************
// ------------------------------------------------ Step -----------------------------------------------
$('select#sourceStep').change(function() {
	changeStep();
});

$('table#destinationStepTable').on('click','#destinationCheck', toggleDestinationElement);
$('table#destinationStepTable').on('change','#receiveNgQty', validateReceiveNgQty);
$('input#qtyNGSend').on('change', setNgQtySendToDest);



// *********************************************** Method **********************************************
// ------------------------------------- Toggle check destination step ---------------------------------
function toggleDestinationElement() {
	$(this).closest("tr").find('input#receiveNgQty').prop('disabled', !this.checked);

	if(this.checked) {
		$(this).closest("tr").find('input#receiveNgQty').val(
			calcQtyWithNbSub($(this).closest("tr").find('input#destinationNbSub').val())
		);
		$(this).closest("tr").addClass("bg-info");
	} else {
		$(this).closest("tr").find('input#receiveNgQty').val(0);
		$(this).closest("tr").removeClass("bg-info");	
	}
}
// ---------------------------------------- Change receive ng qty --------------------------------------
function validateReceiveNgQty() {
	let maxReceive = calcQtyWithNbSub($(this).closest("tr").find('input#destinationNbSub').val());
	if($(this).val() > maxReceive) {
		swal("Warning"
			, "This step can receive maximum NG qty at : " + maxReceive + "\nPlease check data."
			, "warning");

		$(this).val(maxReceive);
	}
}
// ------------------------------------------ Change send ng qty ---------------------------------------
function setNgQtySendToDest() {
	$('table#destinationStepTable > tbody > tr').each(function() {
		$(this).closest("tr").find('input#receiveNgQty').val(
			calcQtyWithNbSub($(this).closest("tr").find('input#destinationNbSub').val())
		);
	});
}






//************************************************** AJAX **********************************************
// ------------------------------------------ Change step mode------------------------------------------
function changeStep(){
	let jobID = $('select#job').val();
	let stepID = $('select#sourceStep :selected').val();
	
	if(stepID == 0){ fullResetAllStep(); }
	else {
		let data = { 
			'jobID'		: jobID,
			'stepID'	: stepID,
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
			success: function(dsSourceDestStepStock) {
				if(Object.keys(dsSourceDestStepStock).length > 0) {
					disStepChoose(dsSourceDestStepStock);
				} else {
					swal("Error", "ไม่พบข้อมูล Job, Step และ  Stock ในฐานข้อมูล กรุณาแจ้งผู้ดูแลระบบ\n" + "Not found Job, Step and Stock in database", "error");
				}
			}
		});
	}
}





//************************************************** Tool ****************************************************
//******************************************** Set Display Flow **********************************************
function disStepChoose(dsSourceDestStepStock) {
	clearWarningInputRequire();
	// Source Step.
	if(Object.keys(dsSourceDestStepStock['dsSourceStepStock'][0]).length > 0) {
		$('select#sourceStep').val(dsSourceDestStepStock['dsSourceStepStock'][0].stepId);
		$('input#sourceSubAssembly').val(dsSourceDestStepStock['dsSourceStepStock'][0].subAssamblyName);
		$('input#sourceQtyNG').val(dsSourceDestStepStock['dsSourceStepStock'][0].Qty_NG);
		$('input#sourceNbSub').val(dsSourceDestStepStock['dsSourceStepStock'][0].nbSub);
		$('input#qtyNGSend').prop('disabled', false);
	}
	// Destination Step.
	let dsDestStepStockLength = Object.keys(dsSourceDestStepStock['dsDestStepStock']).length;
	if(dsDestStepStockLength > 0) {
		let htmlTbody = "";
		let calcSourceNbSub = ($('input#sourceNbSub').val() > 0 ? $('input#sourceNbSub').val() : 1);
		for(let i=0; i < dsDestStepStockLength; i++) {
			htmlTbody += genHtmlTbodyDestStepStock(dsSourceDestStepStock['dsDestStepStock'][i]);
		}
		$('table#destinationStepTable tbody').html(htmlTbody);
	}
}

function genHtmlTbodyDestStepStock(dsFullStep) {
	let eleChecked = '';
	let eleDisabled = ' disabled';
	let valReceiveNgQty = '';
	let eleClassBgColor = '';

	if(dsFullStep.stepId > 0) {
		eleChecked = ' checked';
		eleDisabled = '';
		eleClassBgColor = ' class="bg-info"';
		if($('input#qtyNGSend').val() == "") {
			valReceiveNgQty = "";
		} else {
			valReceiveNgQty = calcQtyWithNbSub(dsFullStep.nbSub);
		}
	}
	let htmlTbody = '<tr' + eleClassBgColor + '>'
		+ '<td class="text-center td-group">'
			+ '<input type="checkbox" class="form-control td-group" id="destinationCheck"'
			+ 'type="text" name="destinationCheck[]"' + eleChecked + eleDisabled
			+ ' value="' + dsFullStep.stepId + '" />'
		+ '</td>'
		+ '<td class="text-center td-group">'
			+ '<input class="form-control text-center textLeft" id="destinationStep"'
			+ 'type="text" name="destinationStep[]" disabled value="' + dsFullStep.stepDesc + '">'
		+ '</td>'
		+ '<td class="text-center td-group">'
			+ '<input class="form-control text-center textLeft" id="destinationSubAssembly"'
			+ 'type="text" name="destinationSubAssembly[]" disabled value="' + dsFullStep.subAssamblyName + '">'
		+ '</td>'
		+ '<td class="text-center td-group">'
			+ '<input class="form-control text-center textRight" id="receiveNgQty"'
			+ 'type="number" name="receiveNgQty[]"' + eleDisabled 
			+ ' value="' + valReceiveNgQty + '">'

			+ '<input class="form-control text-center textRight hidden" id="destinationNbSub"'
			+ 'type="number" name="destinationNbSub[]" value="' + dsFullStep.nbSub + '">'
		+ '</td>'
	+ '</tr>"';

	return htmlTbody;
}

function calcQtyWithNbSub(destinationNbSub) {
	let valReceiveNgQty = ( ($('input#qtyNGSend').val() == "") ? 0 : $('input#qtyNGSend').val() )
		* (destinationNbSub
		/ ($('input#sourceNbSub').val() == 0) || ($('input#sourceNbSub').val() == "")
		? 1 : $('input#sourceNbSub').val());
	
	return valReceiveNgQty;
}

//************************************** Reset Full Process input fill ***************************************
function fullResetAllStep(){
	clearWarningInputRequire();
	// Source Step.
	$('select#sourceStep').val(0);
	$('input#sourceSubAssembly').val('');
	$('input#sourceQtyNG').val('');
	$('input#qtyNGSend').val('');
	$('input#qtyNGSend').prop('disabled', true);
	// Destination Step.
	dsFullStep = {stepId : 0, stepDesc : "", subAssamblyName : "", nbSub : 1};
	$('table#destinationStepTable tbody').html(genHtmlTbodyDestStepStock(dsFullStep));
	$('input#destinationCheck').prop('checked', false);
}
function clearWarningInputRequire(){
	$('select#job').removeClass('bg-error');
	// Source Step.
	$('select#sourceStep').removeClass('bg-error');
	$('input#qtyNGSend').removeClass('bg-error');
	// Destination Step.
	//$('select#destinationStep').removeClass('bg-error');
}