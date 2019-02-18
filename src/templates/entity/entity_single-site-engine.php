<div class="entity form-block" entity-type="single-site-engine">
    <span class="entity-type-label">Site Engine</span>
    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::textinput( array(
        'name'        => 'label',
        'default'     => $D->get( 'label', 'A New Site Engine' ),
        'placeholder' => 'Label',
        'class'       => 'label-input'
    ) ); ?>

    <a href="<?php echo $D->get( 'site-url' ); ?>"><?php echo $D->get( 'site-url', '(no site yet)' ); ?></a>

    <span><?php echo $D->get( 'owner', '(no owner)' ); ?></span>

    <?php echo $W::textinput( [
        'name'          => 'site-path',
        'default'       => $D->get( 'site-path', '' ),
        'placeholder'   => 'Site Path',
    ] ); ?>

    <?php echo $W::textinput( [
        'name'          => 'site-title',
        'default'       => $D->get( 'site-title', '' ),
        'placeholder'   => 'Site Title',
    ] ); ?>

    <?php echo $W::textinput( [
        'name'          => 'site-tagline',
        'default'       => $D->get( 'site-tagline', '' ),
        'placeholder'   => 'Site Tagline',
    ] ); ?>
</div>