$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
        typeof(confirmBox)!=='undefined' && confirmBox instanceof Function){
        var confirm_string_dismiss_user = $("#confirm_string_dismiss_user").html();
        $("table.userlist").on("click", "td.dismiss span", function(){
            var $this = $(this).closest("tr");
            var userId = $this.data("id");
            var companyId = $this.data("company-id"); 
            confirmBox(confirm_string_dismiss_user.replace('{{fullname}}',$this.find("td.name").html()), function(){
                ajaxRequest("/ajax/company/" + companyId + "/dismissuser/" + userId,null,function(data){
                    $this.remove();
                });
            }, null);
        });
    }
});