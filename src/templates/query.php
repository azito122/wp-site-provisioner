<div class="entity form storable" entity-type="query">
    <?php echo $R::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $R::select( array(
        'name' => 'remoteid',
        'label' => 'Remote',
        'options' => $D->get( 'remotes'),
        'default' => $D->get( 'remoteid' )
    ) ); ?>

    <?php echo $R::textinput( array(
        'name'        => 'path',
        'label'       => 'Path',
        'default'     => $D->get( 'path' ),
        'placeholder' => 'Path',
    ) ); ?>

    <?php echo $D->get( 'params' ) ?>

    <?php echo $D->get( 'response' ) ?>
</div>