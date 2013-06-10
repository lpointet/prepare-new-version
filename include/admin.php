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
        // @TODO: handle duplication actions
    }

    /**
     * Add meta box with links to duplicatas
     */
    public static function add_meta_boxes() {
        $post_type = WPPD_Option::get_post_types();

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
        switch($column) {
            case 'duplicata':
                $duplicata = WPPD::get_duplicata( $post_id );
                echo count( $duplicata );
                break;
        }
    }
}