<div>
    <h4><?php echo $D->get( 'name' ); ?></h4>
    <?php echo $W::select( [
        'name' => 'possible-meta',
        // 'label' => 'Remote',
        'options' => $D->get( 'possible-metas' ),
        // 'default' => $D->get( 'remoteid' )
    ] ); ?>
</div>