<form class="group-type">
    <h2><?php echo $D->get( 'group-type-label', 'Unlabeled Group Type' ); ?></h2>
    <?php echo $R::textinput( 'group-type-label', 'Label', $D->get( 'group-type-label' ), 'Label' ); ?>
    <?php echo $R::entity( $D->get( 'sourcequery' ) ); ?>
    <?php echo $R::entity( $D->get( 'templatequery' ) ); ?>
    <div class="group-type-permissions">
        <?php
        $roles = array(
            'manager' => 'Manager',
            'user'    => 'User',
        );
        echo $R::select( 'group-type-permissions', 'Who has access to this Group Type?', $roles, 0);
        ?>
    </div>
</form>