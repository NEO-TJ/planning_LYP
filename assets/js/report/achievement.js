// ************************************************ Event **********************************************
// ----------------------------------------------- Doc Load --------------------------------------------
$(document).ready(function() {
	document.title += '-Achievement';
});


//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
function getReport() {
	var strDateStart = $('input#dateStart').val();
	var strDateEnd = $('input#dateEnd').val();
	var arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; } ).get();
	var arrayLineID = $('select#lineID').multiselect("getChecked").map(function() { return this.value; } ).get();

	var data = {
			'strDateStart': strDateStart,
			'strDateEnd': strDateEnd,
			'jobID' : arrayJobID,
			'stepID' : arrayStepID,
			'lineID' : arrayLineID,
	};

	// Get achievement report by ajax.
	$.ajax({
		url: 'achievementReport/ajaxGetAchievementReport',
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
		success: function(dsAchievement) {
			$('table#achievementReport > tbody').html(genReport(dsAchievement));
		}
	});
}




//--------------------------------------------- Generate Html ------------------------------------------
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
function genSummary(totalPlanQty, totalActualQty) {
	var htmlReport;
	var totalAchievementQty = ( totalActualQty / 
							( ((totalPlanQty == 0) && (totalActualQty > 0)) ? 100 : totalPlanQty) ) * 100;

	htmlReport +='<tr>';
	htmlReport +='<td class="text-left"></td>';

	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Total Qty Plan">';
	htmlReport +=totalPlanQty.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';

	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Total Qty OK">';
	htmlReport +=totalActualQty.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';

	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Total Qty Achievement">';
	htmlReport +=parseFloat(totalAchievementQty).toFixed(2)
					.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '  %';
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';

	htmlReport +='</tr>';

	return htmlReport;
}
function genData(row) {
	var htmlReport;
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-left">' + row['dateStamp'] + '</td>';
	htmlReport +='<td class="text-right">' + row['planQtyOK'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + row['actualQtyOK'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + parseFloat(row['achievementQtyOK'])
					.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '  %</td>';
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genReport(dsAchievement) {
	var htmlReport = "";
	
	var r = 0;
	var totalPlanQty = 0;
	var totalActualQty = 0;
	var lineName = " (TJ Start) ";
	
	var row;
	for(var i=0; i<dsAchievement.length; i++)
	{
		row = dsAchievement[i];
		
		if(lineName != row['lineName']) {
			if(lineName != " (TJ Start) ") {
				//Summary.
				htmlReport += genSummary(totalPlanQty, totalActualQty);
				r++;
			}
			//Line Group.
			lineName = row['lineName'];
			if((r%2) == 0) {
				htmlReport +='<tr></tr> <tr></tr> <tr></tr>';
				r += 3;
			}
			htmlReport += genLineGroup(lineName);
			totalPlanQty = 0;
			totalActualQty = 0;
			r++;
		}
		//Data.
		htmlReport += genData(row);
		totalPlanQty = parseInt(totalPlanQty) + parseInt(row['planQtyOK']);
		totalActualQty = parseInt(totalActualQty) + parseInt(row['actualQtyOK']);
		r++;
	}

	if(r > 0) {
		//Summary.
		htmlReport += genSummary(totalPlanQty, totalActualQty);
		r++;
	}
	
	
	$('#headerPage').prop('title', "Total Record : " + dsAchievement.length);
	return htmlReport;
}