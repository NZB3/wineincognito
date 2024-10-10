$(function(){
  var $body = $('html, body');
  $(document).on("click", ".tasting-vintage-ranking-edit td.raise-index span, .tasting-vintage-ranking-edit td.lower-index span", function(){
    var $this = $(this).closest("td");
    var $tr = $this.closest("tr");
    var newPosition = parseInt($tr.find("td.index input").val())+($this.hasClass("raise-index")?-1:1);
    var oldY = $tr.offset().top;
    moveRow($tr,newPosition);
    $body.animate({scrollTop: $body.scrollTop() + $tr.offset().top - oldY}, 400);
  });
  function moveRow($tr,newPosition){
    var curPosition = parseInt($tr.find("td.index input").val());
    if(curPosition==newPosition){
      return true;
    }
    var isRising = (curPosition>newPosition);
    $mark = undefined;
    $tr.siblings("tr").each(function(){
      var $tr = $(this);
      if(parseInt($tr.find("td.index input").val())==newPosition){
        $mark = $tr;
        return false;
      }
    });
    if($mark===undefined){
      return false;
    }
    if(isRising){
      $tr.insertBefore($mark);
    } else {
      $tr.insertAfter($mark);
    }
    $tr
      .find("td.index")
        .find("input").val(newPosition).end()
        .find("span").html(newPosition).end()
      .end()
      .find("div.dropbox").addClass("fresh").find("li").removeClass("selected")
        .find("input").prop("checked",false)
          .filter(function(){
            return this.value == newPosition;
          }).prop("checked", true).closest("li").addClass("selected").end().end().end().end().end()
      .siblings("tr").each(function(){
        var $tr = $(this);
        var position = parseInt($tr.find("td.index input").val());
        if(isRising){
          if(position<newPosition || position>curPosition){
            return true;
          }
          $tr
            .find("td.index")
              .find("input").val(position+1).end()
              .find("span").html(position+1).end().end()
            .find("div.dropbox").addClass("fresh").find("li").removeClass("selected")
              .find("input").prop("checked",false)
                .filter(function(){
                  return this.value == position+1;
                }).prop("checked", true).closest("li").addClass("selected");
            ;
        } else {
          if(position<curPosition || position>newPosition){
            return true;
          }
          $tr.find("td.index")
            .find("input").val(position-1).end()
            .find("span").html(position-1).end().end()
            .find("div.dropbox").addClass("fresh").find("li").removeClass("selected")
              .find("input").prop("checked",false)
                .filter(function(){
                  return this.value == position-1;
                }).prop("checked", true).closest("li").addClass("selected");
        }
      });
    window.dropBoxSetLabels();
  }
  $(document).on("dropbox-change", ".tasting-vintage-ranking-edit div.dropbox",function(e,group,values){
    if(!values.length){
      return true;
    }
    var newPosition = values.pop();
    moveRow($(this).closest("tr"), newPosition);
  });
  (function(){
    var $currentlyDragging = undefined;
    var isDragging = false;
    var raiseIndexPosition = undefined;
    var lowerIndexPosition = undefined;
    var raiseIndexY = undefined;
    var lowerIndexY = undefined;
    $(document).on("mousedown",".tasting-vintage-ranking-edit td.index",function(e){
      if(e.which != 1){
        return true;
      }
      drop();
      isDragging = true;
      $currentlyDragging = $(this).closest("tr").closest("table").addClass("dragging").end();
      startDrag();
    });
    $(document).on("mouseup",function(){
      drop();
    });
    $(document).on("mousemove",function(e){
      if(!isDragging){
        return true;
      }
      if(e.pageY>lowerIndexY){
        moveRow($currentlyDragging,lowerIndexPosition);
        startDrag();
        return true;
      }
      if(e.pageY<raiseIndexY){
        moveRow($currentlyDragging,raiseIndexPosition);
        startDrag();
        return true;
      }
    });
    function startDrag(){
      var curPosition = parseInt($currentlyDragging.find("td.index input").val());
      raiseIndexPosition = curPosition - 1;
      lowerIndexPosition = curPosition + 1;
      raiseIndexY = undefined;
      lowerIndexY = undefined;
      $currentlyDragging.siblings("tr").each(function(){
        var $tr = $(this);
        var position = parseInt($tr.find("td.index input").val());
        if(position==raiseIndexPosition){
          raiseIndexY = $tr.offset().top;
          if(lowerIndexY){
            return false;
          }
          return true;
        }
        if(position==lowerIndexPosition){
          lowerIndexY = $tr.offset().top+$tr.outerHeight();
          if(raiseIndexY){
            return false;
          }
          return true;
        }
      });
    }
    function drop(){
      if(!isDragging){
        return;
      }
      $currentlyDragging.closest("table").removeClass("dragging");
      $currentlyDragging = undefined;
      isDragging = false;
    }
  })();
  

});