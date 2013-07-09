<?php
if( !empty( $original ) ) {
    $duplicata = WPPD::get_duplicata( $original );
}
else
    $duplicata = WPPD::get_duplicata();
?>
<ul>
    <?php foreach( $duplicata as $dup ): ?>
        <li>
            <a href="<?php echo add_query_arg( array( 'post' => $dup, 'action' => 'edit' ), admin_url( 'post.php' ) ); ?>" <?php echo get_the_ID() == $dup ? 'class="current"' : ''; ?>><?php echo get_the_title( $dup ) . ' - ' . get_the_time( get_option('date_format'), $dup ) . ' - ' . get_the_time( '', $dup ); ?></a>
        </li>
    <?php endforeach; ?>
</ul>
<p>
    <a href="<?php echo add_query_arg( WPPD_ACTION_NAME, WPPD_DUPLICATE_ACTION, $action_url ); ?>" id="duplicate" class="button button-primary button-large"><?php echo WPPD_STR_DUPLICATE_BUTTON; ?></a>

    <?php if( empty( $original ) ): ?>
        <a href="<?php echo add_query_arg( WPPD_ACTION_NAME, WPPD_COPY_ACTION , $action_url ); ?>" id="copy" class="button button-primary button-large"><?php echo WPPD_STR_COPY_BUTTON; ?></a>
    <?php endif; ?>
</p>