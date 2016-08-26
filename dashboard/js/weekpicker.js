 var startDate;
 var endDate;

 $('.week-picker').datepicker({
     showOtherMonths: true,
     selectOtherMonths: true,
     firstDay: 1,
     showWeek: true,
     onSelect: function (dateText, inst) {
         dateFormat: "'Week Number '" + $.datepicker.iso8601Week(new Date(dateText)),
         alert($.datepicker.iso8601Week(new Date(dateText)))
     },
     onChangeMonthYear: function (year, month, inst) {
         selectCurrentWeek();
     }
 });

 $('.week-picker .ui-datepicker-calendar tr').on('mousemove', function () {
     $(this).addClass('week-hover');
 });
 $('.week-picker .ui-datepicker-calendar tr').on('mouseleave', function () {
     $(this).removeClass('week-hover');
 });