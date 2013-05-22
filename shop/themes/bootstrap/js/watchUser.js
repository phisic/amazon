$(function() {
    $('.watch-click').on('click', function() {
        var el = $(this);
        if(el.hasClass('in-watch'))
            return false;
        
        var params = {
            'ASIN': el.attr('id'),
        };
        
        $.ajax({
            type: "POST",
            url: watchUrl,
            data: params,
        }).done(function(msg) {
            if(msg && msg.ok){
                el.text('In Watch');
                el.removeClass('watch-click').addClass('in-watch');
            }    
        });
        return false;
    });
    
    $('.match-cpu').on('change', function(){
        var el = $(this);
        var params = {
            'ASIN': el.attr('id'),
            'part': el.val(),
        };
        $.ajax({
            type: "POST",
            url: matchUrl,
            data: params,
        }).done(function(msg) {
            if(msg && msg.ok){
                el.addClass('text-success');
            }else
                el.addClass('text-warning');
        });
    })
})