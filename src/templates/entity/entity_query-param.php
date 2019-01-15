<div class="entity form" entity-type="query-param">
    <?php echo $W::textinput( array(
        'name'        => 'key',
        'default'     => $D->get( 'key' ),
        'placeholder' => 'Key',
    ) ); ?>
    =>
    <?php echo $W::textinput( array(
        'name'        => 'value',
        'default'     => $D->get( 'value' ),
        'placeholder' => 'Value',
    ) ); ?>
</div>