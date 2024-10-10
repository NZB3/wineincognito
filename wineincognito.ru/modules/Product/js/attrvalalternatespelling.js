$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
       typeof(fillTemplate)!=='undefined' && fillTemplate instanceof Function){
        $(document).on("click", ".attrvalspellinglist thead span.add", function(){
			var $this = $(this);
			var spellingTemplateItem = $this.closest("th").find(".spelling-template-item").html();
			if(!spellingTemplateItem){
				$this.hide();
				return true;
			}
			scrollIntoView($(spellingTemplateItem).appendTo($this.closest("table.attrvalspellinglist").find("tbody")));
        });
		$(document).on("click",".attrvalspellinglist tbody tr:not(.processing) td.delete span", function(){
			var $this = $(this).closest("tr");
			var id = $this.data("id");
			if(id===undefined){
				$this.remove();
				return true;
			}
			var attrval_id = $this.closest("table").data("id");
			$this.removeClass("editing").addClass("processing").find("td.spelling input").prop("disabled",true);
			ajaxRequest("/ajax/moderate/product/attributes/"+attrval_id+"/alternatespelling/"+id+"/remove", null, function(data){//success
				$this.remove();
            }, function(){//error
                $this.removeClass("processing");
            });
			
		});
		$(document).on("click",".attrvalspellinglist tbody tr:not(.processing) td.edit span.edit", function(){
			var $this = $(this).closest("tr").addClass("editing").find("td.spelling input").prop("disabled",false);
		});
		$(document).on("click",".attrvalspellinglist tbody tr:not(.processing) td.edit span.save", function(){
			var $this = $(this).closest("tr");
			var id = $this.data("id");
			if(id===undefined){
				addAlternateSpelling($this);
			} else {
				saveAlternateSpelling($this, id);
			}
		});
		function saveAlternateSpelling($tr,id){
			var attrval_id = $tr.closest("table").data("id");
			var $input = $tr.removeClass("editing").addClass("processing").find("td.spelling input").prop("disabled",true);
			var spelling = $input.val();
			ajaxRequest("/ajax/moderate/product/attributes/"+attrval_id+"/alternatespelling/"+id+"/edit", {
                spelling:spelling,
            }, function(data){//success
                $input.prop("disabled", true);
				$tr.removeClass("editing processing");
            }, function(){//error
                $input.prop("disabled", false);
				$tr.removeClass("processing").addClass("editing");
            });
		}
		function addAlternateSpelling($tr){
			var attrval_id = $tr.closest("table").data("id");
			var $input = $tr.removeClass("editing").addClass("processing").find("td.spelling input").prop("disabled",true);
			var spelling = $input.val();
			ajaxRequest("/ajax/moderate/product/attributes/"+attrval_id+"/alternatespelling/add", {
                spelling:spelling,
            }, function(data){//success
				$tr.data("id", data.id);
                $input.prop("disabled", true);
				$tr.removeClass("editing processing");
            }, function(){//error
                $input.prop("disabled", false);
				$tr.removeClass("processing").addClass("editing");
            });
		}
		
		function scrollIntoView($element){
			if(!$element || $element.length==0){
				return;
			}
			var minTop = null;
			$element.filter(":visible").each(function(){
				var $this = $(this);
				var top = $this.offset().top;
				if (top < minTop || minTop===null) {
					minTop = top;
				}
			});
			if(minTop===null){
				return;
			}
			setTimeout(function(){
				$([document.documentElement, document.body]).animate({
					scrollTop: minTop
				}, 500);
			});
		}

        
    }
});