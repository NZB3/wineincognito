$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
     typeof(confirmBox)!=="undefined" && confirmBox instanceof Function){
    $(document).on("click",".view-tasting-contest .head-buttons span.change-status",function(){
      var $this = $(this);
      var tcId = $this.closest(".view-tasting-contest").data("tc-id");
      var deleting = $this.hasClass("delete");
      if(deleting){
        confirmBox($this.closest("th").find(".confirm_string_delete").html(), function(){
          ajaxRequest("/ajax/contest/" + tcId + "/delete", null, function(){
            var href = $this.closest("th").find("a.back").attr("href");
            if(href!==undefined){
              window.location = href;
            } else {
              window.location.reload();
            }
          }, null);
        });
      } else {
        confirmBox(fillTemplate($this.closest("th").find(".confirm_string_change_status").html(),{name:$this.data("status-text")}), function(){
          ajaxRequest("/ajax/contest/" + tcId + "/status/change", {
            status:$this.data("status"),
          }, function(){
            window.location.reload();
          }, null);
        });
      }
    });
    $(document).on("click",".view-tasting-contest .head-buttons span.assess",function(){
      var $this = $(this);
      var tcId = $this.closest(".view-tasting-contest").data("tc-id");
      if($this.hasClass("approve")){
        ajaxRequest("/ajax/contest/" + tcId + "/assess/approve", null, function(){
          $this.closest(".head-buttons").find("span.assess").remove();
        }, null);
      } else if($this.hasClass("deny")){
        ajaxRequest("/ajax/contest/" + tcId + "/assess/deny", null, function(){
          $this.closest(".head-buttons").find("span.assess").remove();
        }, null);
      }
    });
  }
});