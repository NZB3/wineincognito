$(function(){
    if(typeof(ajaxRequest)!=='undefined' && ajaxRequest instanceof Function){
        $companyMailSettingsFormChangeImageFile = $(".company-mail-settings-form .logo #company-mail-settings-form-change-image-file");
        $(".company-mail-settings-form .company-mail-settings-form-change-image").on("click",function(){
            $(this).closest("td").find(".company-mail-settings-form-change-image-file").trigger("click");
        });
        $(".company-mail-settings-form .company-mail-settings-form-change-image-file").on('change', function(){
            var $this = $(this);
            var companyId = $this.closest("table").data("company-id");
            var formData = new FormData();
            if(!$this[0].files.length){
                return;
            }
            var file = $this[0].files[0];
            if(!file.type.match(/(.jpeg)|(.jpg)$/i) || file.size > 50*1024){
                return;
            } 
            formData.append("image", file);
            formData.append("type", $this.data("type"));
            $this[0].value = "";
            ajaxRequest("/ajax/company/" + companyId + "/mailsettings/image/upload", formData, function(data){//success
                setImage($this.closest("td"),data["file_url"]);
            });
        });
        function setImage($td,url){
            var $img = $td.find("input[type=hidden]").val(url).end().find("img");
            if(url){
                $img.prop("src",url).removeClass("no-image");
            } else {
                $img.removeAttr("src").addClass("no-image");
            }
        }
        $(".company-mail-settings-form .company-mail-settings-form-change-image-remove").on("click",function(){
            setImage($(this).closest("td"),null);
        });


        $(".company-mail-settings-form .company-mail-settings-form-reset-default").on("click",function(){
            var $this = $(this);
            var $table = $this.closest("tbody");
            setImage($table.find("input[name=header_logo_url]").closest("td"),$this.data("header-logo-url"));
            setImage($table.find("input[name=footer_logo_url]").closest("td"),$this.data("footer-logo-url"));
            $table.find("input[name=text_color]").val($this.data("text-color"));
            $table.find("input[name=anchor_color]").val($this.data("anchor-color"));
            $table.find("input[name=header_background_color]").val($this.data("header-background-color"));
            $table.find("input[name=footer_background_color]").val($this.data("footer-background-color"));
        });



        $(".company-mail-settings-form .company-mail-settings-form-color").mask("cccccc", {
            translation: {
                'c': {
                    pattern: /[0-9a-f]/, optional: false
                },
            },
            clearIfNotMatch: true,
            placeholder:"______"
        });
    }
});