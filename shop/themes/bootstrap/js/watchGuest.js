$(function() {
    $('.watch-click').click(function() {
        return false;
    });
    $('.watch-form .btn-primary').live('click', function() {
        var watchForm = $(this.form);
        var params = {
            'ASIN': $(this).attr('tag'),
            'FirstName': watchForm.find('input[name="FirstName"]').val(),
            'Email': watchForm.find('input[name="Email"]').val()
        };
        
        $.ajax({
            type: "POST",
            url: watchForm.attr('action'),
            data: params,
        }).done(function(msg) {
            if (msg && msg.error) {
                var errText = [];
                var i = 0;
                for (var attr in msg.error) {
                    errText[i] = msg.error[attr].join();
                    i++;
                }
                watchForm.find('div.text-error').removeClass('hide').html('<div>'+errText.join('</div><div>')+'</div>');
            }
            if(msg && msg.ok){
                watchForm.find('div.text-error').removeClass('hide').html('<div class="text-success">Successfully added to watch!<br>We will email to you when price is dropped.</div>');
                watchForm.find('.watch-body').hide();
                setTimeout(function(){
                    var elId = watchForm.find('.btn-primary').attr('tag');
                    $('#' + elId).popover('hide');
                }, 8000)
            }    

        });
    })
    $('.form-horizontal .btn-warning').live('click', function() {
        var elId = $(this).prev().attr('tag');
        $('#' + elId).popover('hide');
    })
    $('.watch-click').each(function() {
        var el = $(this);
        var id = el.attr('id');
        $('.watch-form-body .btn-primary').attr('tag', id);
        el.popover({"html": true, "content": $('.watch-form-body').html(), "placement": "bottom"});
    });
});