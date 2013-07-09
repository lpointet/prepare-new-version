<?php
/**
 * Plugin Name:         Prepare New Version
 * Plugin URI:          http://www.globalis-ms.com/
 * Description:         Allow creating post 'duplicates' to prepare new versions of one content
 * Author:              Lionel POINTET, GLOBALIS media systems
 * Author URI:          http://www.globalis-ms.com
 *
 * Version:             1.0.0
 * Requires at least:   3.6.0
 * Tested up to:        3.6
 */

if( !class_exists( 'PNV' ) ) {
    // Load configuration
    require_once realpath( dirname( __FILE__ ) ) . '/include/config.php';
    require_once PNV_COMPLETE_PATH . '/include/option.php';

    // Load textdomain
    load_plugin_textdomain( PNV_DOMAIN, NULL, PNV_PATH . '/language/' );

    // Load language
    require_once PNV_COMPLETE_PATH . '/include/lang.php';

    if( is_admin() ) {
        require_once PNV_COMPLETE_PATH . '/include/admin.php';
        PNV_Admin::hooks();
    }

    /**
     * Main class of the plugin
     */
    class PNV {
        /**
         * Register hooks used by the plugin
         */
        public static function hooks() {
            // Register (de)activation hook
            register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
            register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate' ) );
            register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );

            add_action( 'init', array( __CLASS__, 'init' ) );
            add_action( 'delete_post', array( __CLASS__, 'delete_post' ) );
        }

        /**
         * What to do on plugin activation
         */
        public static function activate() {
            // Nothing for now.
        }

        /**
         * What to do on plugin deactivation
         */
        public static function deactivate() {
            // Nothing for now.
        }

        /**
         * What to do on plugin uninstallation
         */
        public static function uninstall() {
            // Nothing for now.
        }

        /**
         * Plugin init: create 'duplicata' status
         */
        public static function init() {
            $args = array(
                'label' => PNV_STR_DUPLICATA_STATUS_LABEL,
                'public' => FALSE,
                'exclude_from_search' => TRUE,
                'show_in_admin_all_list' => FALSE,
                'label_count' => _n_noop( 'Pending version <span class="count">(%s)</span>', 'Pending versions <span class="count">(%s)</span>', PNV_DOMAIN ),
            );

            $args = apply_filters( 'PNV_duplicata_status_args', $args );

            register_post_status( PNV_STATUS_NAME, $args );
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

            return PNV_STATUS_NAME === $post->post_status;
        }

        /**
         * Return the posts corresponding to duplicatas for the given post ID
         */
        public static function get_duplicata( $post_id = NULL, $id = TRUE ) {
            if( !$post_id ) {
                $post = get_post();
                $post_id = $post->ID;
            }

            $post_type = PNV_Option::get_post_types();

            $posts = get_posts(array(
                'post_type' => $post_type,
                'suppress_filters' => FALSE,
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => PNV_META_NAME,
                        'value' => $post_id,
                        'type' => 'NUMERIC',
                    ),
                ),
                'post_status' => PNV_STATUS_NAME,
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

            return get_post_meta( $post_id, PNV_META_NAME, TRUE );
        }

        /**
         * Remove duplicatas when an original post is deleted
         */
        public function delete_post( $post_id ) {
            $post = get_post( $post_id );
            $post_type = PNV_Option::get_post_types();

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

        /**
         * Erase a post content with the one of another
         * If destination is not set, will create a duplicata of source
         * If duplicate is set to false, just create a copy of the post (no duplicate)
         */
        public static function erase_content( $source, $destination = NULL, $action = PNV_DUPLICATE_ACTION ) {
            $default_post = array(
                'ID' => '',
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_author' => '',
                'post_content' => '',
                'post_date' => '',
                'post_date_gmt' => '',
                'post_excerpt' => '',
                'post_parent' => '',
                'post_status' => 'duplicata',
                'post_title' => '',
                'post_type' => 'post',
                'menu_order' => 0,
            );

            $destination = wp_parse_args( (array) $destination, $default_post );

            // Fill in the values from source
            foreach( array_keys( $destination ) as $field ) {
                if(
                    'ID' === $field ||
                    'guid' === $field ||
                    'post_name' === $field ||
                    'ancestors' === $field ||
                    'post_date' === $field ||
                    'post_date_gmt' === $field ||

                    // Erase status only if we are doing a real "copy"
                    ( 'post_status' === $field && PNV_COPY_ACTION !== $action )
                )
                    continue;

                $destination[$field] = $source->$field;
            }

            // We may prepend some string to the post title
            switch( $action ) {
                case PNV_DUPLICATE_ACTION:
                    $destination['post_title'] = PNV_STR_DUPLICATE_PREPEND_TITLE . ' ' . $destination['post_title'];
                    break;
                case PNV_COPY_ACTION:
                    $destination['post_title'] = PNV_STR_COPY_PREPEND_TITLE . ' ' . $destination['post_title'];
                    break;
            }

            $destination = apply_filters( 'PNV_erase_content_destination', $destination, $source, $duplicate );

            $post_id = wp_insert_post( $destination );

            if( !$post_id )
                return;

            // Add terms
            $taxonomies = PNV_Option::get_object_taxonomies( $source->post_type );
            foreach( $taxonomies as $taxonomy ) {
                $tax_terms = array();
                $terms = get_the_terms( $source->ID, $taxonomy );

                if( !$terms )
                    continue;

                foreach( $terms as $term )
                    $tax_terms[] = $term->slug;
                wp_set_object_terms( $post_id, $tax_terms, $taxonomy );
            }

            // Add metadatas
            $meta = get_post_meta( $source->ID );
            foreach( $meta as $key => $val ) {
                if( self::is_filtered_meta( $key ) )
                    continue;

                $val = count( $val ) > 1 ? array_map( 'maybe_unserialize', $val ) : maybe_unserialize( $val[0] );

                update_post_meta( $post_id, $key, $val );
            }

            if( '' === self::get_original( $post_id ) ) {
                $val = PNV_DUPLICATE_ACTION === $action ? $source->ID : '0';

                // Don't link to a duplicata
                if( $val && self::is_duplicata( $source->ID ) )
                    $val = self::get_original( $source->ID );

                update_post_meta( $post_id, PNV_META_NAME, $val );
            }

            do_action( 'PNV_erase_content', $source, $destination, $duplicate, $post_id );

            return $post_id;
        }

        /**
         * Return TRUE or FALSE whether a meta should be erased: TRUE = it shouldn't, FALSE = it should
         */
        public static function is_filtered_meta($key) {
            $meta = array(
                PNV_META_NAME => TRUE,
            );

            $meta = apply_filters( 'PNV_filtered_metas', $meta );

            return isset($meta[$key]);
        }

        /**
         * Return URL where actions have to be sent
         */
        public static function get_action_url( $post ) {
            $action_url = add_query_arg( array( 'ID' => $post->ID, 'action' => 'edit', 'post' => $post->ID ), admin_url( '/post.php' ) );
            $action_url = wp_nonce_url( $action_url, PNV_ACTION_NONCE );

            return $action_url;
        }
    }
    PNV::hooks();
}