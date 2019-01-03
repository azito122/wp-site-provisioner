(function($) {

WPSP = {};

WPSP.renderEntity = function(entity, selector) {
    console.log('prep ajax for rendering ' + entity);
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        dataType: 'html',
        data: {type: 'entity', entity: entity, action: 'wpsp_render'},
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
    // console.log(result);
    return result;
}

WPSP.store = function(type, data) {
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        // dataType: 'html',
        data: {type: type, action: 'wpsp_store', data: data},
        success: function(response, status) {
            console.log(status, response);
        }
    })
}

})(jQuery)