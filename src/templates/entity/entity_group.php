<div class="entity form-block" entity-type="group">
    <?php echo $W::textinput( array(
        'name'        => 'label',
        'default'     => $D->get( 'label' ),
        'placeholder' =>'Label',
        'required'    => true,
        'class'       => 'label-input'
    ) ); ?>

    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::hidden( 'meta', $D->get( 'meta' ) ); ?>

    <fieldset class="site-engines-list" name="site-engines" data-type="array" data-array-selector=".entity[entity-type*='site-engine']">
        <?php echo $D->get( 'site-engines' ); ?>
    </fieldset>

    <?php echo $W::button('+ Add Site Engine', '', [ 'class' => 'add-button add-site-engine' ] ); ?>

    <button class="save button">Save</button>
</div>