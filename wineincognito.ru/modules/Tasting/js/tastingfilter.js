$(function(){
    $(".tasting-filter .filter-form #tasting-filter-form-start-date-from").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            $endDate = $(this).closest("form").find("#tasting-filter-form-start-date-to");
            $endDate.datepicker("option", "minDate", selectedDate);
            if($endDate.datepicker("getDate")===null){
                $endDate.datepicker("setDate",selectedDate);
            }
        }
    });
    $(".tasting-filter .filter-form #tasting-filter-form-start-date-to").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function( selectedDate ) {
            $startDate = $(this).closest("form").find("#tasting-filter-form-start-date-from");
            $startDate.datepicker("option", "maxDate", selectedDate);
            if($startDate.datepicker("getDate")===null){
                $startDate.datepicker("setDate",selectedDate);
            }
        }
    }).each(function(){
        var $this = $(this);
        var selectedDate = $this.datepicker("getDate");
        if(selectedDate!==null){
            $startDate = $this.closest("form").find("#tasting-filter-form-start-date-from");
            $startDate.datepicker("option", "maxDate", selectedDate);
            if($startDate.datepicker("getDate")===null){
                $startDate.datepicker("setDate",selectedDate);
                $this.datepicker("option", "minDate", selectedDate);
            }
        }
    });

    $(".tasting-filter .filter-form #tasting-filter-form-start-date-from").each(function(){
        var $this = $(this);
        var selectedDate = $this.datepicker("getDate");
        if(selectedDate!==null){
            $endDate = $this.closest("form").find("#tasting-filter-form-start-date-to");
            $endDate.datepicker("option", "minDate", selectedDate);
            if($endDate.datepicker("getDate")===null){
                $endDate.datepicker("setDate",selectedDate);
                $this.datepicker("option", "maxDate", selectedDate);
            }
        }
    });
});