$(function(){
    $(".agerestrict input.confirm").on("click",function(){
        var date = new Date();
        date.setDate(date.getDate() + 180);
        document.cookie = "majority=1; expires=" + date.toUTCString() + "; path=/";
        document.location.reload(true);
    });
    $(".agerestrict input.deny").on("click",function(){
        $(this).closest("table")
            .find("tr.confirm, tr.buttons input").remove().end()
            .find("tr.deny").show();
    });
});