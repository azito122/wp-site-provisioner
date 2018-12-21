<div class="query form">
    <?php echo $R::textinput( 'query-label', 'Label', 'Label', $D->get( 'query-label' ) ); ?>
    <?php echo $R::select( 'remotes', 'Remote', $D->get( 'remotes'), $D->get( 'remoteid' ) ); ?>
</div>