<form class="query">
    <?php echo $R::textinput( 'query-label', 'Label', $D->get( 'query-label' ), 'Label' ); ?>
    <?php echo $R::entity( $D->get( 'remote' ) ); ?>
</form>