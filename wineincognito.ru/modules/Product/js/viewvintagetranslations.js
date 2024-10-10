$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
        $(document).on("click",".vintage-translations tbody td.approve span", function(){
            processTranslation($(this).closest("tr"),true);
        });
        $(document).on("click",".vintage-translations tbody td.deny span", function(){
            processTranslation($(this).closest("tr"),false);
        });
        function processTranslation($tr,approve){
            var id = $tr.data("id");
            ajaxRequest("/ajax/translation/vintage/" + id + "/approve", {
                approve:approve?1:0,
            }, function(){
                $tr.remove();
                if(approve){
                    location.reload();
                }
            },null);
        }
    }
});