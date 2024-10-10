$(function(){
  $(document).on("change","div.tasting-vintage-preparation input.tasting-vintage-preparation-type",function(){
    var $form = $(this).closest("form");
    var val = $form.find("input.tasting-vintage-preparation-type:checked").val();
    if(val>0){
      $form.find(".tasting-vintage-preparation-time-elapsed").show();
    } else {
      $form.find(".tasting-vintage-preparation-time-elapsed").hide();
    }
  });
});