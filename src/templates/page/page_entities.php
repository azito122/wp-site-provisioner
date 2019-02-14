<div class="wpsp-page page-entities <?php echo strtolower( $D->get( 'entity-type' ) ) ?>">
    <h1><?php echo $D->get( 'page-title' ) ?></h1>
    <div class="existing-entities">
        <?php echo $D->get( 'existing-entities' ); ?>
    </div>
    <?php echo $W::button(
        '+ Add ' . $D->get( 'entity-type-name' ),
        $D->get( 'add-button-href' ),
        [
            'class' => 'add-button add-' . $D->get( 'entity-type' ),
            'entity-type' => $D->get( 'entity-type' ),
        ]
        ); ?>
    <div class="new-entity"></div>
</div>