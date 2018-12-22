(function($) {

WPSP = {};

WPSP.render = function(template, selector) {
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        dataType: 'html',
        data: {type: 'entity', entity: 'GroupType', action: 'wpsp_render'},
        success: function(response, status) {
            if(status == 'success') {
                $(selector).append(response);
            }
        }
    })
}

WPSP.getValue = function(e) {
    var tag = $(e).prop('tagName');
    if (tag === 'INPUT') {
        return $(e).val();
    } else if (tag === 'SELECT') {
        $.foreach($(select).children('option'), function(i, e) {
            if ($(e).attr('selected') === 'selected') {
                return $(e).val();
            }
        })
    }
}

WPSP.derender = function(html) {
    var result = {};
    $.each($(html).children(), function(i, e) {
        var datakey = $(e).attr('datakey');
        if (typeof datakey === 'string') {
            result[datakey] = WPSP.getValue(e);
        }
    })
    console.log(result);
}

})(jQuery)