$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
     typeof(wrapForm)!=="undefined" && wrapForm instanceof Function){
    $(document).on("click", ".view-contest-user-list td.remove span", function(){
      var $tr = $(this).closest("tr");
      var userId = $tr.data("id");
      var tcId = $tr.closest("table").data("tc-id");
      ajaxRequest("/ajax/contest/" + tcId + "/user/" + userId + "/remove", null, function(){
          $tr.remove();
      }, null);
    });
    $(document).on("click", ".view-contest-user-list span.add", function(){
      var $table = $(this).closest("table");
      var tcId = $table.data("tc-id");
      ajaxRequest("/ajax/user/filter/form", null, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("click","td.custom-add span, td.name a",function(e){
          var $tr = $(this).closest("tr");
          var userId = $(this).closest("tr").data("id");
          ajaxRequest("/ajax/contest/" + tcId + "/user/invite", {
            id:userId,
          }, function(data){
            refreshUserList($table,tcId);
          });
          e.preventDefault();
        });
      });
    });
    $(document).on("click",".view-contest-user-list span.refresh",function(){
      var $table = $(this).closest("table");
      var tcId = $table.data("tc-id");
      refreshUserList($table, tcId);
    });
    function refreshUserList($table,tcId){
      ajaxRequest("/ajax/contest/" + tcId + "/user/list", null, function(data){
        var userlistItemTemplate = $table.closest(".group-block").find(".userlist-item-template").first().html();
        var $tbody = $table.find("tbody");
        $tbody.empty();
        $.each(data,function(key,row){
          $tbody.append(fillTemplate(userlistItemTemplate,row));
        });
        // hideEmptyRows($table);
      });
    }
    // function hideEmptyRows($table){
    //   var $tr = $table.find("tbody tr");
    //   var rowsCount = $tr.first().find("td").length;
    //   var nonEmptyRows = [];
    //   $tr.each(function(){
    //     $(this).find("td").each(function(key,val){
    //       if(nonEmptyRows.indexOf(key)!==-1){
    //         return true;
    //       }
    //       if($(this).html().trim().length){
    //         nonEmptyRows.push(key);
    //       }
    //     });
    //     if(nonEmptyRows.length==rowsCount){
    //       return true;
    //     }
    //   });
    //   if(nonEmptyRows.length==rowsCount){
    //     return;
    //   }
    //   var emptyRows = [];
    //   var $th = $table.find("th").removeClass("empty-col");
    //   $tr.find("td.empty-col").removeClass("empty-col");
    //   for(var i=0;i<rowsCount;i++){
    //     if(nonEmptyRows.indexOf(i)===-1){
    //       $tr.find("td:nth-child("+(i+1)+")").addClass("empty-col");
    //       $th.filter(":nth-child("+(i+1)+")").addClass("empty-col");
    //     }
    //   }
    // }
    // $(".view-contest-user-list").each(function(){
    //   hideEmptyRows($(this));
    // });
  }
});