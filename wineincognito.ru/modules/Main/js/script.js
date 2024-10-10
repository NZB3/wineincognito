$(function(){
    var $document = $(document);
    var $window = $(window);
    $("body > #menu > ul > li").on("mouseenter", function(){
        var $this = $(this);
        var $ul = $this.find("ul");
        if(!$ul.length){
            return true;
        }
        var windowTop = $this.offset().top - $document.scrollTop();
        var ulHeight = $ul.outerHeight();
        var windowHeight = $window.height();
        if(windowHeight - windowTop > ulHeight){
            $ul.css({top:0,bottom:"auto"});
            return true;
        }
        if(windowTop + $this.height() > ulHeight){
            $ul.css({top:"auto",bottom:0});
            return true;
        }
        $ul.css({top:(windowHeight-windowTop-ulHeight)+"px",bottom:"auto"});
        return true;
    });
    var $menuBackground = $("body > #menu-background");
    if($menuBackground.length){
        var leftOffset = parseInt($menuBackground.css('left')); //Grab the left position left first
        $(window).scroll(function(){
            $menuBackground.css({
                'left': -$(this).scrollLeft() + leftOffset //Use it later
            });
        });    
    }
    $("body > div#searchbar table td.menu-visibility-toggle span").on("click",function(){
        var $this = $(this).closest("div#searchbar");
        if($this.hasClass("menu-show")){
            $this.removeClass("menu-show");
        } else {
            $this.addClass("menu-show");
        }
    });
    // infoBlock
    (function(){
        const infoBlockGroupMinTop = 10;
        var $infoBlockGroup = $("#infoBlock-group");
        var $infoBlockGroupFiller = $("#infoBlock-group-filler");
        if($infoBlockGroupFiller.length){
            var $window = $(window);
            var infoBlockGroupFillerTop = $infoBlockGroupFiller.offset().top;
            var currentInfoBlockGroupTop = infoBlockGroupMinTop;
            function onWindowScroll(){
                var infoBlockGroupTop = Math.max(infoBlockGroupFillerTop-$window.scrollTop(),10);
                if(currentInfoBlockGroupTop!=infoBlockGroupTop){
                    currentInfoBlockGroupTop = infoBlockGroupTop;
                    $infoBlockGroup.css({
                        top: infoBlockGroupTop
                    })
                }

            }
            $window.on("scroll",onWindowScroll);
            onWindowScroll();

            // setInterval(function(){
            //     $infoBlockGroupFiller.height($infoBlockGroup.outerHeight());
            // },100);
            var infoBlockGroupFillerHeight = $infoBlockGroupFiller.height();
            function onInfoBlockGroupResize(){
                var infoBlockGroupHeight = $infoBlockGroup.height();
                if(infoBlockGroupHeight != infoBlockGroupFillerHeight){
                    $infoBlockGroupFiller.height(infoBlockGroupFillerHeight = infoBlockGroupHeight);
                }
            }
            var infoBlockGroupResizeIntervalHandler = null;
            var infoBlockGroupResizeStarted = 0;
            $infoBlockGroup.on("resize-start",function(){
                infoBlockGroupResizeStarted++;
                if(infoBlockGroupResizeStarted<=1){
                    infoBlockGroupResizeStarted = 1;
                    infoBlockGroupResizeIntervalHandler = setInterval(function(){
                        onInfoBlockGroupResize();
                    },15);    
                }
            });
            $infoBlockGroup.on("resize-stop",function(){
                infoBlockGroupResizeStarted--;
                if(infoBlockGroupResizeStarted<=0){
                    infoBlockGroupResizeStarted = 0;
                    clearInterval(infoBlockGroupResizeIntervalHandler);
                }
            });
            onInfoBlockGroupResize();

            $infoBlockGroup.on("mousedown", "div.infoBlock", function(e){
                if(e.button==2){ 
                    var $this = $(this);
                    $infoBlockGroup.trigger("resize-start");
                    $this.slideUp(400, function(){
                        $this.remove();
                        $infoBlockGroup.trigger("resize-stop");
                    });
                    e.preventDefault();
                    return false;
                }
            });
            $infoBlockGroup.on("contextmenu", "div.infoBlock", function(e){
                e.preventDefault();
            });
            $(document).on("mousedown", "div.ext-infoBlock", function(e){
                if(e.button==2){ 
                    var $this = $(this);
                    $this.slideUp(400, function(){
                        $this.remove();
                    });
                    e.preventDefault();
                }
            });
            $(document).on("contextmenu", "div.ext-infoBlock", function(e){
                e.preventDefault();
            });
        }
        
        
    })();
    
    // $(function() {
    // var a = function() {
    // var b = $(window).scrollTop();
    // var d = $("#scroller-anchor").offset({scroll:false}).top;
    // var c=$("#scroller");
    // if (b>d) {
    // c.css({position:"fixed",top:"0px"})
    // } else {
    // c.css({position:"relative",top:""})
    // }
    // };
    // $(window).scroll(a);a()
    // });
    
    // ml_tab
    $(document).on("change","input:radio.ml_tab",function(e){
        var $table = $(this).closest("table");
        $.each($table.find("input:radio.ml_tab"),function(){
            var $this = $(this);
            if($this.is(':checked')){
                $table.addClass("show_lang_" + $this.val());
                if (e.originalEvent !== undefined){
                    $table.find(".lang_"+ $this.val()).find("input[type=text],textarea").first().focus();
                }
            } else {
                $table.removeClass("show_lang_" + $this.val());
            }
        });
    });
    window.initLanguageTabs = function($parent){
        var curLangId = $("body > #searchbar .language-selector").data("cur-lang");
        $.unique($parent.find("input:radio.ml_tab").closest("table")).each(function(){
            $(this).find(".tabblock").each(function(){
                var $radios = $(this).find("input:radio.ml_tab");
                if(curLangId){
                    $radios = $radios.filter(function(){
                        return this.value == curLangId;
                    });
                }
                $radios.first().prop("checked",true).trigger("change");
            });
        });    
    };
    initLanguageTabs($("body"));

    var $confirmBox = $("#confirmBox");
    var confirmBox_callback_ok = null;
    var confirmBox_callback_cancel = null;
    function confirmBox(text, callback_ok, callback_cancel){
        confirmBox_callback_ok = callback_ok;
        confirmBox_callback_cancel = callback_cancel;
        $confirmBox.find(".text").html(text);
        $confirmBox.show();
    }
    $confirmBox.find(".cancel").on("click", function(){
        $confirmBox.hide();
        if (typeof confirmBox_callback_cancel === 'function') {
            confirmBox_callback_cancel();
        }
    });
    $confirmBox.on("click", function(){
        if(event.target != this){
            return true;
        }
        $confirmBox.hide();
        if (typeof confirmBox_callback_cancel === 'function') {
            confirmBox_callback_cancel();
        }
    });
    $confirmBox.find(".ok").on("click", function(){
        $confirmBox.hide();
        if (typeof confirmBox_callback_ok === 'function') {
            confirmBox_callback_ok();
        }
    });
    window.confirmBox = confirmBox;
    // modal wrapper
    $(document).on("click","body > .modal-wrapper > .modal > .modal-close, body > .modal-wrapper > .modal .close",function(e){
        // if($(e.target).is("body > .modal-wrapper, body > .modal-wrapper .close")){
        var $this = $(this).closest(".modal-wrapper");
        $this.fadeOut("fast",function(){
            $this.remove();
        });
        // }
    });
    window.wrapForm = function(form){
        var $wrapper = $("<div class=\"modal-wrapper\"><div class=\"helper\"></div><div class=\"modal\"><div class=\"modal-close\"></div>" + form + "</div></div>").appendTo($("body")).fadeIn("fast");
        if(typeof(window.dropBoxSetLabels)!=='undefined' && window.dropBoxSetLabels instanceof Function){
            window.dropBoxSetLabels();
        }
        if(typeof(window.initLanguageTabs)!=='undefined' && window.initLanguageTabs instanceof Function){
            window.initLanguageTabs($wrapper);
        }
        $wrapper.find("form").trigger("wrapFormInit");
        return $wrapper;
    } 
    //keepAlive
    var browserSupportsLocalStorage = supports_local_storage();
    var keepAliveLastTime = Math.floor(Date.now()/600000);
    if(browserSupportsLocalStorage){
        localStorage.setItem("keepAliveLastTime", keepAliveLastTime);
    }
    setInterval(function(){
        var keepAliveCurTime = Math.floor(Date.now()/600000);
        if(browserSupportsLocalStorage){
            keepAliveLastTime = localStorage.getItem("keepAliveLastTime");
        }
        if(keepAliveCurTime>keepAliveLastTime && document.cookie.indexOf("majority=1")>=0){
            ajaxRequest("/ajax/keepalive",null,function(){
                keepAliveLastTime = keepAliveCurTime;
                if(browserSupportsLocalStorage){
                    localStorage.setItem("keepAliveLastTime", keepAliveLastTime);
                }
            });
        }
    },60000);
    //tooltip
    $document.on("click","[data-tooltip]",function(){
        var $this = $(this);
        if($this.hasClass("non-sticky-tooltip")){
            return;
        }
        if($this.hasClass("show-tooltip")){
            $this.removeClass("show-tooltip");
        } else {
            $("[data-tooltip].show-tooltip").removeClass("show-tooltip");
            $this.addClass("show-tooltip");
        }
    });
    //compactable
    $(document).on("click", "table.compactable thead tr.header th", function(){
      var $table = $(this).closest("table.compactable");
      if($table.hasClass("compact")){
          $table.removeClass("compact");
      } else {
          $table.addClass("compact");
      }
    });
});

$(function(){
    var $infoBlockGroup = $("body > #content > #infoBlock-group");
    var $successes = $infoBlockGroup.find(".infoBlock").data("appeared",Date.now()).filter(".success");
    if($successes.length){
        setTimeout(function(){
            $infoBlockGroup.trigger("resize-start");
            $successes.slideUp(400,function(){
                $successes.remove();
                $infoBlockGroup.trigger("resize-stop");
            });
        },2500);
    }
});
function UIOutputInfoBlock(msg,type){
    var className;
    switch(type){
        case 0:
            className = "error";
            break;
        case 1:
            className = "warning";
            break;
        case 2:
            className = "success";
            break;
        default:
            return;
    }
    var $infoBlockGroup = $("body > #content > #infoBlock-group");
    // var $infoBlock = $infoBlockGroup.children(".infoBlock");
    // if($infoBlock.length){
    //     $infoBlock.slideUp(400,function(){
    //         $infoBlock.remove();
    //         $infoBlockGroup.prepend('<div class="infoBlock '+className+'" style="display: none">'+msg+'</div>').children(".infoBlock").slideDown(400);
    //     });    
    // } else {
        UIFlushInfoBlocks();
        var $newInfoBlock = $('<div class="infoBlock '+className+'" style="display: none">'+msg+'</div>').data('appeared',Date.now());
        $infoBlockGroup.append($newInfoBlock);
        $infoBlockGroup.trigger("resize-start");
        $newInfoBlock.slideDown(400,function(){
            if(type==2){
                setTimeout(function(){
                    $infoBlockGroup.trigger("resize-start");
                    $newInfoBlock.slideUp(400,function(){
                        $(this).remove();
                        $infoBlockGroup.trigger("resize-stop");
                    });
                },2500);
            }
            $infoBlockGroup.trigger("resize-stop");
        });
    // }
}
function UIFlushInfoBlocks(){
    var seconds = Date.now();
    var $infoBlockGroup = $("body > #content > #infoBlock-group");
    var $infoBlock = $infoBlockGroup.children(".infoBlock").filter(function(){
        return seconds>$(this).data("appeared")+2500;
    });
    if($infoBlock.length){
        $infoBlockGroup.trigger("resize-start");
        $infoBlock.slideUp(400,function(){
            $infoBlock.remove();
            $infoBlockGroup.trigger("resize-stop");
        });
    }
}
function supports_local_storage(){
    try{
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e){
        return false;
    }
}
function ajaxRequest(url, post, callback_success, callback_error) {
    UIFlushInfoBlocks();
    var isFormData = post instanceof FormData;
    return jQuery.ajax({
        url: url,
        type: "POST",
        data: post,
        contentType: isFormData?false:"application/x-www-form-urlencoded; charset=UTF-8",
        processData: isFormData?false:true,
        dataType: "JSON",
        success: function(data) {
            if(data["err"] !== undefined){
                var errMsg;
                if(data["errmsg"] !== undefined){
                    if(data["errmsg"] !== null){
                        errMsg = data["errmsg"];
                    } else {
                        errMsg = "Unknown Error";
                    }
                }
                var proceedDefault;
                if (typeof callback_error === 'function'){
                    proceedDefault = callback_error(errMsg);
                }
                if(proceedDefault!==false && errMsg!==undefined){
                    UIOutputInfoBlock(errMsg,0);
                }
            }
            if(data["success"] !== undefined){
                if (typeof callback_success === 'function'){
                    callback_success(data["data"]);
                }
                if(data["successmsg"] !== undefined){
                    UIOutputInfoBlock(data["successmsg"],2);
                }
            }
        },
        error: function(xhr, text_status, error_thrown){
            if (text_status != "abort"){
                var proceedDefault;
                var errMsg = "Service is temporary unavailable, please retry later.";
                if (typeof callback_error === 'function'){
                    proceedDefault = callback_error(errMsg);
                }
                if(proceedDefault!==false){
                    UIFlushInfoBlocks();
                    UIOutputInfoBlock(errMsg,0);    
                }
                
            }
        }
    });
}
function fillTemplate(template,data,escaping=true){
    jQuery.each(data, function(key, val){
        if(typeof val == "string" || typeof val == "number"){
            if(escaping){
                val = escapeStr(val);
            }
            template = template.replace(new RegExp("\\{\\{"+key+"\\}\\}","g"), val);
        }
    });
    template = template.replace(/\{\{\w+\}\}/g, "")
    $.each(template.match(/\{ifdef\{\w+\}\}/g),function(dummy,val){
        var key = val.match(/^\{ifdef\{(\w+)\}\}$/);
        if(key===null){
            return true;//never
        }
        key = key[1];
        if(key in data && ( typeof data[key] == "string" || typeof data[key] == "number" ) ){
            template = template.replace(new RegExp("\\{(?:end)?ifdef\\{"+key+"\\}\\}","g"),"");
        } else {
            template = template.replace(new RegExp("\\{ifdef\\{"+key+"\\}\\}.+?\\{endifdef\\{"+key+"\\}\\}","g"),"");
        }
    });
    $.each(template.match(/\{if\{\w+\}\}/g),function(dummy,val){
        var key = val.match(/^\{if\{(\w+)\}\}$/);
        if(key===null){
            return true;//never
        }
        key = key[1];
        if(key in data && ( typeof data[key] == "string" || typeof data[key] == "number" || typeof data[key] == "boolean" ) && data[key] ){
            template = template.replace(new RegExp("\\{(?:end)?if\\{"+key+"\\}\\}","g"),"");
        } else {
            template = template.replace(new RegExp("\\{if\\{"+key+"\\}\\}.+?\\{endif\\{"+key+"\\}\\}","g"),"");
        }
    });
    $.each(template.match(/\{!if\{\w+\}\}/g),function(dummy,val){
        var key = val.match(/^\{!if\{(\w+)\}\}$/);
        if(key===null){
            return true;//never
        }
        key = key[1];
        if(!(key in data && ( typeof data[key] == "string" || typeof data[key] == "number" || typeof data[key] == "boolean" ) && data[key] )){
            template = template.replace(new RegExp("\\{(?:end)?!if\\{"+key+"\\}\\}","g"),"");
        } else {
            template = template.replace(new RegExp("\\{!if\\{"+key+"\\}\\}.+?\\{end!if\\{"+key+"\\}\\}","g"),"");
        }
    });
    template = template.replace(/\{\w+\{\w+\}\}/g, "");
    return template;
}
function sizeOfObject(obj){
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)){
            size++;
        } 
    }
    return size;
}
function escapeStr(str){
    if(typeof str != "string"){
        return str;
    }
    return str.replace(/[<>\&]/gim,function(match){//\u00A0-\u9999
       return '&#'+match.charCodeAt(0)+';';
    });
}
function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
function prepareMultilineValue(text){
    return "<p class=\"multiline-value\">" + escapeStr(text).replace(/\n+/g, "</p><p>") + "</p>";
}
function postRedirect(url, postdata, blank){
    var inputs = '';
    $.each(postdata, function(index, value){
        inputs += '<input type="hidden" name="'+index+'" value="'+value+'" />';
    });
    $('<form action="'+ url +'" method="post" '+(blank?'target="_blank"':'')+'>'+inputs+'</form>').appendTo('body').submit().remove();
    return true;
}