$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
        $(document).on("click", ".edit-vintage-form .edit-vintage-form-check-doubles", function(){
            var $this = $(this);
            var pid = $this.data("pid");
            var id = $this.data("id");
            var $form = $this.closest(".edit-vintage-form");
            var year = $form.find(".edit-vintage-form-year").val();
            ajaxRequest("/ajax/product/" + pid + "/vintage/check", {
                year:year,
                id:id,
            }, function(data){
                if(data&&data["double_id"]){
                    $form.trigger("fetchId", data["double_id"]);
                    return;
                }
                $form.removeClass("step1").addClass("step2")
                    .find("input.edit-vintage-form-year").prop("readonly",true);
            }, null);
        });
        $(document).on("submit", ".edit-vintage-form", function(e){
            var $form = $(this);
            var pid = $form.data("pid");
            var postData = $form.serialize();
            ajaxRequest("/ajax/product/" + pid + "/vintage/add", postData, function(data){
                if(data&&data["id"]){
                    $form.trigger("fetchId", data["id"]);
                    return;
                }
            }, null);
            e.preventDefault();
        });

        $(document).on("dropbox-change", ".edit-vintage-form tr.grape-variety div.dropbox",function(){//,values
            var items = [];
            var $this = $(this);
            $this.find("li.item input:checked").closest("li").each(function(){
                var $li = $(this);
                items.push({id:$li.find("input").val(),name:$li.find("label").text(),autogenerate:true,val:0});
            });
            var $table = $this.closest("td").find("table.blend tbody");
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
            var grapeVarietyConcentrationRowTemplate = $table.closest("form").find(".grape-variety-concentration-row-template").html();
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
                    val = 100;
                }
                autogenerateUsed+=val;
            });
            if(autogenerateCount>0){
                $table.find("tr input.autogenerate").val((autogenerateUsed<=100)?Math.floor((100-autogenerateUsed)/autogenerateCount):0);
            }
            //sort
            var items = [];
            $table.find("tr").each(function(){
                var $tr = $(this);
                var $input = $tr.find("input");
                var val = parseInt($input.val());
                if(isNaN(val)){
                    val = 0;
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
            var grapeVarietyConcentrationRowTemplate = $table.closest("form").find(".grape-variety-concentration-row-template").html();
            $.each(items,function(index,item){
                $table.append($(fillTemplate(grapeVarietyConcentrationRowTemplate,item)));
            });
            $table.find("input").mask('999');
        }
        $(document).on("wrapFormInit",".edit-vintage-form",function(){
            fillGrapeVarietyAutogenerate($(this).find("tr.grape-variety table.blend tbody"));
        });
        $(document).on("change", ".edit-vintage-form tr.grape-variety table.blend input",function(){
            var $this = $(this);
            $this.removeClass("autogenerate");
            fillGrapeVarietyAutogenerate($this.closest("table.blend tbody"));
        });
    }
});