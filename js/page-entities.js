(function($) {
$(document).ready(function() {
    $(document).on( 'click', '.entity.form .save.button', function(e) {
        WPSP.storeEntity( $(e.target).parent() );
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
