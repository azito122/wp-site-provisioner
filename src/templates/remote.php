<div class="remote form">
    <h2><?php echo $D->get( 'label', 'Unlabeled Remote' ); ?></h2>
    <?php echo $R::textinput( 'label', 'Label', $D->get( 'label' ), 'Label' ); ?>
    <?php echo $R::textinput( 'url', 'URL', $D->get( 'url'), 'URL' ); ?>
    <button class="save button">Save</button>
</div>