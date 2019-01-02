(function($) {
$(document).ready(function() {
    console.log("grouptypes code loaded");

    $(document).on( 'click', '.save.button', function(e) {
        form = $(e.target).parent();
        derendered = WPSP.derender(form);
        // console.log(derendered);
        // WPSP.store(derendered);
    })

    $('.add-group').on( 'click', function( e ) {
        WPSP.render('group-type', '.group-types');
    })

})
})(jQuery)
