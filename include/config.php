<?php
/**
 * Remember plugin path & URL
 */
define( 'WPPD_PATH', plugin_basename( realpath( dirname( __FILE__ ).'/..') ) );
define( 'WPPD_COMPLETE_PATH', WP_PLUGIN_DIR.'/'.WPPD_PATH );
define( 'WPPD_URL', WP_PLUGIN_URL.'/'.WPPD_PATH );

/**
 * Translation domain name for this plugin
 */
define( 'WPPD_DOMAIN', 'wp_post_duplication' );

/**
 * Name of the meta storing the id of the original post
 */
define( 'WPPD_META_NAME', '_wppd_duplicata' );

/**
 * Name of the status saved with duplicatas
 */
define( 'WPPD_STATUS_NAME', 'duplicata' );

/**
 * Actions
 */
define( 'WPPD_ACTION_NONCE', 'wppd_action_nonce' );
define( 'WPPD_ACTION_NAME', 'wppd_action' );
define( 'WPPD_DUPLICATE_ACTION', 'duplicate' );
define( 'WPPD_COPY_ACTION', 'copy' );
define( 'WPPD_ERASE_ACTION', 'erase' );