(function($) {

$(document).ready(function() {
    $(document).on( 'input', '.devalidated', function(e) {
        $(e.target).removeClass('devalidated');
    });
})

WPSP = {};

WPSP.renderEntity = function(entitytype, selector, id) {
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        dataType: 'html',
        data: {
            action: 'wpsp_render',
            entity: entitytype,
            entityid: id,
            rendertype: 'entity',
        },
        success: function(response, status) {
            if(status == 'success') {
                $(selector).append(response);
            }
        }
    })
}

WPSP.renderNewEntity = function(entitytype) {
    WPSP.renderEntity(entitytype, '.new-entity');
}

// WPSP.rerenderEntity = function(element, id) {
//     type = $(element).attr('entity-type');
//     $.ajax({
//         type: 'post',
//         url: WPSP_AJAX.ajaxurl,
//         dataType: 'html',
//         element: element,
//         data: {type: 'entity', entity: type, entityid: id, action: 'wpsp_render'},
//         success: function(response, status) {
//             if(status == 'success') {
//                 $(this.element).replaceWith(response);
//             }
//         }
//     })
// }

WPSP.storeEntity = function(element) {
    if ( ! WPSP.validate( element ) ) {
        return;
    }
    derendered = WPSP.derender(element);
    console.log(derendered);
    isnew = $('.new-entity').has(element).length > 0 ? true : false;
    if ( isnew ) {
        success = function(response, status) {
            console.log(response);
            if (response && response['rerendered']) {
                type = $(element).attr('entity-type');
                $(element).remove();
                $('.existing-entities').append(response['rerendered']);
            }
        }
    } else {
        success = function(response, status) {
            id = response['id'];
            $('input[type="hidden"][value="' + id + '"]').parent().replaceWith(response['rerendered']);
        }
    }
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        data: {type: $(element).attr('entity-type'), action: 'wpsp_store', data: derendered, rerender: isnew ? false : true},
        success: success,
        error: function(xhr, status, error) {
            console.log(xhr, status, error);
        }
    })
}

WPSP.validate = function(element) {
    let check = true;
    $.each($(element).children(), function(i, e) {
        let required = $(e).attr('required');
        let val = WPSP.getValue(e);
        if (required && ( ! val || val == '' ) ) {
            check = false;
            $(e).addClass('devalidated');
        }
    })
    return check;
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
        if ($(e).hasClass('sub-entity')) {
            result[datakey] = WPSP.derender($(e).children('.entity.form'));
        } else if (typeof datakey === 'string') {
            result[datakey] = WPSP.getValue(e);
        }
    })
    return result;
}

})(jQuery)