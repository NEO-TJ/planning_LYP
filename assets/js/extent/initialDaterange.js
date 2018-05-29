// -------------------------------------------------------------------------------------------- Init DatetimePicker.
let strDateStart, strDateEnd;

function initDaterange() {
    let drpFormat = "YYYY -MMMM- DD";
    let drpdbFormat = "YYYY-MM-DD";

    $('#daterange').dateRangePicker({
        autoClose: true,
        format: drpFormat,
        startOfWeek: 'sunday',
        startDate: false,
        endDate: false,
        extraClass: 'daterangepickerCss',
        monthSelect: true,
        yearSelect: function(current) {
            return [current - 10, current + 10];
        },
        setValue: function(s,s1,s2){
            $('#daterange').val(s1 + ' |||||||||| ' + s2);
            strDateStart = moment(s1, drpFormat).format(drpdbFormat);
            strDateEnd = moment(s2, drpFormat).format(drpdbFormat);
        },
        getValue: function()
	    {
		    return this.value;
    	},
    }).bind('datepicker-closed', function() {
        //ChangeDaterange();*/
    });

    $('#daterange').data('dateRangePicker').setDateRange(
        moment().subtract(1, 'month').startOf('month').format(drpFormat),
        moment().format(drpFormat)
    );
}
// -------------------------------------------------------------------------------------------- End Init DatetimePicker.