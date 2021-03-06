(function($) {
$(document).ready(function() {
    $(document).on( 'click', '.entity .save.button', function(e) {
        let element = e.target;
        var isnew = $('.new-entity').has(element).length > 0 ? true : false;

        if (isnew) {
            callback = function(response, status) {
                console.log(response);
                if (response && response['rerendered']) {
                    rerenderel = WPSP.rerendermap[response['rerenderid']];
                    $(rerenderel).remove();
                    $('.existing-entities').append(response['rerendered']);
                    $(element).removeClass('loading');
                }
            }
        } else {
            callback = function(response, status) {
                console.log(response);
                rerenderel = WPSP.rerendermap[response['rerenderid']];
                $(rerenderel).replaceWith(response['rerendered']);
                $(element).removeClass('loading');
            }
        }

        $(element).addClass('loading');
        WPSP.storeEntity( $(element).parent()[0], callback);
    })

    $(document).on( 'click', '.entity[entity-type="query-params"] > .add-button', function( e ) {
        WPSP.renderEntity('QueryParam', $(e.target).parent().children('.params-list') );
    })

    $(document).on( 'click', '.entity[entity-type="query-response-map"] > .add-button', function( e ) {
        WPSP.renderEntity('QueryResponseMapping', $(e.target).parent().children('.mappings-list') );
    })

    $(document).on( 'click', '.entity[entity-type="group"] .add-button', function( e ) {
        WPSP.alterEntity($(e.target).parents('.entity'), 'addSubEntity', 'SingleSiteEngine');
    })
    $(document).on( 'click', '.entity[entity-type="single-site-engine"] .trash', function(e) {
        WPSP.removeSiteEngine($(e.target).parents('.entity[entity-type="group"]'), $(e.target).parents('.entity[entity-type="single-site-engine"]'));
    })

    $(document).on( 'click', '.page-entities .add-button[entity-type]', function( e ) {
        if ( $('.new-entity').has('.entity').length > 0 ) {
            return;
        }
        entitytype = $(e.target).attr( 'entity-type' );
        WPSP.renderNewEntity(entitytype);
    })

    $(document).on( 'change', 'select[name=possible-metas]', function(e) {
        var metaid = WPSP.getValue(e.currentTarget);
        var grouptypeid = $(e.currentTarget).parents('.group-type-block').attr('group-type-id');

        window.location.assign('?action=new-group&grouptypeid=' + grouptypeid + '&metaid=' + metaid);
    })

})
})(jQuery)