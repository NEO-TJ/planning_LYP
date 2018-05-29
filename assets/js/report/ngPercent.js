// ************************************************ Event **********************************************
// ----------------------------------------------- Doc Load --------------------------------------------
$(document).ready(function() {
	initDaterange();
	document.title += '-NG Percent';
});


//************************************************ Method **********************************************
//------------------------------------------------ AJAX ------------------------------------------------
function getReport() {
	var arrayLineID = $('select#lineID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; } ).get();

	var data = {
			'strDateStart': strDateStart,
			'strDateEnd': strDateEnd,
			'lineID' : arrayLineID,
			'jobID' : arrayJobID,
			'stepID' : arrayStepID,
	};

	// Get percent of NG report by ajax.
	$.ajax({
		url: 'ngPercentReport/ajaxGetNGPercentReport',
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
		success: function(dsNGPercent) {
			var strHtml = ((arrayLineID.length > 0) ? genReportGroupByLine(dsNGPercent) : genReportNoGroup(dsNGPercent));
			$('table#ngPercentReport > tbody').html(strHtml);
		}
	});
}




//--------------------------------------------- Generate Html ------------------------------------------
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

function genLineGroup(lineName) {
	var htmlReport;
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-left" rowspan="1" colspan="4">';
	htmlReport +='<h5><strong><u>';
	htmlReport +=lineName;
	htmlReport +='</us></strong></h5>';
	htmlReport +='</td>';	
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genSummary(totalQtyOK, totalQtyNG) {
	var htmlReport;
	var totalNGPercent = ( totalQtyNG / (((totalQtyOK + totalQtyNG) == 0) ? 1 : (totalQtyOK + totalQtyNG)) ) * 100;
	var textColorClass = genTextColorClass(totalNGPercent);

	htmlReport +='<tr>';
	htmlReport +='<td class="text-left"></td>';
	htmlReport +='<td class="text-left"></td>';
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
function genData(row) {
	var htmlReport;
	var textColorClass = genTextColorClass(row['ngPercent']);
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-left">' + row['dateStamp'] + '</td>';
	htmlReport +='<td class="text-left">' + row['jobName'] + '</td>';
	htmlReport +='<td class="text-right">' + row['Number'] + '</td>';
	htmlReport +='<td class="text-left">  -   ' + row['DESC'] + '</td>';
	htmlReport +='<td class="text-right">' + row['qtyOK'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + row['qtyNG'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right '+textColorClass+'">' + parseFloat(row['ngPercent'])
					.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '  %</td>';
	htmlReport +='</tr>';
	
	return htmlReport;
}

//**************************************** No Group by Line Report *************************************
function genReportNoGroup(dsNGPercent) {
	var htmlReport = "";
	
	var totalQtyOK = 0;
	var totalQtyNG = 0;
	
	var row;
	for(var i=0; i<dsNGPercent.length; i++)
	{
		row = dsNGPercent[i];
		
		//Data.
		htmlReport += genData(row);
		totalQtyOK = parseInt(totalQtyOK) + parseInt(row['qtyOK']);
		totalQtyNG = parseInt(totalQtyNG) + parseInt(row['qtyNG']);
	}

	if(dsNGPercent.length > 0) {
		//Summary.
		htmlReport += genSummary(totalQtyOK, totalQtyNG);
	}
	
	
	$('#headerPage').prop('title', "Total Record : " + dsNGPercent.length);
	return htmlReport;
}

//****************************************** Group by Line Report **************************************
function genReportGroupByLine(dsNGPercent) {
	var htmlReport = "";
	
	var r = 0;
	var totalQtyOK = 0;
	var totalQtyNG = 0;
	var lineName = " (TJ Start) ";
	
	var row;
	for(var i=0; i<dsNGPercent.length; i++)
	{
		row = dsNGPercent[i];
		
		if(lineName != row['lineName']) {
			if(lineName != " (TJ Start) ") {
				//Summary.
				htmlReport += genSummary(totalQtyOK, totalQtyNG);
				r++;
			}
			//Line Group.
			lineName = row['lineName'];
			if((r%2) == 0) {
				htmlReport +='<tr></tr> <tr></tr> <tr></tr>';
				r += 3;
			}
			htmlReport += genLineGroup(lineName);
			totalQtyOK = 0;
			totalQtyNG = 0;
			r++;
		}
		//Data.
		htmlReport += genData(row);
		totalQtyOK = parseInt(totalQtyOK) + parseInt(row['qtyOK']);
		totalQtyNG = parseInt(totalQtyNG) + parseInt(row['qtyNG']);
		r++;
	}

	if(r > 0) {
		//Summary.
		htmlReport += genSummary(totalQtyOK, totalQtyNG);
		r++;
	}
	
	
	$('#headerPage').prop('title', "Total Record : " + dsNGPercent.length);
	return htmlReport;
}