$(function(){
    $(document).on("click",".tab-content ul.tab-content-tabs ul.tab-content-group li:not(.tab-content-selected)",function(){
        var $this = $(this);
        var $tabs = $this.closest("ul.tab-content-tabs");
        $tabs.find("li.tab-content-selected").removeClass("tab-content-selected");
        var $groupli = $this.addClass("tab-content-selected").parent("ul").parent("li");
        if(!$groupli.is(":last-child")){
            $groupli.insertAfter($tabs.children("li:last-child"));
        }
        var tabId = $this.data("tab-content-id");
        $tabs.closest(".tab-content").children(".tab-content-content").removeClass("tab-content-selected").filter(function(){
            return $(this).data("tab-content-id") == tabId;
        }).addClass("tab-content-selected");
    });

    // var idGenerator = 0;
    // $(".tab-content").parent().each(function(){
    //     var $parent = $(this);
    //     var $tabContents = $parent.find(".tab-content");
    //     while($tabContents.length){
    //         var tabContentGroup = [];
    //         var $last = $tabContents.last();
    //         $tabContents.splice($tabContents.index($last[0]),1);
    //         tabContentGroup.unshift($last);
    //         while(($last = $last.prev(".tab-content,.tab-content-tabs")).length){
    //             if($last.hasClass("tab-content-tabs")){
    //                 tabContentGroup = [];
    //                 break;
    //             }
    //             tabContentGroup.unshift($last);
    //             $tabContents.splice($tabContents.index($last[0]),1);
    //         }
    //         if(tabContentGroup.length <= 1){
    //             continue;
    //         }
    //         var tabsHtml = '';
    //         var tabGroupsHtml = '';
    //         var tabcount = 0;
    //         for(var i=0;i<tabContentGroup.length;i++){
    //             var id = idGenerator++;
    //             tabContentGroup[i].data("tab-content-id",id).hide();
    //             tabsHtml += '<li data-tab-content-id="'+id+'">'+tabContentGroup[i].data("header")+'</li>';
    //             tabcount++;
    //             if(tabcount > 1 && (i==tabContentGroup.length-3 || tabcount == 3) || i==tabContentGroup.length-1){
    //                 tabGroupsHtml += '<li><ul class="tab-content-group tab-content-group-for-'+tabcount+' ">'+tabsHtml+'</ul></li>';
    //                 tabcount = 0;
    //                 tabsHtml = '';
    //             }
    //         }
    //         tabContentGroup[0].before('<ul class="tab-content-tabs group-block">'+tabGroupsHtml+'</ul>');
    //     }
    //     $parent.find("ul.tab-content-tabs > li:first-child li:first-child").trigger("click");
    // });
    
});