(function($) {

$(document).ready(function() {
    $(document).on( 'input', '.devalidated', function(e) {
        $(e.target).removeClass('devalidated');
    });
})

WPSP = {};

WPSP.rerendermap = {};

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
                console.log(selector);
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

WPSP.storeEntity = function(element, callback = function(){}, rerender = true) {
    if ( ! WPSP.validate( element ) ) {
        return;
    }
    // $.each( $(element).children(), function(i,e) {
    //     if ( $(e).hasClass('sub-entity') ) {
    //         var entity = $(e).children('.entity')[0];
    //         WPSP.storeEntity(entity, function(response, status) {
    //             rerenderel = WPSP.rerendermap[response['rerenderid']];
    //             $(rerenderel).replaceWith(response['rerendered']);
    //         });
    //     }
    // })
    derendered = WPSP.derender(element);
    console.log(derendered);
    rerenderid = '';
    if (rerender) {
        rerenderid = Math.random();
        WPSP.rerendermap[rerenderid] = element;
    }
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        data: {type: $(element).attr('entity-type'), action: 'wpsp_store', data: derendered, rerenderid: rerenderid},
        success: callback,
        error: function(xhr, status, error) {
            console.log(xhr, status, error);
        }
    })
}

WPSP.store = function( derendered, callback = function(){}, rerender ) {

}

WPSP.validate = function(element) {
    let check = true;
    $.each($(element).children(), function(i, e) {
        if ( $(e).hasClass( 'sub-entity' ) ) {
            check = WPSP.validate( $(e).children('.entity')[0] );
        }
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
        $.each($(e).children('option'), function(i, e) {
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
            var datatype = $(e).attr('datatype');
            if (datatype == 'subentity') {
                result[datakey] = WPSP.derender($(e).children('.entity.form'));
            } else if (datatype == 'array') {
                console.log('data array!');
                var dataarrayselector = $(e).attr('dataarrayselector');
                console.log('data array selector: ' + dataarrayselector);
                var dataarray = [];
                console.log('data array for', e);
                $.each($(e).find(dataarrayselector), function(i, e) {
                    dataarray.push(WPSP.derender(e));
                });
                result[datakey] = dataarray;
            } else {
                result[datakey] = WPSP.getValue(e);
            }
        }
    })
    return result;
}

WPSP.getElementByStoreId = function(id) {
    return $('input[datakey="storeid"][value="' + id + '"]').parent();
}

})(jQuery)