$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
        typeof(confirmBox)!=='undefined' && confirmBox instanceof Function){
        var confirm_string_request_join = $("#confirm_string_request_join").html();
        var confirm_string_delete_company = $("#confirm_string_delete_company").html();
        $(".viewCompany span.join").on("click", function(){
            var $this = $(this);
            var companyId = $this.data("id"); 
            confirmBox(confirm_string_request_join, function(){
                ajaxRequest("/ajax/company/" + companyId + "/requestjoin", null, function(){
                    $this.remove();
                }, null);
            });
        });
        $(".viewCompany span.delete").on("click", function(){
            var $this = $(this);
            var companyId = $this.data("id"); 
            confirmBox(confirm_string_delete_company.replace('{{name}}',$this.find("td.name").html()), function(){
                ajaxRequest("/ajax/company/" + companyId + "/delete", null, function(){
                    $this.remove();
                }, null);
            });
        });
        $(".viewCompany span.approve").on("click", function(){
            var $this = $(this);
            var companyId = $this.data("id"); 
            ajaxRequest("/ajax/company/" + companyId + "/approve", null, function(){
                $this.remove();
            });
        });
    }
});