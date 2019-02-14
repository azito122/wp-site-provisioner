<div class="entity form-block" entity-type="query">
    <h2 class="entity-label"><?php echo $D->get( 'label', 'Query' ) ?></h2>

    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::select( array(
        'name' => 'remoteid',
        'label' => 'Remote',
        'options' => $D->get( 'remotes'),
        'default' => $D->get( 'remoteid' )
    ) ); ?>

    <?php echo $W::textinput( array(
        'name'        => 'path',
        'label'       => 'Path',
        'default'     => $D->get( 'path' ),
        'placeholder' => 'Path',
    ) ); ?>

    <fieldset class="form-block query-params" name="params" data-type="array" data-array-selector=".form.entity[entity-type='query-param']">
        <h3>Parameters</h3>
        <div class="params-list">
            <?php echo $D->get( 'params' ) ?>
        </div>

        <button class="button add-button">+ Add Param</button>
    </fieldset>

    <fieldset name="response" data-type="subentity">
        <?php echo $D->get( 'response' ) ?>
    </fieldset>
</div>