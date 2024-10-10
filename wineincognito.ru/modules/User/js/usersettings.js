$(function(){
  if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
    $(document).on("submit","form.user-settings-direct-auth",function(e){
      var $form = $(this);
      var userId = $form.data("user-id");
      var $table = $(this).find("table.user-settings-direct-auth");
      var isEnabling = !$table.hasClass("status-enabled");
      ajaxRequest("/ajax/user/" + userId + "/directauth/set", {
          enable:isEnabling?1:0,
      }, function(data){
          if(isEnabling){
            $table.removeClass("status-disabled").addClass("status-enabled");
            if(data["url"]!==undefined){
              $table.find("tr.direct-auth-url").find("td.value").html("<a href=\"" + data["url"] + "\">" + data["url"] + "</a>").end().show();
            }
          } else {
            $table.removeClass("status-enabled").addClass("status-disabled").find("tr.direct-auth-url").hide();
          }
      },null);
      e.preventDefault();
    });
  }
});