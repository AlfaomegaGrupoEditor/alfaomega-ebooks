<?php 

if( !class_exists('Alfaomega_Ebooks_Post_Type') ){
    class Alfaomega_Ebooks_Post_Type{

        /**
         * Constructor
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function __construct(){
            add_action('init', [$this, 'create_post_type']);
            add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
//            add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
//            add_filter( 'manage_mv-slider_posts_columns', array( $this, 'alfaomega_ebook_cpt_columns' ) );
//            add_action( 'manage_mv-slider_posts_custom_column', array( $this, 'alfaomega_ebook_custom_columns'), 10, 2 );
//            add_filter( 'manage_edit-mv-slider_sortable_columns', array( $this, 'alfaomega_ebook_sortable_columns' ) );
        }

        /**
         * Create post type
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function create_post_type(): void
        {
            register_post_type(
                ALFAOMEGA_EBOOKS_POST_TYPE,
                [
                    'label'               => esc_html__('AO eBook', 'alfaomega-ebook'),
                    'description'         => esc_html__('Alfaomega eBooks', 'alfaomega-ebook'),
                    'labels'              => [
                        'name'          => esc_html__('AO eBooks', 'alfaomega-ebook'),
                        'singular_name' => esc_html__('AO eBook', 'alfaomega-ebook'),
                    ],
                    'public'              => true,
                    'supports'            => ['title', 'editor', 'author', 'thumbnail'],
                    'hierarchical'        => false,
                    'show_ui'             => true,
                    'show_in_menu'        => true,
                    'menu_position'       => 56,  // bellow WooCommerce Products
                    'show_in_admin_bar'   => true,
                    'show_in_nav_menus'   => false,
                    'can_export'          => true,
                    'has_archive'         => false,
                    'exclude_from_search' => true,
                    'publicly_queryable'  => false,
                    'show_in_rest'        => true,
                    'menu_icon'           => 'dashicons-book',
                    //'register_meta_box_cb'  =>  array( $this, 'add_meta_boxes' )
                ]
            );
        }

        /**
         * Manage plugin table columns
         * @return void
         * @since 1.0.0
         * @access public
         * @param array $columns  Columns array
         */
        public function alfaomega_ebook_cpt_columns( $columns ){
            $columns = array(
                'cb' => $columns['cb'],
                'title' => __( 'Title', 'alfaomega-ebook' ),
                'alfaomega_ebook_link_text' => esc_html__( 'Link Text', 'alfaomega-ebook' ),
                'alfaomega_ebook_link_url' => esc_html__( 'Link URL', 'alfaomega-ebook' ),
                'date' => __( 'Date', 'alfaomega-ebook' ),
            );
            return $columns;
        }

        /**
         * Manage plugin table custom columns content
         * @return void
         * @since 1.0.0
         * @access public
         * @param string $column  Column name
         * @param int $post_id  Post ID
         */
        public function alfaomega_ebook_custom_columns( $column, $post_id ){
            switch( $column ){
                case 'alfaomega_ebook_link_text':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_ebook_link_text', true ) );
                break;
                case 'alfaomega_ebook_link_url':
                    echo esc_url( get_post_meta( $post_id, 'alfaomega_ebook_link_url', true ) );
                break;                
            }
        }

        /**
         * Make plugin table columns sortable
         * @return void
         * @since 1.0.0
         * @access public
         * @param array $columns  Columns array
         */
        public function alfaomega_ebook_sortable_columns( $columns ){
            $columns['alfaomega_ebook_link_text'] = 'alfaomega_ebook_link_text';
            $columns['alfaomega_ebook_link_url'] = 'alfaomega_ebook_link_url';
            return $columns;
        }

        /**
         * Add meta boxes
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function add_meta_boxes() : void
        {
            add_meta_box(
                'alfaomega_ebook_meta_box',
                esc_html__('eBook Information', 'alfaomega-ebook'),
                [$this, 'add_inner_meta_boxes'],
                ALFAOMEGA_EBOOKS_POST_TYPE,
                'normal', // side
                'high'
            );
        }

        /**
         * Add inner meta boxes view
         * @return void
         * @since 1.0.0
         * @access public
         * @param object $post  Post object to be passed to the view
         */
        public function add_inner_meta_boxes( $post ): void
        {
//            $meta = get_post_meta( $post->ID );
//            $link_text = get_post_meta( $post->ID, 'alfaomega_ebook_link_text', true );
//            $link_url = get_post_meta( $post->ID, 'alfaomega_ebook_link_url', true );
//            require_once( MV_SLIDER_PATH . 'src/views/mv-slider_metabox.php' );
        }

        /**
         * Save post
         * @return void
         * @since 1.0.0
         * @access public
         * @param int $post_id  Post ID to be saved
         */
        public function save_post( $post_id ){
            // A series of guard clauses to make sure we are saving the right data
            // 1. Check if nonce is set
            if( isset( $_POST['alfaomega_ebook_nonce'] ) ){
                if( ! wp_verify_nonce( $_POST['alfaomega_ebook_nonce'], 'alfaomega_ebook_nonce' ) ){
                    return;
                }
            }

            // 2. Check if we're doing autosave
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }

            // 3. Check if user has permissions to save data
            if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'alfaomega-ebook' ){
                if( ! current_user_can( 'edit_page', $post_id ) ){
                    return;
                }elseif( ! current_user_can( 'edit_post', $post_id ) ){
                    return;
                }
            }

            // Now we can actually save the data
            // First, check if the form is sending the right POST action
            if ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) {
                // Populate an array with the fields we want to save
                $fields = array(
                    'alfaomega_ebook_link_text' => array(
                        'old' => get_post_meta( $post_id, 'alfaomega_ebook_link_text', true ),
                        'new' => $_POST['alfaomega_ebook_link_text'],
                        'default' => esc_html__( 'Add some text', 'alfaomega-ebook' ),
                    ),
                    'alfaomega_ebook_link_url' => array(
                        'old' => get_post_meta( $post_id, 'alfaomega_ebook_link_url', true ),
                        'new' => $_POST['alfaomega_ebook_link_url'],
                        'default' => '#',
                    ),
                );
            
                // Loop through the array and save the data
                foreach ( $fields as $field => $data ) {
                    $new_value = sanitize_text_field( $data['new'] );
                    $old_value = $data['old'];
            
                    if ( empty( $new_value ) ) {
                        $new_value = $data['default'];
                    }
            
                    update_post_meta( $post_id, $field, $new_value, $old_value );
                }
            }
        }

    }
}
