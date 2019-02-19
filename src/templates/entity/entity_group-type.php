<div class="entity form-block" entity-type="group-type">
    <span class="entity-type-label">Group Type</span>
    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <?php echo $W::textinput( array(
        'name'        => 'label',
        'default'     => $D->get( 'label' ),
        'placeholder' =>'Label',
        'required'    => true,
        'class'       => 'entity-label label-input'
    ) ); ?>

    <fieldset name="meta-query" data-type="subentity">
        <?php echo $D->get( 'meta-query' ); ?>
    </fieldset>

    <fieldset name="user-query" data-type="subentity">
        <?php echo $D->get( 'user-query' ); ?>
    </fieldset>

    <!-- <div class="group-type-permissions">
        <?php
        $roles = array(
            'manager' => 'Manager',
            'user'    => 'User',
        );
        echo $W::select( 'group-type-permissions', 'Who has access to this Group Type?', $roles, 0);
        ?>
    </div> -->

    <button class="save button">Save</button>
</div>