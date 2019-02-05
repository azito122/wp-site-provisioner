<div class="wpsp-page">
    <div class="existing-entities">
        <?php echo $D->get( 'existing-entities' ); ?>
    </div>
    <button
        class="add-button add-<?php echo strtolower( $D->get( 'entity-type' ) ) ?> button"
        type="button"
        entity-type="<?php echo $D->get( 'entity-type' ) ?>"
    >+ Add <?php echo $D->get( 'entity-type-name' ) ?></button>
    <div class="new-entity"></div>
</div>