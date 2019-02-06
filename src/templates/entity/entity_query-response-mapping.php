<div class="entity key-val-block" entity-type="query-response-mapping">
    <?php echo $W::textinput( array(
        'name'        => 'localkey',
        'default'     => $D->get( 'localkey' ),
        'placeholder' => 'Local Key',
    ) ); ?>
    <span class="key-val-separator">=></span>
    <?php echo $W::textinput( array(
        'name'        => 'responsekey',
        'default'     => $D->get( 'responsekey' ),
        'placeholder' => 'Response Key',
    ) ); ?>
</div>