$(function(){
    function showError(msg){
        if(typeof(UIOutputInfoBlock)!=='undefined' && UIOutputInfoBlock instanceof Function){
            UIOutputInfoBlock(msg,0);
        } else {
            alert(msg);
        }
    }
    function flushMessages(){
        if(typeof(UIFlushInfoBlocks)!=='undefined' && UIFlushInfoBlocks instanceof Function){
            UIFlushInfoBlocks();
        }
    }
    $(".register-user-form tr.consent input").on("change", function(){
        if(this.checked){
            $(this).closest("form").find("tr.buttons input").prop("disabled",false);
        } else {
            $(this).closest("form").find("tr.buttons input").prop("disabled",true);
        }
    }).closest("form").find("tr.buttons input").prop("disabled",true);
    $(".register-user-form").on("submit", function(){
        var $this = $(this);
        $this.find(".highlighted").removeClass("highlighted");
        flushMessages();
        if(!$this.find("input[name=consent]").prop("checked")){
            showError($this.find(".error_strings_consent_required").html());
            return false;
        }
        var $passwordField = $this.find("input#register-user-form-password");
        var $repeatPasswordField = $this.find("input#register-user-form-rpassword");
        if($passwordField.val()!==$repeatPasswordField.val()){
            showError($this.find(".error_strings_password_match").html());
            $passwordField.addClass("highlighted");
            $repeatPasswordField.addClass("highlighted");
            return false;
        }
        return true;
    });
});