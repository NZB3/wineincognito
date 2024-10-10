$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
        $(document).on("change",".filter-block .filter-form .filter thead .dropdown label input",function(){
            if(this.checked){
                $(this).closest("table.filter").removeClass("minimize");
            } else {
                $(this).closest("table.filter").addClass("minimize");
            }
        });
        function managePagination(count,curPage,pageLimit,$datalist){
            var $pagination = $datalist.find("ul.pagination").empty().hide();
            var maxPage = Math.ceil(count/pageLimit);
            if(maxPage <= 1){
                return;
            }
            var datalistPaginationItemTemplate = $datalist.closest(".filter-block").find(".datalist-pagination-item-template").html();
            var datalistPaginationSeparatorTemplate = $datalist.closest(".filter-block").find(".datalist-pagination-separator-template").html();
            var pages = [];
            if(curPage > 1){
                $pagination.append(fillTemplate(datalistPaginationItemTemplate,{
                    page:curPage-1,
                    caption:"<",
                }));
            }
            var milestones = [0,curPage,maxPage+1];
            var lastPage = 1;
            for(var pageIter=1;pageIter<=maxPage;pageIter++){
                var invalid = true;
                for(var i=0;i<milestones.length;i++){
                    if(Math.abs(pageIter - milestones[i])<=2){
                        invalid = false;
                        break;
                    }
                }
                if(invalid){
                    continue;
                }
                if(pageIter - lastPage > 1){
                    $pagination.append(datalistPaginationSeparatorTemplate);    
                }
                $pagination.append(fillTemplate(datalistPaginationItemTemplate,{
                    page:pageIter,
                    caption:pageIter,
                    current:pageIter==curPage,
                }));
                lastPage = pageIter;
            }
            if(curPage < maxPage){
                $pagination.append(fillTemplate(datalistPaginationItemTemplate,{
                    page:curPage+1,
                    caption:">",
                }));
            }
            $pagination.show();
        }
        function submitFilter($form){
            $form.find(".filter thead .dropdown label input").prop("checked",false).trigger("change");
            var $datalist = $form.closest(".filter-block").find(".datalist");
            $datalist
                .find("tr.item").remove().end()
                .find("tr.noentries").hide().end()
                .find("tr.errmsg").hide().end()
                .find("tr.loading").show().end()
                .show();
            var postData = $form.serialize();
            var page = $form.data("filter-block-page");
            var pageLimit = $form.data("filter-block-page-limit");
            postData += (postData.length?"&":"") + $.param({page:page,pagelimit:pageLimit});
            var orderByField = $form.data("filter-block-order-by-field");
            if(orderByField){
                postData += (postData.length?"&":"") + $.param({orderbyfield:orderByField,orderbydirection:$form.data("filter-block-order-by-direction")?1:0});
            }
            var xhrhndl = $form.data("xhrhndl");
            if(xhrhndl && xhrhndl.readyState != 4){
                xhrhndl.abort();
            }
            var url = $form.find("input.filter-url").val();
            xhrhndl = ajaxRequest(url, postData, function(data){
                $datalist
                    .find("tr.item").remove().end()
                    .find("tbody tr").hide();
                var count = data["count"];
                //pagination
                var page = data["page"];
                var pagelimit = data["pagelimit"];
                managePagination(count,page,pagelimit,$datalist);

                if(count==0){
                    $datalist.find("tr.noentries").show();
                    return;
                }

                var list = data["list"];
                if(sizeOfObject(list)==0){
                    $datalist.find("tr.noentries").show();
                    return;
                }
                var datalistItemTemplate = $form.closest(".filter-block").find(".datalist-item-template").first().html();
                var $tbody = $datalist.find("tbody");
                $.each(list,function(key,row){
                    $tbody.append(fillTemplate(datalistItemTemplate,row));
                });
                markEmptyCols($tbody.closest("table"));
            },function(errMsg){
                $datalist
                    .find("tr.item").remove().end()
                    .find("tr.noentries").hide().end()
                    .find("tr.loading").hide();
                if(typeof errMsg === 'string'){
                    $datalist
                        .find("tr.errmsg")
                            .find("td")
                                .first().html(errMsg).end().end()
                            .show();
                }
                return false;
            });
            $form.data("xhrhndl",xhrhndl);
        }
        $(document).on("submit",".filter-block .filter-form",function(e){
            var $form = $(this);
            // var pageLimit = $form.find(".page-limit").val();
            $form.data("filter-block-page",1).data("filter-block-page-limit",$form.find(".page-limit").val());
            submitFilter($form);
            e.preventDefault();
            return true;
        });
        $(document).on("click",".filter-block .datalist ul.pagination li.page",function(){
            var $this = $(this);
            var $form = $this.closest(".filter-block").find(".filter-form");
            $form.data("filter-block-page",$this.data("page"));
            // var page = $this.data("page");
            // var pageLimit = $this.data("page-limit");
            submitFilter($form);
        });
        $(document).on("click",".filter-block .datalist th.filter-block-can-order-by",function(){
            var $this = $(this);
            var $form = $this.closest(".filter-block").find(".filter-form");
            var field = $this.data("filter-block-order-field");
            $this = $this.closest(".datalist").find("th.filter-block-can-order-by").filter(function(){
                var $this = $(this);
                if($this.data("filter-block-order-field")==field){
                    return true;
                }
                $this.removeClass("filter-block-order-asc filter-block-order-desc");
                return false;
                
            });
            var direction = 0;
            if($this.hasClass("filter-block-order-asc")){
                $this.removeClass("filter-block-order-asc");
                field = null;
            } else if($this.hasClass("filter-block-order-desc")){
                    $this.removeClass("filter-block-order-desc").addClass("filter-block-order-asc");
                    direction = 1;
            } else {
                    $this.addClass("filter-block-order-desc");
                    direction = 0;
            }
            $form.data("filter-block-order-by-field",field);
            $form.data("filter-block-order-by-direction",direction);
            submitFilter($form);
        });
        $(".filter-block.auto-search .filter-form").trigger("submit");
        $(document).on("wrapFormInit",".filter-block.auto-search form",function(){
            $(this).trigger("submit");
        });

        function markEmptyCols($table){
            var $tr = $table.find("tbody tr.item");
            var rowsCount = $tr.first().find("td").length;
            var nonEmptyCols = [];
            $tr.each(function(){
                $(this).find("td").each(function(key,val){
                    if(nonEmptyCols.indexOf(key)!==-1){
                        return true;
                    }
                    if($(this).html().trim().length){
                        nonEmptyCols.push(key);
                    }
                });
                if(nonEmptyCols.length==rowsCount){
                    return true;
                }
            });
            if(nonEmptyCols.length==rowsCount){
                return;
            }
            var $th = $table.find("th").removeClass("filter-empty-col");
            $tr.find("td.filter-empty-col").removeClass("filter-empty-col");
            for(var i=0;i<rowsCount;i++){
                if(nonEmptyCols.indexOf(i)===-1){
                    if($th.filter(":nth-child("+(i+1)+")").hasClass("separator")){
                        continue;
                    }
                    $tr.find("td:nth-child("+(i+1)+")").addClass("filter-empty-col");
                    $th.filter(":nth-child("+(i+1)+")").addClass("filter-empty-col");
                }
            }
        }
    }
});