$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
        typeof(confirmBox)!=='undefined' && confirmBox instanceof Function){
        $(document).on("click",".viewVintage span.favourite_btn.can-favourite", function(){
            var $this = $(this);
            var id = $this.data("id");
            var isFavourite = $this.hasClass("favourite");
            ajaxRequest("/ajax/vintage/favourite", {
                id:id,
                favourite_to:isFavourite?0:1,
            }, function(data){
                if(isFavourite){
                    $this.removeClass("favourite");
                } else {
                    $this.addClass("favourite");
                }
            },null);
        });
        $(document).on("click",".viewVintage span.company_favourite_btn.can-favourite", function(){
            var $this = $(this);
            var id = $this.data("id");
            var isFavourite = $this.hasClass("company-favourite");
            ajaxRequest("/ajax/product/company_favourite", {
                id:id,
                favourite_to:isFavourite?0:1,
            }, function(data){
                if(isFavourite){
                    $this.removeClass("company-favourite");
                } else {
                    $this.addClass("company-favourite");
                }
            },null);
        });

        $(document).on("click", ".viewVintage span.delete", function(){
            var $this = $(this);
            var $table = $this.closest("table");
            if($table.hasClass("compare")){
                return true;
            }
            var confirm_string_delete_product = $this.closest("th").find(".confirm_string_delete_product").html();
            var id = $this.data("id");
            confirmBox(confirm_string_delete_product, function(){
                ajaxRequest("/ajax/vintage/" + id + "/delete", null, function(){
                    $table.find("tbody").remove();
                    $this.remove();
                }, null);
            });
        });

        $(document).on("click", ".viewVintage span.approve", function(){
            var $this = $(this);
            var productId = $this.data("id");
            ajaxRequest("/ajax/product/" + productId + "/approve", null, function(){
                $this.closest("table").find(".awaiting-approval").remove();
                $this.remove();
            }, null);
        });
    }
});