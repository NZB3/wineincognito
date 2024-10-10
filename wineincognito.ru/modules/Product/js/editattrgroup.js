$(function(){
    if(typeof(dropboxLoad)!=='undefined' && dropboxLoad instanceof Function){
        var $loadings = $(".edit-attr-group").find(".loading");
        //visible
        var $visibleAfter = $loadings.filter(function(){
            return $(this).hasClass("visible-for");
        }).show();
        dropboxLoad($visibleAfter.data("group"),String($visibleAfter.data("values")).split(","),0,0,0,0,0,"visible",$visibleAfter,function(){
            $visibleAfter.remove();
        });
        //required
        var $requiredAfter = $loadings.filter(function(){
            return $(this).hasClass("required-for");
        }).show();
        dropboxLoad($requiredAfter.data("group"),String($requiredAfter.data("values")).split(","),0,0,0,0,0,"required",$requiredAfter,function(){
            $requiredAfter.remove();
        });
        //doublecheck
        var $doublecheckAfter = $loadings.filter(function(){
            return $(this).hasClass("doublecheck-for");
        }).show();
        dropboxLoad($doublecheckAfter.data("group"),String($doublecheckAfter.data("values")).split(","),0,0,0,0,0,"doublecheck",$doublecheckAfter,function(){
            $doublecheckAfter.remove();
        });
        // $(".edit-attr-group").find(".loading.visible-for, .loading.required-for, .loading.doublecheck-for").show();
        // dropboxLoad = 
    }
});