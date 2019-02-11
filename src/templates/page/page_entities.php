<div class="wpsp-page page-entities <?php echo strtolower( $D->get( 'entity-type' ) ) ?>">
    <div class="existing-entities">
        <?php echo $D->get( 'existing-entities' ); ?>
    </div>
    <a href="<?php echo $D->get( 'add-button-href' ); ?>">
        <button
            class="add-button add-<?php echo strtolower( $D->get( 'entity-type' ) ) ?> button"
            type="button"
            entity-type="<?php echo $D->get( 'entity-type' ) ?>"
        >+ Add <?php echo $D->get( 'entity-type-name' ) ?></button>
    </a>
    <div class="new-entity"></div>
</div>