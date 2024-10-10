$(function(){
    $editCompanyForm = $("#edit-company-form");
    $ITNField = $editCompanyForm.find("input#edit-company-form-itn");
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
    function validateITN(itn){
        if(!itn.match(/^\d+$/)) return false;
        if(itn.length==10){
            return itn[9] == String(((2*itn[0] + 4*itn[1] + 10*itn[2] + 3*itn[3] + 5*itn[4] + 9*itn[5] + 4*itn[6] + 6*itn[7] + 8*itn[8]) % 11) % 10);
        } else if(itn.length == 12){
            return itn[10] == String(((7*itn[0] + 2*itn[1] + 4*itn[2] + 10*itn[3] + 3*itn[4] + 5*itn[5] + 9*itn[6] + 4*itn[7] + 6*itn[8] + 8*itn[9]) % 11) % 10) 
            && itn[11] == String(((3*itn[0] +  7*itn[1] + 2*itn[2] + 4*itn[3] + 10*itn[4] + 3*itn[5] + 5*itn[6] +  9*itn[7] + 4*itn[8] + 6*itn[9] + 8*itn[10]) % 11) % 10);
        }
        return false;
    }
    $editCompanyForm.on("submit", function(){
        $editCompanyForm.find(".highlighted").removeClass("highlighted");
        flushMessages();
        var itn = $ITNField.val();
        if(itn.length && !validateITN(itn)){
            showError($editCompanyForm.find("#error_strings_invalid_itn").html());
            $ITNField.addClass("highlighted");
            return false;
        }
        return true;
    });
});