$(document).ready(function() {
    $('.image-thumb').hover(function() {
        var src = $(this).attr('src');
        src = src.replace('._SX38_SY50_CR,0,0,68,80_', '._AA500_');
        $('.image-large').attr('src', src);
        $('.image-thumb').removeClass('image-thumb-active');
        $(this).addClass('image-thumb-active');
    });
});