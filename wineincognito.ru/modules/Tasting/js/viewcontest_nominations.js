$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
     typeof(wrapForm)!=="undefined" && wrapForm instanceof Function){
    $(document).on("click", ".view-contest-nomination-list span.add", function(){
      var $table = $(this).closest("table");
      var tcId = $table.data("tc-id");
      ajaxRequest("/ajax/contest/" + tcId + "/nomination/add/form", null, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("submit","form",function(e){
          var postData = $(this).serialize();
          ajaxRequest("/ajax/contest/" + tcId + "/nomination/add", postData, function(){
            refreshNominationList($table,tcId);
            $wrapper.remove();
          });
          e.preventDefault();
        });
      });
    });
    $(document).on("click",".view-contest-nomination-list span.refresh",function(){
      var $table = $(this).closest("table");
      var tcId = $table.data("tc-id");
      refreshNominationList($table, tcId);
    });
    $(document).on("click", ".view-contest-nomination-list tr.nomination td.remove span", function(){
      var $tr = $(this).closest("tr");
      var tcnId = $tr.data("id");
      var $table = $tr.closest("table");
      var tcId = $table.data("tc-id");
      ajaxRequest("/ajax/contest/" + tcId + "/nomination/" + tcnId + "/remove", null, function(){
          $tr.remove();
          refreshNominationList($table,tcId);
      }, null);
    });
    $(document).on("click", ".view-contest-nomination-list tr.nomination td.edit span", function(){
      var $tr = $(this).closest("tr");
      var tcnId = $tr.data("id");
      var $table = $tr.closest("table");
      var tcId = $table.data("tc-id");
      ajaxRequest("/ajax/contest/" + tcId + "/nomination/" + tcnId + "/edit/form", null, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("submit","form",function(e){
          var postData = $(this).serialize();
          ajaxRequest("/ajax/contest/" + tcId + "/nomination/" + tcnId + "/edit", postData, function(){
            refreshNominationList($table,tcId);
            $wrapper.remove();
          });
          e.preventDefault();
        });
      });
    });
    $(document).on("click", ".view-contest-nomination-list tr.nomination td.add span", function(){
      var $tr = $(this).closest("tr");
      var tcnId = $tr.data("id");
      var $table = $tr.closest("table");
      var tcId = $table.data("tc-id");
      ajaxRequest("/ajax/product/filter/form", {customnameaction:1,contest:tcId}, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("click","td.custom-add span, td.name a",function(e){
          var vid = $(this).closest("tr").data("id");
          ajaxRequest("/ajax/contest/" + tcId + "/nomination/" + tcnId + "/winner/add/form", {vid:vid}, function(data){
            var $wrapper = wrapForm(data);
            $wrapper.on("submit","form",function(e){
              var postData = $(this).serialize();
              ajaxRequest("/ajax/contest/" + tcId + "/nomination/" + tcnId + "/winner/add", postData, function(){
                refreshNominationList($table,tcId);
                $wrapper.remove();
              });
              e.preventDefault();
            });
          });
          e.preventDefault();
        });
      });
    });
    $(document).on("click", ".view-contest-nomination-list tr.nomination td.raise-index span, .view-contest-nomination-list tr.nomination td.lower-index span", function(){
      var $this = $(this);
      var direction = $this.closest("td").hasClass("raise-index")?-1:1;
      var tcnId = $this.closest("tr").data("id");
      var $table = $this.closest("table");
      var tcId = $table.data("tc-id");
      ajaxRequest("/ajax/contest/" + tcId + "/nomination/" + tcnId + "/modifyindex", {direction:direction}, function(){
          refreshNominationList($table, tcId);
      }, null);
    });
    $(document).on("click", ".view-contest-nomination-list tr.nomination-winner td.remove span", function(){
      var $tr = $(this).closest("tr");
      var tcnId = $tr.data("nid");
      var id = $tr.data("id");
      var tcId = $tr.closest("table").data("tc-id");
      ajaxRequest("/ajax/contest/" + tcId + "/nomination/" + tcnId + "/winner/" + id + "/remove", null, function(){
          $tr.remove();
      }, null);
    });
    function refreshNominationList($table,tcId){
      var showempty = $table.data("showempty")?1:0;
      var product = $table.data("product");
      ajaxRequest("/ajax/contest/" + tcId + "/nomination/list", {showempty:showempty,product:product}, function(data){
        var nominationlistItemTemplate = $table.closest(".group-block").find(".nominationlist-item-template").first().html();
        var nominationwinnerlistItemTemplate = $table.closest(".group-block").find(".nominationwinnerlist-item-template").first().html();
        var $tbody = $table.find("tbody");
        $tbody.empty();
        $.each(data,function(key,row){
          $tbody.append(fillTemplate(nominationlistItemTemplate,row));
          if(row.products){
            var nid = row.id;
            $.each(row.products,function(key,row){
              row.nid = nid;
              $tbody.append(fillTemplate(nominationwinnerlistItemTemplate,row));
            });
          }
        });
      });
    }
  }
});