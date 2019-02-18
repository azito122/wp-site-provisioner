<div class="entity form-block" entity-type="query-response-map">
    <span class="entity-type-label">Response Map</span>
    <h6>Response Mappings</h6>
    <fieldset class="mappings-list key-val-block-wrapper" name="map" data-type="array" data-array-selector=".entity[entity-type='query-response-mapping']">
        <?php echo $D->get( 'mappings' ); ?>
    </fieldset>

    <button class="button add-button">+ Add Mapping</button>
</div>