<div class="submitbox">
    <p>
        <a href="<?php echo add_query_arg( WPPD_ACTION_NAME, WPPD_ERASE_ACTION, $action_url ); ?>" id="erase" class="button button-primary button-large"><?php echo WPPD_STR_ERASE_BUTTON; ?></a>
    </p>
    <a href="<?php echo add_query_arg( WPPD_ACTION_NAME, WPPD_COPY_ACTION , $action_url ); ?>" id="copy" class="button button-primary button-large"><?php echo WPPD_STR_COPY_BUTTON; ?></a>
    <div id="delete-action">
    <?php
    if ( current_user_can( "delete_post", $post->ID ) ) {
        if ( !EMPTY_TRASH_DAYS )
            $delete_text = __( 'Delete Permanently' );
        else
            $delete_text = __( 'Move to Trash' );
        ?>
        <a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php echo $delete_text; ?></a><?php
    } ?>
    </div>
    <div id="publishing-action">
        <input type="submit" name="save" id="publish" value="<?php echo esc_attr( WPPD_STR_SAVE_DUPLICATA_BUTTON ); ?>" class="button button-primary button-large" />
    </div>
    <div class="clear"></div>
</div>