<div class="form entity response-mapping">
    <?php echo $R::textinput( array(
        'name'        => 'localkey',
        'default'     => $D->get( 'localkey' ),
        'placeholder' => 'Local Key',
    ) ); ?>
    =>
    <?php echo $R::textinput( array(
        'name'        => 'responsekey',
        'default'     => $D->get( 'responsekey' ),
        'placeholder' => 'Response Key',
    ) ); ?>
</div>