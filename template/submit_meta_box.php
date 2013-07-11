<div id="submitpost" class="submitbox">
    <div id="major-publishing-actions">
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
            <span class="spinner"></span>
            <a href="<?php echo add_query_arg( PNV_ACTION_NAME, PNV_ERASE_ACTION, $action_url ); ?>" id="erase" class="button button-primary button-large"><?php echo PNV_STR_ERASE_BUTTON; ?></a>
            <input type="submit" name="save" id="publish" value="<?php echo esc_attr( PNV_STR_SAVE_DUPLICATA_BUTTON ); ?>" class="button button-large" />
        </div>
        <div class="clear"></div>
        <a href="<?php echo add_query_arg( PNV_ACTION_NAME, PNV_COPY_ACTION , $action_url ); ?>" id="copy" class=""><?php echo PNV_STR_COPY_BUTTON; ?></a>
    </div>
    <div id="preview-action">
        <?php
        $preview_link = set_url_scheme( get_permalink( $post->ID ) );
        $preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ) ) );
        $preview_button = __( 'Preview' );
        ?>
        <a class="preview button" href="<?php echo $preview_link; ?>" target="wp-preview" id="post-preview"><?php echo $preview_button; ?></a>
        <input type="hidden" name="wp-preview" id="wp-preview" value="" />
    </div>
</div>