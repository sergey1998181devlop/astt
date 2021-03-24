$(document).ready(function(){
    $('.categories-list input').bind("keydown", function(e) {
        var ths = $(this);
        var keyKode = e.keyCode;
        if (keyKode>95 && keyKode<106 || keyKode>47 && keyKode<58 || keyKode == 8 || keyKode == 46 || keyKode ==9) { } else return false;
    });

});
