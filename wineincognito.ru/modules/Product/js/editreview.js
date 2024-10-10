$(function(){
    var $document = $(document);
    // var currentWineType = undefined;
    // var currentSpectrumColor = undefined;
    var stepNum = 0;
    var maxStep = 10;

    var colorSpectrumSubColorData = {};
    var $colorSpectrumSubColorData = $("script.color-spectrum-subcolor-data");
    if($colorSpectrumSubColorData.length){
        colorSpectrumSubColorData = JSON.parse($colorSpectrumSubColorData.html());
    }
    $colorSpectrumSubColorData = undefined;

    function getcolorSpectrumSubColorData(color,subcolor,depth){
        var result = null;
        $.each(colorSpectrumSubColorData,function(key,val){
            if(val.color==color && val.subcolor==subcolor && val.depth == depth){
                result = val;
                return false;//break
            }
        });
        return result;
    }
    function scrollIntoView($element){
        if(!$element || $element.length==0){
            return;
        }
        var minTop = null;
        $element.filter(":visible").each(function(){
            var $this = $(this);
            var top = $this.offset().top;
            if (top < minTop || minTop===null) {
                minTop = top;
            }
        });
        if(minTop===null){
            return;
        }
        setTimeout(function(){
            $([document.documentElement, document.body]).animate({
                scrollTop: minTop
            }, 500);
        });
    }
    function resizeButtons($table){
        $table.find("tr:visible ul").filter(".count-1,.count-2,.count-3,.count-4,.count-5,.count-6").each(function(){
            var $ul = $(this);
            var count = $ul.find("li:visible").length;
            var countClass;
            if(count==0){
                return true;//continue
            }
            if(count>6){
                count = 6;
            }
            countClass = "count-" + count;
            if($ul.hasClass(countClass)){
                return true;//continue
            }
            $ul.removeClass("count-1 count-2 count-3 count-4 count-5 count-6").addClass(countClass);
        });
    }
    function nextStep(step,$table,disableScroll=false,autofill=true){
        var $scrollTo;
        var needScroll;
        if(step>stepNum){
            stepNum=step;
            needScroll = !disableScroll;
            $scrollTo = $table.find("tr.step.step"+stepNum+":not(.subelement)");
        } else {
            needScroll = false;
        }
        var $steps = $table.find("tr.step").filter(function(){
            var $this = $(this);
            for(var i=0;i<=stepNum;i++){
                if($this.hasClass("step"+i)){
                    return true;
                }
            }
            return false;
        });
        var typeClass = 'null';
        // switch(currentWineType){
        //     case 1:
        //         typeClass = "type_still";
        //         break;
        //     case 2:
        //         typeClass = "type_sparkling";
        //         break;
        //     case 3:
        //         typeClass = "type_fortified";
        //         break;
        // }
        // var spectrumClass = 'null';
        // switch(currentSpectrumColor){
        //     case 1:
        //         spectrumClass = "spectrum-white";
        //         break;
        //     case 2:
        //         spectrumClass = "spectrum-pink";
        //         break;
        //     case 3:
        //         spectrumClass = "spectrum-red";
        //         break;
        // }
        
        //,.spectrum-white,.spectrum-pink,.spectrum-red
        //:not(.spectrum-white):not(.spectrum-pink):not(.spectrum-red)
        $steps
            // .removeClass("dynamic-optional")
            .filter(":not(.subelement):not(.dynamic-optional)").show().end()
            .filter(".dynamic-optional").hide();
            // .filter(":not(.type_still):not(.type_sparkling):not(.type_fortified)").show().end().end()
            // .filter(".type_still,.type_sparkling,.type_fortified").each(function(){
            //     var $this = $(this);
            //     if($this.is(".type_still,.type_sparkling,.type_fortified")){
            //         if($this.is("."+typeClass)){
            //             $this.show();
            //         } else {
            //             $this.addClass("dynamic-optional").hide();
            //             return true;//continue;
            //         }
            //     }
            //     // if($this.is(".spectrum-white,.spectrum-pink,.spectrum-red")){
            //     //     if($this.is("."+spectrumClass)){
            //     //         $this.show();    
            //     //     } else {
            //     //         $this.addClass("dynamic-optional").hide();
            //     //     }
            //     // }
            // });

                // .filter(":not(." + typeClass + ")").addClass("dynamic-optional").hide().end()
                // .filter("." + typeClass).show().end()
                // .end()
                //,li input.spectrum-white,li input.spectrum-pink,li input.spectrum-red
        // $steps.find("li input.type_still,li input.type_sparkling,li input.type_fortified").each(function(){
        //         var $this = $(this);
        //         if($this.is(".type_still,.type_sparkling,.type_fortified")){
        //             if($this.is("."+typeClass)){
        //                 $this.closest("li").removeClass("dynamic-optional").show();
        //             } else {
        //                 $this.closest("li").addClass("dynamic-optional").hide();
        //                 return true;//continue;
        //             }
        //         }
        //         // if($this.is(".spectrum-white,.spectrum-pink,.spectrum-red")){
        //         //     if($this.is("."+spectrumClass)){
        //         //         $this.closest("li").removeClass("dynamic-optional").show();    
        //         //     } else {
        //         //         $this.closest("li").addClass("dynamic-optional").hide();
        //         //     }
        //         // }
        //     });
                // .filter(":not(." + typeClass + ")").closest("li").hide().end().end()
                // .filter("." + typeClass).closest("li").show();
        // $steps.filter("tr:has(li.dynamic-optional)").not("tr:has(li:not(.dynamic-optional) input)").addClass("dynamic-optional").hide().prev().not(":has(input)").addClass("dynamic-optional").hide();
        var $labelStep = $headerStep = null;
        $steps.each(function(){
            var $step = $(this);
            if($step.is("tr:has(td.header)")){
                if($headerStep!==null){
                    $headerStep.hide();
                }
                if($labelStep!==null){
                    $labelStep.hide();
                    $labelStep = null;
                }
                $headerStep = $step;
                return true;//continue
            }
            if($step.is("tr:has(td.label)")){
                if($labelStep!==null){
                    $labelStep.hide();
                }
                $labelStep = $step;
                return true;//continue
            }
            if($step.is(":visible") || $step.hasClass("multilang")){
                if($headerStep!==null){
                    $headerStep.show();
                    $headerStep = null;
                }
                if($labelStep!==null){
                    $labelStep.show();
                    $labelStep = null;
                }
            }
        });
        if($headerStep!==null){
            $headerStep.hide();
            $headerStep = null;
        }
        if($labelStep!==null){
            $labelStep.hide();
            $labelStep = null;
        }
        
        if($steps.filter(".step" + stepNum).filter(":visible").length==0 && checkStepCompletion(stepNum,$table)){
            return nextStep(stepNum+1,$table,false);
        }
        $steps.filter(".step" + stepNum +".has-subelements:visible").each(function(){
            var $input = $(this).find("input:checked").first();
            if(!$input.length){
                return true;//continue
            }
            $input.trigger("change");
        });
        if(autofill){
            $steps.filter(".step" + stepNum +".autofill").each(function(){
                var $tr = $(this);
                var autofillFrom = $tr.data("autofill-from");
                if(!autofillFrom){
                    return true;//continue
                }
                var vals = [];
                $tr.closest("form").find("tr.element-"+autofillFrom+" input:checked").each(function(){
                    vals.push(this.value);
                });
                $tr.find("input").each(function(){
                    var val = this.value;
                    if(vals.indexOf(this.value)==-1){
                        this.checked = false;
                    } else {
                        this.checked = true;
                    }
                }).filter(":checked").trigger("change");
            });
        }
        resizeButtons($table);
        if(needScroll && $scrollTo.length){
            scrollIntoView($scrollTo);
        }
    }
    function checkStepCompletion(step,$table,pointTo=false){
        if(step>maxStep){
            return false;
        }
        // return true;
        // if(step==1){
        //     return checkColorSpectrumStepCompletion($table);
        // }
        var values = {};
        var unrequired = [];
        $table.find("tr.step.step" + step + ".highlight").removeClass("highlight");
        $table.find("tr.step.step" + step + " input[type=checkbox], tr.step.step" + step + " input[type=radio]").closest("tr").each(function(){
            var $tr = $(this);
            if($tr.hasClass("optional") || $tr.hasClass("dynamic-optional")){
                return true;
            }
            var trnames = [];
            $tr.find("input[type=checkbox],input[type=radio]").each(function(){
                var $this = $(this);
                var name = $this.prop("name");
                if(trnames.indexOf(name)!==-1){//skipping excess processing
                    return true;
                }
                trnames.push(name);
                if(!values.hasOwnProperty(name) || values[name]==undefined){//color-spectrum exception
                    var val = $tr.find("input[name=\"" + name + "\"]:checked").first().val();
                    values[name] = val;
                    if($tr.hasClass("has-subelements") && val <= 0){
                        $table.find("tr.step.step" + step + "." + name + "-subelement input").each(function(){
                            var name = $(this).prop("name");
                            if(unrequired.indexOf(name)==-1){
                                unrequired.push(name);
                            }
                        });
                    }
                }
            });
        });
        for(var i=0;i<unrequired.length;i++){
            delete values[unrequired[i]];
        }
        var hasUndefined = false;
        var undefinedKeys = [];
        $.each(values,function(key,value){
            if(value===undefined){
                hasUndefined = true;
                if(!pointTo){
                    return false;//break    
                }
                undefinedKeys.push(key);
            }
        });
        if(!hasUndefined){
            if(step>=maxStep){
                $table.find("tr.next-btn").remove();    
            }
            // $table.find("tr.didnottry").remove();
            return true;
        }
        if(pointTo){
            var selectorTexts = [];
            for(var i=0;i<undefinedKeys.length;i++){
                selectorTexts.push("tr.step.step" + step + ":visible input[name=\"" + undefinedKeys[i] + "\"]");
            }
            $highlighted = $table.find(selectorTexts.join(",")).closest("tr");
            $.each($highlighted,function(){
                var $element = $(this);
                while(true){
                    $element = $element.prev("tr.step.step" + step + ":visible");
                    if($element.length){
                        $.merge($highlighted,$element);
                    }
                    if(!$element.find("input").length){//including first label
                        break;
                    }
                }
            });
            // $.merge($highlighted, $highlighted.prevUntil("tr:has(input)","tr.step.step" + step + ":visible").filter(function(){
            //     var $this = $(this);
            //     if($this.next().is("tr:has(input)")){
            //         return true;
            //     }
            //     return false;
            // }));
            scrollIntoView($highlighted.addClass("highlight"));
        }
        return false;
    }
    // function checkColorSpectrumStepCompletion($table){
    //     var colorSpectrum = $table.find("input.color-spectrum:checked").val();
    //     if(!colorSpectrum){
    //         return false;
    //     }
    //     var colorSpectrumSubcolor;
    //     var colorSpectrumDepth;
    //     $table.find("tr.color-spectrum-subdata input:checked").each(function(){
    //         var $this = $(this);
    //         switch($this.prop("name")){
    //             case 'color-spectrum-subcolor':
    //                 colorSpectrumSubcolor = $this.val();
    //                 break;
    //             case 'color-spectrum-depth':
    //                 colorSpectrumDepth = $this.val();
    //                 break;
    //         }
    //     });
    //     if(colorSpectrumSubcolor && colorSpectrumDepth){//next step
    //         return true;
    //     }
    //     return false;
    // }
    // $document.on("change","tr.step:not(.step0) input",function(){
    //     if(!$(this).closest("tr").hasClass("step"+stepNum)){
    //         return true;
    //     }
    //     var $table = $(this).closest("tbody");
    //     if(checkStepCompletion(stepNum, $table)){
    //         nextStep(stepNum+1,$table);
    //     }
    // });
    function saveDraft($form){
        if(!supports_local_storage()){
            return;
        }
        var tpvId = $form.data("tpv-id");
        localStorage.setItem("tpvDraft" + tpvId,$form.serialize());
        var tpvDraftTimeOut = JSON.parse(localStorage.getItem('tpvDraftTimeOut'));
        if(tpvDraftTimeOut===null){
            tpvDraftTimeOut = {};
        }
        var currentTimeOutTime = Math.floor(Date.now()/7200000);//2 hours
        tpvDraftTimeOut[tpvId] = currentTimeOutTime + 1;
        $.each(tpvDraftTimeOut, function(key,val){
            if(val<currentTimeOutTime){
                localStorage.removeItem("tpvDraft" + key);
                delete tpvDraftTimeOut[key];
            }
        });
        localStorage.setItem('tpvDraftTimeOut', JSON.stringify(tpvDraftTimeOut));
    }
    $document.on("click",".edit-review-form tr.next-btn input.next-btn",function(){
        var $table = $(this).closest("tbody");
        if(checkStepCompletion(stepNum, $table, true)){
            nextStep(stepNum+1,$table);
        }
        saveDraft($table.closest(".edit-review-form"));
    });

    // $document.on("change",".edit-review-form input.wine-type",function(){//step0
    //     var $table = $(this).closest("tbody");
    //     currentWineType = $table.find("input.wine-type:checked").first().val();
    //     if(!currentWineType){//never
    //         return;
    //     }
    //     currentWineType = parseInt(currentWineType);
    //     $table.find("tr.didnottry").remove();
    //     // if(currentWineType==2){//sparkling
    //     //     $table.find("tr.external-observation input.external-observation-deposit-petillance").closest("li").hide().closest("ul").removeClass("count-3").addClass("count-2");
    //     // } else {
    //     //     $table.find("tr.external-observation input.external-observation-deposit-petillance").closest("li").show().closest("ul").removeClass("count-2").addClass("count-3");
    //     // }
    //     nextStep(0,$table,true);
    // });

    function setColorSpectrumLabel(color,subcolor,depth,$table){
        $table.find("tr.color-spectrum-subdata").find("input").each(function(){
            var $this = $(this);
            var name = $this.prop("name");
            var val = $this.val();
            var localSubcolor = subcolor;
            var localDepth = depth;
            var checked;
            switch($this.prop("name")){
                case 'color-spectrum-subcolor':
                    localSubcolor = $this.val();
                    checked = (localSubcolor==subcolor);
                    break;
                case 'color-spectrum-depth':
                    localDepth = $this.val();
                    checked = (localDepth==depth);
                    break;
            }
            var colorSpectrumSubColorData = getcolorSpectrumSubColorData(color,localSubcolor,localDepth);
            if(colorSpectrumSubColorData===null){
                    $this.prop("checked",false).closest("li").hide();
                    return true;
                }
            var example = (colorSpectrumSubColorData.example!==null && colorSpectrumSubColorData.example!==undefined)?colorSpectrumSubColorData.example:"inherit";
            $this.prop("checked",checked).closest("li").find("label")
                    .find("span.title").html(colorSpectrumSubColorData.title).end()
                    .find("span.example").css("background-color",example)
                .end().end().show();
        }).end().show();
        var colorSpectrumSubColorData = getcolorSpectrumSubColorData(color,subcolor,depth);
        if(colorSpectrumSubColorData!==null && colorSpectrumSubColorData.example!==null){
            var example = (colorSpectrumSubColorData.example!==null && colorSpectrumSubColorData.example!==undefined)?colorSpectrumSubColorData.example:"inherit";
            $table.find("tr.color-spectrum-subdata-example").find("span.example").css("background-color",example).end().show();
        } else {
            $table.find("tr.color-spectrum-subdata-example").find("span.example").css("background-color","inherit").end().hide();
        }
        
    }
    // function showSegment($elem,activatedSegmentCores,deactivatedSegmentCores){
    //     var show = true;
    //     for(var segmentCoreIter=0;segmentCoreIter<activatedSegmentCores.length;segmentCoreIter++){
    //         var foundClass = false;
    //         for(var i=0;i<activatedSegmentCores[segmentCoreIter].length;i++){
    //             if($elem.hasClass(activatedSegmentCores[segmentCoreIter][i])){
    //                 tempShow = true;
    //                 break;
    //             }
    //         }
    //         if(foundClass){
    //             continue;
    //         }
    //         foundClass = false;
    //         for(var i=0;i<deactivatedSegmentCores[segmentCoreIter].length;i++){
    //             if($elem.hasClass(deactivatedSegmentCores[segmentCoreIter][i])){
    //                 foundClass = true;
    //                 break;
    //             }
    //         }
    //         if(foundClass){
    //             show = false;
    //             break;
    //         }
    //     }
    //     return show;
    // }
    (function(){
        var segmentCores = [];
        var $table = $(".edit-review-form table").first();
        $table.find("input.segment-base").closest("tr").each(function(){
            var segmentCore = [];
            $(this).find("input.segment-base").each(function(){
                var $this = $(this);
                var segmentClass = $this.data("segment-base");
                if(segmentClass===undefined){
                    return true;//continue;
                }
                segmentCore.push(segmentClass);
            });
            segmentCores.push(segmentCore);
        });
        var segmentCoreClassChecks = [];
        for(var i=0;i<segmentCores.length;i++){
            segmentCoreClassChecks[i] = '.' + segmentCores[i].join(",.");
        }
        var $segments = $table.find("tr.segment, input.segment");
        for(var i=0;i<segmentCores.length;i++){
            if(!segmentCores[i].length){
                continue;
            }
            $segments.filter('.' + segmentCores[i].join(",.")).each(function(){
                var $this = $(this);
                for(var k=0;k<segmentCores[i].length;k++){
                    if(!$this.hasClass(segmentCores[i][k])){
                        $this.addClass("hide-segment-for-" + segmentCores[i][k]);
                    }
                }
            });
        }
    })();
    $document.on("change",".edit-review-form tr input.segment-base",function(){
        var $table = $(this).closest("table");
        var segmentClasses = [];
        $table.find("input.segment-base:checked,tr:not(:has(input.segment-base:checked)) input.segment-base").each(function(){
            var $this = $(this);
            var segmentClass = $this.data("segment-base");
            if(segmentClass===undefined){
                return true;//continue;
            }
            segmentClasses.push(segmentClass);
        });
        if(!segmentClasses.length){
            return;
        }
        var hideSegmentCheck = ".hide-segment-for-" + segmentClasses.join(",.hide-segment-for-");
        $table.find("tr li input.segment")
            .filter(":not("+hideSegmentCheck+")")
                .closest("li").removeClass("dynamic-optional").show().end()
                .end()
            .filter(hideSegmentCheck).each(function(){
                if(this.checked){
                    $(this).closest("tr").find("input[name=\""+this.name+"\"][value="+this.value+"]:not("+hideSegmentCheck+")").prop("checked", true);
                }
            }).prop("checked",false).closest("li").addClass("dynamic-optional").hide();

        $table.find("tr.dynamic-optional").removeClass("dynamic-optional");

        $table.find("tr:has(li.dynamic-optional)").not("tr:has(li:not(.dynamic-optional) input)").addClass("dynamic-optional");
        $table.find("tr.segment" + hideSegmentCheck).addClass("dynamic-optional").find("input:checked").prop("checked",false).each(function(){
            $table.find("tr.segment:not("+hideSegmentCheck+") input[name=\""+this.name+"\"][value="+this.value+"]").prop("checked", true);
        });

        $table.find("tr.score table.score-calc").find("tr.dynamic-optional").hide().find("input:checked").prop("checked",false).end().end().find("tr:not(.dynamic-optional)").show();
        nextStep(0,$table,true,false);
    });
    $document.on("change",".edit-review-form tr.color-spectrum input",function(e){
        var $table=$(this).closest("tbody");
        var val = $table.find("tr.color-spectrum input:checked").first().val();
        if(!val){//never
            return;
        }
        // currentSpectrumColor = parseInt(val);

        var colorSpectrumSubcolor = null;
        var colorSpectrumDepth = null;
        if(e.originalEvent === undefined){
            $table.find("tr.color-spectrum-subdata input:checked").each(function(){
                var $this = $(this);
                switch($this.prop("name")){
                    case 'color-spectrum-subcolor':
                        colorSpectrumSubcolor = $this.val();
                        break;
                    case 'color-spectrum-depth':
                        colorSpectrumDepth = $this.val();
                        break;
                }
            });
        }
        

        setColorSpectrumLabel(val,colorSpectrumSubcolor,colorSpectrumDepth,$table);
        nextStep(0,$table,true);
        // $table
        //     .find("tr.color-spectrum-subdata input.color-spectrum-subdata:checked").prop("checked",false).end()
        //     .find("tr.color-spectrum-subdata span.example").css("background-color","inherit").end()
        //     .find("tr.color-spectrum-subdata-example")
        //         .find("span.example").css("background-color","inherit").end()
        //         .hide().end()
        //     .find("tr.color-spectrum-subdata").hide().filter(".color-spectrum-subdata-"+val).show();
    });
    $document.on("change",".edit-review-form tr.color-spectrum-subdata input",function(){
        var $table = $(this).closest("tbody");
        var colorSpectrum = $table.find("tr.color-spectrum input:checked").val();
        if(!colorSpectrum){
            return;//never
        }
        var colorSpectrumSubcolor = null;
        var colorSpectrumDepth = null;
        $table.find("tr.color-spectrum-subdata input:checked").each(function(){
            var $this = $(this);
            switch($this.prop("name")){
                case 'color-spectrum-subcolor':
                    colorSpectrumSubcolor = $this.val();
                    break;
                case 'color-spectrum-depth':
                    colorSpectrumDepth = $this.val();
                    break;
            }
        });
        setColorSpectrumLabel(colorSpectrum,colorSpectrumSubcolor,colorSpectrumDepth,$table);
        // if(colorSpectrumSubcolor){
        //     $table.find("tr.color-spectrum-subdata-"+colorSpectrum+" input.color-spectrum-subdata[name=color-spectrum-depth]").each(function(){
        //         var $this = $(this);
        //         var depth = $this.val();
        //         var colorSpectrumSubColorData = getcolorSpectrumSubColorData(colorSpectrum,colorSpectrumSubcolor,depth);
        //         if(colorSpectrumSubColorData===null){
        //             $this.closest("li").hide();
        //             return true;
        //         }
        //         $this.closest("li").hide()
        //             .find("span.title").html(colorSpectrumSubColorData.title).end()
        //             .find("span.example").css("background-color",colorSpectrumSubColorData.example).end()
        //             .show();
        //     })
        // }
        // if(colorSpectrumDepth){
        //     $table.find("tr.color-spectrum-subdata-"+colorSpectrum+" input.color-spectrum-subdata[name=color-spectrum-subcolor]").each(function(){
        //         var $this = $(this);
        //         var subcolor = $this.val();
        //         var colorSpectrumSubColorData = getcolorSpectrumSubColorData(colorSpectrum,subcolor,colorSpectrumDepth);
        //         if(colorSpectrumSubColorData===null){
        //             $this.closest("li").hide();
        //             return true;
        //         }
        //         $this.closest("li").hide()
        //             .find("span.title").html(colorSpectrumSubColorData.title).end()
        //             .find("span.example").css("background-color",colorSpectrumSubColorData.example).end()
        //             .show();
        //     })
        // }
        // if(colorSpectrumSubcolor && colorSpectrumDepth){//next step
        //     var colorSpectrumSubColorData = getcolorSpectrumSubColorData(colorSpectrum,colorSpectrumSubcolor,colorSpectrumDepth);
        //     if(colorSpectrumSubColorData===null){
        //         $table.find("tr.color-spectrum-subdata-example").hide();
        //     } else {
        //         $table.find("tr.color-spectrum-subdata-example")
        //             .find("span.example").css("background-color",colorSpectrumSubColorData.example).end()
        //             .show();    
        //     }
            
        //     // nextStep(2,$table);
        // }
    });
    $document.on("click",".edit-review-form tr.color-spectrum-subdata-example span.example",function(){
        var $this = $(this);
        var height = $this.outerHeight();
        if(height<100){
            scrollIntoView($this);
            $this.animate({"height":"600px"},500);
        } else {
            $this.animate({"height":"50px"},500);
        }
    });

    // $document.on("change","tr.sparkling-rating input.sparkling-rating",function(){
    //     var $table = $(this).closest("tbody");
    //     var bubblesize_found = false;
    //     var quantity_found = false;
    //     var continuance_found = false;
    //     $table.find("tr.sparkling-rating input.sparkling-rating:checked").each(function(){
    //         var $this = $(this);
    //         switch($this.prop("name")){
    //             case "sparkling-rating-bubblesize":
    //                 bubblesize_found = true;
    //                 break;
    //             case "sparkling-rating-quantity":
    //                 quantity_found = true;
    //                 break;
    //             case "sparkling-rating-continuance":
    //                 continuance_found = true;
    //                 break;
    //         }
    //     });
    //     if(bubblesize_found && quantity_found && continuance_found){
    //         nextStep(3,$table);
    //     }
    // });

    // $document.on("change","tr.step input.faultcheck",function(){
    //     var $table = $(this).closest("tbody");
    //     if($table.find("input.faultcheck:checked").val()!=1){
    //         $table.find("tr.faultcheck-options input").prop("checked",false).filter(function(){
    //             if($(this).val()==0){
    //                 return true;
    //             }
    //             return false;
    //         }).prop("checked",true);
    //     }
    // });
    $document.on("change",".edit-review-form tr.has-subelements input",function(){
        var $tr = $(this).closest("tr");
        var $inputs = $tr.find("input[type=checkbox],input[type=radio]");
        if(!$inputs){//never
            return;
        }
        var processedNames = [];
        $inputs.each(function(){
            var $this = $(this);
            var name = $this.prop("name");
            if(processedNames.indexOf(name)>=0){
                return true;//continue
            }
            var val = $tr.find("input[name=\"" + name + "\"]:checked").first().val();
            if(val>0){
                $tr.closest("tbody").find("tr."+name+"-subelement:not(.dynamic-optional)").show();
            } else {
                $tr.closest("tbody").find("tr."+name+"-subelement").hide().find("input").prop("checked",false).filter(".default").prop("checked",true);
            }
        });
    });
    $document.on("change",".edit-review-form tr.balance-score input",function(){
        var $table = $(this).closest("tbody");
        var values = [];
        var names = [];
        var calculateAverageScore = true;
        $table.find("tr.balance-score input[type=checkbox],tr.balance-score input[type=radio]").each(function(){
            var $this = $(this);
            var name = $this.attr("name");
            if(names.indexOf(name)!==-1){
                return true;//continue
            }
            names.push(name);
            var $checkedInput = $table.find("tr.balance-score input[name=\"" + name + "\"]:checked").first();
            if(!$checkedInput.length){
                calculateAverageScore = false;
                return false;//break
            }
            values.push($checkedInput.val());
        });
        if(!calculateAverageScore || values.length==0){
            return true;
        }
        var totalScore = 1;
        for(var i=0;i<values.length;i++){
            totalScore *= parseInt(values[i])*100;
        }
        totalScore = Math.floor(Math.round(Math.pow(totalScore, 1/values.length))/100);
        $table.find("tr.average-balance-score td.average-balance-score").html($table.find("tr.balance-score input[value="+totalScore+"],tr.balance-score input[value="+totalScore+"]").first().siblings("label").html().replace(/<br(?: \/)?>/gm, " "));
    });
    // $document.on("change","tr.overall-aroma input",function(){
    //     var values = {};
    //     var unrequired = [];
    //     var $table = $(this).closest("tbody");
    //     $table.find("tr.overall-aroma input").closest("tr").each(function(){
    //         var $tr = $(this);
    //         var name = $tr.find("input").first().prop("name");
    //         var val = $tr.find("input:checked").first().val();
    //         values[name] = val;
    //         var hasSubelements = $tr.hasClass("has-subelements");
    //         if(hasSubelements && val <= 0){
    //             $table.find("tr.overall-aroma." + name + "-subelement input").each(function(){
    //                 var name = $(this).prop("name");
    //                 if(unrequired.indexOf(name)==-1){
    //                     unrequired.push(name);
    //                 }
    //             });
    //         }
    //     });
    //     for(var i=0;i<unrequired.length;i++){
    //         delete values[unrequired[i]];
    //     }
    //     var hasUndefined = false;
    //     $.each(values,function(key,value){
    //         if(value===undefined){
    //             hasUndefined = true;
    //             return false;//break
    //         }
    //     });
    //     if(!hasUndefined){
    //         nextStep(4,$table);
    //     }
    // });
    $document.on("click",".edit-review-form input.comlementary-submit",function(){
        var $this = $(this);
        var $form = $this.closest("form");
        confirmBox($form.find(".confirm_string_stop_review").html(), function(){
            if($this.hasClass("didnottry")){
                $form.trigger("submit",["didnottry"]);
                return;
            }
            if($this.hasClass("wineisfaulty")){
                $form.trigger("submit",["wineisfaulty"]);
                return;
            }
        });
        
    });
    $document.on("submit",".edit-review-form",function(e,comlementary_submit_type){
        var $form = $(this);
        saveDraft($form);
        $form.find("input.comlementary-submit-type").val(0);
        var isCancelingReview = false;
        if(comlementary_submit_type!==undefined){
            switch(comlementary_submit_type){
                case "didnottry":
                    $form.find("input.comlementary-submit-type.didnottry").val(1);
                    isCancelingReview = true;
                    break;
                case "wineisfaulty":
                    $form.find("input.comlementary-submit-type.wineisfaulty").val(1);
                    isCancelingReview = true;
                    break;
            }
        }
        if(isCancelingReview){
            return;
        }
        var preventSubmitting = false;
        $form.find("tr.score table.score-calc tr:not(.optional):not(.dynamic-optional)").removeClass("highlight").each(function(){
            var $tr = $(this);
            if($tr.find("input:checked").length==0){
                $tr.addClass("highlight");
                preventSubmitting = true;
            }
        });
        if(preventSubmitting){
            scrollIntoView($form.find("tr.score table.score-calc tr.highlight").first());
            e.preventDefault();
            return false;
        }
        
        var $scoreInput = $form.find("tr.score #edit-review-form-score");
        if($scoreInput.length){
            var val = parseFloat($scoreInput.val().trim());
            var minVal = 75;
            if($form.data("personal-review") || !$form.data("tasting-assessment")){
                minVal = 0;
            }
            if(val !== val //isNaN
                || val < minVal || val > 100){
                scrollIntoView($form.find("tr.score").addClass("highlight"));
                e.preventDefault();
            }
        }
        $(".edit-review-form tr.flow-temperature .edit-review-form-flow-temperature").each(function(){
            var val = this.value;
            if(val.length && (val < 5 || val > 25) ){
                scrollIntoView($form.find("tr.flow-temperature").addClass("highlight"));
                e.preventDefault();
                return false;//break
            }
        });
    });
    $document.on("keyup",".edit-review-form tr.score.highlight #edit-review-form-score", function(){
        var $this = $(this);
        var val = parseFloat($this.val().trim());
        if(val === val //!isNan
            && val >= 75 && val <= 100){
            $this.closest(".edit-review-form").find("tr.score.highlight").removeClass("highlight");    
        }
    });
    $document.on("keyup",".edit-review-form tr.flow-temperature.highlight .edit-review-form-flow-temperature", function(){
        var flowTemperatureInvalid = false;
        var $trs = $(this).closest(".edit-review-form").find("tr.flow-temperature");
        $trs.find(".edit-review-form-flow-temperature").each(function(){
            var val = this.value;
            if(val.length && (val < 5 || val > 25) ){
                flowTemperatureInvalid = true;
                return false;//break
            }
        });
        if(!flowTemperatureInvalid){
            $trs.removeClass("highlight");
        }
    });
    $document.on("change",".edit-review-form .form-nv-year input[type=checkbox]",function(){
        var $input = $(this).closest(".form-nv-year").find("input[type=text]");
        if(this.checked){
            $input.val("").prop("disabled", true);
        } else {
            $input.prop("disabled", false).focus();
        }
    });
    $document.on("click",".edit-review-form table.edit-review span.tip.folded",function(){
        $(this).removeClass("folded");
    });
    (function(){
        var $window = $(window);
        var $headers = $(".edit-review-form table.edit-review td.header");
        var $scrollHeader = $(".edit-review-form div.scroll-header");
        var windowTopMin = 0;
        var windowTopMax = 0;
        function onWindowScroll(){
            var windowTop = $window.scrollTop();
            if(windowTop>=windowTopMin && windowTop<=windowTopMax){
                return;
            }
            var found = false;
            $headers.filter(":visible").each(function(){
                var $this = $(this);
                var headerTop = $this.offset().top;
                if(headerTop >= windowTop){
                    return true;//continue
                }
                var $next = $this.closest("tr").nextAll(":has(>td.header,>td.submit):visible").first();
                if(!$next.length){
                    return true;//never
                }
                var nextTop = $next.offset().top;
                if(windowTop >= nextTop){
                    return true;//continue
                }
                var scrollHeaderOuterHeight = $scrollHeader.outerHeight();
                if(windowTop >= nextTop - scrollHeaderOuterHeight){
                    windowTopMin = nextTop - scrollHeaderOuterHeight;
                    windowTopMax = nextTop;
                    //will hide scrollheader w/ found = false
                    return false;//break
                }
                windowTopMin = headerTop;
                windowTopMax = nextTop - scrollHeaderOuterHeight;
                $scrollHeader.html($this.html()).show();
                found = true;
                return false;//break
            });
            if(!found){
                $scrollHeader.hide();
                windowTopMin = windowTopMax = 0;
            }

        }
        $window.on("scroll",onWindowScroll);
        onWindowScroll();
    })();
    
    (function(){
        if(!supports_local_storage()){
            return;
        }
        if(typeof(fillTemplate)!=='undefined' && fillTemplate instanceof Function){
            $(".edit-review-form").each(function(){
                var $form = $(this);
                var itemTemplate = $form.find(".tpv-draft-template-item").html();
                var formTemplate = $form.find(".tpv-draft-template-form").html();
                if(!itemTemplate || !formTemplate){
                    return true;
                }
                var tpvId = $form.data("tpv-id");
                var postData = localStorage.getItem("tpvDraft" + tpvId);
                if(postData===null){
                    return true;//continue
                }
                var items = fillTemplate(itemTemplate,{name:'action',value:'load-review-draft'});
                $.each(postData.split("&"),function(key,pair){
                    if((matches = pair.match(/^([^=]+?)=(.*)$/))===null){
                        return true;
                    }
                    var name = decodeURIComponent(matches[1]);
                    if(name=='action'){//skipping action
                        return true;
                    }
                    var value = decodeURIComponent(matches[2]);
                    items += fillTemplate(itemTemplate,{name:name,value:value});
                })
                $form.before(fillTemplate(formTemplate,{items:items},false));
            });
        }
    })();

    //score calc
    $(document).on("click",".edit-review-form tr.score span.show-score-calc",function(){
        var $this = $(this);
        $this.hide().closest("tr.score").find("table.score-calc").show();
    });
    $(document).on("change",".edit-review-form tr.score table.score-calc input",function(){
        var $table = $(this).closest("table.score-calc");
        // if(this.checked){
        //     $(this).closest("td").siblings("td").find("input:checked").prop("checked",false);
        // }
        var isOptional = $table.hasClass("optional");
        var totalScore = 0;
        $table.find("tr:not(.dynamic-optional)").each(function(){
            var scores = [];
            var score = undefined;
            $(this).find("input").each(function(){
                if(this.checked){
                    score = parseInt(this.value);
                    return false;//break
                }
                scores.push(parseInt(this.value));
            });
            if(score===undefined){
                if(isOptional && scores.length){
                    scores.sort(function(a,b){return a-b});
                    score = scores[Math.floor(scores.length/2)];  
                } else {
                    score = 0;
                }
            }
            totalScore+=score;
        });
        var $form = $table.closest(".edit-review-form");
        var minVal = 75;
        if($form.data("personal-review") || !$form.data("tasting-assessment")){
            minVal = 0;
        }
        $form.find("tr.score #edit-review-form-score").val(Math.max(minVal,Math.min(100,totalScore)));
    });
    
    (function(){
        var $tbody = $(".edit-review-form table.edit-review tbody").first();
        $tbody.find("input.segment-base").first().trigger("change");
        $tbody.find("tr.color-spectrum input:checked").trigger("change");
        $tbody.find(".balance-score input:checked").first().trigger("change");
        $tbody.find(".form-nv-year input[type=checkbox]:checked").closest(".form-nv-year").find("input[type=text]").val("").prop("disabled", true);
        for(var step=0;step<=maxStep;step++){
            if(!checkStepCompletion(step,$tbody,false)){
                break;
            }
            nextStep(step+1,$tbody,true,false);
        }
    })();
    //:not(.spectrum-white):not(.spectrum-pink):not(.spectrum-red)
    $(".edit-review-form table.edit-review tbody tr.step.step0:not(.type_still):not(.type_sparkling):not(.type_fortified):not(.subelement)").show();
    $(".edit-review-form .edit-review-form-year").mask('#999');
    $(".edit-review-form .edit-review-form-alcohol-content").mask('99,99');
    $(".edit-review-form .edit-review-form-flow-temperature").mask('#9');
    $(".edit-review-form .edit-review-form-minutes").mask('##9');
    var ScoreMaskBehavior = function(val){
      return val.length>=3&&parseInt(val)!=100? '99' : '199';
      // return val.length>=3&&parseFloat(val.replace(',','.'))!=100? '99,99' : '199,99';
    };
    $(".edit-review-form #edit-review-form-score").mask(ScoreMaskBehavior, {
      translation: {
        '1': {pattern: /1/,optional:true},
      },
      onKeyPress: function(val, e, field, options) {
        field.mask(ScoreMaskBehavior.apply({}, arguments), options);
      }
    });
});