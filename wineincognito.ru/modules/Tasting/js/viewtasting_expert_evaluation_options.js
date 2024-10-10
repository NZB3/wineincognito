$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function){
    $(document).on("change", ".viewTasting-expert-evaluation-options table td input.viewTasting-expert-evaluation-options-automatic",function(){
      var $table = $(this).closest("table");
      var val = $table.find("input.viewTasting-expert-evaluation-options-automatic:checked").val();
      if(val>0){
        $table.addClass("show-automatic-evaluation-options");
      } else {
        $table.removeClass("show-automatic-evaluation-options");
      }
    });
    $(".viewTasting-expert-evaluation-options table input.viewTasting-expert-evaluation-options-automatic:checked").trigger("change");
    
    $(document).on("change", ".viewTasting-expert-evaluation-options table td.score ul li input",function(){
      $(this).closest("ul").find("input:checked").closest("li").prevAll().addBack().addClass("checked").end().end().nextAll().removeClass("checked");
    });
    $(".viewTasting-expert-evaluation-options table td.score ul li input:checked").trigger("change");

    $(document).on("change", ".viewTasting-expert-evaluation-options table tr.score-permissible-variation td ul li input",function(){
      var $td = $(this).closest("td");
      var val = $td.find("ul li input:checked").val();
      if(val>0){
        $td.find("input.viewTasting-expert-evaluation-options-score-permissible-variation-manual-value").show();
      } else {
        $td.find("input.viewTasting-expert-evaluation-options-score-permissible-variation-manual-value").hide();
      }
    });
    $(".viewTasting-expert-evaluation-options table tr.score-permissible-variation td ul li input:checked").trigger("change");
    $(document).on("submit","form.viewTasting-expert-evaluation-options",function(e){
      var $form = $(this);
      var postData = $form.serialize();
      if($form.hasClass("expert-evaluation-template")){
        ajaxRequest("/ajax/tasting/expert_evaluation/base/change", postData);
      } else {
        var tId = $form.data("t-id");
        ajaxRequest("/ajax/tasting/" + tId + "/evaluation/base/change", postData);
      }
      
      e.preventDefault();
    });
    
    $(".viewTasting-expert-evaluation-options table td input.viewTasting-expert-evaluation-options-score-permissible-variation-manual-value").mask('99,99');
  }
});