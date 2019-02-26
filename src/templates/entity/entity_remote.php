<div class="entity form-block" entity-type="remote">
    <span class="entity-type-label">Remote</span>
    <?php echo $W::hidden( 'uid', $D->get( 'uid' ) ) ?>

    <?php echo $W::textinput( array(
        'name'        => 'label',
        'default'     => $D->get( 'label' ),
        'placeholder' =>'Label',
        'required'    => true,
        'class'       => 'entity-label label-input'
    ) ); ?>

    <?php echo $W::textinput( array(
        'name'        => 'url',
        'label'       => 'URL',
        'default'     => $D->get( 'url'),
        'placeholder' => 'URL',
        'required'    => true,
    ) ); ?>

    <button class="save button">Save</button>
</div>