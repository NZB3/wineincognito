$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){

        var $sidebar = $("#sidebar");
        var $datalist = $sidebar.find("table.datalist");
        var datalistItemTemplate = $sidebar.find(".datalist-item-template").first().html();
        var infoTableImageTemplate = $sidebar.find(".info-table-image-template").first().html();
        var infoTableAttributeTemplate = $sidebar.find(".info-table-attribute-template").first().html();
        var infoTableAttributeValueTemplate = $sidebar.find(".info-table-attribute-value-template").first().html();
        var infoTableTemplate = $sidebar.find(".info-table-template").first().html();
        var $vintageSelector = $("tr#vintage-selector td ul");
        var vintageSelectorItemTemplate = $vintageSelector.closest("td").find(".vintage-selector-item-template").first().html();
        var $form = $("tr#searchbar td.searchbar form");
        var $content = $("#content");
        var contentItemTemplate = $content.find(".content-item-template").first().html();
        var contentPaginationItemTemplate = $content.find(".content-pagination-item-template").html();
        var contentPaginationSeparatorTemplate = $content.find(".content-pagination-separator-template").html();
        var $contentTable = $content.find("table");
        var $contentTablePaginationUl = $contentTable.find("tr.pagination ul");
      


        function getVintageList(){
            var vintageList = [];
            $form.closest("table").find("tr#vintage-selector ul li input:checked").each(function(){
                vintageList.push(this.value);
            });
            return vintageList;
        }
        var urlTemplate = "{if{lang}}/{{lang}}{endif{lang}}/searcher{if{searchtext}}/s/{{searchtext}}{endif{searchtext}}{if{vintage}}/v/{{vintage}}{endif{vintage}}{if{product}}/p/{{product}}{endif{product}}"
        const REFRESH_STATE_TYPE_SEARCH = 0;
        const REFRESH_STATE_TYPE_PRODUCT = 1;
        const REFRESH_STATE_TYPE_PAGE = 2;
        function refreshState(type){
            if(typeof(history.pushState)!=='undefined' && history.pushState instanceof Function){
                var $info = $sidebar.find(".info");
                var state = {
                    product:$info.data("id")?$info.data("id"):null,
                    searchtext:$form.data("search-text")?$form.data("search-text"):"",
                    vintage:getVintageList(),
                    page:type==REFRESH_STATE_TYPE_PAGE&&$contentTable.data("page")?$contentTable.data("page"):1,
                };
                if(JSON.stringify(state)===JSON.stringify(window.history.state)){
                    return;
                }
                var templateData = {
                    product:state.product?state.product:null,
                    searchtext:!state.product&&state.searchtext?encodeURIComponent(state.searchtext):null,
                    vintage:state.vintage.length?encodeURIComponent(state.vintage.join(',')):null,

                };
                history.pushState(state, document.title, fillTemplate(urlTemplate,templateData));
                $("body > table tr#searchbar td.language-selector a").each(function(){
                    var $this = $(this);
                    var templateDataCopy = Object.assign({}, templateData);
                    templateDataCopy.lang = $this.data("code");
                    $this.prop("href",fillTemplate(urlTemplate,templateDataCopy));
                });
            }
        }
        $(window).on("popstate", function(){
            if(window.history.state){
                var searchtext = window.history.state.searchtext?window.history.state.searchtext:"";
                $form.find("input").val(searchtext);
                manageVintagesList(window.history.state.vintage,true);
                if(window.history.state.product){
                    loadProductInfo(window.history.state.product,true);
                    loadPriceList(LOAD_PRICE_LIST_TYPE_BY_ID,window.history.state.product,window.history.state.page,true);
                } else {
                    if(searchtext.length || window.history.state.vintage || window.history.state.page > 1){
                        $form.data("search-text",searchtext);
                        submitFilter(false,true);
                        loadPriceList(LOAD_PRICE_LIST_TYPE_BY_STRING,searchtext,window.history.state.page,true);
                    }
                }
            }
        });
        var timeoutHandle;
        function submitFilter(loadNextPage,skipRefreshState=false){
            clearTimeout(timeoutHandle);
            $sidebar.find("table.info, div.loading, div.errmsg").remove();

            var page = loadNextPage?$form.data("next-page"):1;
            var checkVintages = !loadNextPage;
            var search_text = $form.data("search-text");
            if(!loadNextPage){
                $form.data("vintage-list",getVintageList());
            }
            var postData = {
                action:"vintage_filter",
                search_text:search_text,
                "only_having_vintages[]":$form.data("vintage-list"),
                return_vintages:checkVintages,
                page:page
            };
            if(!loadNextPage){
                if(JSON.stringify(postData)===JSON.stringify(Object.assign({},$form.data("post")))){//exact same query
                    $datalist.show();
                    return;
                }
                $form.data("post",Object.assign({},postData));
            }
            $datalist
                    .find("tr.noentries").hide().end()
                    .find("tr.loadmore").hide().end()
                    .find("tr.loading").show().end()
                .show();
            if(page==1){
                $datalist.find("tr.item").remove();
            }
            var xhrhndl = $form.data("xhrhndl");
            if(xhrhndl && xhrhndl.readyState != 4){
                xhrhndl.abort();
            }
            xhrhndl = ajaxRequest("/ajax/searcher/search", postData, function(data){
                if(checkVintages){
                    manageVintagesList(Object.values(data["vintages"]),false);
                }
                $datalist.find("tr.loading,tr.noentries,tr.loadmore").hide();
                var count = data["count"];
                //pagination
                var page = data["page"];
                var pageLimit = data["pagelimit"];
                if(page==1){
                    $datalist.find("tr.item").remove();
                    //noentries
                    if(count==0){
                        $datalist.find("tr.noentries").show();
                        return;
                    }
                }
                

                var list = data["list"];
                if(sizeOfObject(list)==0){
                    // $datalist.find("tr.noentries").show();
                    return;
                }
                // if(count==1&&page==1&&sizeOfObject(list)==1){
                //     loadProductInfo(list[0]['pid'],true);
                //     return;
                // }
                var $noentries = $datalist.find("tr.noentries");
                $.each(list,function(key,row){
                    $noentries.before(fillTemplate(datalistItemTemplate,row));
                });
                if(page*pageLimit < count){
                    $form.data("next-page",page+1);
                    $datalist.find("tr.loadmore").show();
                } else {
                    $form.data("next-page",null);
                }
            },function(errMsg){
                $datalist.hide().find("tr.item").remove().end().closest("td#sidebar").find("div.errmsg, table.info, div.loading").remove();
                if(typeof errMsg === 'string'){
                    $sidebar.append("<div class=\"errmsg\">"+errMsg+"</div>");
                }
                return false;
            });
            if(!loadNextPage && !skipRefreshState){
                refreshState(REFRESH_STATE_TYPE_SEARCH);
            }
            $form.data("xhrhndl",xhrhndl);
        }
        function managePriceListPagination(count,curPage,pageLimit){
            $contentTablePaginationUl.empty().hide();
            var maxPage = Math.ceil(count/pageLimit);
            if(maxPage <= 1){
                return;
            }
            if(curPage > 1){
                $contentTablePaginationUl.append(fillTemplate(contentPaginationItemTemplate,{
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
                    $contentTablePaginationUl.append(contentPaginationSeparatorTemplate);    
                }
                $contentTablePaginationUl.append(fillTemplate(contentPaginationItemTemplate,{
                    page:pageIter,
                    caption:pageIter,
                    current:pageIter==curPage,
                }));
                lastPage = pageIter;
            }
            if(curPage < maxPage){
                $contentTablePaginationUl.append(fillTemplate(contentPaginationItemTemplate,{
                    page:curPage+1,
                    caption:">",
                }));
            }
            $contentTablePaginationUl.show();
        }
        function manageVintagesList(vintages,popstate=false){
            var vintageObjects = [];
            if(popstate){
                $vintageSelector.empty();
            }
            $vintageSelector.find("li input:checked").each(function(){
                var vintage = this.value;
                var index = vintages.indexOf(vintage);
                if(index!==-1){
                    vintages.splice(index,1);
                }
                vintageObjects.push({
                        vintage:vintage,
                        selected:true,
                        redundant:(index===-1)
                    });
            });
            $.each(vintages,function(key,vintage){
                vintageObjects.push({
                        vintage:vintage,
                        selected:popstate,
                    });
            })
            vintageObjects.sort(function(a, b){
                if(a.vintage==='NV'){
                    return -1;
                }
                if(b.vintage==='NV'){
                    return 1;
                }
                return parseInt(a.vintage) - parseInt(b.vintage);
            });
            var visibleCount = 0;
            $vintageSelector.hide().empty();
            $.each(vintageObjects,function(key,row){
                visibleCount++;
                $vintageSelector.append(fillTemplate(vintageSelectorItemTemplate,row));
            });
            if(visibleCount>1){
                $vintageSelector.show();
            }
        }
        const LOAD_PRICE_LIST_TYPE_BY_STRING = 0;
        const LOAD_PRICE_LIST_TYPE_BY_ID = 1;
        const LOAD_PRICE_LIST_TYPE_LOAD = 2;
        function loadPriceList(type,param=null,param2=null,skipRefreshState=false){
            var post;
            switch(type){
                case LOAD_PRICE_LIST_TYPE_BY_ID:
                    post = {
                        id:param,
                        omit_fullname:1,
                    };
                    if(param2>1){
                        post.page = param2;
                    }
                    break;
                case LOAD_PRICE_LIST_TYPE_BY_STRING:
                    post = {
                        search_text:param,
                    };
                    if(param2>1){
                        post.page = param2;
                    }
                    break;
                case LOAD_PRICE_LIST_TYPE_LOAD:
                    post = Object.assign({}, $contentTable.data("post"));
                    if(!post){
                        return;
                    }
                    if(param){
                        post.page = param;
                    }
                    break;
                default://never
                    return;
            }
            var orderByField = $contentTable.data("order-by-field");
            if(orderByField){
                post["orderbyfield"] = orderByField;
                post["orderbydirection"] = $contentTable.data("order-by-direction")?1:0;
            } else {
                delete post["orderbyfield"];
                delete post["orderbydirection"];
            }
            post["only_having_vintages"] = getVintageList();
            if(JSON.stringify(post)===JSON.stringify($contentTable.data("post"))){//exact same query
                return;
            }
            $contentTable.data("post",Object.assign({}, post));
            var checkSingles = type!==LOAD_PRICE_LIST_TYPE_LOAD;
            if(checkSingles){
                post["check_singles"] = true;
            }
            post["omit_score"] = (!checkSingles && $contentTable.hasClass("single-vintage"))?1:0;
            var checkVintages = type===LOAD_PRICE_LIST_TYPE_BY_ID;
            
            post["action"] = "pricelist_get";
            post["return_vintages"] = checkVintages?1:0;
            $contentTable.removeClass("hidden").addClass("loading");
            ajaxRequest("/ajax/searcher/pricelist", post, function(data){
                if(checkVintages){
                    manageVintagesList(Object.values(data["vintages"]),false);
                }
                if(checkSingles){
                    if(data["singlevintage"]){
                        $contentTable.addClass("single-vintage");
                    } else {
                        $contentTable.removeClass("single-vintage");
                    }
                    if(data["singlevolume"]){
                        $contentTable.addClass("single-volume").find("th.volume").removeClass("can-order-by order-asc order-desc");
                    } else {
                        $contentTable.removeClass("single-volume").find("th.volume").addClass("can-order-by");
                    }
                }

                

                $contentTable.find("tr.item").remove().end().addClass("loading").removeClass("hidden errmsg noentries");
                var count = data["count"];
                var page = data["page"];
                managePriceListPagination(count,page,data["pagelimit"]);

                if(count==0){
                    $contentTable.removeClass("loading").addClass("noentries");
                    return;
                }
                var list = data["list"];
                if(sizeOfObject(list)==0){
                    $contentTable.removeClass("loading").addClass("noentries");
                    return;
                }
                var $noentries = $contentTable.find("tr.noentries");
                $.each(list,function(key,row){
                    $noentries.before(fillTemplate(contentItemTemplate,row));
                });
                //non active name for single
                if(type===LOAD_PRICE_LIST_TYPE_BY_ID){
                    $contentTable.removeClass("active-product-name");
                } else {
                    $contentTable.addClass("active-product-name");
                }
                $contentTable.data("page",page).removeClass("loading");
                if(type===LOAD_PRICE_LIST_TYPE_LOAD && !skipRefreshState){
                    refreshState(REFRESH_STATE_TYPE_PAGE);
                }
            },function(errMsg){
                $contentTable.removeClass("loading noentries").addClass("hidden").find("tr.item").remove();
                if(typeof errMsg === 'string'){
                    $contentTable.find("tr.errmsg").addClass("errmsg").end().removeClass("hidden");
                }
                return false;
            });

        }
        function loadProductInfo(product_id,skipRefreshState=false){
            if(product_id==$sidebar.find(".info").data("id")){//exact same query
                return;
            }
            clearTimeout(timeoutHandle);
            $sidebar.find("table.datalist").hide().end()
                .find("table.info, div.loading, div.errmsg").remove().end()
                .append("<div class=\"loading\"></div>");
            ajaxRequest("/ajax/searcher/info/" + product_id, null, function(data){
                $sidebar.find("div.loading").remove();
                var infodata = {fullname:data["fullname"],id:data["product_id"]};
                var images_html = "";
                $.each(data["images"],function(){
                    images_html += fillTemplate(infoTableImageTemplate,this);
                });
                infodata["images"] = images_html;
                var attributes_html = "";
                $.each(data["attributes"],function(){
                    var values_html = "";
                    $.each(this["values"],function(){
                        values_html += fillTemplate(infoTableAttributeValueTemplate,this);
                    });
                    attributes_html += fillTemplate(infoTableAttributeTemplate,{label:this["label"],values:values_html},false);
                });
                infodata["attributes"] = attributes_html;
                $sidebar.append(fillTemplate(infoTableTemplate,infodata,false));
                if(!skipRefreshState){
                    refreshState(REFRESH_STATE_TYPE_PRODUCT);    
                }
                
            },function(errMsg){
                $sidebar.find("table.datalist").hide().end().find("div.errmsg, table.info, div.loading").remove();
                if(typeof errMsg === 'string'){
                    $sidebar.append("<div class=\"errmsg\">"+errMsg+"</div>");
                }
                return false;
            });
        }
        $form.on("submit",function(e){
            var search_text = $form.find("input").val().trim();
            $form.data("search-text",search_text);
            submitFilter(false);
            loadPriceList(LOAD_PRICE_LIST_TYPE_BY_STRING,search_text);
            e.preventDefault();
            return true;
        });
        $("tr#vintage-selector").on("change","li:not(.redundant) input",function(){
            clearTimeout(timeoutHandle);
            timeoutHandle = setTimeout(function(){
                if($contentTable.data("type")===LOAD_PRICE_LIST_TYPE_BY_ID){//user is in single product mode
                    loadPriceList(LOAD_PRICE_LIST_TYPE_LOAD,1);
                } else {
                    submitFilter(false);
                    loadPriceList(LOAD_PRICE_LIST_TYPE_BY_STRING,$form.data("search-text"));
                }
            },1000);
        });
        $datalist.find("tr.loadmore span").on("click",function(){
            submitFilter(true);
        });

        $datalist.on("click","tr.item",function(){
            var product_id = $(this).closest("table").hide().end().data("pid");
            loadProductInfo(product_id);
            loadPriceList(LOAD_PRICE_LIST_TYPE_BY_ID,product_id);
        });
        $content.on("click","table.active-product-name tr.item td.name",function(){
            var product_id = $(this).closest("tr").data("id");
            loadProductInfo(product_id);
            loadPriceList(LOAD_PRICE_LIST_TYPE_BY_ID,product_id);
        });
        $contentTable.on("click","th.can-order-by",function(){
            var field = $(this).data("order-field");
            $this = $contentTable.find("th.can-order-by").filter(function(){
                var $this = $(this);
                if($this.data("order-field")==field){
                    return true;
                }
                $this.removeClass("order-asc order-desc");
                return false;
            });
            var direction = 0;
            if($this.hasClass("order-asc")){
                $this.removeClass("order-asc");
                field = null;
            } else if($this.hasClass("order-desc")){
                $this.removeClass("order-desc").addClass("order-asc");
                direction = 1;
            } else {
                $this.addClass("order-desc");
                direction = 0;
            }
            $contentTable.data("order-by-field",field);
            $contentTable.data("order-by-direction",direction);
            loadPriceList(LOAD_PRICE_LIST_TYPE_LOAD,null,null,true);
        });
        $contentTablePaginationUl.on("click","li.page",function(){
            loadPriceList(LOAD_PRICE_LIST_TYPE_LOAD,$(this).data("page"));
        });


        (function(){
            var $ul = $("tr#vintage-selector ul");
            if($ul.find("li").length>0){
                $ul.show();
            }
            if($form.data("id")!==undefined){
                var product_id = $form.data("id");
                loadProductInfo(product_id);
                loadPriceList(LOAD_PRICE_LIST_TYPE_BY_ID,product_id);
            } else if($form.hasClass("auto-search")){
                $form.removeClass("auto-search").trigger("submit");
            }
            // $("tr#searchbar td.searchbar form.auto-search").removeClass("auto-search").trigger("submit");
        })();
    }
});