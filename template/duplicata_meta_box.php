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
<p id="prepare-actions">
    <a href="<?php echo add_query_arg( PNV_ACTION_NAME, PNV_DUPLICATE_ACTION, PNV::get_action_url( $post ) ); ?>" id="duplicate" class="button <?php echo !empty( $original ) ? '' : 'button-primary button-large'; ?>"><?php echo PNV_STR_DUPLICATE_BUTTON; ?></a><br/>

    <?php
    if( empty( $original ) ) {
        $class = ' class="button"';
        $title = PNV_STR_COPY_BUTTON;
    }
    else {
        $class = '';
        $title = PNV_STR_ADD_NEW_FROM_BUTTON;
    }
    ?>
    <a href="<?php echo add_query_arg( PNV_ACTION_NAME, PNV_COPY_ACTION , PNV::get_action_url( $post, PNV_COPY_ACTION ) ); ?>" id="copy"<?php echo $class; ?>><?php echo $title; ?></a>
</p>