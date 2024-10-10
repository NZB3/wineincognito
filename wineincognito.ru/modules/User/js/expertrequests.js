$(function(){
    if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function &&
        typeof(confirmBox)!=="undefined" && confirmBox instanceof Function &&
        typeof(wrapForm)!=="undefined" && wrapForm instanceof Function){
        $(document).on("click", ".expert-requests tr.active td.decline span", function(){
            var $tr = $(this).closest("tr");
            var confirm_string_decline_request = $tr.closest(".group-block").find(".confirm_string_decline_request").html();
            var uerId = $tr.data("id");
            var userId = $tr.closest("table").data("user-id");
            confirmBox(confirm_string_decline_request, function(){
                ajaxRequest("/ajax/user/" + userId + "/expertrequest/" + uerId + "/resolve",{approve:0},function(data){
                    $tr.removeClass("active").addClass("history declined");
                });
            }, null);
        });
        $(document).on("click", ".expert-requests tr.active td.approve span", function(){
            var $tr = $(this).closest("tr");
            var uerId = $tr.data("id");
            var userId = $tr.closest("table").data("user-id");
            var expertRequestExpertChangeFormTemplate = $tr.closest(".group-block").find(".expert-request-expert-change-form-template").html().replace("<\\/script>","</script>");
            if(expertRequestExpertChangeFormTemplate===undefined){
                return true;
            }
            var $wrapper = wrapForm(expertRequestExpertChangeFormTemplate);
            $wrapper.on("submit","form",function(e){
                var expertLevel = $(this).find("input[name=expertlevel]:checked").first().val();
                if(expertLevel===undefined){
                    expertLevel = null;
                }
                ajaxRequest("/ajax/user/" + userId + "/expertrequest/" + uerId + "/resolve",{approve:1,expert_level:expertLevel},function(data){
                    $tr.removeClass("active").addClass("history approved");
                    $wrapper.remove();
                });
                e.preventDefault();
            });
        });
    }
});