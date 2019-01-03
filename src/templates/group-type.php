<div class="entity form" entity-type="group-type">
    <h2><?php echo $D->get( 'label', 'Unlabeled Group Type' ); ?></h2>

    <?php echo $R::hidden( 'storeid', $D->get( 'storeid' ) ); ?>
    <?php echo $R::textinput( array(
        'name'        => 'label',
        'label'       => 'Label',
        'default'     => $D->get( 'label' ),
        'placeholder' =>'Label',
        'required'    => true,
    ) ); ?>

    <div class="sub-entity" datakey="meta-query">
        <?php echo $D->get( 'meta-query' ); ?>
    </div>
    <div class="sub-entity" datakey="user-query">
        <?php echo $D->get( 'user-query' ); ?>
    </div>

    <!-- <div class="group-type-permissions">
        <?php
        $roles = array(
            'manager' => 'Manager',
            'user'    => 'User',
        );
        echo $R::select( 'group-type-permissions', 'Who has access to this Group Type?', $roles, 0);
        ?>
    </div> -->

    <button class="save button">Save</button>
</div>