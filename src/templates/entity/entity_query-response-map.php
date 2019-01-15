<div class="entity form" entity-type="query-response-map">
    <h6>Response Mappings</h6>
    <div class="mappings-list" datakey="map" datatype="array" dataarrayselector=".form.entity[entity-type='query-response-mapping']">
        <?php echo $D->get( 'mappings' ); ?>
    </div>

    <button class="button add-button">+ Add Mapping</button>
</div>