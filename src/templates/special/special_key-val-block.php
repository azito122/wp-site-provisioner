<div class="entity key-val-block" entity-type="<?php echo $D->get( 'entity-type' ); ?>">
    <?php echo $W::textinput( array(
        'name'        => $D->get( 'key_name' ),
        'default'     => $D->get( 'key' ),
        'placeholder' => $D->get( 'key_label' ),
    ) ); ?>
    <span class="key-val-separator">=></span>
    <?php echo $W::textinput( array(
        'name'        => $D->get( 'value_name' ),
        'default'     => $D->get( 'value' ),
        'placeholder' => $D->get( 'value_label' ),
    ) ); ?>
</div>