(function($) {

WPSP = {};

WPSP.render = function(template, selector) {
    console.log(WPSP_AJAX.ajaxurl);
    $.ajax({
        type: 'post',
        url: WPSP_AJAX.ajaxurl,
        dataType: 'html',
        data: {type: 'entity', entity: 'GroupType', action: 'wpsp_render'},
        error: function(jqxhr, status, exception) {
            console.log(jqxhr);
            console.log(exception);
        },
        success: function(response, status) {
            console.log("status:" + status);
            console.log(response);
            if(status == 'success') {
                $(selector).append(response);
            }
        }
    })
}

$(document).ready(function() {

    $('.save.button').on( 'click', function(e) {
        form = $(e).parent();
        derendered = WPSP.derender(form);
        WPSP.store(derendered);
    })

    $('.add-group').on( 'click', function( e ) {
        WPSP.render('group-type-form', '.group-types');
    })

})

})(jQuery)