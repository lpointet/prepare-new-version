<?php
class WPPD_Admin {
    private static $current_screen_pointers = array();

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

        add_action( 'admin_print_styles-edit.php', array( __CLASS__, 'admin_print_styles_edit' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
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
                $post_id = WPPD::erase_content( $source, NULL, $_GET[WPPD_ACTION_NAME] );
                break;
            case WPPD_ERASE_ACTION:
                $destination = get_post( WPPD::get_original( $_GET['ID'] ) );
                $post_id = WPPD::erase_content( $source, $destination, $_GET[WPPD_ACTION_NAME] );
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
        global $wp_list_table;

        $current_screen = get_current_screen();
        $post_type_obj = get_post_type_object( $current_screen->post_type );

        // If we cannot create posts of that type, we cannot see duplicatas
        if( !current_user_can( $post_type_obj->cap->edit_posts ) )
            return $columns;

        if( self::is_duplicata_listing() || $wp_list_table->is_trash )
            $columns+= array( 'original' => WPPD_STR_ORIGINAL_COLUMN_TITLE );

        if( !self::is_duplicata_listing() )
            $columns+= array( 'duplicata' => WPPD_STR_DUPLICATA_COLUMN_TITLE );

        return $columns;
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
            case 'original':
                $original = WPPD::get_original( $post_id );
                $val = !empty( $original ) ? '<a href="' . esc_url( add_query_arg( array( 'post' => $original, 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . get_the_title( $original ) . '</a>' : ' - ';
                break;
        }

        echo apply_filters( 'wppd_' . $column . '_column_value', $val, $post_id );
    }

    /**
     * Return TRUE if we're listing duplicates
     */
    public static function is_duplicata_listing() {
        return !empty( $_GET['post_status'] ) && 'duplicata' === $_GET['post_status'];
    }

    /**
     * Enqueue styles on listing WordPress pages
     */
    public static function admin_print_styles_edit() {
        wp_enqueue_style( 'wppd_admin_css', WPPD_URL . '/css/wppd_admin.css' );
    }

    /**
     * Enqueue scripts on listing WordPress pages
     */
    public static function admin_enqueue_scripts( $hook ) {
        // Display pointer to invite people duplicate posts
        self::show_pointers();

        if( 'edit.php' !== $hook )
            return;

        wp_enqueue_script( 'wppd_admin_js', WPPD_URL . '/js/wppd_admin.js', array(), NULL, TRUE );
    }

    /**
     * Retrieves pointers for the current admin screen. Use the 'wppd_admin_pointers' hook to add your own pointers.
     * @return array Current screen pointers
     */
    private static function get_current_screen_pointers(){
        $pointers = '';

        $screen = get_current_screen();
        $screen_id = $screen->id;

        // Format : array( 'screen_id' => array( 'pointer_id' => array([options : target, content, position...]) ) );

        $default_pointers = array(
            'plugins' => array(
                'wppd_install' => array(
                    'target' => '#menu-posts',
                    'content' => '<h3>'. WPPD_STR_ACTIVATION_POINTER_TITLE .'</h3> <p>'. WPPD_STR_ACTIVATION_POINTER_CONTENT .'</p>',
                    'position' => array( 'edge' => 'top', 'align' => 'top' ),
                ),
            ),
        );

        if( !empty( $default_pointers[$screen_id] ) )
            $pointers = $default_pointers[$screen_id];

        return apply_filters( 'wppd_admin_pointers', $pointers, $screen_id );
    }

    /**
     * Enqueue scripts to display WP-Pointer
     */
    public static function show_pointers() {
        // Don't run on WP < 3.3
        if( get_bloginfo( 'version' ) < '3.3' ){
            return;
        }

        $pointers = self::get_current_screen_pointers();

        // No pointers? Don't do anything
        if( empty( $pointers ) || ! is_array( $pointers ) )
            return;

        // Get dismissed pointers.
        // Note : dismissed pointers are stored by WP in the "dismissed_wp_pointers" user meta.

        $dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
        $valid_pointers = array();

        // Check pointers and remove dismissed ones.
        foreach( $pointers as $pointer_id => $pointer ) {
            // Sanity check
            if( in_array( $pointer_id, $dismissed ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['content'] ) )
                continue;

            // Add the pointer to $valid_pointers array
            $valid_pointers[$pointer_id] =  $pointer;
        }

        // No valid pointers? Stop here.
        if( empty( $valid_pointers ) )
            return;

        // Set our static $current_screen_pointers
        self::$current_screen_pointers = $valid_pointers;

        // Add our javascript to handle pointers
        add_action( 'admin_print_footer_scripts', array( __CLASS__, 'admin_print_footer_scripts' ) );

        // Add pointers style and javascript to queue.
        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'wp-pointer' );
    }

    /**
     * Finally prints the javascript that'll make our pointers alive.
     */
    public static function admin_print_footer_scripts(){
        if( !empty(self::$current_screen_pointers) ):
            ?>
            <script type="text/javascript">// <![CDATA[
                jQuery(document).ready(function($) {
                    if(typeof(jQuery().pointer) != 'undefined') {
                        <?php foreach(self::$current_screen_pointers as $pointer_id => $data): ?>
                            $('<?php echo $data['target'] ?>').pointer({
                                content: '<?php echo $data['content'] ?>',
                                position: {
                                    edge: '<?php echo $data['position']['edge'] ?>',
                                    align: '<?php echo $data['position']['align'] ?>'
                                },
                                close: function() {
                                    $.post( ajaxurl, {
                                        pointer: '<?php echo $pointer_id ?>',
                                        action: 'dismiss-wp-pointer'
                                    });
                                }
                            }).pointer('open');
                        <?php endforeach ?>
                    }
                });
            // ]]></script>
            <?php
        endif;
    }
}