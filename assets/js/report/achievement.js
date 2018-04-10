// ************************************************ Event **********************************************
// ----------------------------------------------- Doc Load --------------------------------------------
$(document).ready(function() {
	document.title += '-Achievement';
});


//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
function getReport() {
	let strDateStart = $('input#dateStart').val();
	let strDateEnd = $('input#dateEnd').val();
	let arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; } ).get();
	let arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; } ).get();
	let arrayLineID = $('select#lineID').multiselect("getChecked").map(function() { return this.value; } ).get();

	let data = {
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
	let htmlReport;
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-left" rowspan="1" colspan="4">';
	htmlReport +='<h5><strong><u>';
	htmlReport +=lineName;
	htmlReport +='</us></strong></h5>';
	htmlReport +='</td>';	
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genSummary(totalPlanOkQty, totalActualOkQty) {
	let htmlReport;
	let totalAchievementQty = ( totalActualOkQty / 
							( ((totalPlanOkQty == 0) && (totalActualOkQty > 0)) ? 100 : totalPlanOkQty) ) * 100;

	htmlReport +='<tr>';
	htmlReport +='<td class="text-left"></td>';

	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Total Qty Plan">';
	htmlReport +=totalPlanOkQty.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	htmlReport +='</abbr>';
	htmlReport +='</em></strong></mark></u><h5>';
	htmlReport +='</td>';

	htmlReport +='<td class="text-right border-report">';
	htmlReport +='<h5><u><mark><strong><em>';
	htmlReport +='<abbr title="Total Qty OK">';
	htmlReport +=totalActualOkQty.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
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
	let htmlReport;
	
	htmlReport +='<tr>';
	htmlReport +='<td class="text-left">' + row['dateStamp'] + '</td>';
	htmlReport +='<td class="text-right">' + row['planOkQty'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + row['actualOkQty'].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '</td>';
	htmlReport +='<td class="text-right">' + parseFloat(row['achievementOkQty'])
					.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + '  %</td>';
	htmlReport +='</tr>';
	
	return htmlReport;
}
function genReport(dsAchievement) {
	let htmlReport = "";
	
	let r = 0;
	let totalPlanOkQty = 0;
	let totalActualOkQty = 0;
	let lineName = " (TJ Start) ";
	
	let row;
	for(let i=0; i<dsAchievement.length; i++)
	{
		row = dsAchievement[i];
		
		if(lineName != row['lineName']) {
			if(lineName != " (TJ Start) ") {
				//Summary.
				htmlReport += genSummary(totalPlanOkQty, totalActualOkQty);
				r++;
			}
			//Line Group.
			lineName = row['lineName'];
			if((r%2) == 0) {
				htmlReport +='<tr></tr> <tr></tr> <tr></tr>';
				r += 3;
			}
			htmlReport += genLineGroup(lineName);
			totalPlanOkQty = 0;
			totalActualOkQty = 0;
			r++;
		}
		//Data.
		htmlReport += genData(row);
		totalPlanOkQty = parseInt(totalPlanOkQty) + parseInt(row['planOkQty']);
		totalActualOkQty = parseInt(totalActualOkQty) + parseInt(row['actualOkQty']);
		r++;
	}

	if(r > 0) {
		//Summary.
		htmlReport += genSummary(totalPlanOkQty, totalActualOkQty);
		r++;
	}
	
	
	$('#headerPage').prop('title', "Total Record : " + dsAchievement.length);
	return htmlReport;
}