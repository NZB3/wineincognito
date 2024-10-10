$(function(){
    var widths = {};
    function recalcWidths(){
        newwidths = {};
        $(".translationlist td span.hiddenmeasure + input[type=text]").each(function(){
            var $this = $(this);
            var langId = $this.closest("td").data("lang-id");
            var width = $this.siblings("span").width();
            if(newwidths[langId]==undefined || newwidths[langId]<width){
                newwidths[langId] = width;
            }
        });
        $.each(newwidths,function(langId,val){
            if(widths[langId]==undefined || widths[langId]!=val){
                changeInputWidth(langId,val);
            }
        });
    }
    function changeInputWidth(langId,width){
        widths[langId] = width;
        $(".translationlist td input[type=text]").filter(function(){
            return $(this).closest("td").data("lang-id")==langId;
        }).width(width);
    }
    $(".translationlist td span.hiddenmeasure + input[type=text]").each(function(){
        var $this = $(this);
        $this.siblings("span.hiddenmeasure").text($this.val()).css("font-size", $this.css("font-size")).css("font-family", $this.css("font-family"));
    })
    recalcWidths();
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
        $(".translationlist td input[type=text]").prop("disabled",true).closest("td").removeClass("editing saving").addClass("disabled");

        $(".translationlist").on("input","input[type=text]",function(){
            var $input = $(this);
            var langId = $input.closest("td").data("lang-id");
            if($input.width()!=widths[langId]){
                recalcWidths();
            }
            var width = $input.siblings("span.hiddenmeasure").text($input.val()).width();
            if(width<$input.width()){
                return;
            }
            changeInputWidth(langId,width);
        });
        $(".translationlist").on("click", "span.edit",function(){
            var $input = $(this).siblings("input[type=text]");
            $input.prop("disabled", false).closest("td").removeClass("disabled saving").addClass("editing");
        });

        $(".translationlist").on("click", "span.save",function(){
            var $input = $(this).siblings("input[type=text]")
            $input.prop("disabled", true).closest("td").removeClass("editing disabled").addClass("saving");
            recalcWidths();
            var id = $input.closest("tr").data("id");
            var lang = $input.closest("td").data("lang-id");
            var translation = $input.val();

            ajaxRequest("/ajax/translation/interface/item/" + id + "/edit", {
                lang:lang,
                translation:translation,
            }, function(){//success
                $input.prop("disabled", true).closest("td").removeClass("editing saving").addClass("disabled");
            }, function(){//error
                $input.prop("disabled", false).closest("td").removeClass("disabled saving").addClass("editing");
            });
        });
    }

    

    
    
});