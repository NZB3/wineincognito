$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
     typeof(wrapForm)!=="undefined" && wrapForm instanceof Function){
        // $(document).on("click","table.viewUser td.expert span.deny",function(){
        //     approveExpert($(this).closest("tr"),false);
        // });
        // $(document).on("click","table.viewUser td.expert span.approve",function(){
        //     approveExpert($(this).closest("tr"),true);
        // });
        // function approveExpert($tr,approve){
        //     var id = $tr.data("id");
        //     ajaxRequest("/ajax/user/" + id + "/expert/approve", {
        //         approve:approve?1:0,
        //     }, function(){
        //         if(approve){
        //             $tr.find("span.approve,span.deny").remove();
        //         } else {
        //             $tr.remove();
        //         }
        //     },null);
        // }

        $(document).on("dropbox-change", ".viewUser .user-expert-level div.dropbox",function(e,group,values){
          if(!values.length){
            return true;
          }
          var expertLevel = values.pop();
          var $dropbox = $(this);
          var userId = $dropbox.closest("table.viewUser").data("id");

          var userExpertChangeDateFormTemplate = $dropbox.closest(".group-block").find(".user-expert-change-date-form-template").html();
          if(userExpertChangeDateFormTemplate===undefined){
            ajaxRequest("/ajax/user/" + userId + "/expert_level/set", {
              expertLevel:expertLevel,
            }, null, function(){//error
              $dropbox.trigger("uncheck-all");
            });
            return true;
          }
          var $wrapper = wrapForm(userExpertChangeDateFormTemplate);
          $wrapper.on("submit","form",function(e){
            var date = $(this).find("#expert-change-date-form-date").val();
            ajaxRequest("/ajax/user/" + userId + "/expert_level/set", {
              expertLevel:expertLevel,
              date:date,
            }, function(){
              $wrapper.remove();
            }, function(){//error
              $dropbox.trigger("uncheck-all");
            });
            e.preventDefault();
          });
        });

        $(document).on("click","table.viewUser .request-expert-change",function(){
          var $this = $(this);
          var userRequestExpertChangeFormTemplate = $this.closest(".group-block").find(".user-request-expert-change-form-template").html();
          if(userRequestExpertChangeFormTemplate===undefined){
            return true;
          }
          var $wrapper = wrapForm(userRequestExpertChangeFormTemplate);
          $wrapper.on("submit","form",function(e){
            var postData = $(this).serialize();
            ajaxRequest("/ajax/user/expert/request", postData, function(){
              $this.remove();
              $wrapper.remove();
            });
            e.preventDefault();
          });
        })
        $(document).on("wrapFormInit","form.expert-change-date-form",function(){
          var $dateinput = $(this).find("#expert-change-date-form-date");
          $dateinput.datepicker({
            changeMonth: true,
            numberOfMonths: 1
          });
          $dateinput.focus();
        })
    }
});