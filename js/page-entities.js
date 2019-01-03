(function($) {
$(document).ready(function() {
    $(document).on( 'click', '.entity.form .save.button', function(e) {
        WPSP.storeEntity( $(e.target).parent() );
    })

    $('.add-button').on( 'click', function( e ) {
        entitytype = $(e.target).attr( 'entity-type' );
        WPSP.renderEntity(entitytype, '.new-entity');
    })

})
})(jQuery)
