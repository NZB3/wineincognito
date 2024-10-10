$(function(){
    $changePasswordForm = $("#edit-user-change-password-form");
    $passwordField = $changePasswordForm.find("input#edit-user-change-password-form-password");
    $repeatPasswordField = $changePasswordForm.find("input#edit-user-change-password-form-rpassword");
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
    $changePasswordForm.on("submit", function(){
        $changePasswordForm.find(".highlighted").removeClass("highlighted");
        flushMessages();
        if($passwordField.val()!==$repeatPasswordField.val()){
            showError($changePasswordForm.find("#error_strings_password_match").html());
            $passwordField.addClass("highlighted");
            $repeatPasswordField.addClass("highlighted");
            return false;
        }
        return true;
    });
});