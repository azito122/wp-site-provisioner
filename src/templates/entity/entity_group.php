<div class="entity form-block" entity-type="group">
    <h2><?php echo $D->get( 'label', 'Unlabeled Group' ); ?></h2>

    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::hidden( 'meta', $D->get( 'meta' ) ); ?>

    <fieldset name="site-engines" data-type="array" data-array-selector=".entity[entity-type='site-engine']">
        <?php echo $D->get( 'siteengines' ); ?>
    </fieldset>

    <?php $W::button('+ Add Site Engine', '', [ 'class' => 'add-button add-site-engine' ] ); ?>

    <button class="save button">Save</button>
</div>