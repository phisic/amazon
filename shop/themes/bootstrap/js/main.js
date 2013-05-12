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

    $('#searchbox').typeahead({
        items: 10,
        //minLength: 3,
        matcher: function () { return true; },
        source: function(query, process) {
            return $.getJSON(
                    $('#searchbox-form').attr('action'),
                    {search: query},
            function(data) {
                return process(data);
            });
        }

    });

});