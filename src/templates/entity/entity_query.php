<div class="entity form-block" entity-type="query">
    <span class="entity-type-label">Query</span>
    <h2 class="entity-label"><?php echo $D->get( 'label', 'Query' ) ?></h2>

    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $D->get( 'remotesmenu' ) ?>

    <?php echo $W::textinput( array(
        'name'        => 'path',
        'label'       => 'Path',
        'default'     => $D->get( 'path' ),
        'placeholder' => 'Path',
    ) ); ?>

    <fieldset name="params" data-type="subentity">
        <?php echo $D->get( 'params' ); ?>
    </fieldset>

    <fieldset name="response" data-type="subentity">
        <?php echo $D->get( 'response' ) ?>
    </fieldset>
</div>