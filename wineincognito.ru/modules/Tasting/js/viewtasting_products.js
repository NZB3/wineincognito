$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
     typeof(wrapForm)!=="undefined" && wrapForm instanceof Function){
    $(document).on("click", ".viewTasting-vintage-list td.raise-index span, .viewTasting-vintage-list td.lower-index span", function(){
      var $this = $(this);
      var direction = $this.closest("td").hasClass("raise-index")?-1:1;
      var tpvId = $this.closest("tr").data("tpv-id");
      var tId = $this.closest("table").data("t-id");
      ajaxRequest("/ajax/tasting/" + tId + "/product/" + tpvId + "/modifyindex", {direction:direction}, function(){
          refreshProductList($this.closest("table"), tId);
      }, null);
    });
    $(document).on("click", ".viewTasting-vintage-list td.request-reviews span", function(){
      var $this = $(this);
      var tpvId = $this.closest("tr").data("tpv-id");
      var tId = $this.closest("table").data("t-id");
      ajaxRequest("/ajax/tasting/" + tId + "/product/" + tpvId + "/reviews/request", null, function(){
          refreshProductList($this.closest("table"), tId);
      }, null);
    });
    $(document).on("click", ".viewTasting-vintage-list td.stop-reviews span", function(){
      var $tr = $(this).closest("tr");
      var tpvId = $tr.data("tpv-id");
      var tId = $tr.closest("table").data("t-id");
      confirmBox(fillTemplate($tr.closest(".group-block").find(".confirm_string_stop_reviews").html(),{name:$tr.find("td.index").text() + ". " + $tr.find("td.name").text()}), function(){
        ajaxRequest("/ajax/tasting/" + tId + "/product/" + tpvId + "/reviews/stop", null, function(){
          refreshProductList($tr.closest("table"), tId);
        }, null);
      });
      
    });
    $(document).on("click", ".viewTasting-vintage-list td.edit span", function(){
      var tpvId = $(this).closest("tr").data("tpv-id");
      var $table = $(this).closest("table");
      var tId = $table.data("t-id");
      ajaxRequest("/ajax/tasting/" + tId + "/product/" + tpvId + "/edit/form", null, function(data){
        var $wrapper = wrapForm(data["form"]);
        $wrapper.on("submit","form",function(e){
          var postData = $(this).serialize();
          ajaxRequest("/ajax/tasting/" + tId + "/product/" + tpvId + "/edit", postData, function(){
            refreshProductList($table,tId);
            $wrapper.remove();
          });
          e.preventDefault();
        });
      });
    });

    $(document).on("click", ".viewTasting-vintage-list td.remove span", function(){
      var $tr = $(this).closest("tr");
      var tpvId = $tr.data("tpv-id");
      var tId = $tr.closest("table").data("t-id");
      ajaxRequest("/ajax/tasting/" + tId + "/product/" + tpvId + "/remove", null, function(){
          refreshProductList($tr.closest("table"), tId);
      }, null);
    });
    $(document).on("click", ".viewTasting-vintage-list td.preparation.can-change span", function(){
      var $tr = $(this).closest("tr");
      var $table = $tr.closest("table");
      var tpvId = $tr.data("tpv-id");
      var tId = $tr.closest("table").data("t-id");
      ajaxRequest("/ajax/tasting/" + tId + "/vintage/" + tpvId + "/preparation/form", null, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("submit","form",function(e){
          var postData = $(this).serialize();
          ajaxRequest("/ajax/tasting/" + tId + "/vintage/" + tpvId + "/preparation/change", postData, function(){
            refreshProductList($table,tId);
            $wrapper.remove();
          });
          e.preventDefault();
        });
      });
    });
    $(document).on("click", ".viewTasting-vintage-list span.add", function(){
      var $table = $(this).closest("table");
      ajaxRequest("/ajax/product/filter/form", {customnameaction:1}, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("click","td.custom-add span, td.name a",function(e){
          var pid = $(this).closest("tr").data("pid");
          ajaxRequest("/ajax/product/" + pid + "/vintage/add/form", null, function(data){
            switch(data["formtype"]){
              case "vintageadd":
                processVintageAddForm($table,data["form"]);
                break;
              case "vintageview":
                processVintageViewForm($table,data["form"]);
                break;
            }
          });
          e.preventDefault();
        });
      });
    });
    $(document).on("click", ".viewTasting-vintage-list.viewTasting-vintage-list-multiple-select tbody tr td.multiple-select, .viewTasting-vintage-list.viewTasting-vintage-list-multiple-select tbody tr td.name", function(e){
      e.preventDefault();
      var $tr = $(this).closest("tr");
      if($tr.hasClass("viewTasting-vintage-list-multiple-select-selected")){
        $tr.removeClass("viewTasting-vintage-list-multiple-select-selected");
        return;
      }
      if($tr.closest("table").hasClass("viewTasting-vintage-list-swap-reviews") && $tr.closest("tbody").find("tr.viewTasting-vintage-list-multiple-select-selected").length>=2){
        return;
      }
      $tr.addClass("viewTasting-vintage-list-multiple-select-selected");
    });
    $(".viewTasting-vintage-list").each(function(){
      setAutoRefresh($(this));
      hideEmptyRows($(this));
    });
    function setAutoRefresh($table){
      var autoRefreshHndl = $table.data("auto-refresh-timer-handle");
      if(autoRefreshHndl!==undefined){
        clearInterval(autoRefreshHndl);
      }
      var autoRefreshTimer = $table.data("auto-refresh-timer");
      if(autoRefreshTimer===undefined || autoRefreshTimer<=0){
        return;
      }
      var tId = $table.data("t-id");
      var hndl = setInterval(function(){
        refreshProductList($table, tId);
      }, autoRefreshTimer*1000);
      $table.data("auto-refresh-timer-handle",hndl);
    }
    $(document).on("click",".viewTasting-vintage-list span.refresh",function(){
      var $table = $(this).closest("table");
      var tId = $table.data("t-id");
      refreshProductList($table, tId);
      setAutoRefresh($table);
    });

    function processVintageAddForm($table,form){
      var $wrapper = wrapForm(form);
      $wrapper.on("fetchId","form",function(e,id){
        $wrapper.remove();
        ajaxRequest("/ajax/vintage/" + id + "/view/form", null, function(data){
          if(data&&data["formtype"]=="vintageview"){
            processVintageViewForm($table,data["form"]);
          }
        });
      });
    }

    function processVintageViewForm($table,form){
      var $wrapper = wrapForm(form);
      $wrapper.on("submit","form",function(e){
        var tId = $table.data("t-id");
        var postData = $(this).serialize();
        ajaxRequest("/ajax/tasting/" + tId + "/product/add", postData, function(data){
          refreshProductList($table,tId);
          $wrapper.remove();
        });
        e.preventDefault();
      });
    }

    function refreshProductList($table,tId){
      var actions = $table.data("actions")?1:0;
      var requestReview = $table.data("request-review")?1:0;
      var evaluations = $table.data("evaluations")?1:0;
      var show_global_expert_automatic_evaluation = $table.data("show-global-expert-automatic-evaluation")?1:0;
      var scores = $table.data("scores")?1:0;
      var awaiting_review_count = $table.data("awaiting-review-count")?1:0;
      var show_desc = $table.data("show-desc")?1:0;
      var user_id = $table.data("user-id");
      var tpv_id = $table.data("tpv-id");
      var order_by_index = $table.data("order-by-index")?1:0;
      ajaxRequest("/ajax/tasting/" + tId + "/product/list", {
        actions:actions,
        request_review:requestReview,
        evaluations:evaluations,
        show_global_expert_automatic_evaluation:show_global_expert_automatic_evaluation,
        scores:scores,
        awaiting_review_count:awaiting_review_count,
        show_desc:show_desc,
        user_id:user_id,
        tpv_id:tpv_id,
        order_by_index:order_by_index
      }, function(data){
        var vintagelistItemTemplate = $table.closest(".group-block").find(".vintagelist-item-template").first().html();
        var vintagelistItemUserscoreTemplate = $table.closest(".group-block").find(".vintagelist-item-userscore-template").first().html();
        
        var $tbody = $table.find("tbody");
        $tbody.empty();
        $.each(data,function(key,row){
          var vintagelistItemTemplateWithDesc = vintagelistItemTemplate;
          if(typeof row["desc"] == "string"){
            vintagelistItemTemplateWithDesc = vintagelistItemTemplateWithDesc.replace(new RegExp("\\{\\{desc\\}\\}","g"), prepareMultilineValue(row["desc"]));
          }
          if(scores){
            if(row["scores"]!==undefined){
              $.each(row["scores"],function(expertLevel,scoreData){
                row["scores_"+expertLevel] = 1;
                row["scores_"+expertLevel+"_multiplereviews"] = scoreData["count"]>1?1:0;
                row["scores_"+expertLevel+"_score"] = scoreData["score"];
                row["scores_"+expertLevel+"_count"] = scoreData["count"];
                row["scores_"+expertLevel+"_review_id"] = scoreData["review_id"];
              });
              delete row['scores'];
            }
            if(row["userscore"]!==undefined){
              var userscore_urls = "";
              $.each(row["userscore"],function(key,value){
                value["id"] = row["id"];
                userscore_urls += fillTemplate(vintagelistItemUserscoreTemplate,value);
              });
              if(userscore_urls.length){
                vintagelistItemTemplateWithDesc = vintagelistItemTemplateWithDesc.replace(new RegExp("\\{\\{userscore_urls\\}\\}","g"), userscore_urls);
              }
            }
          }
          $tbody.append(fillTemplate(vintagelistItemTemplateWithDesc,row));
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

  }
});