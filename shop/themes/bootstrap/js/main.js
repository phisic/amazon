$(function() {
    $(document).on('click', '#loginWithAjax', function() {
        if (_formValidation('#login-form')) {
            jQuery.ajax({'type': 'POST',
                'url': '/site/ajaxLogin',
                'cache': false,
                'data': jQuery(this).parents("form").serialize(),
                'success': function(data) {
                    if (data.success) {
                        location.href = data.url;
                    } else {
                        alert('Incorrect username or password');
                    }
                },
                'dataType': 'json'
            });
        }
        return false;
    });

    $(document).on('click', '#registerWithAjax', function() {
        if (_formValidation('#register-form')) {
            jQuery.ajax({'type': 'POST',
                'url': '/site/ajaxRegister',
                'cache': false,
                'data': jQuery(this).parents("form").serialize(),
                'success': function(data) {
                    if (data.success) {
                        location.href = data.url;
                    } else {
                        alert('Incorrect username or password');
                    }
                },
                'dataType': 'json'
            });
        }
        return false;
    });

    var _formValidation = function($id) {
        var $form = $($id);
        var hasError = false;
        var settings = $form.data("settings");
        $.each(settings.attributes, function() {
            this.status = 2;
        });
        $form.data("settings", settings);
        // trigger ajax validation
        $.fn.yiiactiveform.validate($form, function(data) {
            $.each(settings.attributes, function() {
                hasError = $.fn.yiiactiveform.updateInput(this, data, $form) || hasError;
            });
            $.fn.yiiactiveform.updateSummary($form, data);
        });

        return !hasError;
    }

    //Watch Price form
    $('.watch-click').click(function() {
        return false;
    });
    $('.watch-form .btn-primary').live('click', function() {
        alert(1);
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