$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
     typeof(wrapForm)!=="undefined" && wrapForm instanceof Function){
    $(document).on("click", ".viewTasting-user-list td.remove span", function(){
      var $tr = $(this).closest("tr");
      var userId = $tr.data("id");
      var tId = $tr.closest("table").data("t-id");
      ajaxRequest("/ajax/tasting/" + tId + "/user/" + userId + "/remove", null, function(){
          $tr.remove();
      }, null);
    });
    $(document).on("click", ".viewTasting-user-list span.add", function(){
      var $table = $(this).closest("table");
      var tId = $table.data("t-id");
      ajaxRequest("/ajax/user/filter/form", null, function(data){
        var $wrapper = wrapForm(data);
        $wrapper.on("click","td.custom-add span, td.name a",function(e){
          var $tr = $(this).closest("tr");
          var userId = $(this).closest("tr").data("id");
          ajaxRequest("/ajax/tasting/" + tId + "/user/invite", {
            id:userId,
          }, function(data){
            refreshUserList($table,tId);
          });
          e.preventDefault();
        });
      });
    });
    $(document).on("click",".viewTasting-user-list span.refresh",function(){
      var $table = $(this).closest("table");
      var tId = $table.data("t-id");
      refreshUserList($table, tId);
    });
    function refreshUserList($table,tId){
      var only_present = $table.data("only-present")?1:0;
      var evaluation_scores = $table.data("evaluation-scores")?1:0;
      var show_global_expert_automatic_evaluation = $table.data("show-global-expert-automatic-evaluation")?1:0;
      var show_response = $table.data("show-response")?1:0;
      var show_background = $table.data("show-background")?1:0;
      var product_id = $table.data("product-id");
      var user_id = $table.data("user-id");
      ajaxRequest("/ajax/tasting/" + tId + "/user/list", {only_present:only_present,evaluation_scores:evaluation_scores,show_global_expert_automatic_evaluation:show_global_expert_automatic_evaluation,show_response:show_response,show_background:show_background,product_id:product_id,user_id:user_id}, function(data){
        var userlistItemTemplate = $table.closest(".group-block").find(".userlist-item-template").first().html();
        var $tbody = $table.find("tbody");
        $tbody.empty();
        var hasGuests = false;
        var hasNonGuests = false;
        $.each(data,function(key,row){
          if(!hasGuests && row["isguest"]){
            hasGuests = true;
          }
          if(!hasNonGuests && !row["isguest"]){
            hasNonGuests = true;
          }
          if(row["score"]!==undefined){
            row["score_"+row["score"]["expert_level"]] = 1;
            row["score_score"] = row["score"]["score"];
            row["score_review_id"] = row["score"]["review_id"];
            row["score_faulty"] = row["score"]["faulty"];
            row["score_didnottaste"] = row["score"]["didnottaste"];
            delete row["score"];
          }
          $tbody.append(fillTemplate(userlistItemTemplate,row));
        });
        if(hasNonGuests&&hasGuests){
          $tbody.closest("table").addClass("has-guests");
        } else {
          $tbody.closest("table").removeClass("has-guests");
        }
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
    $(".viewTasting-user-list").each(function(){
      hideEmptyRows($(this));
    });
    $(document).on("click",".viewTasting-user-list tfoot tr.showguests",function(){
      var $table = $(this).closest("table.viewTasting-user-list");
      if($table.hasClass("guests-hide")){
        $table.removeClass("guests-hide");
      } else {
        $table.addClass("guests-hide");
      }
    });
    //presence absence
    $(document).on("click",".viewTasting-user-list tr:not(.user-was-present) td.present span",function(){
      markPresence($(this).closest("tr"),true);
    });
    $(document).on("click",".viewTasting-user-list tr.user-was-present td.absent span",function(){
      markPresence($(this).closest("tr"),false);
    });

    function markPresence($tr,isPresent){
      var userId = $tr.data("id");
      var tId = $tr.closest("table").data("t-id");
      ajaxRequest("/ajax/tasting/" + tId + "/user/" + userId + "/mark_presence", {
        present:isPresent?1:0
      }, function(){
        if(isPresent){
          $tr.addClass("user-was-present");
        } else {
          $tr.removeClass("user-was-present");
        }
      }, null);
    }

    $(document).on("click",".viewTasting-user-list td.block-review span",function(){
      var $this = $(this).closest("td.block-review");
      var $table = $this.closest("table");
      var tId = $table.data("t-id");
      var userId = $this.closest("tr").data("id");
      var productId = $table.data("product-id");
      var block = $this.hasClass("review-blocked")?0:1;
      ajaxRequest("/ajax/tasting/" + tId + "/product/" + productId + "/user/" + userId + "/blockreview", {
        block:block
      }, function(){
        if(block){
          $this.addClass("review-blocked");
        } else {
          $this.removeClass("review-blocked");
        }
      }, null);
    });
  }
});