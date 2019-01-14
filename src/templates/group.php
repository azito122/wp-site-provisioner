<div class="entity form storable" entity-type="group">
    <h2><?php echo $D->get( 'label', 'Unlabeled Group' ); ?></h2>

    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::hidden( 'meta', $D->get( 'meta' ) ); ?>

    <div class="site-engines-wrapper sub-entity" datakey="site-engines">
        <?php echo $R::loopTemplate( 'site-engine', $D->get( 'siteengines' ) ) ?>
    </div>

    <button class="save button">Save</button>
</div>