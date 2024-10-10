$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function){
    $(document).on("submit", ".refresh-global-evaluation-form", function(e){
      e.preventDefault();
      ajaxRequest("/ajax/tasting/expert_evaluation/base/refresh", null);
    });

  }
});