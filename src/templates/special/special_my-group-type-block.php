<div class="group-type-block" group-type-id="<?php echo $D->get( 'group-type-id' ); ?>">
    <h4><?php echo $D->get( 'name' ); ?></h4>
    <?php echo $W::select( [
        'name' => 'possible-metas',
        // 'label' => 'Remote',
        'options' => $D->get( 'possible-metas' ),
        // 'default' => $D->get( 'remoteid' )
    ] ); ?>
</div>