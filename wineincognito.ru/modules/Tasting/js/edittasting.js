$(function(){
  var $form = $("#edit-tasting-form");
  $form.find("#edit-tasting-form-start-date").datepicker({
    changeMonth: true,
    numberOfMonths: 1,
    onClose: function( selectedDate ) {
      $endDate = $(this).closest("form").find("#edit-tasting-form-end-date");
      $endDate.datepicker("option", "minDate", selectedDate);
      if($endDate.datepicker("getDate")===null){
        $endDate.datepicker("setDate",selectedDate);
      }
    }
  });
  $form.find("#edit-tasting-form-end-date").datepicker({
    changeMonth: true,
    numberOfMonths: 1,
    onClose: function( selectedDate ) {
      $startDate = $(this).closest("form").find("#edit-tasting-form-start-date");
      $startDate.datepicker("option", "maxDate", selectedDate);
      if($startDate.datepicker("getDate")===null){
        $startDate.datepicker("setDate",selectedDate);
      }
    }
  }).each(function(){
    var $this = $(this);
    var selectedDate = $this.datepicker("getDate");
    if(selectedDate!==null){
      $startDate = $this.closest("form").find("#edit-tasting-form-start-date");
      $startDate.datepicker("option", "maxDate", selectedDate);
      if($startDate.datepicker("getDate")===null){
        $startDate.datepicker("setDate",selectedDate);
        $this.datepicker("option", "minDate", selectedDate);
      }
    }
  });

  $form.find("#edit-tasting-form-start-date").each(function(){
    var $this = $(this);
    var selectedDate = $this.datepicker("getDate");
    if(selectedDate!==null){
      $endDate = $this.closest("form").find("#edit-tasting-form-end-date");
      $endDate.datepicker("option", "minDate", selectedDate);
      if($endDate.datepicker("getDate")===null){
        $endDate.datepicker("setDate",selectedDate);
        $this.datepicker("option", "maxDate", selectedDate);
      }
    }
  });
  
  $form.on("change","input[name=participation]",function(){
    toggleParticipationRatingDisplay();
  });
  var $participationRating = $form.find(".participation-rating");
  function toggleParticipationRatingDisplay(){
    if($form.find("input[name=participation]:checked").val()==1){
      $participationRating.show();
    } else {
      $participationRating.hide();
    }
  }
  toggleParticipationRatingDisplay();
  
  $form.on("change","input[name=chargeability]",function(){
    togglepriceGridDisplay();
  });
  var $priceGrid = $form.find(".price-grid");
  function togglepriceGridDisplay(){
    if($form.find("input[name=chargeability]:checked").val()==1){
      $priceGrid.show();
    } else {
      $priceGrid.hide();
    }
  }
  togglepriceGridDisplay();

  $form.find("#edit-tasting-form-start-time, #edit-tasting-form-end-time").mask("H0:M0", {
    translation: {
      'H': {
        pattern: /[0-2]/, optional: true
      },
      'M': {
        pattern: /[0-5]/, optional: false
      },
    },
    clearIfNotMatch: true,
    placeholder:"__:__"
  });
  $form.find(".rating-limitation").mask("999");
  $form.find(".price").mask("999999");
});