$(function(){
    $(document).on("dropbox-change", ".edit-editattrval-form div.dropbox",function(e,group,values){
        group = 1;
        var value = 0;
        if(values.length){
            value = values.pop();
        }
        console.log(value);
        $(this).closest("form").find(".edit-editattrval-form-parent").val(value);
    });
});