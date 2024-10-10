$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
        function sendRequest(userId, approve, $row){
            ajaxRequest("/ajax/company/" + companyId + "/resolvejoinrequest/" + userId,{"approve":approve?1:0},function(data){
                $row.remove();
            });
        }
        $("table.userlist").on("click", "td.approve span", function(){
            var $this = $(this).closest("tr");
            sendRequest($this.data("id"),true,$this);
        });
        $("table.userlist").on("click", "td.deny span", function(){
            var $this = $(this).closest("tr");
            sendRequest($this.data("id"),false,$this);
        });

    }
});