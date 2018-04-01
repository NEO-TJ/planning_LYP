// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Overall %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// ********************************************** Variable *********************************************
const dltOK = 0;					// Success
const dltValidate = 1;				// Validate error
const dltValidateEnoughStock = 2;	// Validate Enough Stock error
// ************************************************ Event **********************************************
$(window).load(function() {
	resetRecoveryNGPage();
});
$(document).ready(function() {
	$('#dateTimeStamp').datetimepicker();
	$('input#dateTimeStamp').val(moment().format('DD-MMM-YYYY HH:mm'));
	
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
});

$('input#dateTimeStamp').focusout(function(e) {
	validateDateTimeStamp();
});

$(document).on('keydown', 'input[type="number"]', function(e) {
	numericFilter(e, this, true);
});
//************************************************ Method **********************************************
//************************************************* Tool ***********************************************
function showDialog($type){
	if($type == dltOK){
		
	}else if($type == dltValidate) {
		swal("Warning", "Please check your input key.","warning");
	}else if($type == dltValidateEnoughStock) {
		swal("Warning"
			, "You can not send NG qty more than total Qty NG : " + parseInt($('input#sourceQtyNG').val())
			,"warning");
	}
}








//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% Quantity Input %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//************************************************ Submit & Reset **************************************
$('form#formRecoveryNG').submit(function(e) {
	e.preventDefault();
	
	if(validateRequireFill()) {
		if(validateEnoughStock()) {
			saveAll();
		} else {
			showDialog(dltValidateEnoughStock);
		}
	} else {
		showDialog(dltValidate);
	}
});
$('form#formRecoveryNG button#resetAllStep').click(function(e) {
	fullResetAllStep();
});


//************************************************ Method **********************************************
//------------------------------------------------- Save -----------------------------------------------
function saveAll(){
	let jobID = $('select#job :selected').val();
	let dateTimeStamp = $('input#dateTimeStamp').val();
	let workerID = $('select#worker :selected').val();
	let sourceStepID = $('select#sourceStep :selected').val();
	let qtyNGSend = $('input#qtyNGSend').val();
	let dsDestinationStep = getDataDestinationTable();
	let firstStepStock = ( ((dsDestinationStep.length == 1) 
		&& ($('select#destinationStep :selected').val() == dsDestinationStep[0]['stepId'])) 
		? 1 : 0);

	let data = {
		'jobID'							: jobID,
		'dateTimeStamp'			: dateTimeStamp,
		'workerID'					: workerID,
		'sourceStepID'			: sourceStepID,
		'qtyNGSend'					: qtyNGSend,
		'dsDestinationStep'	: dsDestinationStep,
		'firstStepStock'		: firstStepStock,
	};

	// Get process table one row by ajax.
	$.ajax({
		url: 'recoveryNG/ajaxSaveRecoveryNG',
		type: 'post',
		data: data,
		beforeSend: function(){
			swal({title:"Saving...", 
				text: '<span class="text-info"><i class="fa fa-refresh fa-spin"></i> Saving please wait...</span>', 
				showConfirmButton: false, 
			});
		},
		error: function(xhr, textStatus){
			swal("Error", textStatus + xhr.responseText, "error");
		},
		complete: function(){
		},
		success: function(result) {
			if(result == 0) {
				swal({
					title: "Success",
					text: "Save Recovery NG to database has success",
					type: "success",
					showCancelButton: false,
					allowOutsideClick: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				}).then(function(){
					window.location.href="recoveryNG"
				});
			}
			else if(result == 1) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Recovery NG </span> Not complete...!<p>'
							+ 'Not enougth stock please check stock'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else if(result == 2) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Recovery NG </span> Not complete...!<p>'
							+ 'Error at update stock'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else if(result == 3) {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Recovery NG </span> Not complete...!<p>'
							+ 'Error at insert activity'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
			else {
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> Recovery NG </span> Not complete...!<p>'
							+ 'Error'
							+ result,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}

function getDataDestinationTable() {
	let dsDestinationStep = Array();
	let i = 0;

	$('table#destinationStepTable input:checked').each(function() {
		let curTr = $(this).closest("tr");

		let destinationStep = {
			'stepId'				: curTr.find('input#destinationCheck').val(),
			'receiveNgQty' 	: curTr.find('input#receiveNgQty').val(),
		};
		dsDestinationStep.push(destinationStep);
	});

	return dsDestinationStep;
}




//********************************************** Validation *******************************************
function validateRequireFill(){
	let result = false;
	
	let resultJobID = false;
	let resultDateTimeStamp = false;
	let resultWorker = false;
	let resultSourceQtyNG = false;
	let resultDestinationQtyOK = false;
	let resultQtyNGSend = false;
	
	let qtyNGSend = $('input#qtyNGSend').val();
	
	// Check job id selected?
	resultJobID = validateFillSelectElement($('select#job'));
	// Check DateTimeStamp selected?
	resultDateTimeStamp = validateDateTimeStamp();
	// Check worker id selected?
	resultWorker = validateFillSelectElement($('select#worker'));
	// Check Source Step id selected?
	resultSourceQtyNG = validateFillSelectElement($('select#sourceStep'));
	// Check Quantity NG for send require has input?
	resultQtyNGSend = validateFillInputElement($('input#qtyNGSend'));
	if($('input#qtyNGSend').val() > 0) {
		$('input#qtyNGSend').removeClass('bg-error');
		resultQtyNGSend &= true;
	} else {
		$('input#qtyNGSend').addClass('bg-error');
		resultQtyNGSend = false;
	}
	// Check Destination Step id selected?
	resultDestinationQtyOK = validateStepDestinationTable();
	
	result = (resultJobID && resultDateTimeStamp && resultWorker && resultSourceQtyNG
				&& resultDestinationQtyOK && resultQtyNGSend);
	return result;
}
function validateDateTimeStamp(){
	let result = false;

	if($('input#dateTimeStamp').length) {
		let dateTimeStamp = $('input#dateTimeStamp').val();
		if(isEmpty(dateTimeStamp)) {
			swal("Warning", "Please check your 'DateTime Stamp'.","warning");
		} else {
			if(moment(dateTimeStamp, 'DD-MMM-YYYY HH:mm') > moment()) {
				swal("Warning", "Can't choose future DateTime.\n Please check your 'DateTime Stamp'.","warning");
				$('input#dateTimeStamp').val(moment().format('DD-MMM-YYYY HH:mm'))
			} else { result = true; }
		}
	} else {result = true;}

	return result;
}
function validateStepDestinationTable() {
	let valid = 0;
	let nonvalid = 0;
	let receiveNgQty = 0;

	$('table#destinationStepTable input:radio').each(function() {
		if(this.checked) {
			receiveNgQty = ($(this).closest("tr").find('input#receiveNgQty').val() == "")
			? 0 : $(this).closest("tr").find('input#receiveNgQty').val();

			if(receiveNgQty > 0) {
				$(this).closest("tr").find('input#receiveNgQty').removeClass('bg-error');
				valid++;
			} else {
				$(this).closest("tr").find('input#receiveNgQty').addClass('bg-error');
				nonvalid++;
			}
		}
	});

	return ((nonvalid == 0) && (valid > 0));
}

function validateEnoughStock() {
	let sourceQtyNG = parseInt($('input#sourceQtyNG').val());
	let qtyNGSend = parseInt($('input#qtyNGSend').val());
	
	return ( ((sourceQtyNG < qtyNGSend) || (qtyNGSend < 1)) ? false : true);
}




//************************************************** Tool ****************************************************
//******************************************** Reset input fill **********************************************
function resetRecoveryNGPage(jobID, sourceStepID){
	if(isEmpty(jobID)) { jobID = 0; }
	if(isEmpty(sourceStepID)) { sourceStepID = 0; }

	$('select').val(0);
	
	$('select#job').val(jobID);
	$('select#job').trigger('change');

	if(sourceStepID != 0) {
		$('select#sourceStep').val(sourceStepID);
		$('select#sourceStep').trigger('change');
	}
}
