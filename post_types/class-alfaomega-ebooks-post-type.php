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
            add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
            add_filter( 'manage_alfaomega-ebook_posts_columns', [$this, 'alfaomega_ebook_cpt_columns'] );
            add_action( 'manage_alfaomega-ebook_posts_custom_column', [$this, 'alfaomega_ebook_custom_columns'], 10, 2 );
            add_filter( 'manage_edit-alfaomega-ebook_sortable_columns', [$this, 'alfaomega_ebook_sortable_columns'] );
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
                    'show_in_menu'        => false,
                    'menu_position'       => 56,  // bellow WooCommerce Products
                    'show_in_admin_bar'   => true,
                    'show_in_nav_menus'   => false,
                    'can_export'          => true,
                    'has_archive'         => false,
                    'exclude_from_search' => true,
                    'publicly_queryable'  => false,
                    'show_in_rest'        => true,
                    'menu_icon'           => 'dashicons-book',
                    'capability_type'     => 'post',
                    'capabilities'        => [
                        'create_posts' => false, // Removes the ability to add new
                    ],
                    'map_meta_cap'        => true,
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
        public function alfaomega_ebook_cpt_columns($columns): array
        {
            return [
                'cb'                     => $columns['cb'],
                'title'                  => esc_html__('Title', 'alfaomega-ebook'),
                'alfaomega_ebook_isbn'   => esc_html__('Digital ISBN', 'alfaomega-ebook'),
                'alfaomega_ebook_id'     => esc_html__('PDF Id', 'alfaomega-ebook'),
                'alfaomega_ebook_url'    => esc_html__('HTML Url', 'alfaomega-ebook'),
                'alfaomega_ebook_tag_id' => esc_html__('Tag Id', 'alfaomega-ebook'),
                'date'                   => esc_html__('Date', 'alfaomega-ebook'),
            ];
        }

        /**
         * Manage plugin table custom columns content
         * @return void
         * @since 1.0.0
         * @access public
         * @param string $column  Column name
         * @param int $post_id  Post ID
         */
        public function alfaomega_ebook_custom_columns( $column, $post_id ): void
        {
            switch( $column ){
                case 'alfaomega_ebook_isbn':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_ebook_isbn', true ) );
                break;
                case 'alfaomega_ebook_id':
                    echo esc_url( get_post_meta( $post_id, 'alfaomega_ebook_id', true ) );
                break;
                case 'alfaomega_ebook_url':
                    echo esc_url( get_post_meta( $post_id, 'alfaomega_ebook_url', true ) );
                    break;
                case 'alfaomega_ebook_tag_id':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_ebook_tag_id', true ) );
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
        public function alfaomega_ebook_sortable_columns( $columns ): array
        {
            $columns['alfaomega_ebook_isbn'] = 'alfaomega_ebook_isbn';
            $columns['alfaomega_ebook_id'] = 'alfaomega_ebook_id';
            $columns['alfaomega_ebook_url'] = 'alfaomega_ebook_url';
            $columns['alfaomega_ebook_tag_id'] = 'alfaomega_ebook_tag_id';
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
            //$meta = get_post_meta( $post->ID );
            $isbn = get_post_meta( $post->ID, 'alfaomega_ebook_isbn', true );
            $id = get_post_meta( $post->ID, 'alfaomega_ebook_id', true );
            $url = get_post_meta( $post->ID, 'alfaomega_ebook_url', true );
            $tag_id = get_post_meta( $post->ID, 'alfaomega_ebook_tag_id', true );
            require_once( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_metabox.php' );
        }

        /**
         * Save post
         * @return void
         * @since 1.0.0
         * @access public
         * @param int $post_id  Post ID to be saved
         */
        public function save_post( $post_id ): void
        {
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
                $fields = [
                    'alfaomega_ebook_isbn' => [
                        'old'     => get_post_meta($post_id, 'alfaomega_ebook_isbn', true),
                        'new'     => $_POST['alfaomega_ebook_isbn'],
                        'default' => '',
                    ],
                    'alfaomega_ebook_id'   => [
                        'old'     => get_post_meta($post_id, 'alfaomega_ebook_id', true),
                        'new'     => $_POST['alfaomega_ebook_id'],
                        'default' => '',
                    ],
                    'alfaomega_ebook_url'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_ebook_url', true),
                        'new'     => $_POST['alfaomega_ebook_url'],
                        'default' => '',
                    ],
                    'alfaomega_ebook_tag_id'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_ebook_tag_id', true),
                        'new'     => $_POST['alfaomega_ebook_url'],
                        'default' => '',
                    ],
                ];

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
