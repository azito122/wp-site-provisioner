(function($) {
$(document).ready(function() {
    $(document).on( 'click', '.entity.form .save.button', function(e) {
        let element = e.target;
        var isnew = $('.new-entity').has(element).length > 0 ? true : false;

        if (isnew) {
            callback = function(response, status) {
                if (response && response['rerendered']) {
                    rerenderel = WPSP.rerendermap[response['rerenderid']];
                    $(rerenderel).remove();
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

    $(document).on( 'click', '.query-params.wrapper > .add-button', function( e ) {
        WPSP.renderEntity('QueryParam', $(e.target).parent().children('.params-list') );
    })

    $(document).on( 'click', '.entity.form[entity-type="query-response-map"] > .add-button', function( e ) {
        WPSP.renderEntity('QueryResponseMapping', $(e.target).parent().children('.mappings-list') );
    })

    $('.page-entities > .add-button').on( 'click', function( e ) {
        if ( $('.new-entity').has('.entity').length > 0 ) {
            return;
        }
        entitytype = $(e.target).attr( 'entity-type' );
        WPSP.renderNewEntity(entitytype);
    })

})
})(jQuery)