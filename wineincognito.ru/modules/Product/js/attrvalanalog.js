$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
       typeof(fillTemplate)!=='undefined' && fillTemplate instanceof Function &&
       typeof(wrapForm)!=='undefined' && wrapForm instanceof Function){
        $(document).on("click", ".attrval-analog thead span.add", function(){
            var $table = $(this).closest("table");
            var id = $table.data("id");
            var analogAddFormTemplate = $table.find(".analog-add-form-template").html();
            var $wrapper = wrapForm(analogAddFormTemplate);
            $wrapper.find("form").on("submit",function(e){
                e.preventDefault();
                var analogId = $(this).find("li.item input:checked").first().val();
                ajaxRequest("/ajax/moderate/product/attributes/" + id + "/analog/add",{id:analogId}, function(){
                    reloadList($table);
                    $wrapper.fadeOut("fast",function(){
                        $wrapper.remove();
                    });
                });
            });
        });
        $(document).on("click",".attrval-analog tbody td.remove span", function(){
            var $table = $(this).closest("tr");
            var analogId = $table.data("id");
            var $table = $table.closest("table");
            var id = $table.data("id");
            ajaxRequest("/ajax/moderate/product/attributes/"+id+"/analog/"+analogId+"/remove", null, function(){//success
                reloadList($table);
            }, function(){//error
                reloadList($table);
            });
        });
        var xhrhndl;
        function reloadList($table){
            var id = $table.data("id");
            if(xhrhndl && xhrhndl.readyState != 4){
                xhrhndl.abort();
            }
            xhrhndl = ajaxRequest("/ajax/moderate/product/attributes/"+id+"/analog/list", null, function(data){//success
                var analogItemTemplate = $table.find(".analog-item-template").html();
                var list = "";
                $.each(data,function(key,row){
                    list += fillTemplate(analogItemTemplate, row);
                });
                $table.find("tbody").empty().append(list);
            });
        }
    }
});