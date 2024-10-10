$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function){
    $(document).on("dropbox-change", ".viewTasting-user-attendance-response div.dropbox",function(e,group,values){
      if(!values.length){
        return true;
      }
      var response = values.pop();
      var $dropbox = $(this);
      var tId = $dropbox.closest(".viewTasting-user-attendance-response").data("t-id");
      ajaxRequest("/ajax/tasting/" + tId + "/user/respond", {
        response:response,
      }, null, function(){//error
        $dropbox.trigger("uncheck-all");
      });
    });
  }
});