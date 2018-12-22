<div class="group-type form">
    <h2><?php echo $D->get( 'group-type-label', 'Unlabeled Group Type' ); ?></h2>
    <?php echo $R::textinput( 'group-type-label', 'label', 'Label', $D->get( 'group-type-label' ), 'Label' ); ?>
    <?php echo $D->get( 'source-query' ); ?>
    <?php echo $D->get( 'template-query' ); ?>
    <div class="group-type-permissions">
        <?php
        $roles = array(
            'manager' => 'Manager',
            'user'    => 'User',
        );
        echo $R::select( 'group-type-permissions', 'Who has access to this Group Type?', $roles, 0);
        ?>
    </div>
    <button class="save button">Save</button>
</div>