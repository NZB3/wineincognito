$(function(){
    $("ul#menu-structure").on("click", "li > span.line .visibility-toggle", function(e){
        var $li = $(this).closest("li");
        var id = $li.data("id");
        if(!id){
            return;
        }
        if($li.hasClass("block-visibility-change")){
            return;
        }
        $li.addClass("block-visibility-change");
        var active = $li.hasClass("visible");
        $.ajax({
            url:"/admin/menu/activateMenuItem/" + id + "/" + (active?0:1),
            dataType: "json",
            success: function(data){
                if(data.err){
                    $li.removeClass("block-visibility-change");
                    if (typeof UIOutputInfoBlock == 'function'){
                        UIOutputInfoBlock(data.err.msg,0); 
                    }
                    return;
                }
                $li.removeClass("block-visibility-change");
                $li.removeClass("visible hidden").addClass(data.active?"visible":"hidden");
            },
            error: function(){
                $li.removeClass("block-visibility-change");
            }
        });
    });
    $("ul#menu-structure").on("mousedown", "li > span.line .visibility-toggle",function(e){
        e.stopPropagation();
    });
    var dragNDropStarted = false;
    var moveBefore = null;
    var moveAfter = null;
    var $body = $("body");
    var $movingLi;
    $("ul#menu-structure").on("mousedown", "li > span.line",function(){
        if(dragNDropStarted){
            return true;//never
        }
        dragNDropStarted = true;
        $body.addClass("isDnDing");
        $movingLi = $(this).closest("li").addClass("is-moving");
        moveBefore = null;
        moveAfter = null;
        var prevCoordinate;
        var $prevItem;
        var nextCoordinate;
        var $nextItem;
        var selfTop;
        var $siblings = $movingLi.siblings();
        function recalcData(){

            selfTop = $movingLi.children("span.line").offset().top;
            prevCoordinate = -99999999;
            $prevItem = null;
            nextCoordinate = 99999999;
            $prevItem = null;
            $siblings.each(function(){
                var $sibling = $(this).children("span.line");
                var top = $sibling.offset().top;
                if(selfTop < top && top < nextCoordinate){
                    nextCoordinate = top;
                    $nextItem = $sibling.closest("li");
                }
                if(selfTop > top){
                    var bottom = top + $sibling.outerHeight();
                    if(bottom > prevCoordinate){
                        prevCoordinate = bottom;
                        $prevItem = $sibling.closest("li");
                    }
                }
            });
        }
        recalcData();
        document.onmousemove = function(e) {
            if(e.pageY<prevCoordinate && $prevItem!==null || e.pageY>nextCoordinate && $nextItem!==null){
                if(e.pageY<prevCoordinate && $prevItem!==null){
                    moveAfter = null;
                    moveBefore = $prevItem.data("id");
                    $movingLi.detach().insertBefore($prevItem);
                } else if(e.pageY>nextCoordinate && $nextItem!==null){
                    moveBefore = null;
                    moveAfter = $nextItem.data("id");
                    $movingLi.detach().insertAfter($nextItem);
                }
                recalcData();
            }
        }
    });
    $(document).on("mouseup",function(){
        if(!dragNDropStarted){
            return;
        }
        document.onmousemove = null;
        $body.removeClass("isDnDing");
        if(!moveBefore && !moveAfter){
            dragNDropStarted = false;
            $movingLi.removeClass("is-moving");
            return;
        }
        var type;
        var toId;
        if(moveBefore){
            type = 'before';
            toId = moveBefore;
        } else {
            type = 'after';
            toId = moveAfter;
        }
        var id = $movingLi.data("id");
        if(!id){
            return;
        }
        $.ajax({
            url:"/admin/menu/moveMenuItem/" + id + "/" + type + "/" +toId,
            dataType: "json",
            success: function(data){
                if(data.err){
                    dragNDropStarted = false;
                    $movingLi.removeClass("is-moving");
                    if (typeof UIOutputInfoBlock == 'function'){
                        UIOutputInfoBlock(data.err.msg,0); 
                    }
                    return;
                }
                dragNDropStarted = false;
                $movingLi.removeClass("is-moving");
            },
            error: function(){
                dragNDropStarted = false;
                $movingLi.removeClass("is-moving");
            }
        });
    });

});