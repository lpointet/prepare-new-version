<?php
/**
 * Remember plugin path & URL
 */
define( 'PNV_PATH', plugin_basename( realpath( dirname( __FILE__ ).'/..') ) );
define( 'PNV_COMPLETE_PATH', WP_PLUGIN_DIR.'/'.PNV_PATH );
define( 'PNV_URL', WP_PLUGIN_URL.'/'.PNV_PATH );

/**
 * Translation domain name for this plugin
 */
define( 'PNV_DOMAIN', 'prepare_new_version' );

/**
 * Name of the meta storing the id of the original post
 */
define( 'PNV_META_NAME', '_pnv_duplicata' );

/**
 * Name of the status saved with duplicatas
 */
define( 'PNV_STATUS_NAME', 'duplicata' );

/**
 * Actions
 */
define( 'PNV_ACTION_NONCE', 'pnv_action_nonce' );
define( 'PNV_ACTION_NAME', 'pnv_action' );
define( 'PNV_DUPLICATE_ACTION', 'duplicate' );
define( 'PNV_COPY_ACTION', 'copy' );
define( 'PNV_ERASE_ACTION', 'erase' );