<div class="entity form wrapper" entity-type="group">
    <h2><?php echo $D->get( 'label', 'Unlabeled Group' ); ?></h2>

    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::hidden( 'meta', $D->get( 'meta' ) ); ?>

    <fieldset name="site-engines" data-type="subentity">
        <?php echo $R::loopTemplate( 'site-engine', $D->get( 'siteengines' ) ) ?>
    </fieldset>

    <button class="save button">Save</button>
</div>