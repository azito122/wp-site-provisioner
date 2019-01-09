<div class="form entity query-param">
    <?php echo $R::textinput( array(
        'name'        => 'key',
        'default'     => $D->get( 'key' ),
        'placeholder' => 'Key',
    ) ); ?>
    =>
    <?php echo $R::textinput( array(
        'name'        => 'value',
        'default'     => $D->get( 'value' ),
        'placeholder' => 'Value',
    ) ); ?>
</div>