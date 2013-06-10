<?php
$post = get_post();
$original = WPPD::get_original();
$action_url = add_query_arg( array( 'post_type' => $post->post_type, 'ID' => $post->ID ), admin_url( '/edit.php' ) );

if( !empty( $original ) ) {
    $original = get_post( $original );
}
else
    $duplicata = WPPD::get_duplicata();
?>
<p>
    <?php if( !empty( $original ) ): ?>
        <?php echo sprintf( WPPD_STR_ORIGINAL_POST . '<a href="%s">%s</a>', add_query_arg( array( 'post' => $original, 'action' => 'edit' ), admin_url( 'post.php' ) ), get_the_title( $original ) ); ?>
        <a href="<?php echo add_query_arg( WPPD_ERASE_ACTION, 1, $action_url ); ?>" id="erase" class="button button-primary button-large"><?php echo WPPD_STR_ERASE_BUTTON; ?></a>
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
            <a href="<?php echo add_query_arg( array( 'post' => $dup, 'action' => 'edit' ), admin_url( 'post.php' ) ); ?>" <?php echo get_the_ID() === $dup->ID ? 'class="current"' : ''; ?>><?php echo get_the_title( $dup ); ?></a>
        </li>
    <?php endforeach; ?>
</ul>
<p>
    <a href="<?php echo add_query_arg( WPPD_DUPLICATE_ACTION, 1, $action_url ); ?>" id="duplicate" class="button button-primary button-large"><?php echo WPPD_STR_DUPLICATE_BUTTON; ?></a>
    <a href="<?php echo add_query_arg( WPPD_COPY_ACTION, 1 , $action_url ); ?>" id="copy" class="button button-primary button-large"><?php echo WPPD_STR_COPY_BUTTON; ?></a>
</p>