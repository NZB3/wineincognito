$(function(){
    if(typeof(fillTemplate)!=='undefined' && fillTemplate instanceof Function){
        var images = [];
        var index = 0;
        var $body = $("body");

        var wrapperTemplate = '<div class="wi-gallery-wrapper"><span class="helper"></span><div class="wi-gallery-container"><div class="prev"></div><div class="next"></div><div class="close"></div></div></div>';
        var imageTemplate = '<div class="image"><span class="helper"></span><img src="{{src}}" /></div>';

        function slideImage(forward){
            if(forward){
                index++;
            } else {
                index--;
            }
            if(index<0){
                index = images.length-1;
            }
            if(index>=images.length){
                index = 0;
            }
            var src = images[index];
            $("body > .wi-gallery-wrapper .image").fadeOut("fast",function(){
                $(this).remove();
            });
            $(fillTemplate(imageTemplate,{src:images[index]})).appendTo($("body > .wi-gallery-wrapper > .wi-gallery-container")).fadeIn("fast");
        }

        var $wiGalleryWrapper;

        $(document).on("click", ".wi-gallery",function(e){
            var $target = $(e.target);
            if(!$target.is("img")){
                $target = $target.find("img").first();
            }
            if(!$target.length){
                return true;
            }
            images = [];
            index = 0;
            $target.closest(".wi-gallery").find("img").each(function(){
                var $img = $(this);
                images.push($img.prop("src"));
                if($img.is($target)){
                    index = images.length - 1;
                }
            });
            if(!images.length){
                return true;
            }
            var $wiGalleryWrapper = $body.append(wrapperTemplate).children(".wi-gallery-wrapper");
            $wiGalleryWrapper.children(".wi-gallery-container").append(fillTemplate(imageTemplate,{src:images[index]})).find(".image").show();
            if(images.length==1){
                $wiGalleryWrapper.find(".next,.prev").remove();
            }
            $wiGalleryWrapper.fadeIn("fast");
        });
        $(document).on("click",".wi-gallery-wrapper, .wi-gallery-container .close",function(e){
            $("body > .wi-gallery-wrapper").fadeOut("fast",function(){
                $(this).remove();
                images = [];
                index = 0;
            });
        });
        $(document).on("click",".wi-gallery-container, .wi-gallery-container .next",function(e){
            slideImage(true);
            e.stopPropagation();
        });
        $(document).on("click",".wi-gallery-container .prev",function(e){
            slideImage(false);
            e.stopPropagation();
        });
        $(document).on("mouseenter",".wi-gallery-container .prev, .wi-gallery-container .next, .wi-gallery-container .close",function(e){
            $(this).stop().animate({'opacity':'0.7'}, 500);
        });
        $(document).on("mouseleave",".wi-gallery-container .prev, .wi-gallery-container .next, .wi-gallery-container .close",function(e){
            $(this).stop().animate({'opacity':'0.2'}, 500);
        });

    }
});