<div class="entity form storable" entity-type="query">
    <?php echo $R::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $R::textinput( array(
        'name'        => 'label',
        'label'       => 'Label',
        'default'     => $D->get( 'label' ),
        'placeholder' =>'Label',
        'required'    => true,
    ) ); ?>

    <?php echo $R::select( array(
        'name' => 'remoteid',
        'label' => 'Remote',
        'options' => $D->get( 'remotes'),
        'default' => $D->get( 'remoteid' )
    ) ); ?>

    <?php echo $D->get( 'params' ) ?>

    <?php echo $D->get( 'response' ) ?>
</div>