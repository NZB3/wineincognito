$(function(){
  $("#edit-tasting-contest-form td.edit-tasting-contest-form-image-block input[type=button]").on("click",function(){
    $(this).siblings("input[type=file]").trigger("click");
  });
  $("#edit-tasting-contest-form td.edit-tasting-contest-form-image-block input[type=file]").on("change",function(){
    if(this.files && this.files[0]){
      // if(!file.type.match(/(.png)|(.jpeg)|(.jpg)|(.gif)$/i) || file.size > 100*1024){
      //               return true;
      // $editProductFormAddImageFile[0].value = "";
      //           } 
      var reader = new FileReader();
      var $img = $(this).siblings("img");
      reader.onload = function(e) {
        $img.attr('src', e.target.result);
        $img.removeClass("empty");
      }
      reader.readAsDataURL(this.files[0]);
    }
  });
});