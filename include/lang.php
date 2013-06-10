<?php
if( !function_exists( '_noop' ) ) {
    /**
     * Identity function for translation
     */
    function _noop( $string ) {
        return $string;
    }
}

define( 'WPPD_STR_DUPLICATA_META_BOX_TITLE', _x( 'Duplicates', 'meta box title', WPPD_DOMAIN ) );
define( 'WPPD_STR_DUPLICATA_COLUMN_TITLE', _x( 'Duplicates', 'post list column title', WPPD_DOMAIN ) );
define( 'WPPD_STR_DUPLICATA_STATUS_LABEL', __( 'Duplicate', WPPD_DOMAIN ) );
define( 'WPPD_STR_DUPLICATA_STATUS_LABEL_COUNT_SINGULAR', _noop( 'Duplicate <span class="count">(%s)</span>' ) );
define( 'WPPD_STR_DUPLICATA_STATUS_LABEL_COUNT_PLURAL', _noop( 'Duplicates <span class="count">(%s)</span>' ) );

//
// Meta box
//
define( 'WPPD_STR_ORIGINAL_POST', __( 'Original: ', WPPD_DOMAIN ) );
define( 'WPPD_STR_CURRENT_ORIGINAL_POST', __( 'This is an original', WPPD_DOMAIN ) );
define( 'WPPD_STR_META_BOX_DUPLICATA_LIST_TITLE', _x( 'Duplicates', 'duplicatas list title in meta box', WPPD_DOMAIN ) );
define( 'WPPD_STR_ERASE_BUTTON', __( 'Replace with this one', WPPD_DOMAIN ) );
define( 'WPPD_STR_DUPLICATE_BUTTON', _x( 'Duplicate', 'Duplication button', WPPD_DOMAIN ) );
define( 'WPPD_STR_COPY_BUTTON', __( 'Create a copy', WPPD_DOMAIN ) );
define( 'WPPD_STR_PUBLISH_META_BOX_TITLE', __( 'Publish', WPPD_DOMAIN ) );
define( 'WPPD_STR_SAVE_DUPLICATA_BUTTON', __( 'Save duplicate', WPPD_DOMAIN ) );