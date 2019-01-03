<div class="entity form" entity-type="query">
    <?php echo $R::hidden( 'storeid', $D->get( 'storeid' ) ); ?>
    <?php echo $R::textinput( array(
        'name'        => 'label',
        'label'       => 'Label',
        'default'     => $D->get( 'label' ),
        'placeholder' =>'Label',
        'required'    => true,
    ) ); ?>
    <!-- <?php echo $R::select( 'remotes', 'Remote', $D->get( 'remotes'), $D->get( 'remoteid' ) ); ?> -->
</div>