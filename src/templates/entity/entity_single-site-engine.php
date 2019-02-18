<div class="entity form-block" entity-type="site-engine">
    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::textinput( array(
        'name'        => 'label',
        'default'     => $D->get( 'label', 'A New Site Engine' ),
        'placeholder' => 'Label',
        'class'       => 'label-input'
    ) ); ?>

    <a href="<?php echo $D->get( 'site-url' ); ?>"><?php echo $D->get( 'site-url', '(no site yet)' ); ?></a>

    <span><?php echo $D->get( 'owner', '(no owner)' ); ?></span>

    <button class="save button">Save</button>
</div>