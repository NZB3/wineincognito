$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
     typeof(confirmBox)!=="undefined" && confirmBox instanceof Function){
    function __statusChange($this){
      var tId = $this.closest(".viewTasting").data("t-id");
      var status = $this.data("status");
      ajaxRequest("/ajax/tasting/" + tId + "/status/change", {
        status:status,
      }, function(){
        var href;
        if(status==8 && (href = $this.closest("th").find("a.back").attr("href"))!==undefined){
          window.location = href;
        } else {
          window.location.reload();
        }
      }, null);
    }
    $(document).on("click",".viewTasting .head-buttons span.change-status",function(){
      var $this = $(this);
      var status = $this.data("status");
      var msg;
      switch(status){
        case 8:
          msg = $this.closest("th").find(".confirm_string_change_status.delete").html();
          break;
        case 3:
          msg = $this.closest("th").find(".confirm_string_change_status.finish").html();
          break;
      }
      if(msg){
        confirmBox(msg, function(){
          __statusChange($this);
        });
      } else {
        __statusChange($this);
      }
    });
    $(document).on("click",".viewTasting .head-buttons span.assess",function(){
      var $this = $(this);
      var tId = $this.closest(".viewTasting").data("t-id");
      if($this.hasClass("approve")){
        ajaxRequest("/ajax/tasting/" + tId + "/assess/approve", null, function(){
          $this.closest(".head-buttons").find("span.assess").remove();
        }, null);
      } else if($this.hasClass("deny")){
        ajaxRequest("/ajax/tasting/" + tId + "/assess/deny", null, function(){
          $this.closest(".head-buttons").find("span.assess").remove();
        }, null);
      }
    });
  }
});