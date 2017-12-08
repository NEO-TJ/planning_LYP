// ************************************************ Event **********************************************
// ----------------------------------------------- Doc Load --------------------------------------------
$(document).ready(function() {
	document.title += '-Top Reject';
});


//************************************************ Method **********************************************
//------------------------------------------------ AJAX -----------------------------------------------
function getReport() {
	var strDateStart = $('input#dateStart').val();
	var strDateEnd = $('input#dateEnd').val();
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; } ).get();

	var data = {
			'strDateStart': strDateStart,
			'strDateEnd': strDateEnd,
			'jobID' : arrayJobID,
			'stepID' : arrayStepID,
	};

	// Get top reject report by ajax.
	$.ajax({
		url: 'topRejectReport/ajaxGetTopRejectReport',
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
			var dsNGPercent = dsData['dsNGPercent'];
			var dsTopReject = dsData['dsTopReject'];
			
			$('table#ngPercentByStepReport > tbody').html(genNGPercentByStepReport(dsNGPercent));
			$('table#topRejectReport > tbody').html(genTopRejectReport(dsTopReject));
		}
	});
}




//--------------------------------------------- Generate Html ------------------------------------------
//*************************************** NG Percent by Step Report ************************************
function genTextColorClass(qty) {
	var textColorClass;
	if(qty < 5) {
		textColorClass = 'text-success';
	} else if(qty > 15) {
		textColorClass = 'text-danger';
	} else {
		textColorClass = 'text-warning';
	}	
	
	return textColorClass;
}
function genNGPercentByStepSummary(totalQtyOK, totalQtyNG) {
	var htmlReport;
	var totalNGPercent = ( totalQtyNG / (((totalQtyOK + totalQtyNG) == 0) ? 1 : (totalQtyOK + totalQtyNG)) ) * 100;
	var textColorClass = genTextColorClass(totalNGPercent);

	htmlReport +='<tr>';
	htmlReport +='<td class="text-left"></td>';
	htmlReport +='<td class="text-left"></td>';

	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Qty OK Summary">';
	htmlReport +=totalQtyOK.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';
	
	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Qty NG Summary">';
	htmlReport +=totalQtyNG.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';
	
	htmlReport +='<td class="text-right border-report '+textColorClass+'">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Qty NG Percent Average">';
	htmlReport +=parseFloat(totalNGPercent).toFixed(2)
					.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '  %';
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';
		
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genNGPercentByStepData(row, no) {
	var htmlReport;
	var textColorClass = genTextColorClass(row['ngPercent']);
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-right">' + row['Number'] + '</td>';
	htmlReport +='<td class="text-left">  -   ' + row['DESC'] + '</td>';
	htmlReport +='<td class="text-right">' + row['qtyOK'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + row['qtyNG'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right '+textColorClass+'">' + parseFloat(row['ngPercent'])
					.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '  %</td>';
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genNGPercentByStepReport(dsNGPercent) {
	var htmlReport = "";
	
	var totalQtyOK = 0;
	var totalQtyNG = 0;
	
	var row;
	for(var i=0; i<dsNGPercent.length; i++)
	{
		row = dsNGPercent[i];
		
		//Data.
		htmlReport += genNGPercentByStepData(row);
		totalQtyOK = parseInt(totalQtyOK) + parseInt(row['qtyOK']);
		totalQtyNG = parseInt(totalQtyNG) + parseInt(row['qtyNG']);
	}

	if(dsNGPercent.length > 0) {
		//Summary.
		htmlReport += genNGPercentByStepSummary(totalQtyOK, totalQtyNG);
	}
	
	
	$('#headerPage').prop('title', "Total Record : " + dsNGPercent.length);
	return htmlReport;
}



//****************************************** Top Reject Report *****************************************
function genTopRejectReport(dsTopReject) {
	var htmlReport = "";
	
	var totalRejectQty = 0;
	var row;
	for(var i=0; i<dsTopReject.length; i++)
	{
		row = dsTopReject[i];
		
		//Data.
		htmlReport += genTopRejectData(row, i+1);
		totalRejectQty = parseInt(totalRejectQty) + parseInt(row['rejectQty']);
	}

	if(dsTopReject.length > 0) {
		//Summary.
		htmlReport += genTopRejectSummary(totalRejectQty);
	}
	
	
	$('#headerPage').prop('title', "Total Record : " + dsTopReject.length);
	return htmlReport;
}
function genTopRejectSummary(totalRejectQty) {
	var htmlReport;

	htmlReport +='<tr>';
	htmlReport +='<td class="text-right"></td>';
	htmlReport +='<td class="text-left"></td>';

	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Qty NG Summary">';
	htmlReport +=totalRejectQty.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';
	
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genTopRejectData(row, no) {
	var htmlReport;
	var textColorClass = (isEmpty(row['defectName']) ? ' bg-danger' : '');
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-center'+textColorClass+'">' + no + '</td>';
	htmlReport +='<td class="text-left'+textColorClass+'">' + row['defectName'] + '</td>';
	htmlReport +='<td class="text-right'+textColorClass+'">' + row['rejectQty']
		.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='</tr>';
	
	return htmlReport;
}