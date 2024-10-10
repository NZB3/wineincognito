$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
     typeof(wrapForm)!=="undefined" && wrapForm instanceof Function){
    $(document).on("click", ".view-contest-tasting-list td.remove span", function(){
      var $tr = $(this).closest("tr");
      var tId = $tr.data("id");
      var tcId = $tr.closest("table").data("tc-id");
      ajaxRequest("/ajax/contest/" + tcId + "/tasting/" + tId + "/remove", null, function(){
          $tr.remove();
      }, null);
    });
    $(document).on("click", ".view-contest-tasting-list span.add", function(){
      var $table = $(this).closest("table");
      var tcId = $table.data("tc-id");
      ajaxRequest("/ajax/tasting/filter/form", {contest:tcId}, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("click","td.custom-add span, td.name a",function(e){
          var tId = $(this).closest("tr").data("id");
          ajaxRequest("/ajax/contest/" + tcId + "/tasting/add", {
            id:tId,
          }, function(data){
            refreshTastingList($table,tcId);
          });
          e.preventDefault();
        });
      });
    });
    $(document).on("click",".view-contest-tasting-list span.refresh",function(){
      var $table = $(this).closest("table");
      var tcId = $table.data("tc-id");
      refreshTastingList($table, tcId);
    });
    function refreshTastingList($table,tcId){
      var showowner = $table.data("showowner")?1:0;
      var showstatus = $table.data("showstatus")?1:0;
      var showassessment = $table.data("showassessment")?1:0;
      ajaxRequest("/ajax/contest/" + tcId + "/tasting/list", {showowner:showowner,showstatus:showstatus,showassessment:showassessment}, function(data){
        var tastinglistItemTemplate = $table.closest(".group-block").find(".tastinglist-item-template").first().html();
        var $tbody = $table.find("tbody");
        $tbody.empty();
        $.each(data,function(key,row){
          $tbody.append(fillTemplate(tastinglistItemTemplate,row));
        });
        hideEmptyRows($table);
      });
    }
    function hideEmptyRows($table){
      var $tr = $table.find("tbody tr");
      var rowsCount = $tr.first().find("td").length;
      var nonEmptyRows = [];
      $tr.each(function(){
        $(this).find("td").each(function(key,val){
          if(nonEmptyRows.indexOf(key)!==-1){
            return true;
          }
          if($(this).html().trim().length){
            nonEmptyRows.push(key);
          }
        });
        if(nonEmptyRows.length==rowsCount){
          return true;
        }
      });
      if(nonEmptyRows.length==rowsCount){
        return;
      }
      var emptyRows = [];
      var $th = $table.find("th").removeClass("empty-col");
      $tr.find("td.empty-col").removeClass("empty-col");
      for(var i=0;i<rowsCount;i++){
        if(nonEmptyRows.indexOf(i)===-1){
          $tr.find("td:nth-child("+(i+1)+")").addClass("empty-col");
          $th.filter(":nth-child("+(i+1)+")").addClass("empty-col");
        }
      }
    }
    $(".view-contest-tasting-list").each(function(){
      hideEmptyRows($(this));
    });
  }
});