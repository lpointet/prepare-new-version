<?php
class WPPD_Admin {
    /**
     * Register hooks used on admin side by the plugin
     */
    public static function hooks() {
        add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
    }

    /**
     * Do some actions at the beginning of an admin script
     */
    public static function admin_init() {
        self::handle_action();

        // Add other hooks
        $post_type = WPPD_Option::get_post_types();
        foreach( $post_type as $type ) {
            add_action( 'manage_' . $type . '_posts_columns', array( __CLASS__, 'manage_posts_columns' ) );
            add_action( 'manage_' . $type . '_posts_custom_column', array( __CLASS__, 'manage_posts_custom_column' ), 10, 2 );
        }
    }

    /**
     * Handle duplicata / copy creation
     */
    public static function handle_action() {
        if( !isset( $_GET[WPPD_ACTION_NAME] ) || !isset( $_GET['ID'] ) || !check_admin_referer( WPPD_ACTION_NONCE ) )
            return;

        $source = get_post( $_GET['ID'] );

        switch( $_GET[WPPD_ACTION_NAME] ) {
            case WPPD_DUPLICATE_ACTION:
                $post_id = WPPD::erase_content( $source );
                break;
            case WPPD_COPY_ACTION:
                // Copy status is "draft"
                $source->post_status = 'draft';
                $post_id = WPPD::erase_content( $source, NULL, FALSE );
                break;
            case WPPD_ERASE_ACTION:
                $destination = get_post( WPPD::get_original( $_GET['ID'] ) );
                $post_id = WPPD::erase_content( $source, $destination, FALSE );
                break;
        }

        if( !isset( $post_id ) || empty( $post_id ) )
            return;

        $url = add_query_arg( array(
            'post' => $post_id,
            'action' => 'edit',
        ), admin_url( '/post.php' ) );

        if( !( $url = apply_filters( 'wppd_action_url_redirect', $url, $post_id ) ) )
            return;

        wp_safe_redirect($url);
        exit;
    }

    /**
     * Add meta box with links to duplicatas
     */
    public static function add_meta_boxes() {
        $post_type = WPPD_Option::get_post_types();

        // If we are on a duplicata, remove default submit meta box and replace it with our box
        $current_screen = get_current_screen();
        $post = get_post();
        if( in_array( $current_screen->post_type, $post_type ) && WPPD::is_duplicata( $post->ID ) ) {
            remove_meta_box( 'submitdiv', $current_screen->post_type, 'side' );
            add_meta_box( 'wppd_submit_meta_box', WPPD_STR_PUBLISH_META_BOX_TITLE, array( __CLASS__, 'submit_meta_box' ), $current_screen->post_type, 'side', 'core' );
        }

        foreach( $post_type as $type )
            add_meta_box( 'wppd_duplicata_meta_box', WPPD_STR_DUPLICATA_META_BOX_TITLE, array( __CLASS__, 'duplicata_meta_box' ), $type, 'side', 'core' );
    }

    /**
     * Display duplicata meta box
     */
    public static function duplicata_meta_box() {
        require WPPD_COMPLETE_PATH . '/template/duplicata_meta_box.php';
    }

    /**
     * Display publish meta box
     */
    public static function submit_meta_box() {
        require WPPD_COMPLETE_PATH . '/template/submit_meta_box.php';
    }

    /**
     * Add columns to the post types lists
     */
    public static function manage_posts_columns( $columns ) {
        $current_screen = get_current_screen();
        $post_type_obj = get_post_type_object( $current_screen->post_type );

        // If we cannot create posts of that type, we cannot see duplicatas
        if( !current_user_can( $post_type_obj->cap->edit_posts ) )
            return $columns;

        return $columns + array('duplicata' => WPPD_STR_DUPLICATA_COLUMN_TITLE);
    }

    /**
     * Display data for added columns
     */
    public static function manage_posts_custom_column( $column, $post_id ) {
        $val = '';

        switch( $column ) {
            case 'duplicata':
                $duplicata = WPPD::get_duplicata( $post_id );
                $val = count( $duplicata );
                break;
        }

        echo apply_filters( 'wppd_' . $column . '_column_value', $val, $post_id );
    }
}