<?php
class PNV_Admin {
    private static $current_screen_pointers = array();

    /**
     * Register hooks used on admin side by the plugin
     */
    public static function hooks() {
        add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ), 10, 2 );

        // 2 hooks because of 2 types of content types: hierarchical (page) or not (post)
        add_filter( 'page_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );
        add_filter( 'post_row_actions', array( __CLASS__, 'row_actions' ), 10, 2 );

        add_filter( 'post_updated_messages', array( __CLASS__, 'post_updated_messages' ) );
    }

    /**
     * Do some actions at the beginning of an admin script
     */
    public static function admin_init() {
        self::handle_action();

        // Add other hooks
        $post_type = PNV_Option::get_post_types();
        foreach( $post_type as $type ) {
            add_action( 'manage_' . $type . '_posts_columns', array( __CLASS__, 'manage_posts_columns' ) );
            add_action( 'manage_' . $type . '_posts_custom_column', array( __CLASS__, 'manage_posts_custom_column' ), 10, 2 );
        }

        add_action( 'admin_print_styles', array( __CLASS__, 'admin_print_styles' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
        add_action( 'admin_head-post.php', array( __CLASS__, 'admin_head_post' ) );
    }

    /**
     * Handle duplicata / copy creation
     */
    public static function handle_action() {
        if( !isset( $_GET[PNV_ACTION_NAME] ) || !isset( $_GET['post'] ) || !check_admin_referer( PNV_ACTION_NONCE . '_' . $_GET[PNV_ACTION_NAME] . '_' . $_GET['post'] ) )
            return;

        $source = get_post( $_GET['post'] );
        $post_types = PNV_Option::get_post_types();

        // Check post type
        if( !in_array( $source->post_type, $post_types ) )
            return;

        $post_type_object = get_post_type_object( $source->post_type );

        // Check user rights on that post
        if( !current_user_can( $post_type_object->cap->edit_post, $source->ID ) )
            return;

        switch( $_GET[PNV_ACTION_NAME] ) {
            case PNV_DUPLICATE_ACTION:
                $post_id = PNV::erase_content( $source );
                break;
            case PNV_COPY_ACTION:
                // Copy status is "draft"
                $source->post_status = 'draft';
                $post_id = PNV::erase_content( $source, NULL, $_GET[PNV_ACTION_NAME] );
                break;
            case PNV_ERASE_ACTION:
                $destination = get_post( PNV::get_original( $_GET['post'] ) );
                $post_id = PNV::erase_content( $source, $destination, $_GET[PNV_ACTION_NAME] );
                break;
        }

        if( !isset( $post_id ) || empty( $post_id ) )
            return;

        $url = add_query_arg( array(
            'post' => $post_id,
            'action' => 'edit',
        ), admin_url( '/post.php' ) );

        if( !( $url = apply_filters( 'pnv_action_url_redirect', $url, $post_id ) ) )
            return;

        wp_safe_redirect($url);
        exit;
    }

    /**
     * Add meta box with links to duplicatas
     */
    public static function add_meta_boxes( $current_post_type, $post = null ) {
        $post_type = PNV_Option::get_post_types();

        // Post type not supported: don't do anything
        if( !in_array( $current_post_type, $post_type ) )
            return;

        // Be sure we have a post
        $post = !empty( $post ) ? $post : get_post();

        $title = PNV_STR_DUPLICATA_META_BOX_TITLE;

        // If we are on a duplicata, remove default submit meta box and replace it with our box
        if( PNV::is_duplicata( $post->ID ) ) {
            remove_meta_box( 'submitdiv', $current_post_type, 'side' );
            add_meta_box( 'pnv_submit_meta_box', PNV_STR_PUBLISH_META_BOX_TITLE, array( __CLASS__, 'submit_meta_box' ), $current_post_type, 'side', 'core' );

            // Replace "duplicates" meta box title
            $title = PNV_STR_OTHER_DUPLICATA_META_BOX_TITLE;
        }

        add_meta_box( 'pnv_duplicata_meta_box', $title, array( __CLASS__, 'duplicata_meta_box' ), $current_post_type, 'side', 'core' );
    }

    /**
     * Display duplicata meta box
     */
    public static function duplicata_meta_box() {
        $post = get_post();
        $original = PNV::get_original();

        require PNV_COMPLETE_PATH . '/template/duplicata_meta_box.php';
    }

    /**
     * Display publish meta box
     */
    public static function submit_meta_box() {
        $post = get_post();
        $original = PNV::get_original();

        require PNV_COMPLETE_PATH . '/template/submit_meta_box.php';
    }

    /**
     * Add columns to the post types lists
     */
    public static function manage_posts_columns( $columns ) {
        global $wp_list_table;

        $current_screen = get_current_screen();

        // get_current_screen() could return null (in AJAX context for ex, when quick editing a post)
        if( !$current_screen )
            return $columns;

        $post_type_obj = get_post_type_object( $current_screen->post_type );

        // If we cannot create posts of that type, we cannot see duplicatas
        if( !current_user_can( $post_type_obj->cap->edit_posts ) )
            return $columns;

        if( self::is_duplicata_listing() || $wp_list_table->is_trash )
            $columns+= array( 'original' => PNV_STR_ORIGINAL_COLUMN_TITLE );

        if( !self::is_duplicata_listing() )
            $columns+= array( 'duplicata' => PNV_STR_DUPLICATA_COLUMN_TITLE );

        return $columns;
    }

    /**
     * Display data for added columns
     */
    public static function manage_posts_custom_column( $column, $post_id ) {
        $val = '';

        switch( $column ) {
            case 'duplicata':
                $duplicata = PNV::get_duplicata( $post_id );
                $val = count( $duplicata );
                break;
            case 'original':
                $original = PNV::get_original( $post_id );
                $val = !empty( $original ) ? '<a href="' . esc_url( add_query_arg( array( 'post' => $original, 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . get_the_title( $original ) . '</a>' : ' - ';
                break;
        }

        echo apply_filters( 'pnv_' . $column . '_column_value', $val, $post_id );
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
    public static function admin_print_styles() {
        $screen = get_current_screen();
        $post_type = PNV_Option::get_post_types();

        // Don't include our styles if we don't need it on that screen
        if( !in_array( $screen->base, array( 'post', 'edit' ) ) || !in_array( $screen->post_type, $post_type ) )
            return;

        wp_enqueue_style( 'pnv_admin_css', PNV_URL . '/css/pnv_admin.css' );
    }

    /**
     * Enqueue scripts on listing WordPress pages
     */
    public static function admin_enqueue_scripts( $hook ) {
        // Display pointer to invite people duplicate posts
        self::show_pointers();

        if( 'edit.php' !== $hook )
            return;

        wp_enqueue_script( 'pnv_admin_js', PNV_URL . '/js/pnv_admin.js', array(), NULL, TRUE );
    }

    /**
     * Retrieves pointers for the current admin screen. Use the 'PNV_admin_pointers' hook to add your own pointers.
     * @return array Current screen pointers
     */
    private static function get_current_screen_pointers(){
        $pointers = '';

        $screen = get_current_screen();
        $screen_id = $screen->id;

        // Format : array( 'screen_id' => array( 'pointer_id' => array([options : target, content, position...]) ) );

        $default_pointers = array(
            'plugins' => array(
                'pnv_install' => array(
                    'target' => '#menu-posts',
                    'content' => '<h3>'. PNV_STR_ACTIVATION_POINTER_TITLE .'</h3> <p>'. PNV_STR_ACTIVATION_POINTER_CONTENT .'</p>',
                    'position' => array( 'edge' => 'top', 'align' => 'top' ),
                ),
            ),
        );

        if( !empty( $default_pointers[$screen_id] ) )
            $pointers = $default_pointers[$screen_id];

        return apply_filters( 'pnv_admin_pointers', $pointers, $screen_id );
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
                                content: '<?php echo addslashes( $data['content'] ) ?>',
                                position: {
                                    edge: '<?php echo addslashes( $data['position']['edge'] ) ?>',
                                    align: '<?php echo addslashes( $data['position']['align'] ) ?>'
                                },
                                close: function() {
                                    $.post( ajaxurl, {
                                        pointer: '<?php echo addslashes( $pointer_id ) ?>',
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

    /**
     * Add row actions on post list tables
     */
    public static function row_actions( $actions, $post ) {
        $post_types = PNV_Option::get_post_types();

        // Don't do anything on an unsupported post type
        if( !in_array( $post->post_type, $post_types ) )
            return $actions;

        // Add "Prepare new version" action
        $action_url = PNV::get_action_url( $post );
        $actions['prepare_new_version'] = '<a href="' . add_query_arg( PNV_ACTION_NAME, PNV_DUPLICATE_ACTION, $action_url ) . '" title="' . esc_attr( PNV_STR_DUPLICATE_BUTTON ) . '">' . PNV_STR_DUPLICATE_BUTTON . '</a>';

        return $actions;
    }

    /**
     * Hide "Add new" button from a duplicate edit screen
     * Edit title from that screen
     */
    public static function admin_head_post() {
        global $post_new_file, $post, $title;

        // We're not on a duplicate: don't do anything
        if( !PNV::is_duplicata( $post ) )
            return;

        $post_new_file = null;
        $title.= ' ' . PNV_STR_VERSION;
    }

    /**
     * Add a message to admin screens to display that a post is a duplicate
     */
    public static function post_updated_messages( $messages ) {
        global $post;

        if( !PNV::is_duplicata() )
            return $messages;

        $post_types = PNV_Option::get_post_types();
        $original = PNV::get_original();

        foreach( $post_types as $post_type ) {
            if( $post_type !== $post->post_type )
                continue;

            $index = !empty( $messages[$post_type] ) ? count( $messages[$post_type] ) : 0;
            $messages[$post_type][$index] = PNV_STR_MESSAGE_DUPLICATE . ' <a href="' . esc_url( add_query_arg( array( 'post' => $original, 'action' => 'edit' ), admin_url( 'post.php' ) ) ) . '">' . get_the_title( $original ) . '</a>';

            if( !isset( $_GET['message'] ) )
                $_GET['message'] = $index;
        }

        return $messages;
    }
}