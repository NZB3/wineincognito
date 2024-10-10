$(function(){
  if(typeof(ajaxRequest)!=="undefined" && ajaxRequest instanceof Function){
    $(document).on("click",".tastingswapreviews-button button",function(){
      var $this = $(this);
      var tpv_ids = [];
      $("table.viewTasting-vintage-list-swap-reviews tbody tr.viewTasting-vintage-list-multiple-select-selected").each(function(){
          tpv_ids.push($(this).data("tpv-id"));
      });
      tpv_ids = $.unique(tpv_ids);
      if(tpv_ids.length!=2){
        var err_str = $this.closest("div.tastingswapreviews-button").find(".err_string_two_products").html();
        if(err_str && typeof(UIOutputInfoBlock)!=="undefined" && UIOutputInfoBlock instanceof Function){
            UIOutputInfoBlock(err_str,0);
        }
        return;
      }
      var tid = $this.data("tid");
      var uid = $this.data("uid");
      ajaxRequest("/ajax/tasting/"+tid+"/swapreviews/user/"+uid+"/swap", {'ids':tpv_ids}, function(data){
        $(".viewTasting-vintage-list-swap-reviews span.refresh").trigger("click");
      });
    });
  };
});