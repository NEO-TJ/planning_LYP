// ********************************************** Variable *********************************************
let days = ["Sun", "Mon.", "Tue", "Wed.", "Thu.", "Fri.", "Sat."];
let months = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "June", "July", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."];

let uiChanged = false;
// ************************************************ Event **********************************************
// ------------------------------------------------ Load -----------------------------------------------
$(document).ready(function() {
    // Multiselect.
    $('select#jobID').multiselect({
        header: true,
        noneSelectedText: 'Default selected all job',
        click: function(event, ui) { uiChanged = true; },
        close: function(event, ui) { changeJob(); }
    }).multiselectfilter();

    bindingMultiselect('jobTypeID', 'job type');
    bindingMultiselect('stepID', 'step');
    bindingMultiselect('lineID', 'line');

    // DateTimePicker.
    $('#dtsStart').datetimepicker({
        viewMode: 'days',
        format: 'DD-MMM-YYYY',
        useCurrent: true
    });
    $('#dtsStart').val(moment().format('DD-MMM-YYYY'));

    // UI Block.
    $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

});
//------------------------------------------------- Search ---------------------------------------------
$('button#search').click(function(e) { submitDisplay(); });

//-------------------------------------------- Change Start Date ---------------------------------------
$("#dtsStart").on("dp.change", function(e) {
    let curDate = new Date().getTime();
    let dtsStartDate = e.date;
    let dDiff = Math.round(((dtsStartDate) - curDate) / (1000 * 60 * 60 * 24)) + 1;

    $('input#diffStartCurrentDate').val(dDiff);
    uiChanged = true;
});

//------------------------------------------- Change date slot qty -------------------------------------
$("select#dayOfPlan").on("change", function(e) { uiChanged = true; });


//----------------------------------------------- Shift Date -------------------------------------------
$(document).on('click', 'button#next-date', function(e) {
    nextDate();
    submitDisplay();
});
$(document).on('click', 'button#previous-date', function(e) {
    previousDate();
    submitDisplay();
});

// --------------------------------------------- Plan input --------------------------------------------
$(document).on('keydown', 'input.numeric', function(e) { numericFilter(e, this, false); });


$(document).on('change', 'input[name^="okQtySlot"]', function(e) {
    let index = $(this).prop('name').match(/\[(.*?)\]/)[1];
    saveOKQtyPlan($(this), index);
});
$(document).on('change', 'input[name^="workerQtySlot"]', function(e) {
    let index = $(this).prop('name').match(/\[(.*?)\]/)[1];
    saveWorkerQtyPlan($(this), index);
});



// ----------------------------------------------- Delay -----------------------------------------------
$(document).on('keydown', '#delayDayQty', function(e) {
    numericFilter(e, this, false);
});
$(document).on('click', "#submitDelay", function() {
    let stockID = $('input#stockID').val();
    let delayDayQty = $('input#delayDayQty').val();

    if (isEmpty(stockID) || (stockID < 1)) {
        $('span#delayValidate').text("Can't find stock id please refresh!");
    } else if (isEmpty(delayDayQty) || (delayDayQty < 1)) {
        $('span#delayValidate').text("Please input Day number!");
    } else {
        shiftDatePlanDelayWithOffsetSun(stockID, delayDayQty);
        swal.close();
    }
});
$(document).on('click', "#cancelDelay", function() {
    swal.closeModal();
});
$(document).on('click', 'button#delay', function(e) {
    let tr = $(e.target).closest('tr');
    let stockID = tr.find('td:last-child button#delay').val();


    swal({
        title: "Plan delay",
        text: '<div class="row panel panel-primary">' +
            '<div class="col-md-12 margin-input">' +
            '<div class="input-group">' +
            '<span class="input-group-btn">' +
            '<button class="btn btn-primary disabled" type="button">Day number : </button>' +
            '</span>' +
            '<input type="number" class="form-control text-right numeric"' +
            ' autocomplete="off" id="delayDayQty"' +
            ' placeholder="Day number for delay..." value="" />'

            +
            '</div>' +
            '</div>' +
            '<div class="col-md-12">' +
            '<span class="label label-default pull-left" id="delayValidate"></span>' +
            '</div>' +
            '</div>'

            +
            '<div class="row">' +
            '<div class="col-md-3">' +
            '</div>' +
            '<div class="col-md-3">' +
            '<button type="button" class="btn btn-primary btn-submit" id="submitDelay">Submit</button>' +
            '</div>' +
            '<div class="col-md-3 pull-left">' +
            '<button type="button" class="btn btn-cancel btn-reset pull-left" id="cancelDelay">Cancel</button>' +
            '</div>' +
            '<div class="col-md-3">' +
            '<input type="text" class="hide numeric" id="stockID" value="' + stockID + '" />' +
            '</div>' +
            '</div>',
        showConfirmButton: false,
        showCancelButton: false,
        allowOutsideClick: false,
        closeOnConfirm: false,
    });
});
//-----------------------------------------------------------------------------------------------------
//--------------------------------------------- Event Method ------------------------------------------
//-----------------------------------------------------------------------------------------------------

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Start freeze table header.
function freezeTableHeader() {
    $('table#planning').floatThead({
        position: 'fixed',
        autoReflow: 'true',
    });
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Binding multiselect element.
function bindingMultiselect(elementID, captionName) {
    $('select#' + elementID).multiselect({
        header: true,
        noneSelectedText: 'Default selected all ' + captionName,
        click: function(event, ui) { uiChanged = true; }
    }).multiselectfilter();
}
//-----------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------






//************************************************ Method **********************************************
//------------------------------------------------- AJAX -----------------------------------------------
//__________________________________________ Get planning data _________________________________________
function displayFullPlanningTable() {
    let baseUrl = window.location.origin + "/" + window.location.pathname.split('/')[1] + "/";
    let arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; }).get();
    let arrayStepID = $('select#stepID').multiselect("getChecked").map(function() { return this.value; }).get();
    let arrayLineID = $('select#lineID').multiselect("getChecked").map(function() { return this.value; }).get();
    let arrayJobTypeID = $('select#jobTypeID').multiselect("getChecked").map(function() { return this.value; }).get();
    let diffStartCurrentDate = parseInt($('input#diffStartCurrentDate').val(), 10);
    let totalSlotDate = parseInt($('select#dayOfPlan :selected').val());

    let data = {
        'jobID': arrayJobID,
        'stepID': arrayStepID,
        'lineID': arrayLineID,
        'jobTypeID': arrayJobTypeID,
        'diffStartCurrentDate': diffStartCurrentDate,
        'totalSlotDate': totalSlotDate,
    };

    // Get top reject report by ajax.
    $.ajax({
        url: baseUrl + 'planning/ajaxGetFullDsPlanning',
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(result) {
            let dsFullPlanning = result['dsFullPlanning'];
            let diffStartCurrentDate = result['diffStartCurrentDate'];

            genTable(dsFullPlanning, diffStartCurrentDate, totalSlotDate);
            $('input#diffStartCurrentDate').val(diffStartCurrentDate);
            freezeTableHeader();

            uiChanged = false;
        }
    });
}



//____________________________________________ OK Qty Plan save ________________________________________
function saveOKQtyPlan(obj, offsetDiffDate) {
    let stockID = getStockIDAtRow(obj);
    let diffStartCurrentDate = getDiffCurrentDate(offsetDiffDate);
    let okQtyPlan = obj.val();

    let data = {
        'stockID': stockID,
        'diffStartCurrentDate': diffStartCurrentDate,
        'okQtyPlan': okQtyPlan,
    }

    // Get plan join table many row by ajax.
    $.ajax({
        url: 'planning/ajaxSaveOKQtyPlan',
        type: 'post',
        data: data,
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(result) {
            if (result == 0) {} else {
                obj.val('');
            }
        }
    });
}
//___________________________________________Worker Qty Plan save_______________________________________
function saveWorkerQtyPlan(obj, offsetDiffDate) {
    let stockID = getStockIDAtRow(obj);
    let diffStartCurrentDate = getDiffCurrentDate(offsetDiffDate);
    let workerQtyPlan = obj.val();

    let data = {
        'stockID': stockID,
        'diffStartCurrentDate': diffStartCurrentDate,
        'workerQtyPlan': workerQtyPlan,
    }

    // Get plan join table many row by ajax.
    $.ajax({
        url: 'planning/ajaxSaveWorkerQtyPlan',
        type: 'post',
        data: data,
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(result) {
            if (result == 0) {} else {
                obj.val('');
            }
        }
    });
}
//________________________________ Shift date plan delay with offset sunday ____________________________
function shiftDatePlanDelayWithOffsetSun(stockID, delayDayQty) {
    let data = {
        'stockID': stockID,
        'delayDayQty': delayDayQty,
    };
    // Shift date by dalay with offset sun.
    $.ajax({
        url: 'planning/ajaxShiftDatePlanDelayWithOffsetSun',
        type: 'post',
        data: data,
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(result) {
            if (result > 0) {
                swal({
                    title: "Success",
                    text: "Shift plan day " + delayDayQty + " day\n" +
                        "Total shift " + result + " record.",
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "Done",
                    confirmButtonClass: "btn btn-success",
                }).then(function() {
                    submitDisplay();
                });
            } else if (result == -1) {
                swal({
                    title: "Information",
                    text: "Not have plan to shift plan day",
                    type: "info",
                    showCancelButton: false,
                    confirmButtonText: "OK",
                    confirmButtonClass: "btn btn-info",
                });
            } else {
                swal({
                    title: "Warning!",
                    text: '<span class="text-info">Shift plan day ' + delayDayQty + ' day</span> Not complete...!<p>' +
                        'Please check<span class="text-info"> Database or plan date. </span>',
                    type: "error",
                    confirmButtonColor: "#DD6B55"
                });
            }
        }
    });
}




//------------------------------------------------- Mode -----------------------------------------------
//******************************************** Change job mode *****************************************
function changeJob() {
    let arrayJobID = $('select#jobID').multiselect("getChecked").map(function() { return this.value; }).get();

    let data = { 'jobID': arrayJobID };

    // Get project table one row by ajax.
    $.ajax({
        url: 'planning/ajaxGetDsStepLineByJobID',
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function() {},
        error: function(xhr, textStatus) {
            swal("Error", textStatus + xhr.responseText, "error");
        },
        complete: function() {},
        success: function(rResult) {
            filterStep(rResult.dsStep);
            filterLine(rResult.dsLine);
        }
    });
}

function filterStep(dsStep) {
    let tableTagInputCaption = '<span class="input-group-btn">';
    tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Step number : </button>';
    tableTagInputCaption += '</span>';

    let tableTagInputSelecter = '<select class="form-control multi-select" id="stepID" name="stepID[]" multiple="multiple">';
    for (let i = 0; i < dsStep.length; i++) {
        tableTagInputSelecter += '<option value=' + dsStep[i]['id'] + '>'
        tableTagInputSelecter += dsStep[i]['Number'] + ' - ' + dsStep[i]['DESC'] + '</option>';
    }

    $('div#stepID').html(tableTagInputCaption + tableTagInputSelecter);
    bindingMultiselect('stepID', 'step');
}

function filterLine(dsLine) {
    let tableTagInputCaption = '<span class="input-group-btn">';
    tableTagInputCaption += '<button class="btn btn-primary disabled" type="button">Line : </button>';
    tableTagInputCaption += '</span>';

    let tableTagInputSelecter = '<select class="form-control multi-select" id="lineID" name="lineID[]" multiple="multiple">';
    for (let i = 0; i < dsLine.length; i++) {
        tableTagInputSelecter += '<option value=' + dsLine[i]['id'] + '>'
        tableTagInputSelecter += dsLine[i]['Name'] + '</option>';
    }

    $('div#lineID').html(tableTagInputCaption + tableTagInputSelecter);
    bindingMultiselect('lineID', 'line');
}









//************************************************ Method **********************************************
//------------------------------------------------ Normal ----------------------------------------------
function submitDisplay() { displayFullPlanningTable(); }
//_________________________________________________ Date _______________________________________________
function nextDate() {
    let diffStartCurrentDate = parseInt($('input#diffStartCurrentDate').val(), 10);
    $('input#diffStartCurrentDate').val(++diffStartCurrentDate);
}

function previousDate() {
    let diffStartCurrentDate = parseInt($('input#diffStartCurrentDate').val(), 10);
    $('input#diffStartCurrentDate').val(--diffStartCurrentDate);
}




//------------------------------------------------ Helper ----------------------------------------------
//___________________________________________ Save worker & plan _______________________________________
function getStockIDAtRow(obj) {
    return obj.closest('tr').find('td:last-child button#delay').val();
}

function getDiffCurrentDate(offsetDiffDate) {
    return (parseInt($('input#diffStartCurrentDate').val(), 10) + parseInt(offsetDiffDate, 10));
}
//__________________________________________ Calculate Total Column ____________________________________
function getTotalColumn(diffStartCurrentDate, totalSlotDate) {
    let totalSpanSlotDate = ((diffStartCurrentDate > 0) ? 0 :
        (diffStartCurrentDate < ((totalSlotDate - 1) * (-1))) ? totalSlotDate :
        (1 - diffStartCurrentDate));

    return ((totalSpanSlotDate * 2) + (totalSlotDate * 2) + 9);           // 9 is fix column span.
}



//******************************************* Generate table HTML **************************************
function genTable(dsFullPlanning, diffStartCurrentDate, totalSlotDate) {
    let result = false;
    let tableHtml = [];

    tableHtml.push('<table class="table table-bordered table-components');
    tableHtml.push(' table-condensed table-hover table-striped table-responsive"');
    tableHtml.push(' id="planning" style="width:100%">');

    tableHtml.push(genHeader(diffStartCurrentDate, totalSlotDate));
    tableHtml.push(genBody(dsFullPlanning, diffStartCurrentDate, totalSlotDate));
    tableHtml.push('</table>');
    document.getElementById("divPlanning").innerHTML = tableHtml.join('');

    $('#headerPage').prop('title', "Total Record : " + dsFullPlanning.dsMain.length);
    return true;
}

function genHeader(diffStartCurrentDate, totalSlotDate) {
    let htmlHeader = "";

    let totalColumn = getTotalColumn(diffStartCurrentDate, totalSlotDate);
    // Header row 0
    htmlHeader += '<thead class="table-header">';
    htmlHeader += '<tr>';
    htmlHeader += '<th id="tableHeader" class="text-center" colspan="' + totalColumn + '"><h4><b>Planning</b></h4></th>';
    htmlHeader += '</tr>';

    // Header row 1
    htmlHeader += '<tr>';
    htmlHeader += '<th class="text-center" colspan="8">';
    htmlHeader += '<button type="button" class="btn btn-warning text-left pull-right" id="previous-date">Previous</button>';
    htmlHeader += '</th>';
    for (let i = 0; i < totalSlotDate; i++) {
        htmlHeader += '<th id="slot-' + (i + 1) + '" class="text-center" colspan="' +
            (((parseInt(diffStartCurrentDate) + i) > 0) ? 2 : 4) + '">' + (i + 1) + '</th>';
    }
    htmlHeader += '<th class="text-right">';
    htmlHeader += '<button type="button" class="btn btn-warning pull-right" id="next-date">Next</button>';
    htmlHeader += '</th>';
    htmlHeader += '</tr>';

    // Header row 2
    htmlHeader += '<tr>';
    htmlHeader += '<th class="text-center" rowspan="2">Job</th>';
    htmlHeader += '<th class="text-center" rowspan="2">Next step</th>';
    htmlHeader += '<th class="text-center nowrap" rowspan="2">Step-Description</th>';
    htmlHeader += '<th class="text-center" rowspan="2">Line</th>';
    htmlHeader += '<th class="text-center" colspan="4">Total</th>';
    for (let i = 0; i < totalSlotDate; i++) {
        htmlHeader += '<th class="text-center" colspan="' + (((parseInt(diffStartCurrentDate) + i) > 0) ? 2 : 4) +
            '" id="date-slot-' + (i + 1) + '">';
        htmlHeader += genDateSlotCaption(parseInt(diffStartCurrentDate) + i);
        htmlHeader += '</th>';
    }
    htmlHeader += '<th class="text-center" rowspan="2">Delay</th>';
    htmlHeader += '</tr>';

    // Header row 3
    htmlHeader += '<tr>';
    htmlHeader += '<th class="text-center">Sub Assembly</th>';
    htmlHeader += '<th class="text-center">Stock</th>';
    htmlHeader += '<th class="text-center">OK</th>';
    htmlHeader += '<th class="text-center">NG</th>';
    let elementHidden = false;
    for (let i = 0; i < totalSlotDate; i++) {
        elementHidden = (((parseInt(diffStartCurrentDate) + i) > 0) ? true : false);

        htmlHeader += '<th class="text-center">...Plan...</th>';
        htmlHeader += (elementHidden
                        ? ''
                        : '<th class="text-center" id="ngQtySlotH' + (i + 1) + '">NG</th>');
        htmlHeader += '<th class="text-center" id="workerQtySlotH' + (i + 1) + '">Worker No.</th>';
        htmlHeader += (elementHidden
                        ? ''
                        : '<th class="text-center" id="totalTimeSlotH' + (i + 1) + '">Time</th>');
    }
    htmlHeader += '</tr>';

    htmlHeader += '</thead>';

    return htmlHeader;
}

function genBody(dsFullPlanning, diffStartCurrentDate, totalSlotDate) {
    let htmlBody = '<tbody>';

    let duplicateCount = 0;
    let iDuplicate = 0;
    let previousStockId = 0;
    let rowMain;
    let rowSlotDate;
    let dsSlotDate;

    let okQty;
    let ngQty;
    let workerQty;
    let totalTime;
    let rSlotDateAttr = getSlotDateAttr(diffStartCurrentDate, totalSlotDate);

    let dsPlanningMain = dsFullPlanning.dsMain;
    let dsPlanningSlotDate = dsFullPlanning.dsSlotDate;

    for (let i = 0; i < dsPlanningMain.length; i++) {
        rowMain = dsPlanningMain[i];
        dsSlotDate = $.grep(dsPlanningSlotDate, function(e){ return e.StockId === rowMain.StockId; });

    // Calc duplicate count.
        if(iDuplicate >= duplicateCount) {
            iDuplicate = 0;                                             // Reset for calculate count.
            for(let j = (i+1); j < dsPlanningMain.length; j++) {
                if(rowMain.StockId == dsPlanningMain[j].StockId) {    // Current == Next?
                    iDuplicate++;
                } else { break; }
            }
            duplicateCount = iDuplicate + 1;
            iDuplicate = 0;                                             // Reset again for run to dender.
        }
    // End Calc duplicate count.


    // Render main data.
        htmlBody += '<tr>';
        if (iDuplicate == 0) {
            htmlBody += '<td class="text-left nowrap" rowspan=' + duplicateCount + '>' + rowMain.JobName + '</td>';
            htmlBody += '<td class="text-left" rowspan=' + duplicateCount + '>' + rowMain.Next_Step_Number + '</td>';
            htmlBody += '<td class="text-left nowrap" rowspan=' + duplicateCount + '>' + rowMain.NumberAndDESC + '</td>';
            htmlBody += '<td class="text-left" rowspan=' + duplicateCount + '>' + rowMain.LineName + '</td>';
        }

        htmlBody += '<td class="text-left bg-success nowrap">' + rowMain.SubAssemblyName + '</td>';
        htmlBody += '<td class="text-right bg-success">' + rowMain.StockQty + '</td>';

        if (iDuplicate == 0) {
            htmlBody += '<td class="text-right bg-success" rowspan=' + duplicateCount + '>' + rowMain.activity_Qty_OK + '</td>';
            htmlBody += '<td class="text-right bg-success" rowspan=' + duplicateCount + '>' + rowMain.Qty_NG + '</td>';
    // End render main data.

    //Render slot date data.
            for (let iSD = 0; iSD < totalSlotDate; iSD++) {
                // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Set data of slot date
                okQty = "";
                ngQty = "-";
                workerQty = "";
                totalTime = "";
            // Set value of element.
                if(dsSlotDate.length > 0) {
                    dsSubSlotDate = $.grep(dsSlotDate, function(e){ return e.DateStamp === rSlotDateAttr[iSD].strDate; });
                    if(dsSubSlotDate.length > 0) {
                        rowSlotDate = dsSubSlotDate[0];
                        if((parseInt(diffStartCurrentDate) + iSD) > 0) {
                            okQty = (jQuery.isEmptyObject(rowSlotDate.PlanOKQty) ? "" : rowSlotDate.PlanOKQty);
                            workerQty = (jQuery.isEmptyObject(rowSlotDate.PlanWorkerQty) ? "" : rowSlotDate.PlanWorkerQty);
                        } else {
                            okQty = (jQuery.isEmptyObject(rowSlotDate.ActualOKQty) ? "-" : rowSlotDate.ActualOKQty)
                                + "/" + (jQuery.isEmptyObject(rowSlotDate.PlanOKQty) ? "-" : rowSlotDate.PlanOKQty);
                            ngQty = (jQuery.isEmptyObject(rowSlotDate.ActualNGQty) ? "-" : rowSlotDate.ActualNGQty);
                            workerQty = (jQuery.isEmptyObject(rowSlotDate.ActualWorkerQty) ? "-" : rowSlotDate.ActualWorkerQty)
                                + "/" + (jQuery.isEmptyObject(rowSlotDate.PlanWorkerQty) ? "-" : rowSlotDate.PlanWorkerQty);
                            totalTime = (jQuery.isEmptyObject(rowSlotDate.ActualWorkerQty)
                                ? ( (jQuery.isEmptyObject(rowSlotDate.PlanWorkerQty) ? "-" : rowSlotDate.PlanWorkerQty * rowSlotDate.OperationTime) )
                                : rowSlotDate.ActualWorkerQty * rowSlotDate.OperationTime);
                        }
                    }
                }
            // End Set value of element.

            // Set attribute of element.
                htmlBody += '<td class="text-center body-drill-down';
                htmlBody += rSlotDateAttr[iSD].elementDisplayDisabled;
                htmlBody += rSlotDateAttr[iSD].elementStriped;
                htmlBody += rSlotDateAttr[iSD].bgColor + '"';
                htmlBody += ' rowspan="' + duplicateCount + '"';
                htmlBody += ' id="okQtySlot' + iSD + '" name="okQtySlot[' + iSD + ']";>';
                htmlBody += '<input type="text" class="form-control text-right numeric" autocomplete="off"';
                htmlBody += ' id="okQtySlot' + iSD + '"';
                htmlBody += ' name="okQtySlot[' + iSD + ']";';
                htmlBody += ' placeholder="Plan..." value="' + okQty + '"'
                htmlBody += rSlotDateAttr[iSD].elementDisabled + ' />';
                htmlBody += '</td>';

                if(rSlotDateAttr[iSD].elementHidden == false) {
                    htmlBody += '<td class="text-center body-drill-down';
                    htmlBody += rSlotDateAttr[iSD].elementDisplayDisabled;
                    htmlBody += rSlotDateAttr[iSD].bgColor + '"';
                    htmlBody += ' rowspan="' + duplicateCount + '"';
                    htmlBody += ' id="ngQtySlot' + iSD + '">';
                    htmlBody += ngQty;
                    htmlBody += '</td>';
                }

                htmlBody += '<td class="text-center body-drill-down';
                htmlBody += rSlotDateAttr[iSD].elementDisplayDisabled;
                htmlBody += rSlotDateAttr[iSD].elementStriped;
                htmlBody += rSlotDateAttr[iSD].bgColor + '"';
                htmlBody += ' rowspan="' + duplicateCount + '"';
                htmlBody += ' id="workerQtySlot' + iSD + '" name="workerQtySlot[' + iSD + ']";>';
                htmlBody += '<input type="text" class="form-control text-right numeric" autocomplete="off"';
                htmlBody += ' id="workerQtySlot' + iSD + '"';
                htmlBody += ' name="workerQtySlot[' + iSD + ']";';
                htmlBody += ' placeholder="Machine..." value="' + workerQty + '"' 
                htmlBody += rSlotDateAttr[iSD].elementDisabled + ' />';
                htmlBody += '</td>';

                if(rSlotDateAttr[iSD].elementHidden == false) {
                    htmlBody += '<td class="text-center body-drill-down';
                    htmlBody += rSlotDateAttr[iSD].elementDisplayDisabled;
                    htmlBody += rSlotDateAttr[iSD].bgColor + '"';
                    htmlBody += ' rowspan="' + duplicateCount + '"';
                    htmlBody += ' id="totalTimeSlot' + iSD + '" style="white-space:wrap;">';
                    htmlBody += totalTime;
                    htmlBody += '</td>';
                }
            }
        // End Set attribute of element.

        // Delay button
            htmlBody += '<td class="text-center" rowspan="' + duplicateCount + '">';
            htmlBody += '<button type="button" class="btn btn-danger" id="delay" value=' + rowMain.StockId + '>';
            htmlBody += '<i class="fa fa-plus"></i>';
            htmlBody += '</button>';
            htmlBody += '</td>';
        // End Delay button
    //Render slot date data.
        }
        htmlBody += '</tr>';
        iDuplicate++;
    }
    htmlBody += '</tbody>';

    return htmlBody;
}
//_______________________________________________Date Caption __________________________________________
function genDateSlotCaption(diffStartCurrentDate) {
    //let diffStartCurrentDate = parseInt(diffStartCurrentDate, 10);
    let startDate = new Date().addDays(diffStartCurrentDate);
    let d = new Date(startDate.getTime());
    let strDate = (diffStartCurrentDate == 0) ? 'Today' :
        (days[d.getDay()] + ", " + months[d.getMonth()] + " " + d.getDate() + ", " + d.getFullYear());;

    return strDate;
}
//______________________________________________ Object Date ___________________________________________
function getObjectDate(diffStartCurrentDate) {
    //let diffStartCurrentDate = parseInt(diffStartCurrentDate, 10);
    let oDate = new Date().addDays(diffStartCurrentDate);

    return oDate;
}
function getSlotDateAttr(diffStartCurrentDate, totalSlotDate) {
    let oDate = getObjectDate(diffStartCurrentDate);
    let striped = false;
    let strDate = '';

    let elementDisabled = '';
    let elementDisplayDisabled = '';
    let elementHidden = true;
    let elementStriped = (striped ? " warning" : "");

    let bgColor = "";
    let rSlotDateAttr = new Array();

    for (let i = 0; i < totalSlotDate; i++) {
        strDate = oDate.toISOString().slice(0,10);

    // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Set planning mode & striped
        if ((parseInt(diffStartCurrentDate) + i) > 0) {
            elementDisabled = '';
            elementDisplayDisabled = '';
            elementHidden = true;
            elementStriped = (striped ? " warning" : "");
            striped = !striped;
        } else {
            elementDisabled = ' readonly';
            elementDisplayDisabled = ' bg-error';
            elementHidden = false;
            elementStriped = '';
        }
    // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ End Set planning mode & striped

    // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ Set holiday on planning mode
        if (oDate.getDay() == 0) {
            bgColor = " bg-primary";
            elementStriped = "" ;
        } else {
            bgColor = "";
        }
    // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ End Set holiday on planning mode

        rSlotDateAttr[i] = {
                'strDate' : strDate,
                'elementDisabled' : elementDisabled,
                'elementDisplayDisabled' : elementDisplayDisabled,
                'elementHidden' : elementHidden,
                'elementStriped' : elementStriped,
                'bgColor' : bgColor,
        }
        oDate.addDays(+1);
    }

    return rSlotDateAttr;
}