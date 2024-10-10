$(function(){
    var match = window.location.href.match(/[?&]redir=([^&]+)/i);
    if(match!==null){
        setTimeout(function(){
            window.location.replace(decodeURIComponent(match[1]));
        },3000);
    }
});