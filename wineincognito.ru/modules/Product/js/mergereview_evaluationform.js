$(function(){
  $(document).on("change", ".merge-review tbody td.score ul li input",function(){
    $(this).closest("ul").find("input:checked").closest("li").prevAll().addBack().addClass("checked").end().end().nextAll().removeClass("checked");
  });
  $(".merge-review tbody td.score ul li input:checked").trigger("change");

  $(document).on("change", ".merge-review tbody td.score-evaluation-settings table td.score ul li input",function(){
    var $this = $(this);
    var val = $this.closest("ul").find("input:checked").val();
    if(val>0){
      $this.closest("tr").siblings().show();
    } else {
      $this.closest("tr").siblings().hide();
    }
  });
  $(".merge-review tbody td.score-evaluation-settings table td.score ul li input").first().trigger("change");
  
  $(document).on("change", ".merge-review tbody tr.score-permissible-variation td ul li input",function(){
    var $td = $(this).closest("td");
    var val = $td.find("ul li input:checked").val();
    if(val>0){
      $td.find("input.merge-review-expert-evaluation-options-score-permissible-variation-manual-value").show();
    } else {
      $td.find("input.merge-review-expert-evaluation-options-score-permissible-variation-manual-value").hide();
    }
  });
  $(".merge-review tbody tr.score-permissible-variation td ul li input:checked").trigger("change");
  $(".merge-review tbody td input.merge-review-expert-evaluation-options-score-permissible-variation-manual-value,.merge-review tbody td input#merge-review-expert-evaluation-options-score-manual-value").mask('99,99');



});