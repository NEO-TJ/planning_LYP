// ************************************************ Event **********************************************
// ---------------------------------------------- Page Load --------------------------------------------
$(document).ready(function() {
	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
});

//------------------------------------------------ Component -------------------------------------------
$('button#refresh').click(displayFullStockTable);
$('button#search').click(displayFullStockTable);






//************************************************ Method **********************************************
//------------------------------------------------ Search ----------------------------------------------
function displayFullStockTable() {
	getStock();
}



//************************************************ Method **********************************************
//------------------------------------------------ AJAX -----------------------------------------------
function getStock() {
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; } ).get();

	var data = {
			'jobID' : arrayJobID,
			'stepID' : arrayStepID,
	};

	// Get workingCapacity report by ajax.
	$.ajax({
		url: 'stockAdjust/ajaxGetDsFullStock',
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
			$('table#stockAdjust > tbody').html(genBody(dsFullStock));
		}
	});
}




//--------------------------------------------- Generate Html ------------------------------------------
function genBody(dsFullStock) {
	var htmlBody = "";
	
	for(var i=0; i<dsFullStock.length; i++)
	{
		//Data.
		htmlBody += genOneRow(dsFullStock[i]);
	}
	
	$('#headerPage').prop('title', "Total Record : " + dsFullStock.length);
	return htmlBody;
}
function genOneRow(row) {
	var htmlBody;
	
	htmlBody +='<tr>';

	htmlBody +='<td class="text-left">' + row['JobName']
				+ '<input type="text" class="hide" id="allID" value="'
					+ row['JobID'] + ',' + row['StepID'] + ',' + row['FirstStepFlag'] + '" />'
				+ '</td>';
	htmlBody +='<td class="text-left">' + row['NumberAndDesc'] + '</td>';
	htmlBody +='<td class="text-left">' + row['SubAssemblyName'] + '</td>';
	htmlBody +='<td class="text-right" id="stockQty">' 
				+ row['StockQty'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';

	htmlBody +='</tr>';
	
	return htmlBody;
}
