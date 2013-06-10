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

            add_action( 'delete_post', array( __CLASS__, 'delete_post' ) );
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

        /**
         * Return TRUE if the post is a duplicata, FALSE otherwise
         */
        public static function is_duplicata( $post_id = NULL ) {
            if( !$post_id ) {
                $post = get_post();
                $post_id = $post->ID;
            }

            $post = get_post( $post_id );

            return WPPD_STATUS_NAME === $post->post_status;
        }

        /**
         * Return the posts corresponding to duplicatas for the given post ID
         */
        public static function get_duplicata( $post_id = NULL, $id = TRUE ) {
            if( !$post_id ) {
                $post = get_post();
                $post_id = $post->ID;
            }

            $post_type = WPPD_Option::get_post_types();

            $posts = get_posts(array(
                'post_type' => $post_type,
                'suppress_filters' => FALSE,
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => WPPD_META_NAME,
                        'value' => $post_id,
                        'type' => 'NUMERIC',
                    ),
                ),
                'post_status' => WPPD_STATUS_NAME,
                'fields' => ($id ? 'ids' : ''),
            ));

            return $posts;
        }

        /**
         * Return the post ID which is the original for a given post.
         * It returns:
         *  - an empty string if not defined
         *  - 0 if the post is current year
         *  - post ID if there is a parent
         */
        public static function get_original( $post_id = NULL ) {
            if( !$post_id ) {
                $post = get_post();
                $post_id = $post->ID;
            }

            return get_post_meta( $post_id, WPPD_META_NAME, TRUE );
        }

        /**
         * Remove duplicatas when an original post is deleted
         */
        public function delete_post( $post_id ) {
            $post = get_post( $post_id );
            $post_type = WPPD_Option::get_post_types();

            if( !in_array( $post->post_type, $post_type ) || !( $duplicata = self::get_duplicata( $post_id ) ) || !empty( $REQUEST['delete_all'] ) )
                return;

            if( !empty( $_REQUEST['ids'] ) ) {
                $duplicata = array_diff( $duplicata, $_REQUEST['ids'] );
            }

            if( empty( $duplicata ) )
                return;

            foreach( $duplicata as $id )
                wp_delete_post( $duplicata );
        }
    }
}