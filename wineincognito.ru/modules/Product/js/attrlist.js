$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
        $("table.attrlist").on("click", "td.hide span", function(){
            var $this = $(this).closest("tr");
            var attrId = $this.data("id");
            var hideStatus = $this.hasClass("hidden");
            ajaxRequest("/ajax/moderate/product/subattributes/" + attrId + "/hide", {changeTo:hideStatus?0:1}, function(data){
                if(data['hide_status']==0){
                    $this.removeClass("hidden").addClass("visible");
                } else {
                    $this.removeClass("visible").addClass("hidden");
                }
            }, null);
        });
        
    }
});