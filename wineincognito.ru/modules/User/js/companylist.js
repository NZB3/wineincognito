$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
        typeof(confirmBox)!=='undefined' && confirmBox instanceof Function){
        var confirm_string_request_join = $("#confirm_string_request_join").html();
        var confirm_string_delete_company = $("#confirm_string_delete_company").html();
        $("table.companylist").on("click", "td.join span", function(){
            var $this = $(this).closest("tr");
            var companyId = $this.data("id"); 
            confirmBox(confirm_string_request_join.replace('{{name}}',$this.find("td.name").html()), function(){
                ajaxRequest("/ajax/company/" + companyId + "/requestjoin", null, null, null);
            });
        });
        $("table.companylist").on("click", "td.delete span", function(){
            var $this = $(this).closest("tr");
            var companyId = $this.data("id"); 
            confirmBox(confirm_string_delete_company.replace('{{name}}',$this.find("td.name").html()), function(){
                ajaxRequest("/ajax/company/" + companyId + "/delete", null, function(){
                    $this.remove();
                }, null);
            });
        });
        $("table.companylist").on("click", "td.approve span", function(){
            var $this = $(this).closest("tr");
            var companyId = $this.data("id");
            ajaxRequest("/ajax/company/" + companyId + "/approve", null, function(){
                $this.remove();
            });
        });
        $("table.companylist").on("click", "td.api-access span", function(){
            var $td = $(this).closest("td");
            var companyId = $td.closest("tr").data("id");
            var action = $td.hasClass("has-access")?"revoke":"grant";
            ajaxRequest("/ajax/company/" + companyId + "/apiaccess/" + action, null, function(){
                if(action=="grant"){
                    $td.addClass("has-access");
                } else {
                    $td.removeClass("has-access");
                }
            });
        });
    }
});