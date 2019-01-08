(function($) {
$(document).ready(function() {
    $(document).on( 'click', '.entity.form .save.button', function(e) {
        let element = e.target;
        var isnew = $('.new-entity').has(element).length > 0 ? true : false;
        // var issub = $(element).parent().parent().hasClass('sub-entity');

        if (isnew) {
            callback = function(response, status) {
                console.log(response);
                if (response && response['rerendered']) {
                    rerenderel = WPSP.rerendermap[response['rerenderid']];
                    // $(rerenderel).remove();
                    $('.existing-entities').append(response['rerendered']);
                }
            }
        } else {
            callback = function(response, status) {
                rerenderel = WPSP.rerendermap[response['rerenderid']];
                $(rerenderel).replaceWith(response['rerendered']);
            }
        }

        WPSP.storeEntity( $(element).parent()[0], callback);
    })

    $('.add-button').on( 'click', function( e ) {
        if ( $('.new-entity').has('.entity').length > 0 ) {
            return;
        }
        entitytype = $(e.target).attr( 'entity-type' );
        WPSP.renderNewEntity(entitytype);
    })

})
})(jQuery)