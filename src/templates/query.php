<div class="entity form wpsp-wrapper" entity-type="query">
    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::select( array(
        'name' => 'remoteid',
        'label' => 'Remote',
        'options' => $D->get( 'remotes'),
        'default' => $D->get( 'remoteid' )
    ) ); ?>

    <?php echo $W::textinput( array(
        'name'        => 'path',
        'label'       => 'Path',
        'default'     => $D->get( 'path' ),
        'placeholder' => 'Path',
    ) ); ?>

    <?php echo $D->get( 'params' ) ?>

    <div datakey="response" datatype="subentity">
        <?php echo $D->get( 'response' ) ?>
    </div>
</div>