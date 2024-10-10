$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
        $(document).on("click",".filter-block.vintage-filter .datalist tbody td.favourite.can-favourite span", function(){
            var $tr = $(this).closest("tr");
            var id = $tr.data("id");
            var isFavourite = $tr.hasClass("favourite");
            ajaxRequest("/ajax/vintage/favourite", {
                id:id,
                favourite_to:isFavourite?0:1,
            }, function(data){
                if(isFavourite){
                    $tr.removeClass("favourite");
                } else {
                    $tr.addClass("favourite");
                }
            },null);
        });

        $(document).on("click",".filter-block.vintage-filter .datalist tbody td.company-favourite.can-favourite span", function(){
            var $tr = $(this).closest("tr");
            var id = $tr.data("pid");
            var isFavourite = $tr.hasClass("company-favourite");
            ajaxRequest("/ajax/product/company_favourite", {
                id:id,
                favourite_to:isFavourite?0:1,
            }, function(data){
                var $trs = $tr.closest(".datalist tbody").find("tr.item").filter(function(){
                    return $(this).data("pid")==id;
                });
                if(isFavourite){
                    $trs.removeClass("company-favourite");
                } else {
                    $trs.addClass("company-favourite");
                }
            },null);
        });
        $(document).on("change",".filter-block.vintage-filter .filter-form input[type=checkbox][name=onlyscored]",function(){
            $form = $(this).closest("form");
            if($form.data("dropbox-filter-only-scored")!=this.checked?1:0){
                $form.data("dropbox-filter-only-scored",this.checked?1:0).trigger("dropbox-refresh");
            }
        });
        $(document).on("change",".filter-block.vintage-filter .filter-form input[type=checkbox][name=onlyawarded]",function(){
            $form = $(this).closest("form");
            if($form.data("dropbox-filter-only-awarded")!=this.checked?1:0){
                $form.data("dropbox-filter-only-awarded",this.checked?1:0).trigger("dropbox-refresh");
            }
        });
        $(document).on("change",".filter-block.vintage-filter .filter-form input[type=checkbox][name=only_personally_scored]",function(){
            $form = $(this).closest("form");
            if($form.data("dropbox-filter-only-personally-scored")!=this.checked?1:0){
                $form.data("dropbox-filter-only-personally-scored",this.checked?1:0).trigger("dropbox-refresh");
            }
        });
        $(document).on("change",".filter-block.vintage-filter .filter-form input[type=checkbox][name=only_company_favourite]",function(){
            $form = $(this).closest("form");
            if($form.data("dropbox-filter-only-company-favourites")!=this.checked?1:0){
                $form.data("dropbox-filter-only-company-favourites",this.checked?1:0).trigger("dropbox-refresh");
            }
        });
        $(document).on("change",".filter-block.vintage-filter .filter-form input[type=checkbox][name=only_favourite]",function(){
            $form = $(this).closest("form");
            if($form.data("dropbox-filter-only-my-favourites")!=this.checked?1:0){
                $form.data("dropbox-filter-only-my-favourites",this.checked?1:0).trigger("dropbox-refresh");
            }
        });
        $(document).on("reset",".filter-block.vintage-filter .filter-form",function(){
            $form = $(this);
            setTimeout(function(){
                $form.data("dropbox-filter-only-scored",$form.find("input[type=checkbox][name=onlyscored]").prop("checked")?1:0);
                $form.data("dropbox-filter-only-awarded",$form.find("input[type=checkbox][name=onlyawarded]").prop("checked")?1:0);
                $form.data("dropbox-filter-only-personally-scored",$form.find("input[type=checkbox][name=only_personally_scored]").prop("checked")?1:0);
                $form.data("dropbox-filter-only-company-favourites",$form.find("input[type=checkbox][name=only_company_favourite]").prop("checked")?1:0);
                $form.data("dropbox-filter-only-my-favourites",$form.find("input[type=checkbox][name=only_favourite]").prop("checked")?1:0);
            });
            
            // if($form.data("dropbox-filter-only-my-favourites")!=$form.find("input[type=checkbox][name=only_favourite]").prop("checked")?1:0){
            //     $form.data("dropbox-filter-only-my-favourites") = 
            // }
        });
    }
});