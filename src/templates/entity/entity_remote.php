<div class="form entity wrapper" entity-type="remote">
    <h2><?php echo $D->get( 'label', 'Unlabeled Remote' ); ?></h2>

    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ) ?>
    <?php echo $W::textinput( array(
        'name'        => 'label',
        'label'       => 'Label',
        'default'     => $D->get( 'label' ),
        'placeholder' =>'Label',
        'required'    => true,
    ) ); ?>

    <?php echo $W::textinput( array(
        'name' => 'url',
        'label' => 'URL',
        'default' => $D->get( 'url'),
        'placeholder' => 'URL',
    ) ); ?>

    <button class="save button">Save</button>
</div>