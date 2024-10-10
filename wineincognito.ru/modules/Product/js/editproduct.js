$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
       typeof(fillTemplate)!=='undefined' && fillTemplate instanceof Function &&
       typeof(confirmBox)!=='undefined' && confirmBox instanceof Function){
        var $form = $("#edit-product-form");
        var $originNameEdit = $form.find("#edit-product-form-origin-name");
        var fullNameTemplate = $form.data("full-name-template");
        var confirm_string_delete_image = $("#confirm_string_delete_image").html();
        var maskParts = {};
        // $(document).on("dropbox-change", "div.dropbox",function(e,group){//,values
        //     var findMaxDepth = false;
        //     if(fullNameTemplate.indexOf('{{g' + group + '}}')>=0){
        //         findMaxDepth = true;
        //         var maxDepth = -1;
        //         var $dropBox = null;
        //     }
        //     var foundAttrs = [];
        //     $form.find(".dropbox li input:checked").closest(".dropbox").each(function(){
        //         var $this = $(this);
        //         if(findMaxDepth && $this.data("group")==group){
        //             var depth = $this.data("depth");
        //             if(depth > maxDepth){
        //                 maxDepth = depth;
        //                 $dropBox = $this;
        //             }    
        //         }
        //         var $checkbox = $this.find("li input:checked").first();
        //         var attrId = $checkbox.data("attr-id");
        //         if(fullNameTemplate.indexOf('{{a' + attrId + '}}')>=0){
        //             setNewPart("a"+attrId,$checkbox.closest("label").text());
        //             foundAttrs.push("a"+attrId);
        //             return true;
        //         }
        //     });
        //     $.each(maskParts,function(key,val){
        //         if(key.match(/^a(\d+)$/)===null){
        //             return true;
        //         }
        //         if(foundAttrs.indexOf(key)==-1){
        //             setNewPart(key,"");
        //         }
        //     });
        //     if(!$dropBox){
        //         return true;//never
        //     }
        //     setNewPart("g"+group,$dropBox.find("li input:checked").first().closest("label").text());
        // });
        var timeoutId = null;
        $form.on("dropbox-change", "div.dropbox",function(){//,values
            var $this = $(this);
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function(){
                var values = getAllDropBoxValues($this.closest("form"));
                ajaxRequest("/ajax/product/fullnametemplates", {
                    values:values,
                }, function(data){
                    $.each(data,function(langId,template){
                        if(getFullNameTemplate(langId)===template){
                            return true;//continue
                        }
                        fullNameTemplates[langId] = template;
                        $this.closest("table").find("tr.lang_"+langId+" .edit-product-form-name").trigger("keyup");
                    });
                }, null);
            },1000);
        });
        $form.on("change","ul.blend input",function(){
            var $this = $(this);
            var val = $this.closest("ul").find("input:checked").val();
            if(val==1){//blend
                $this.closest("td").find("div.dropbox").addClass("multiple").end().find("table.blend").show();
            } else {//моно
                $this.closest("td").find("div.dropbox").removeClass("multiple").end().find("table.blend").hide();
                
            }
        });
        $form.find("ul.blend input:checked").trigger("change");

        var grapeVarietyConcentrationRowTemplate = $form.find(".grape-variety-concentration-row-template").html();
        $form.on("dropbox-change", "tr.grape-variety div.dropbox",function(){//,values
            var items = [];
            $(this).find("li.item input:checked").closest("li").each(function(){
                var $li = $(this);
                items.push({id:$li.find("input").val(),name:$li.find("label").text(),autogenerate:true,val:''});
            });
            var $table = $(this).closest("td").find("table.blend tbody");
            $table.find("tr").each(function(){
                var id = $(this).data("id");
                var found = false;
                $.each(items,function(index,item){
                    if(item.id==id){
                        found = true;
                        items.splice(index, 1);
                        return false;//break
                    }
                });
                if(!found){
                    $(this).remove();
                }
            });
            $.each(items,function(index,item){
                $table.append(fillTemplate(grapeVarietyConcentrationRowTemplate,item));
            });
            fillGrapeVarietyAutogenerate($table);
        });
        function fillGrapeVarietyAutogenerate($table){
            var autogenerateCount = 0;
            var autogenerateUsed = 0;
            $table.find("tr input").each(function(){
                var $input = $(this);
                if($input.hasClass("autogenerate")){
                    autogenerateCount++;
                    return true;//continue
                }
                var val = parseInt($input.val());
                if(isNaN(val)){
                    val = 0;
                }
                autogenerateUsed+=val;
            });
            if(autogenerateCount>0 && autogenerateUsed>0){
                $table.find("tr input.autogenerate").val((autogenerateUsed<=100)?Math.floor((100-autogenerateUsed)/autogenerateCount):0);
            }
            //sort
            var items = [];
            $table.find("tr").each(function(){
                var $tr = $(this);
                var $input = $tr.find("input");
                var val = parseInt($input.val());
                if(isNaN(val)){
                    val = '';
                }
                items.push({id:$tr.data("id"),name:$tr.find("td:first-child").text(),autogenerate:$input.hasClass("autogenerate"),val:val});
            });
            items.sort(function(a,b){
                if(a.val==b.val){
                    return a.name.localeCompare(b.name);
                }
                return b.val - a.val;
            });
            $table.empty();
            $.each(items,function(index,item){
                $table.append(fillTemplate(grapeVarietyConcentrationRowTemplate,item));
            });
            $table.find("input").mask('999');
        }
        fillGrapeVarietyAutogenerate($form.find("tr.grape-variety table.blend tbody"));
        $form.on("change", "tr.grape-variety table.blend input",function(){
            var $this = $(this);
            $this.removeClass("autogenerate");
            fillGrapeVarietyAutogenerate($this.closest("table.blend tbody"));
        });

        $form.on("dropbox-data-change", "div.dropbox",function(){
            var $this = $(this);
            if($this.data("doublecheck")){
                $this.closest("tr").removeClass("step2");
            } else {
                $this.closest("tr").addClass("step2");
            }
        });
        $form.on("keyup","#edit-product-form-origin-name", function(){
            $(this).closest("form").find(".edit-product-form-name").trigger("keyup");
        });
        $form.on("keyup",".edit-product-form-name", function(){
            var $this = $(this);
            var langId = $this.data("lang");
            var nameText = $this.val().trim();
            if(!nameText.length){
                nameText = $originNameEdit.val().trim();
            }
            $this.closest("table").find("tr.lang_"+langId+" td.edit-product-form-full_name").text(fillTemplate(getFullNameTemplate(langId),{name:nameText}));
        });
        var fullNameTemplates = {};
        function getFullNameTemplate(langId){
            if(fullNameTemplates[langId]===undefined){
                return "{{name}}";
            }
            return fullNameTemplates[langId];
        }
        function setFullNameTemplate(langId,template){
            if(fullNameTemplates[langId]===template){
                return;
            }
            fullNameTemplates[langId] = template;
        }
        $(".edit-product-form-name").each(function(){
            var $this = $(this);
            var langId = $this.data("lang");
            setFullNameTemplate(langId,$this.closest("table").find("tr.lang_"+langId+" td.edit-product-form-full_name").data("template"));
            $this.trigger("keyup");
        });
        // function setNewPart(key,part){
        //     if(fullNameTemplate.indexOf('{{' + key + '}}')==-1){
        //         return;
        //     }
        //     if(part===maskParts[key]){
        //         return;
        //     }
        //     maskParts[key] = part;
        //     $form.find("#edit-product-form-full_name").text(fillTemplate(fullNameTemplate,maskParts));
        // }
        $("#edit-product-form #edit-product-form-check-doubles").on("click", function(){
            var $this = $(this);
            var id = $this.data("id");
            var values = getAllDropBoxValues($(this).closest("form"));
            ajaxRequest("/ajax/product/check", {
                id:id?id:0,
                values:values,
                originname:$originNameEdit.val()
            }, function(){
                $form.removeClass("step1").addClass("step2");
            }, null);
        });
        $editProductFormAddImageFile = $("#edit-product-form-add-image-file");
        $("#edit-product-form-add-image").on("click",function(){
            $editProductFormAddImageFile.trigger("click");
        });
        var $editProductFormImageList = $("#edit-product-form-image-list");
        var editProductFormTemplateImageList = $("#edit-product-form-template-image-list").html();
        $editProductFormAddImageFile.on('change', function() {
            var formData = new FormData();
            var hasImagesToUpload = false;
            $.each($editProductFormAddImageFile[0].files, function(key, file) {
                if(!file.type.match(/(.png)|(.jpeg)|(.jpg)|(.gif)$/i) || file.size > 1.5*1048576){
                    return true;
                } 
                formData.append("image[]", file);
                hasImagesToUpload = true;
            });
            $editProductFormAddImageFile[0].value = "";
            if(!hasImagesToUpload){
                return;
            }
            $.ajax({
                url: '/ajax/product/image/upload',
                type: 'POST',
                contentType: false,
                processData: false,
                dataType: 'json',
                data: formData,
                success: function(data){
                    if(data["success"] === undefined){
                        UIOutputInfoBlock("Unknown Error",0);
                        return true;
                    }
                    $.each(data["data"],function(key,data){
                        if(data["err"] !== undefined && data["errmsg"] !== undefined && data["errmsg"] !== null){
                            UIOutputInfoBlock(data["errmsg"],0);    
                        }
                        if(data["success"] !== undefined){
                            $editProductFormImageList.append(fillTemplate(editProductFormTemplateImageList,data));
                        }
                    });
                }
            });
        });
        $editProductFormImageList.on("click",".delete",function(){
            var $li = $(this).closest("li");
            var id = $li.find("input").val();
            confirmBox(confirm_string_delete_image, function(){
                ajaxRequest("/ajax/product/image/delete", {
                    id:id,
                }, function(){
                    $li.remove();
                }, null);
            });
        });
        $editProductFormImageList.on("click",".make-primary",function(){
            var $li = $(this).closest("li");
            var id = $li.find("input").val();
            ajaxRequest("/ajax/product/image/make_primary", {
                id:id,
            }, function(){
                $li.addClass("primary").siblings("li").removeClass("primary");
            }, null);
        });
    }
});