$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
     typeof(wrapForm)!=="undefined" && wrapForm instanceof Function){
    // vintage
    $(document).on("click", ".viewVintage.compare span.select", function(){
      var $select = $(this);
      var $table = $select.closest("table");
      ajaxRequest("/ajax/product/filter/form", {customnameaction:1}, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("click","td.custom-add span, td.name a",function(e){
          $table.find("tr.merge-into").remove();
          $select.data("current",$(this).closest("tr").data("pid"));
          var pids = [];
          $table.find("thead .select").each(function(){
            var current = $(this).data("current");
            if(current>0){
              pids.push(current);
            }
          });
          var url = $table.data("compare-url");
          for(var i=0;i<pids.length;i++){
            url += "/" + pids[i];
          }
          window.location = url;
          $wrapper.remove();
          e.preventDefault();
        });
      });
    });

    $(document).on("click", ".viewVintage.compare span.delete", function(){
      var $this = $(this);
      var confirm_string_delete_product = $this.closest("th").find(".confirm_string_delete_product").html();
      var id = $this.data("id");
      var slot = $this.data("slot");
      confirmBox(confirm_string_delete_product, function(){
        ajaxRequest("/ajax/vintage/" + id + "/delete", null, function(){
          $this.closest("table").find("tbody tr td:nth-child("+(slot+1)+"),thead tr.header th:nth-child("+(slot+1)+")").empty().end()
            .find("thead tr.head-buttons th:nth-child("+(slot+1)+")").children().not(".select").remove().end().end().end()
            .find("tr.merge-into").remove();
        }, null);
      });
    });

    $(document).on("click", ".viewVintage.compare span.merge-into.merge-products", function(){
        var $this = $(this);
        var $table = $this.closest("table");
        var mergeIntoId = $this.data("id");
        var mergeFromId = $this.data("from-id");
        ajaxRequest("/ajax/product/merge/" + mergeFromId + "/into/" + mergeIntoId, null, function(data){
          if(data && data["redirect_url"]!==undefined){
            setTimeout(function(){
              window.location = data["redirect_url"];
            },1000);
          }
        }, null);
    });
    $(document).on("click", ".viewVintage.compare span.merge-into.merge-vintages", function(){
        var $this = $(this);
        var $table = $this.closest("table");
        var mergeIntoId = $this.data("id");
        var mergeFromId = $this.data("from-id");
        ajaxRequest("/ajax/vintage/merge/" + mergeFromId + "/into/" + mergeIntoId, null, function(){
          window.location.reload();
        }, null);
    });
  }
});