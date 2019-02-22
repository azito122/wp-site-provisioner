(function($) {

$(document).ready(function() {
    $(document).on( 'input', '.devalidated', function(e) {
        $(e.target).removeClass('devalidated');
    });
})

WPSP = {};

WPSP.rerendermap = {};

WPSP.ajax = function(args) {
    let defaultargs = {
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
    }
    $.ajax( Object.assign(defaultargs, args) );
}

WPSP.rerender = function(rerenderid, rerendered) {
    rerenderel = WPSP.rerendermap[rerenderid];
    $(rerenderel).replaceWith(rerendered);
}

WPSP.alterEntity = function(entity, action, extra) {
    let entitytype = WPSP.getEntityType(entity);
    let derendered = WPSP.derender(entity);
    let rerenderid = Math.random();
    WPSP.rerendermap[rerenderid] = entity;

    let ajaxargs = {
        data: {
            rerenderid: rerenderid,
        },
    };

    if ( action == 'addSubEntity' ) {
        let subentitytype = extra;
        if ( subentitytype == 'SingleSiteEngine' && entitytype == 'group' ) {
            ajaxargs.data.action = 'wpsp_addsinglesiteengine';
        }
    }

    ajaxargs.data.data = JSON.stringify(derendered);
    Object.assign(ajaxargs, {
        success: function(response, status) {
            console.log(response);
            if (status == 'success' && response && response['rerendered']) {
                WPSP.rerender(response['rerenderid'], response['rerendered']);
            }
        }
    });

    WPSP.ajax(ajaxargs);
}

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
            console.log(response);
            if(status == 'success') {
                $(selector).append(response);
            }
        }
    })
}

WPSP.renderNewEntity = function(entitytype) {
    WPSP.renderEntity(entitytype, '.new-entity');
}

WPSP.storeEntity = function(element, callback = function(){}, rerender = true) {
    if ( ! WPSP.validate( element ) ) {
        return;
    }
    derendered = WPSP.derender(element);
    console.log('derendered:', derendered);
    rerenderid = '';
    if (rerender) {
        rerenderid = Math.random();
        WPSP.rerendermap[rerenderid] = element;
    }
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        // dataType: "JSON",
        data: {
            type: $(element).attr('entity-type'),
            action: 'wpsp_store',
            data: JSON.stringify(derendered),
            rerenderid: rerenderid
        },
        success: callback,
        error: function(xhr, status, error) {
            console.log(xhr, status, error);
        }
    })
}

WPSP.makeGroup = function(grouptypeid, metaid) {
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        data: {action: 'wpsp_makegroup', grouptypeid: grouptypeid, metaid: metaid},
        error: function(xhr, status, error) {
            console.log(xhr, status, error);
        },
        success: function(response) {
            console.log(response);
        }
    })
}

WPSP.validate = function(element) {
    let check = true;
    $.each($(element).children(), function(i, el) {
        el = WPSP.resolveFormElement(el);
        if ( $(el).hasClass( 'sub-entity' ) ) {
            check = WPSP.validate( $(el).children('.entity')[0] );
        }
        let required = $(el).attr('required');
        let val = WPSP.getValue(el);
        if (required && ( ! val || val == '' ) ) {
            check = false;
            $(el).addClass('devalidated');
        }
    })
    return check;
}

WPSP.getValue = function(e) {
    let tag;
    if ( $(e).hasClass('form-wrapper') ) {
        if ( $(e).children('input').length > 0 ) {
            tag = 'INPUT';
        } else if ( $(e).children('select').length > 0 ) {
            tag = 'SELECT';
        }
    } else {
        tag = $(e).prop('tagName');
    }
    if (tag === 'INPUT') {
        return $(e).val();
    } else if (tag === 'SELECT') {
        return e.options[e.selectedIndex].value;
    }
}

WPSP.resolveFormElement = function(el) {
    if ( $(el).hasClass('form-wrapper') ) {
        el = $(el).children('input,select')[0];
    }
    if ( typeof $(el).attr('name') === 'string' ) {
        return el;
    }
}

WPSP.derender = function(html) {
    var shadow = $(html).clone()
    var result = {};
    $.each($(html).children(), function(i, e) {
        e = WPSP.resolveFormElement(e)
        if (e) {
            var name = $(e).attr('name');
            var datatype = $(e).attr('data-type');
            if (datatype == 'subentity') {
                result[name] = WPSP.derender($(e).children('.entity'));
            } else if (datatype == 'array') {
                var dataarrayselector = $(e).attr('data-array-selector');
                var dataarray = [];
                $.each($(e).find(dataarrayselector), function(i, e) {
                    dataarray.push(WPSP.derender(e));
                });
                result[name] = dataarray;
            } else {
                result[name] = WPSP.getValue(e);
            }
        }
    })
    return result;
}

WPSP.getEntityType = function(entity) {
    return $(entity).attr('entity-type');
}

WPSP.getElementByStoreId = function(id) {
    return $('input[name="storeid"][value="' + id + '"]').parent();
}

})(jQuery)