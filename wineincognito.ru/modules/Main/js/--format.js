$(function(){
    var format = [];

    function getFormat($elem){
        var result = null;
        $.each(format, function(key,val)){
            if(val[0].eq($elem)){
                result = val[1];
                return false;//break
            }
        }
        if(result===null){
            result = generateFormat($elem);
            format.push([$elem,result]);
        }
        return result;
    }
    function generateFormat($elem){
        var format = $elem.data("format");
        console.log(format);
        return null;
    }
    $(document).on("keyup","input[data-format]",function(){
        var $this = $(this);
        var regexr = new RegExp("^" + $this.data("format") + "$");
        if($this.val()!="" && !regexr.test($this.val())){
            $this.val($this.data("format-prev-val"));
            return;
        }
        $this.data("format-prev-val",$this.val());
    });
});