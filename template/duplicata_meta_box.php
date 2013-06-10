<?php
$original = WPPD::get_original();

if( !empty( $original ) ) {
    $original = get_post( $original );
}
else
    $duplicata = WPPD::get_duplicata();
?>
<p>
    <?php if( !empty( $original ) ): ?>
        <?php echo sprintf( WPPD_STR_ORIGINAL_POST . '<a href="%s">%s</a>', admin_url( 'post.php?post=' . $original . '&action=edit' ), get_the_title( $original ) ); ?>
    <?php else: ?>
        <?php echo WPPD_STR_CURRENT_ORIGINAL_POST; ?>
    <?php endif; ?>
</p>
<p>
    <strong><?php echo WPPD_STR_META_BOX_DUPLICATA_LIST_TITLE; ?></strong>
</p>
<ul>
    <?php foreach( $duplicata as $dup ): ?>
        <li>
            <a href="<?php echo admin_url( 'post.php?post=' . $dup . '&action=edit' ); ?>" <?php echo get_the_ID() === $dup->ID ? 'class="current"' : ''; ?>><?php echo get_the_title( $dup ); ?></a>
        </li>
    <?php endforeach; ?>
</ul>