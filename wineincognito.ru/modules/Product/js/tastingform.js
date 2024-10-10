$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function &&
        typeof(confirmBox)!=='undefined' && confirmBox instanceof Function){
        $(document).on("change",".edit-tasting-vintage-form .edit-tasting-vintage-form-blind", function(){
            if(this.checked){
                $(this).closest("table").find("tr.edit-tasting-vintage-form-blindname").show();
            } else {
                $(this).closest("table").find("tr.edit-tasting-vintage-form-blindname").hide();
            }
        });
        $(document).on("change",".edit-tasting-vintage-form tr.edit-tasting-vintage-form-primeur input", function(){
            var $form = $(this).closest("form");
            if($form.find("tr.edit-tasting-vintage-form-primeur input:checked").val()==1){
                $form.find("tr.edit-tasting-vintage-form-lot").hide();
            } else {
                $form.find("tr.edit-tasting-vintage-form-lot").show();
            }
        });
        $(document).on("wrapFormInit",".edit-tasting-vintage-form",function(){
            var $form = $(this);
            formInit($form);
        });
        function formInit($form){
            if($form.find(".edit-tasting-vintage-form-blind").prop("checked")){
                $form.find("tr.edit-tasting-vintage-form-blindname").show();
            } else {
                $form.find("tr.edit-tasting-vintage-form-blindname").hide();
            }
            if($form.find("tr.edit-tasting-vintage-form-primeur input:checked").val()==1){
                $form.find("tr.edit-tasting-vintage-form-lot").hide();
            } else {
                $form.find("tr.edit-tasting-vintage-form-lot").show();
            }
        }
        formInit($(".edit-tasting-vintage-form"));
    }
});