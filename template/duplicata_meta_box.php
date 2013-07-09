<?php
if( !empty( $original ) ) {
    $duplicata = PNV::get_duplicata( $original );
}
else
    $duplicata = PNV::get_duplicata();
?>
<ul>
    <?php if( empty( $duplicata ) ): ?>
        <?php echo PNV_STR_NONE; ?>
    <?php endif; ?>
    <?php foreach( $duplicata as $dup ): ?>
        <li>
            <a href="<?php echo add_query_arg( array( 'post' => $dup, 'action' => 'edit' ), admin_url( 'post.php' ) ); ?>" <?php echo get_the_ID() == $dup ? 'class="current"' : ''; ?>><?php echo get_the_title( $dup ); ?></a><br/>
             <?php echo get_the_time( get_option('date_format'), $dup ) . ' - ' . get_the_time( '', $dup ); ?>
        </li>
    <?php endforeach; ?>
</ul>
<p>
    <a href="<?php echo add_query_arg( PNV_ACTION_NAME, PNV_DUPLICATE_ACTION, $action_url ); ?>" id="duplicate" class="button <?php echo !empty( $original ) ? '' : 'button-primary button-large'; ?>"><?php echo PNV_STR_DUPLICATE_BUTTON; ?></a>

    <?php if( empty( $original ) ): ?>
        <a href="<?php echo add_query_arg( PNV_ACTION_NAME, PNV_COPY_ACTION , $action_url ); ?>" id="copy" class="button"><?php echo PNV_STR_COPY_BUTTON; ?></a>
    <?php endif; ?>
</p>