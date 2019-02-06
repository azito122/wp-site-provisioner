<div class="entity" entity-type="query-response-mapping">
    <?php echo $W::textinput( array(
        'name'        => 'localkey',
        'default'     => $D->get( 'localkey' ),
        'placeholder' => 'Local Key',
    ) ); ?>
    =>
    <?php echo $W::textinput( array(
        'name'        => 'responsekey',
        'default'     => $D->get( 'responsekey' ),
        'placeholder' => 'Response Key',
    ) ); ?>
</div>