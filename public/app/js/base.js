var Select2ValidateFix = function (selector_id) {
    
    // This is a workaround for using jquery-validation-engine with select2 for 'required' validation
    // Since the _required validator for jquery-validation-engine uses .val() to see
    // if there's anything in the input, we can hijack .val() for the container created by select2\
    // and redirect it to the value of the hidden element

    // jquery-validation-engine throws an error if the thing we're validating doesn't have an id
    // so we'll put one on the container created by select2 (that way, the positioning of the prompts
    // will be correct)
    $('#' + selector_id).select2('container').attr('id', 'mySelectorValidate');

    // Mostly lifted from http://stackoverflow.com/questions/6731153/what-are-jquery-valhooks
    $.fn.setTypeForHook = function () {
        this.each(function () {
            this.type = selector_id;
        });
        return this;
    };

    $('#' + selector_id).select2('container').setTypeForHook();

    // With the 'type' set, we can add a valhook to redirect .val() for the container
    // to .val() from the hidden input
    // select2 sets up its own 'val' method, so we'll use that in this case
    $.valHooks[selector_id] = {
        get: function (el) {
            return $('#' + selector_id).select2("val");
        },
        set: function (el, val) {
            $('#' + selector_id).select2("val", val);
        }
    };
    
}

function update_rule(rule_id, key, value) {
    $.ajax({
        url: '/ajax/update_rule/',
        data: 'id=' + rule_id + '&key=' + key + '&value=' + value,
        type: 'GET',
        async: false,
        dataType: 'json',
        success: function(data) {
            if(key != 'weight' && key != 'active') {
                $('#opt_'+rule_id+'_'+key).removeClass('text-muted');
                $('#opt_'+rule_id+'_'+key).removeClass('text-success');

                if(value == 0) {
                    $('#opt_'+rule_id+'_'+key).addClass('text-muted');
                    $('#optlink_'+rule_id+'_'+key).attr("onclick","update_rule(" + rule_id +",'" + key + "', 1)");
                } else {
                    $('#opt_'+rule_id+'_'+key).addClass('text-success');
                    $('#optlink_'+rule_id+'_'+key).attr("onclick","update_rule(" + rule_id +",'" + key + "', 0)");
                }
            } else if(key == 'active') {
                $('#opt_'+rule_id+'_'+key).removeClass('label-warning');
                $('#opt_'+rule_id+'_'+key).removeClass('label-success');

                if(value == 0) {
                    $('#opt_'+rule_id+'_'+key).addClass('label-warning');
                    $('#optlink_'+rule_id+'_'+key).attr("onclick","update_rule(" + rule_id +",'" + key + "', 1)");
                } else {
                    $('#opt_'+rule_id+'_'+key).addClass('label-success');
                    $('#optlink_'+rule_id+'_'+key).attr("onclick","update_rule(" + rule_id +",'" + key + "', 0)");
                }
            }
        }
    });
}