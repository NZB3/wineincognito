$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
        typeof(confirmBox)!=='undefined' && confirmBox instanceof Function){
        $(document).on("click", ".filter-block.user-filter .datalist tbody td.dismiss span", function(){
            var $this = $(this);
            var $tr = $this.closest("tr");
            var confirm_string_dismiss_user = $tr.closest(".filter-block").find(".confirm_string_dismiss_user").html();
            var userId = $tr.data("id");
            var companyId = $tr.data("company-id");
            confirmBox(confirm_string_dismiss_user.replace('{{fullname}}',$tr.find("td.name").html()), function(){
                ajaxRequest("/ajax/company/" + companyId + "/dismissuser/" + userId,null,function(data){
                    if($tr.closest(".filter-block").hasClass("company-list")){
                        $tr.remove();//remove user row cause he is no longer a company member
                    } else {
                        $this.remove();//remove only dismiss button
                    }
                    
                });
            }, null);
        });
        $(document).on("click",".filter-block.user-filter .datalist tbody td.favourite span", function(){
            var $tr = $(this).closest("tr");
            var id = $tr.data("id");
            var isFavourite = $tr.hasClass("favourite");
            ajaxRequest("/ajax/user/favourite", {
                id:id,
                favourite_to:isFavourite?0:1,
            }, function(data){
                if(isFavourite){
                    $tr.removeClass("favourite");
                } else {
                    $tr.addClass("favourite");
                }
            },null);
        });

        function resolveJoinRequest(companyId, userId, approve, $row){
            ajaxRequest("/ajax/company/" + companyId + "/resolvejoinrequest/" + userId,{"approve":approve?1:0},function(data){
                $row.remove();
            });
        }
        $(document).on("click",".filter-block.user-filter .datalist tbody td.approve-join-request span", function(){
            var $this = $(this);
            var $tr = $this.closest("tr");
            resolveJoinRequest($this.data("company-id"),$tr.data("id"),true,$tr);
        });
        $(document).on("click",".filter-block.user-filter .datalist tbody td.reject-join-request span", function(){
            var $this = $(this);
            var $tr = $this.closest("tr");
            resolveJoinRequest($this.data("company-id"),$tr.data("id"),false,$tr);
        });
        
        function approveExpert($tr,approve){
            var id = $tr.data("id");
            ajaxRequest("/ajax/user/" + id + "/expert/approve", {
                approve:approve?1:0,
            }, function(){
                $tr.remove();
            },null);
        }
        $(document).on("click",".filter-block.user-filter .datalist tbody td.approve-expert span",function(){
            approveExpert($(this).closest("tr"),true);
        });
        $(document).on("click",".filter-block.user-filter .datalist tbody td.deny-expert span",function(){
            approveExpert($(this).closest("tr"),false);
        });

        function toggleAddRight($tr,right){
            var id = $tr.data("id");
            var trclass = null;
            switch(right){
                case "add-product":
                    trclass = "can-add-product";
                    break;
                case "add-tasting":
                    trclass = "can-add-tasting";
                    break;
                default:
                    return;
            }
            var enable = !$tr.hasClass(trclass);
            ajaxRequest("/ajax/user/" + id + "/access/change", {
                right:right,
                enable:enable?1:0,
            }, function(){
                if(enable){
                    $tr.addClass(trclass);
                } else {
                    $tr.removeClass(trclass);
                }
            },null);
        }
        $(document).on("click",".filter-block.user-filter .datalist tbody td.add-product-right span",function(){
            var enable = !$(this).closest("tr").hasClass("can-add-product");
            toggleAddRight($(this).closest("tr"),"add-product");
        });
        $(document).on("click",".filter-block.user-filter .datalist tbody td.add-tasting-right span",function(){
            var enable = !$(this).closest("tr").hasClass("can-add-tasting");
            toggleAddRight($(this).closest("tr"),"add-tasting");
        });
        
    }
});