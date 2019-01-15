<div class="entity form wrapper" entity-type="group">
    <h2><?php echo $D->get( 'label', 'Unlabeled Group' ); ?></h2>

    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::hidden( 'meta', $D->get( 'meta' ) ); ?>

    <div class="site-engines" datakey="site-engines" datatype="subentity">
        <?php echo $R::loopTemplate( 'site-engine', $D->get( 'siteengines' ) ) ?>
    </div>

    <button class="save button">Save</button>
</div>