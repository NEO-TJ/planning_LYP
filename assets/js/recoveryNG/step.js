// ************************************************ Event **********************************************
// ------------------------------------------------ Step -----------------------------------------------
$('select#sourceStep').change(function() { changeSourceStep(); });
$('select#destinationStep').change(function() { changeDestinationStep(); });

// ---------------------------------- Destination stock table and child --------------------------------
$('input#qtyNGSend').on('change', setNgQtySendToDest);
$('table#destinationStepTable').on('click','#destinationCheck', toggleDestinationElement);
$('table#destinationStepTable').on('change','#receiveNgQty', validateReceiveNgQty);



// *********************************************** Method **********************************************
// ------------------------------------------ Change send ng qty ---------------------------------------
function setNgQtySendToDest() {
	if( parseInt($(this).val()) > parseInt($('input#sourceQtyNG').val()) ) {
		swal("Warning"
			, "You can not send NG qty more than total Qty NG : " + parseInt($('input#sourceQtyNG').val())
			, "warning");
		$(this).val(parseInt($('input#sourceQtyNG').val()));
	}
	$('table#destinationStepTable input:checkbox').each(function() {
		if(this.checked) {
			$(this).closest("tr").find('input#receiveNgQty').val(
				calcQtyWithNbSub($(this).closest("tr").find('input#destinationNbSub').val())
			);
		}
	});
}
// ------------------------------------- Toggle check destination step ---------------------------------
function toggleDestinationElement() {
	// Toggle attribute enable and class bg-info.
	$(this).closest("tr").find('input#receiveNgQty').prop('disabled', !this.checked);
	((this.checked) 
		? $(this).closest("tr").addClass("bg-info")
		: $(this).closest("tr").removeClass("bg-info"));

	// Calculate Dest-NBSub.
	$(this).closest("tr").find('input#receiveNgQty').val(
		calcQtyWithNbSub($(this).closest("tr").find('input#destinationNbSub').val())
	);
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






//************************************************** AJAX **********************************************
// ---------------------------------------- Change source step mode-------------------------------------
function changeSourceStep(){
	let jobID = $('select#job').val();
	let stepID = $('select#sourceStep :selected').val();

	if(stepID == 0){
		fullResetAllStep();
	} else {
		let data = { 
			'jobID'		: jobID,
			'stepID'	: stepID,
		};

		// Get process table one row by ajax.
		$.ajax({
			url: 'recoveryNG/ajaxGetDsFullSourceStock',
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
			success: function(dsSourceStepStock) {
				if(Object.keys(dsSourceStepStock).length > 0) {
					disSourceStockChoose(dsSourceStepStock);
				} else {
					swal("Error", "Step ที่ท่านเลือก ไม่พบข้อมูล Stock ในฐานข้อมูล\n"
					+ "โปรดตรวจสอบขั้นตอนต่างๆในโปรแซส\n"
					+ " หรือแจ้งผู้ดูแลระบบ\n"
					+ "Not found Stock of this step in database", "error");
				}
			}
		});
	}
}

// -------------------------------------- Change destination step mode----------------------------------
function changeDestinationStep(){
	let jobID = $('select#job').val();
	let stepID = $('select#destinationStep :selected').val();

	if(stepID == 0){ resetDestinationStock(); }
	else {
		let data = {
			'jobID'		: jobID,
			'stepID'	: stepID,
		};

		// Get process table one row by ajax.
		$.ajax({
			url: 'ajaxService/ajaxGetDsFullDestinationStock',
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
			success: function(dsDestinationStepStock) {
				if(Object.keys(dsDestinationStepStock).length > 0) {
					disDestinationStockChoose(dsDestinationStepStock);
				} else {
					resetDestinationStock();
					swal("Error", "Step ที่ท่านเลือก ไม่พบข้อมูล Stock ในฐานข้อมูล\n"
						+ "โปรดตรวจสอบขั้นตอนต่างๆในโปรแซส\n"
						+ " หรือแจ้งผู้ดูแลระบบ\n"
						+ "Not found Stock of this step in database", "error");
				}
			}
		});
	}
}





//************************************************** Tool ****************************************************
//******************************************* Set Display Source *********************************************
function disSourceStockChoose(dsSourceStepStock) {
	clearSourceStepWarningInputRequire();
	// Source Step.
	if(Object.keys(dsSourceStepStock[0]).length > 0) {
		$('select#sourceStep').val(dsSourceStepStock[0].stepId);
		$('input#sourceSubAssembly').val(dsSourceStepStock[0].subAssamblyName);
		$('input#sourceQtyNG').val(dsSourceStepStock[0].Qty_NG);
		$('input#sourceNbSub').val(dsSourceStepStock[0].nbSub);
		$('input#qtyNGSend').prop('disabled', false);
	}
	// Destination Step.
	$('select#destinationStep').prop('disabled', false);
}
//****************************************** Set Display Destination *******************************************
function disDestinationStockChoose(dsDestinationStepStock) {
	clearDestinationStepWarningInputRequire();

	let dsDestinationStepStockLength = Object.keys(dsDestinationStepStock).length;
	if(dsDestinationStepStockLength > 0) {
		let htmlTbody = "";
		let calcSourceNbSub = ($('input#sourceNbSub').val() > 0 ? $('input#sourceNbSub').val() : 1);
		for(let i=0; i < dsDestinationStepStockLength; i++) {
			htmlTbody += genHtmlTbodyDestStock(dsDestinationStepStock[i], i);
		}
		$('table#destinationStepTable tbody').html(htmlTbody);
	}
}

function genHtmlTbodyDestStock(dsFullStep, i) {
	let valReceiveNgQty = ($('input#qtyNGSend').val() == "")
		? "" : calcQtyWithNbSub(dsFullStep.nbSub);

	let htmlTbody = '<tr class="bg-info">'
		+ '<td class="text-center td-group">'
			+ '<input type="checkbox" class="form-control td-group" id="destinationCheck"'
			+ 'type="text" name="destinationCheck[]" checked'
			+ ' value="' + dsFullStep.stepId + '" />'
		+ '</td>'
		+ '<td class="text-center td-group">'
			+ '<input class="form-control text-center textLeft" id="destinationStock"'
			+ 'type="text" name="destinationStock[]" disabled value="' + dsFullStep.stepDesc + '">'
		+ '</td>'
		+ '<td class="text-center td-group">'
			+ '<input class="form-control text-center textLeft" id="destinationSubAssembly"'
			+ 'type="text" name="destinationSubAssembly[]" disabled value="' + dsFullStep.subAssamblyName + '">'
		+ '</td>'
		+ '<td class="text-center td-group">'
			+ '<input class="form-control text-center textRight" id="receiveNgQty"'
			+ 'type="number" name="receiveNgQty[]"' 
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
	clearAllWarningInputRequire();
	// Source Step.
	$('select#sourceStep').val(0);
	$('input#sourceSubAssembly').val('');
	$('input#sourceQtyNG').val('');
	$('input#qtyNGSend').val('');
	$('input#qtyNGSend').prop('disabled', true);
	// Destination Step.
	$('select#destinationStep').val(0);
	$('select#destinationStep').prop('disabled', true);
	resetDestinationStock();
}
function resetDestinationStock() {
	dsFullStep = {stepId : 0, stepDesc : "", subAssamblyName : "", nbSub : 1};
	$('table#destinationStepTable tbody').html(genHtmlTbodyDestStock(dsFullStep, 0));
	$('input#destinationCheck').prop('checked', false);
	$('input#destinationCheck').prop('disabled', true);
	$('input#receiveNgQty').prop('disabled', true);
	$('input#receiveNgQty').val('');
}

//**************************************** Clear warning input require ****************************************
function clearAllWarningInputRequire(){
	$('select#job').removeClass('bg-error');
	$('select#worker').removeClass('bg-error');
	// Source Step.
	clearSourceStepWarningInputRequire();
	// Destination Step.
	clearDestinationStepWarningInputRequire();
}
function clearSourceStepWarningInputRequire(){
	$('select#sourceStep').removeClass('bg-error');
	$('input#qtyNGSend').removeClass('bg-error');
}
function clearDestinationStepWarningInputRequire(){
	$('select#destinationStep').removeClass('bg-error');
	$('input#receiveNgQty').removeClass('bg-error');
}