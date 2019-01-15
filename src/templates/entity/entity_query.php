<div class="entity form wrapper" entity-type="query">
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

    <div class="query-params wrapper" datakey="params" datatype="array" dataarrayselector=".form.entity[entity-type='query-param']">
        <h6>Parameters</h6>
        <div class="params-list">
            <?php echo $D->get( 'params' ) ?>
        </div>

        <button class="button add-button">+ Add Param</button>
    </div>

    <div datakey="response" datatype="subentity">
        <?php echo $D->get( 'response' ) ?>
    </div>
</div>