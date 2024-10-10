$(function(){
    $(document).on("submit","form.viewTasting-review-particularity-options",function(e){
      var $form = $(this);
      var tId = $form.data("t-id");
      var postData = $form.serialize();
      ajaxRequest("/ajax/tasting/" + tId + "/particularity/change", postData);
      e.preventDefault();
    });
    $(document).on("click",".viewTasting-review-particularity-options tbody tr.header td label",function(){
      var $this = $(this).closest("td").find("input");
      var name = $this.attr("name");
      var val = $this.val();
      $this.closest("tbody").find("tr.element.element-" + name + " td input[value="+val+"]").prop("checked",true);
    });
    $(document).on("change",".viewTasting-review-particularity-options tbody tr.element td input",function(){
      var $this = $(this);
      var val = $this.val();
      var name = $this.closest("tr").data("header-group");
      if(!$this.closest("tbody").find("tr.element.element-" + name + " td input[value='"+(val==1?0:1)+"']:checked").length){
        $this.closest("tbody").find("tr.header td input[name='"+name+"'][value="+val+"]").prop("checked",true);
      } else {
        $this.closest("tbody").find("tr.header td input[name='"+name+"']").prop("checked",false);
      }
    });
    $(".viewTasting-review-particularity-options tbody tr.support.header").each(function(){
      var $this = $(this).find("td input");
      var name = $this.first().attr("name");
      var vals = [];
      $this.closest("tbody").find("tr.element.element-" + name + " td input:checked").each(function(){
        var val = $(this).val();
        if(vals.indexOf(val)===-1){
          vals.push(val);
        }
      });
      if(vals.length==1){
        $this.filter("[value="+vals[0]+"]").prop("checked",true);
      } else {
        $this.prop("checked",false);
      }
    });
});