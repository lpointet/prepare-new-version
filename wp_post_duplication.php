<?php
/**
 * Plugin Name:         WP Post Duplication
 * Plugin URI:          http://www.globalis-ms.com/
 * Description:         Allow creating post 'duplicatas' to prepare new versions of one content
 * Author:              Lionel POINTET, GLOBALIS media systems
 * Author URI:          http://www.globalis-ms.com
 *
 * Version:             1.0.0
 * Requires at least:   3.6.0
 * Tested up to:        3.6
 */

if( !class_exists( 'WPPD' ) ) {
    // Load configuration
    require_once realpath( dirname( __FILE__ ) ) . '/include/config.php';
    require_once WPPD_COMPLETE_PATH . '/include/option.php';

    // Load textdomain
    load_plugin_textdomain( WPPD_DOMAIN, NULL, WPPD_PATH . '/language/' );

    // Load language
    require_once WPPD_COMPLETE_PATH . '/include/lang.php';

    if( is_admin() ) {
        require_once WPPD_COMPLETE_PATH . '/include/admin.php';
        WPPD_Admin::hooks();
    }

    /**
     * Main class of the plugin
     */
    class WPPD {
        /**
         * Register hooks used by the plugin
         */
        public static function hooks() {
            // Register (de)activation hook
            register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
            register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate' ) );
            register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
        }

        /**
         * What to do on plugin activation
         */
        public static function activate(){
            // Nothing for now.
        }

        /**
         * What to do on plugin deactivation
         */
        public static function deactivate(){
            // Nothing for now.
        }

        /**
         * What to do on plugin uninstallation
         */
        public static function uninstall(){
            // Nothing for now.
        }
    }
}