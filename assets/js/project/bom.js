// ************************************************ Event ***********************************************
//-------------------------------------------------- RM -------------------------------------------------
$('table#bom_rm.table-components .add-elements').on('click', addNewRmRowTable);
$('table#bom_rm.table-components').on("click", ".delete-elements", deleteRmRowTableAutoIncrementNo);

// ------------------------------------------------- BOM ------------------------------------------------
$('select#bom').change(changeBom);
$('div#collapse-bom').on("show.bs.collapse", setBomCaptionPanelMode);
$('table#bom_rm.table-components tbody').on("change", 'select#rm', function(e) { changeRm(e); });

//------------------------------------------------ Button ------------------------------------------------
//******************************************** Submit & Reset ********************************************
$('form#form-bom').on('submit', function(e) {
	e.preventDefault();
	
	if(validateBom()) {
		saveBom();
	} else {
		showDialog(dltValidate);
	}
});
$('form#form-bom button.btn-reset').click(changeBom);



// *********************************************** Method *************************************************
//------------------------------------------------- Save -----------------------------------------------
function saveBom(){
	var bomID = $('select#bom :selected').val();
	var bomName = $('input#bomName').val();
	var bomDesc = $('input#bomDesc').val();
	var bomDescThai = $('input#bomDescThai').val();

	var dsRm = new Array();
	$('table#bom_rm.table-components tbody tr').each(function(i, row){
		var dictRm = {
					'rmID': $(this).find('td:nth-child(2) select#rm :selected').val(),
					'qty': $(this).find('td:nth-child(3) input#qty').val(),
				};
		dsRm.push(dictRm);
	});

	var oldStrBomRmID = $('input#arrBomRmID').val();
	if((bomID == 0) || (oldStrBomRmID == '') || (oldStrBomRmID == null)) {
		var arrBomRmID = [];													// Add mode.
	} else {
		var arrBomRmID = oldStrBomRmID.split(",").map(Number);
	}
	var dataFullBom = {
				'bomID': bomID, 
				'bomName': bomName, 
				'bomDesc': bomDesc,
				'bomDescThai': bomDescThai,
				'dsRm': dsRm,
				'oldStrBomRmID': oldStrBomRmID
				};
	
	// Get bom table one row by ajax.
	$.ajax({
		url: 'project/ajaxSaveFullBom',
		type: 'post',
		data: dataFullBom,
		dataType: 'json',
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
		success: function(arrResult) {
			if(arrResult['result'] == 0){
				swal({
					title: "Success",
					text: "Save bom to database has success",
					type: "success",
					showCancelButton: false,
					confirmButtonText: "Done",
					confirmButtonClass: "btn btn-success",
				});

				setSelectElement(arrResult['dsBom'], 'bom');
				$('select#bom').val(arrResult['bomID']);
				$('select#bom').trigger('change');
				$('div#collapse-bom').collapse('hide');
			}
			else{
				swal({
					title: "Warning!",
					text: 'Save<span class="text-info"> BOM </span> Not complete...!' + arrResult,
					type: "error",
					confirmButtonColor: "#DD6B55"
				});
			}
		}
	});
}
//********************************************** Validation *******************************************
function validateBom(){
	var result = false;
	
	var resultBomName = false;
	var resultRmID = true;
	var resultRmQty = true;
	
	// Check BOM name require has input?
	resultBomName = validateFillInputElement($('input#bomName'));
	
	$('table#bom_rm.table-components tbody tr').each(function(i, row){
		// Check Raw material id selected?
		resultRmID = validateFillSelectElement( $(this).find('td:nth-child(2) select#rm') );
		// Check Raw material Quantity require has input?
		resultRmQty = validateFillInputElement( $(this).find('td:nth-child(3) input#qty') );
	});
	
	result = (resultBomName && resultRmID && resultRmQty);
	return result;
}

//------------------------------------------------- Mode ----------------------------------------------
//******************************************** Change BOM mode ****************************************
function changeBom(){
	setBomCaptionPanelMode();
	resetFullBomInputFill();
	var bomID = $('select#bom :selected').val();
	
	if(bomID == 0){
		$('input#arrBomRmID').val('');
	} else {
		var data = {'bomID': bomID};
		
		// Get bom table one row by ajax.
		$.ajax({
			url: 'project/ajaxGetDsFullBom',
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
			success: function(dsData) {
				var dsBom = dsData['dsBom'];
				var dsFullBom = dsData['dsFullBom'];
				
				if((dsBom.length) > 0){
					$('input#bomName').val(dsBom[0].Name);
					$('input#bomDesc').val(dsBom[0].DESC);
					$('input#bomDescThai').val(dsBom[0].DESC_Thai);
				}
				
				$('input#arrBomRmID').val('');
				for(var i=0; i< dsFullBom.length; i++){
					if(i != 0){
						cloneRmRowTableAutoIncrementNo();
					}
					setRmLastRowTable(dsFullBom, i);
				}
				var improveString = $('input#arrBomRmID').val();
				$('input#arrBomRmID').val(improveString.substring(1, improveString.length));
			}
		});
	}
}
//********************************************* Change RM mode ****************************************
function changeRm(e){
	var tr = $(e.target).closest('tr');
	var rmID = tr.find('td:nth-child(2) select').val();

	if(rmID == 0){
		tr.find('td:nth-child(4)').html(' - ');
	} else {
		var data = {'rmID': rmID};
		
		// Get bom table one row by ajax.
		$.ajax({
			url: 'project/ajaxGetUnitName',
			type: 'post',
			data: data,
			beforeSend: function(){
			},
			error: function(xhr, textStatus){
				swal("Error", textStatus + xhr.responseText, "error");
			},
			complete: function(){
			},
			success: function(unitName) {
				tr.find('td:nth-child(4)').html(unitName);
			}
		});
	}
}





//*********************************************** Tool ****************************************************
//---------------------------------------------- BOM_RM ---------------------------------------------------
//*********************************** Clone row table with auto increment no ******************************
function addNewRmRowTable(){
	cloneRmRowTableAutoIncrementNo();
	resetRmLastRowTable();
}
//*********************************** Clone row table with auto increment no ******************************
function cloneRmRowTableAutoIncrementNo(){
	var $clone = $("table#bom_rm.table-components tbody tr:first-child");
	
	$clone.find('.btn').removeClass('add-elements btn-default').addClass('delete-elements btn-danger')
		.html('<i class="fa fa-minus"></i>');
	$clone.find('td:first-child').html($('table#bom_rm.table-components tbody tr').length + 1);
	
	$clone.clone().appendTo('table#bom_rm.table-components tbody');
	
	$clone.find('td:first-child').html(1);
	$clone.find('.btn').removeClass('delete-elements btn-danger').addClass('add-elements btn-default')
		.html('<i class="fa fa-plus"></i>');
}
// *********************************** Delete row table and reset auto increment no *************************
function deleteAllCloneRmRowTable(){
	$('table#bom_rm.table-components tbody > tr:not(:first-child)').remove();
	$('table#bom_rm.table-components tbody > tr').removeClass('bg-error');
	
	resetRmLastRowTable();
}
function deleteRmRowTableAutoIncrementNo(){
	$(this).closest("tr").remove();
	
	var n = 0;
	$('table#bom_rm.table-components tbody tr').each(function(){
		n++;
		$(this).find('td:first-child').html(n);
	});
}
//***************************************** Set RM input fill ************************************************
function setRmLastRowTable(dsFullBom, i){
	var currentTr = $('table#bom_rm.table-components tbody tr:last-child');
	
	currentTr.find('td:nth-child(2) select#rm').val(dsFullBom[i].FK_ID_RM);
	currentTr.find('td:nth-child(3) input#qty').val(dsFullBom[i].Qty);
	currentTr.find('td:nth-child(4)').html(dsFullBom[i].unit_name);

	var arrBomRmID = $('input#arrBomRmID').val();
	$('input#arrBomRmID').val(arrBomRmID + "," + dsFullBom[i].id);
}
//************************************** Reset Full BOM input fill *******************************************
function resetFullBomInputFill(){
	$('input#bomName').val('');
	$('input#bomDesc').val('');
	$('input#bomDescThai').val('');

	$('input#bomName').removeClass('bg-error');
	$('input#bomDesc').removeClass('bg-error');
	$('input#bomDescThai').removeClass('bg-error');

	deleteAllCloneRmRowTable();
}
//----------------------------------------- Reset Bom input fill ---------------------------------------------
function resetBomInputFill(){
	$('input#bomName').val('');
	$('input#bomDesc').val('');
	$('input#bomDescThai').val('');

	$('input#bomName').removeClass('bg-error');
	$('input#bomDesc').removeClass('bg-error');
	$('input#bomDescThai').removeClass('bg-error');
}
//------------------------------------------ Reset RM input fill ---------------------------------------------
function resetRmLastRowTable(dsFullBom, i){
	var currentTr = $('table#bom_rm.table-components tbody tr:last-child');
	
	currentTr.find('td:nth-child(2) select#rm').val(0);
	currentTr.find('td:nth-child(3) input#qty').val('');
	currentTr.find('td:nth-child(4)').html('-');

	currentTr.find('td:nth-child(2) select#rm').removeClass('bg-error');
	currentTr.find('td:nth-child(3) input#qty').removeClass('bg-error');
}
//***************************************** Set caption panel mode *******************************************
function setBomCaptionPanelMode(){
	var caption = '';
	
	caption = ($('select#bom :selected').val() == 0)? 'New BOM' : 'Edit BOM';
	$('#panel-caption-bom').html('<span class="text-info"><h1>' + caption + '</h1></span>');
}