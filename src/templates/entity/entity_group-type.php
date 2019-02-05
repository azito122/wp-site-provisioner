<div class="entity form wrapper" entity-type="group-type">
    <!-- <h2 class="entity-label"><?php echo $D->get( 'label', 'Unlabeled Group Type' ); ?></h2> -->

    <?php echo $W::textinput( array(
        'name'        => 'label',
        'label'       => 'Label',
        'default'     => $D->get( 'label' ),
        'placeholder' =>'Label',
        'required'    => true,
        'class'       => 'entity-label'
    ) ); ?>
    <?php echo $W::hidden( 'storeid', $D->get( 'storeid' ) ); ?>

    <div datakey="meta-query" datatype="subentity">
        <?php echo $D->get( 'meta-query' ); ?>
    </div>
    <div datakey="user-query" datatype="subentity">
        <?php echo $D->get( 'user-query' ); ?>
    </div>

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