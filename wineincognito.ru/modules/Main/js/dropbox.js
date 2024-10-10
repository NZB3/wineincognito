$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
       typeof(fillTemplate)!=='undefined' && fillTemplate instanceof Function &&
       typeof(wrapForm)!=='undefined' && wrapForm instanceof Function){

        // var selectTemplate = $("#dropbox-template-select").html();
        // if(!selectTemplate){
        //     return;
        // }
        // var optionTemplate = $("#dropbox-template-option").html();
        // if(!optionTemplate){
        //     return;
        // }
        // var optionHeaderTemplate = $("#dropbox-template-option-header").html();
        // if(!optionHeaderTemplate){
        //     optionHeaderTemplate = "";
        // }
        // var optionAddTemplate = $("#dropbox-template-option-add").html();
        // if(!optionAddTemplate){
        //     optionAddTemplate = "";
        // }

        var maxParentAttrIds = {};

        $(document).on("keyup","div.dropbox > ul li.search input",function(){
            var $this = $(this);
            var searchTexts = asciialias($this.val().trim()).split(' ');
            var $ul = $this.closest("ul").closest("div.dropbox").removeClass("dropbox-stressful-search").end();
            if(searchTexts.length==0){
                $ul.find("li.search-hidden").removeClass("search-hidden");
                return true;
            }
            $this.closest("div.dropbox").removeClass("dropbox-stressful-search").children("ul").find("li.item > label > input").each(function(){
                var $this = $(this);
                var seText = $this.data("se-text");
                if(seText===undefined){
                    seText = ' '+asciialias($this.closest("label").text());
                }
                if(!seText.length){
                    $this.closest("li").addClass("search-hidden");
                    return true;
                }
                var foundAll = true;
                $.each(searchTexts,function(key,searchText){
                    if(seText.indexOf(' ' + searchText)==-1){
                        foundAll = false;
                        return false;//break
                    }
                });
                if(foundAll){
                    $this.closest("li").removeClass("search-hidden");
                } else {
                    $this.closest("li").addClass("search-hidden");
                }
                //if(asciialias($this.text()).indexOf(searchText)>=0){
                //    $this.closest("li").removeClass("search-hidden");
                //} else {
                //    $this.closest("li").addClass("search-hidden");
                //}
            });
            if(!$ul.find("li.item:visible").length){
                $ul.closest("div.dropbox").addClass("dropbox-stressful-search");
            }
        });
        function uncheckAll($dropbox){
            var length = $dropbox.children("ul").find("li.item input:checked").prop("checked",false).closest("li.item").removeClass("selected").length;
            if(length){
                $dropbox.data("valuesChanged",true);
                dropBoxSetLabel($dropbox);
            }
            $dropbox.children("input").prop("checked",false).trigger("change");
        }
        $(document).on("click","div.dropbox > ul li.cancel label",function(){
            var $dropbox = $(this).closest("div.dropbox");
            uncheckAll($dropbox);
        });
        $(document).on("uncheck-all","div.dropbox",function(){
            var $dropbox = $(this);
            uncheckAll($dropbox);
        });
        $(document).on("click","div.dropbox.multiple > ul li.item label",function(e){
            if(e.target===this){
                $(this).closest("div.dropbox").data("forceCheckAndCloseOnNextChange",true);
            }
        });
        $(document).on("change","div.dropbox > ul li.item input",function(){
            var $this = $(this);
            var $dropbox = $this.closest("div.dropbox");
            var forceCheckAndCloseOnNextChange = $dropbox.data("forceCheckAndCloseOnNextChange");
            var forceCheck = false;
            if(!$dropbox.hasClass("multiple")){
                $this.closest("li").siblings().find("input:checked").prop("checked",false).closest("li.item").removeClass("selected");
                forceCheck = true;
            }
            if(forceCheckAndCloseOnNextChange){
                forceCheck = true;
            }
            if(forceCheck && !this.checked){
                this.checked = true;
            } else {
                $dropbox.data("valuesChanged",true);
                dropBoxSetLabel($dropbox);
            }
            
            if(this.checked){
                $this.closest("li.item").addClass("selected");
            } else {
                $this.closest("li.item").removeClass("selected");
            }
            if(!$dropbox.hasClass("multiple") || forceCheckAndCloseOnNextChange){
                $dropbox.children("input").prop("checked",false).trigger("change");//minimize
            }
            if(forceCheckAndCloseOnNextChange){
                $dropbox.removeData("forceCheckAndCloseOnNextChange");
            }
            return true;
        });
        function dropBoxSetLabel($dropbox){
            var count = 0;
            var label = [];
            $dropbox.children("ul").find("input:checked").each(function(){
                count++;
                if(label.length<3){
                    label.push($(this).closest("label").text());    
                }
            });
            if(count){
                $dropbox.addClass("has-selected").children("label").text(label.join(", ") + ((count>label.length)?"...":""));
            } else {
                $dropbox.removeClass("has-selected").children("label").text($dropbox.closest("form").find(".dropbox-template-empty-string").html());
            }
        }
        $(document).on("change", "div.dropbox > input", function(){
            var $dropbox = $(this).closest(".dropbox");
            if(this.checked){
                $dropbox.removeData("valuesChanged");
                $selected = $dropbox.children("ul").find("li.item.selected").first();
                $dropbox.find("li.search:visible input").focus();
                if($selected.length){
                    // setTimeout(function(){
                        $dropbox.children("ul").scrollTop($selected.position().top + $dropbox.children("ul").scrollTop());
                    // });
                }
                
            } else if($dropbox.data("valuesChanged")){
                dropboxChanged($dropbox);
                $dropbox.find("li.search input").val("").trigger("keyup");
            }
        });
        // add attribute value
        $(document).on("click", "div.dropbox > ul > li.add", function(){
            var $this = $(this);
            var id = $this.data("id");

            var optionTemplate = $this.closest("form").find(".dropbox-template-option").html();
            if(!optionTemplate){
                return;
            }

            ajaxRequest("/ajax/attributes/" + id + "/form", null, function(data){
                var $wrapper = wrapForm(data);
                $wrapper.find("form").on("submit",function(e){
                    e.preventDefault();
                    ajaxRequest("/ajax/attributes/" + id + "/add",$(this).serialize(), function(optionData){
                        var id = optionData["id"];
                        var $elem = undefined;
                        $this.siblings("li.item").find("input").each(function(){
                            if(id==this.value){
                                $elem = $(this);
                                return false;
                            }
                        });
                        if($elem===undefined){
                            optionData["name"] = escapeStr(optionData["name"]);
                            optionData["attrId"] = optionData["attr_id"];
                            var fieldname;
                            if((fieldname = $dropbox.data("fieldname"))===undefined){
                                fieldname = "attr";
                            }
                            optionData["fieldname"] = fieldname;
                            $elem = $(fillTemplate(optionTemplate,optionData)).insertAfter($this).find("input");
                            if($this.closest("div.dropbox").find("li.item").length>20){
                                $this.closest("div.dropbox").find("li.search").removeClass("hidden");
                            }
                            $this.closest("div.dropbox").find("li.search input").trigger("change");
                        }
                        $elem.prop("checked",true).trigger("change");
                        $wrapper.fadeOut("fast",function(){
                            $this.remove();
                        });
                    });
                })
                window.initLanguageTabs($wrapper);
            });
        });
        // end of add attribute value
        function asciialias(name){
            name = decodeHtml(name);
            var charlist = {
                'Š':'S', 'š':'s', 'Ð':'Dj','Ž':'Z', 'ž':'z', 'À':'A', 'Á':'A', 'Â':'A', 'Ã':'A', 'Ä':'A',
                'Å':'A', 'Æ':'A', 'Ç':'C', 'È':'E', 'É':'E', 'Ê':'E', 'Ë':'E', 'Ì':'I', 'Í':'I', 'Î':'I',
                'Ï':'I', 'Ñ':'N', 'Ń':'N', 'Ò':'O', 'Ó':'O', 'Ô':'O', 'Õ':'O', 'Ö':'O', 'Ø':'O', 'Ù':'U', 'Ú':'U',
                'Û':'U', 'Ü':'U', 'Ý':'Y', 'Þ':'B', 'ß':'Ss','à':'a', 'á':'a', 'â':'a', 'ã':'a', 'ä':'a',
                'å':'a', 'æ':'a', 'ç':'c', 'è':'e', 'é':'e', 'ê':'e', 'ë':'e', 'ì':'i', 'í':'i', 'î':'i',
                'ï':'i', 'ð':'o', 'ñ':'n', 'ń':'n', 'ò':'o', 'ó':'o', 'ô':'o', 'õ':'o', 'ö':'o', 'ø':'o', 'ù':'u',
                'ú':'u', 'û':'u', 'ü':'u', 'ý':'y', 'ý':'y', 'þ':'b', 'ÿ':'y', 'ƒ':'f',
                'ă':'a', 'î':'i', 'â':'a', 'ș':'s', 'ț':'t', 'Ă':'A', 'Î':'I', 'Â':'A', 'Ș':'S', 'Ț':'T',
                'Ё':'Е', 'ё':'е',//russian
            };
            $.each(charlist,function(key,val){
                var regexp = new RegExp(key, "g");
                name = name.replace(regexp, val);
            });
            return name.toLowerCase().replace(/[^a-z0-9а-я\s]+/g," ").replace(/(?:^[a-z0-9а-я]{1,2}\s|\s[a-z0-9а-я]{1,2}\s|\s[a-z0-9а-я]{1,2}$)/g," ").replace(/\s+/g," ").trim();
        }
        function dropboxChanged($dropbox){
            var values = [];
            var group = $dropbox.data("group");
            var custom = $dropbox.data("custom")?true:false;
            var fieldname = $dropbox.data("fieldname")?$dropbox.data("fieldname"):"attr";
            var loadsiblings = $dropbox.data("loadsiblings")?true:false;
            var $form = $dropbox.closest("form");
            var maxParentAttrId = $form.find(".dropbox-parent-limit-" + group).data("parent-attr-id");
            if(maxParentAttrId===undefined){
                maxParentAttrId = 0;
            }
            
            if(custom){
                $dropbox.children("ul").find("input:checked").each(function(){
                    values.push(this.value);
                });
                if(!values.length){
                    return true;
                }
                $dropbox.trigger("dropbox-change",[group,values.slice()]);
                return true;
            }
            $form.find(".dropbox").filter(function(){
                var $this = $(this);
                return $this.data("fieldname") == fieldname;
            }).children("ul").find("input:checked").each(function(){
                values.push(this.value);
            });

            var depth = $dropbox.data("depth");
            // $dropbox.closest("tr").siblings().find("div.dropbox").filter(function(){
            //     var $this = $(this);
            //     return $this.data("fieldname") == fieldname && $this.data("group") == group && $this.data("depth") > depth;
            // }).closest("tr").remove();

            var maxParentAttrIdReached = false;
            $dropbox.children("ul").find("input:checked").each(function(){
                if(!maxParentAttrIdReached && $(this).data("attr-id")==maxParentAttrId){
                    maxParentAttrIdReached = true;
                    return false;//break
                }
            });
            // if(!values.length){
            //     return true;
            // }
            if(maxParentAttrIdReached){
                $dropbox.trigger("dropbox-change",[group,values.slice()]);
                return true;
            }
            if( $dropbox.data("has-children")==0 && !loadsiblings && (group!=8 || depth>1) ){
                $dropbox.trigger("dropbox-change",[group,values.slice()]);
                return true;
            }
            var selectTemplate = $form.find(".dropbox-template-select").html();
            if(!selectTemplate){
                return;
            }
            var optionTemplate = $form.find(".dropbox-template-option").html();
            if(!optionTemplate){
                return;
            }
            var optionHeaderTemplate = $form.find(".dropbox-template-option-header").html();
            if(!optionHeaderTemplate){
                optionHeaderTemplate = "";
            }
            var optionAddTemplate = $form.find(".dropbox-template-option-add").html();
            if(!optionAddTemplate){
                optionAddTemplate = "";
            }
            var optionImportantToggleHtml = $form.find(".dropbox-template-option-important-toggle").html();
            if(!optionImportantToggleHtml){
                optionImportantToggleHtml = "";
            }
            var optionRegionlockToggleHtml = $form.find(".dropbox-template-option-regionlock-toggle").html();
            if(!optionRegionlockToggleHtml){
                optionRegionlockToggleHtml = "";
            }
            
            var onlyvisible = $form.data("dropbox-show-hidden")?0:1;
            var get_doublecheck = ($dropbox.data("doublecheck")!==undefined && $dropbox.data("doublecheck"))?1:0;
            var foundation_exclusive = ($dropbox.data("foundation_exclusive")!==undefined && $dropbox.data("foundation_exclusive"))?1:0;
            var groupPost = [];
            if(!loadsiblings){
                groupPost.push(group);
                if(group==8 && depth<=1){//country and region
                    $form.find(".dropbox.dropbox-region-lock").each(function(){
                        groupPost.push($(this).data("group"));
                    });
                }
            }

            ajaxRequest("/ajax/attributes/childvalues", {
                values:values,
                group:groupPost,
                maxAttrId:maxParentAttrId,
                onlyvisible:onlyvisible,
                system:$dropbox.data("system")?1:0,
                only_used:$form.data("dropbox-filter-only-used")?1:0,
                onlyblank:$form.data("dropbox-filter-only-blank")?1:0,
                only_waiting_for_approval:$form.data("dropbox-filter-only-waiting-for-approval")?1:0,
                onlyscored:$form.data("dropbox-filter-only-scored")?1:0,
                onlyawarded:$form.data("dropbox-filter-only-awarded")?1:0,
                only_personally_scored:$form.data("dropbox-filter-only-personally-scored")?1:0,
                onlymyfavourites:$form.data("dropbox-filter-only-my-favourites")?1:0,
                onlycompanyfavourites:$form.data("dropbox-filter-only-company-favourites")?1:0,
                showproximity:$form.data("dropbox-filter-show-proximity")?1:0,
                get_doublecheck:get_doublecheck,
                foundation_exclusive:foundation_exclusive?1:0,
            }, function(data){
                var groupList = [];
                $.each(data,function(group,groupData){
                    var triggerValues = [];
                    var depthList = [];
                    $.each(groupData,function(depth,depthData){
                        var index;
                        var options = "";
                        var optionCount = 0;
                        var selectData = {group:group,depth:depth,haschildren:0,fieldname:fieldname,foundation_exclusive:foundation_exclusive};
                        var sizeOfDepthData = sizeOfObject(depthData);
                        options += optionImportantToggleHtml;
                        $.each(depthData,function(key,attrData){
                            if(attrData["regionlock"]==1){
                                options += optionRegionlockToggleHtml;
                                return false;//break
                            }
                        });
                        $.each(depthData,function(key,attrData){
                            if(sizeOfDepthData==1){
                                selectData["name"] = escapeStr(attrData["name"]);
                            } else {
                                options += fillTemplate(optionHeaderTemplate,attrData);
                            }
                            if(attrData["haschildren"]==1){
                                selectData["haschildren"] = 1;
                            }
                            var regionlock = false;
                            if(attrData["regionlock"]==1){
                                selectData["regionlock"] = 1;
                                regionlock = true;
                            }
                            if(index === undefined){
                                selectData["index"] = index = attrData["index"]?attrData["index"]:0;    
                            }

                            if(attrData["can_add"]==1){
                                options += fillTemplate(optionAddTemplate,attrData);
                            }
                            if(get_doublecheck){
                                selectData["doublecheck"] = attrData["doublecheck"]?1:0;
                            }
                            if(attrData["can_null"]==1){
                                selectData["can_null"] = 1;
                            }
                            var regionlockInRegionOptionCount = 0;
                            var attrId = attrData["id"];
                            $.each(attrData.vals,function(key,optionData){
                                optionData["selected"] = optionData["selected"]?1:0;
                                if(optionData["selected"]){
                                    triggerValues.push(optionData["id"]);
                                }
                                optionData['fieldname'] = fieldname;
                                optionData["name"] = escapeStr(optionData["name"]);
                                optionData["attrId"] = attrId;
                                options += fillTemplate(optionTemplate,optionData);
                                optionCount++;
                                if(regionlock && optionData["regionlock_in_region"]){
                                    regionlockInRegionOptionCount++;
                                }
                            });
                            if(regionlock && (optionCount == regionlockInRegionOptionCount || regionlockInRegionOptionCount == 0)){
                                regionlock = false;
                            }
                            
                        });

                        if(optionCount==0){//never
                            return true;//continue;
                        }
                        depthList.push(depth);
                        //search for existing containers
                        var $fieldnameDropBoxList = $form.find(".dropbox").filter(function(){
                            if($(this).data("fieldname")==fieldname){
                                return true;
                            }
                            return false;
                        });
                        if(!$fieldnameDropBoxList.length){
                            return false;//never (no dropboxes exist)
                        }
                        var $groupExistingDropBoxes = $fieldnameDropBoxList.filter(function(){
                            if($(this).data("group")==group){
                                return true;
                            }
                            return false;
                        });
                        if($groupExistingDropBoxes.length){
                            var $existingDropBox = $groupExistingDropBoxes.filter(function(){
                                if($(this).data("depth")==depth){
                                    return true;
                                }
                                return false;
                            });
                            if($existingDropBox.length){
                                $existingDropBox
                                    .children("ul")
                                        .find("li.item, li.header, li.add, li.dropbox-item-list-toggle").remove().end()
                                    .append(options).end()
                                    .addClass("fresh");
                                if(optionCount==1 && !selectData["can_null"]){
                                    $existingDropBox.addClass("disabled");
                                } else {
                                    $existingDropBox.removeClass("disabled");
                                }
                                $existingDropBox.data("has-children",selectData["haschildren"]);
                                if(get_doublecheck){
                                    $existingDropBox.data("doublecheck",selectData["doublecheck"]);
                                }
                                $existingDropBox.trigger("dropbox-data-change");
                                return true;//continue;
                            }
                        }
                        //generate selectHtml
                        selectData["options"] = options;
                        $selectHtml = $(fillTemplate(selectTemplate, selectData, false)).find(".dropbox").addClass("fresh " + ((optionCount==1 && !selectData["can_null"])?"disabled":"")).end();

                        if($groupExistingDropBoxes.length){
                            var $ltDepthDropBox;
                            var $gtDepthDropBox;
                            $groupExistingDropBoxes.each(function(){
                                var $this = $(this);
                                var checkDepth = $this.data("depth");
                                if(checkDepth<depth && ($ltDepthDropBox===undefined || checkDepth>$ltDepthDropBox.data("depth"))){
                                    $ltDepthDropBox = $this;
                                    return true;//continue
                                }
                                if(checkDepth>depth && ($gtDepthDropBox===undefined || checkDepth<$gtDepthDropBox.data("depth"))){
                                    $gtDepthDropBox = $this;
                                }
                            });
                            if($ltDepthDropBox!==undefined){
                                $ltDepthDropBox.closest("tr").after($selectHtml);
                                $selectHtml.find(".dropbox").trigger("dropbox-data-change");
                                return true;//continue
                            }
                            if($gtDepthDropBox!==undefined){
                                $gtDepthDropBox.closest("tr").before($selectHtml);
                                $selectHtml.find(".dropbox").trigger("dropbox-data-change");
                                return true;//continue
                            }
                        }
                        //var index;
                        var $ltIndexDropBox;
                        var $gtIndexDropBox;
                        $fieldnameDropBoxList.each(function(){
                            var $this = $(this);
                            var checkIndex = $this.data("index");
                            if(checkIndex<=index && ($ltIndexDropBox===undefined || checkIndex>=$ltIndexDropBox.data("index"))){
                                $ltIndexDropBox = $this;
                                return true;//continue
                            }
                            if(checkIndex>index && ($gtIndexDropBox===undefined || checkIndex<$gtIndexDropBox.data("index"))){
                                $gtIndexDropBox = $this;
                            }
                        });
                        if($ltIndexDropBox!==undefined){
                            $ltIndexDropBox.closest("tr").after($selectHtml);
                            $selectHtml.find(".dropbox").trigger("dropbox-data-change");
                            return true;//continue
                        }
                        if($gtIndexDropBox!==undefined){
                            $gtIndexDropBox.closest("tr").before($selectHtml);
                            $selectHtml.find(".dropbox").trigger("dropbox-data-change");
                            return true;//continue
                        }
                        //no indexes, no dropboxes with same group or depths in them
                        $fieldnameDropBoxList.last().closest("tr").after($selectHtml);
                        $selectHtml.find(".dropbox").trigger("dropbox-data-change");
                    });
                    //delete depths
                    $form.find(".dropbox").each(function(){
                        var $this = $(this);
                        if($this.data("fieldname")==fieldname && $this.data("group")==group && depthList.indexOf(String($this.data("depth")))===-1){
                            $this.closest("tr").remove();
                        }
                    });
                    if(depthList.length){
                        groupList.push(group);
                    }

                    // if(selectHtml){
                    //     var maxDepth = -1;
                    //     var $maxDepthDropBox = null;
                    //     $groupDropBoxList.each(function(){
                    //         var $this = $(this);
                    //         var depth = $this.data("depth");
                    //         if(depth > maxDepth){
                    //             maxDepth = depth;
                    //             $maxDepthDropBox = $this;
                    //         }
                    //     });
                    //     if(!$maxDepthDropBox){
                    //         $maxDepthDropBox = $form.find(".dropbox").last();
                    //     }
                    //     $maxDepthDropBox.closest("tr").after(selectHtml);
                    //     window.dropBoxSetLabels();
                    // }
                    // if(triggerValues.length){
                    //     var isequal = false;
                    //     if(triggerValues.length==values.length){
                    //         isequal = true;
                    //         for(var i=0;i<triggerValues.length;i++){
                    //             if(values.indexOf(triggerValues[i])===false){//new attr
                    //                 isequal = false;
                    //                 break;
                    //             }
                    //         }
                    //     }
                    //     if(!isequal){
                            var triggerGroup = group;
                            $dropbox.trigger("dropbox-change",[triggerGroup,triggerValues.slice()]);
                    //     }
                    // }
                });
                //delete groups
                if(loadsiblings){
                    $form.find(".dropbox").each(function(){
                        var $this = $(this);
                        if($this.data("fieldname")==fieldname && groupList.indexOf(String($this.data("group")))===-1){
                            $this.closest("tr").remove();
                        }
                    });    
                }
                
                window.dropBoxSetLabels();
            }, null);
        }
        window.dropboxLoad = function(group,values,onlyvisible,system,only_used,get_doublecheck,loadsiblings,fieldname,$after,callback){
            var $form = $after.closest("form");
            var selectTemplate = $form.find(".dropbox-template-select").html();
            if(!selectTemplate){
                return;
            }
            var optionTemplate = $form.find(".dropbox-template-option").html();
            if(!optionTemplate){
                return;
            }
            var optionHeaderTemplate = $form.find(".dropbox-template-option-header").html();
            if(!optionHeaderTemplate){
                optionHeaderTemplate = "";
            }
            var optionAddTemplate = $form.find(".dropbox-template-option-add").html();
            if(!optionAddTemplate){
                optionAddTemplate = "";
            }
            var optionImportantToggleHtml = $form.find(".dropbox-template-option-important-toggle").html();
            if(!optionImportantToggleHtml){
                optionImportantToggleHtml = "";
            }
            var optionRegionlockToggleHtml = $form.find(".dropbox-template-option-regionlock-toggle").html();
            if(!optionRegionlockToggleHtml){
                optionRegionlockToggleHtml = "";
            }
            get_doublecheck = get_doublecheck?1:0;
            loadsiblings = loadsiblings?1:0;
            var groupPost = [];
            if(!loadsiblings){
                groupPost.push(group);
            }
            ajaxRequest("/ajax/attributes/childvalues", {
                values:values,
                group:groupPost,
                maxAttrId:0,
                onlyvisible:onlyvisible?1:0,
                system:system?1:0,
                only_used:$form.data("only_used")?1:0,
                get_doublecheck:get_doublecheck?1:0,
            }, function(data){
                $.each(data,function(group,groupData){
                    $.each(groupData,function(depth,depthData){
                        var index;
                        var options = "";
                        var optionCount = 0;
                        var selectData = {group:group,depth:depth,haschildren:0,fieldname:fieldname,};
                        var sizeOfDepthData = sizeOfObject(depthData);
                        options += optionImportantToggleHtml;
                        $.each(depthData,function(key,attrData){
                            if(attrData["regionlock"]==1){
                                options += optionRegionlockToggleHtml;
                                return false;//break
                            }
                        });
                        $.each(depthData,function(key,attrData){
                            if(sizeOfDepthData==1){
                                selectData["name"] = escapeStr(attrData["name"]);
                            } else {
                                options += fillTemplate(optionHeaderTemplate,attrData);
                            }
                            if(attrData["haschildren"]==1){
                                selectData["haschildren"] = 1;
                            }
                            if(index === undefined){
                                selectData["index"] = index = attrData["index"]?attrData["index"]:0;    
                            }

                            if(attrData["can_add"]==1){
                                options += fillTemplate(optionAddTemplate,attrData);
                            }
                            if(get_doublecheck){
                                selectData["doublecheck"] = attrData["doublecheck"]?1:0;
                            }
                            

                            var attrId = attrData["id"];
                            $.each(attrData.vals,function(key,optionData){
                                optionData["selected"] = optionData["selected"]?1:0;
                                optionData['fieldname'] = fieldname;
                                optionData["name"] = escapeStr(optionData["name"]);
                                optionData["attrId"] = attrId;
                                options += fillTemplate(optionTemplate,optionData);
                                optionCount++;
                            });
                        });
                        if(optionCount==0){//never
                            return true;//continue;
                        }

                        //generate selectHtml
                        selectData["options"] = options;
                        $selectHtml = $(fillTemplate(selectTemplate, selectData, false)).find(".dropbox").addClass("fresh " + ((optionCount==1)?"disabled":"")).end();
                        $after.after($selectHtml);
                        $selectHtml.find(".dropbox").trigger("dropbox-data-change");
                    });
                });
                window.dropBoxSetLabels();
                if (typeof callback === 'function'){
                    callback();
                }
            }, function(){
                if (typeof callback === 'function'){
                    callback();
                }
            });
        }
        $(document).on("click",function(e){
            $currentDropBox = $(e.target).closest("div.dropbox");
            if(!$currentDropBox.length){
                $("div.dropbox > input:checked").prop("checked",false).trigger("change");
            } else {
                $("div.dropbox > input:checked").filter(function(){
                    var $this = $(this);
                    $dropbox = $this.closest("div.dropbox");
                    if(!$dropbox.is($currentDropBox)){
                        return true;
                    }
                    return false;
                }).prop("checked",false).trigger("change");
            }
        });
        function dropBoxSetImportant($dropbox){
            if( $dropbox.find("li.item:not(.not-important)").length<10 || $dropbox.find("li.item.not-important").length == 0 ||
                    !$dropbox.find("li.show-not-important").length || !$dropbox.find("li.hide-not-important").length ){
                $dropbox.removeClass("hide-not-important has-important").find("li.dropbox-item-list-toggle.show-not-important,li.dropbox-item-list-toggle.hide-not-important").remove();
                return;
            }
            $dropbox.addClass("hide-not-important has-important");
        }
        function dropBoxSetRegionlock($dropbox){
            if( $dropbox.find("li.item:not(.regionlock-in-region)").length == 0 || $dropbox.find("li.item.regionlock-in-region").length == 0 ||
                    !$dropbox.find("li.regionlock-show-out-of-region").length || !$dropbox.find("li.regionlock-hide-out-of-region").length){
                $dropbox.removeClass("regionlock-hide-out-of-region regionlock-has-out-of-region").find("li.dropbox-item-list-toggle.regionlock-show-out-of-region,li.dropbox-item-list-toggle.regionlock-hide-out-of-region").remove();
                return;
            }
            $dropbox.addClass("regionlock-hide-out-of-region regionlock-has-out-of-region");
        }
        
        $(document).on("click", "div.dropbox > ul > li.dropbox-item-list-toggle.show-not-important", function(e){
            $(this).closest(".dropbox").removeClass("hide-not-important");
        });
        $(document).on("click", "div.dropbox > ul > li.dropbox-item-list-toggle.hide-not-important", function(e){
            $(this).closest(".dropbox").addClass("hide-not-important");
        });
        $(document).on("click", "div.dropbox > ul > li.dropbox-item-list-toggle.regionlock-show-out-of-region", function(e){
            $(this).closest(".dropbox").removeClass("regionlock-hide-out-of-region");
        });
        $(document).on("click", "div.dropbox > ul > li.dropbox-item-list-toggle.regionlock-hide-out-of-region", function(e){
            $(this).closest(".dropbox").addClass("regionlock-hide-out-of-region");
        });
        window.dropBoxSetLabels = function(){
            $("div.dropbox.fresh").each(function(){
                var $dropbox = $(this).removeClass("fresh");
                dropBoxSetLabel($dropbox);
                dropBoxSetImportant($dropbox);
                dropBoxSetRegionlock($dropbox);
                if($dropbox.find("li.item").length<=20){
                    $dropbox.find("li.search").addClass("hidden");
                } else {
                    $dropbox.find("li.search").removeClass("hidden");
                }
            });
        }
        window.dropBoxSetLabels();
        
        

        $(document).on("reset","form",function(){
            var $form = $(this);
            setTimeout(function(){
                var $lastDropBox;
                $form.find("div.dropbox").each(function(){
                    $lastDropBox = $(this);
                    $lastDropBox.find("li.item input:checked").prop("checked",false);
                    dropBoxSetLabel($lastDropBox);
                    dropBoxSetImportant($lastDropBox);
                    dropBoxSetRegionlock($lastDropBox);
                });
                if($lastDropBox!==undefined){
                    dropboxChanged($lastDropBox);
                }
            },50);
        });

        window.getAllDropBoxValues = function($form){
            var values = [];
            $form.find("div.dropbox > ul").find("input:checked").each(function(){
                values.push(this.value);
            });
            return values;
        }
        window.getGroupDropBoxValues = function($form,group){
            var values = [];
            $form.find("div.dropbox").each(function(){
                var $this = $(this);
                if($this.data("group")!=group){
                    return true;//continue
                }
                $this.children("ul").find("input:checked").each(function(){
                    values.push(this.value);
                });
            });
            return values;
        }

        $(document).on("dropbox-refresh", "form",function(e){
            var $dropbox = $(this).find(".dropbox").first();
            if($dropbox.length){
                dropboxChanged($dropbox);
            }
        });
    }
});
// $dropbox.trigger("uncheck-all");
// $(document).on("dropbox-change", "div.dropbox",function(e,group,values){
            
// });
// $(document).on("dropbox-refresh", "form",function(e,group,values){
            
// });
// $(document).on("dropbox-data-change", "div.dropbox",function(e,group,values){
            
// });