(function($) {
$(document).ready(function() {
    console.log("grouptypes code loaded");

    $(document).on( 'click', '.save.button', function(e) {
        form = $(e.target).parent();
        derendered = WPSP.derender(form);
        console.log(derendered);
        WPSP.store( 'Remote', derendered);
    })

    $('.add-group').on( 'click', function( e ) {
        WPSP.render('group-type', '.group-types');
    })

    $('.add-remote').on( 'click', function( e ) {
        console.log('add remote');
        WPSP.renderEntity('Remote', '.remotes');
    })

})
})(jQuery)
