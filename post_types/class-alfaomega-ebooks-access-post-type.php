<?php 

if( !class_exists('Alfaomega_Ebooks_Access_Post_Type') ){
    class Alfaomega_Ebooks_Access_Post_Type{

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
            add_filter( 'manage_alfaomega-access_posts_columns', [$this, 'alfaomega_ebook_access_cpt_columns'] );
            add_action( 'manage_alfaomega-access_posts_custom_column', [$this, 'alfaomega_ebook_access_custom_columns'], 10, 2 );
            add_filter( 'manage_edit-alfaomega-access_sortable_columns', [$this, 'alfaomega_ebook_access_sortable_columns'] );
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
                ALFAOMEGA_EBOOKS_ACCESS_POST_TYPE,
                [
                    'label'               => esc_html__('AO eBook Access', 'alfaomega-ebook'),
                    'description'         => esc_html__('Alfaomega eBook Access', 'alfaomega-ebook'),
                    'labels'              => [
                        'name'          => esc_html__('Alfaomega eBook Access', 'alfaomega-ebook'),
                        'singular_name' => esc_html__('AO eBook Access', 'alfaomega-ebook'),
                    ],
                    'public'              => true,
                    'supports'            => ['title', 'author', 'thumbnail'],
                    'hierarchical'        => false,
                    'show_ui'             => true,
                    'show_in_menu'        => false,
                    'menu_position'       => 56,  // bellow WooCommerce Products
                    'show_in_admin_bar'   => false,
                    'show_in_nav_menus'   => false,
                    'can_export'          => true,
                    'has_archive'         => false,
                    'exclude_from_search' => true,
                    'publicly_queryable'  => false,
                    'show_in_rest'        => true,
                    'menu_icon'           => 'dashicons-admin-network',
                    'capability_type'     => 'post',
                    'capabilities'        => [
                        'create_posts' => false, // Removes the ability to add new
                    ],
                    'map_meta_cap'        => true,
                    //'register_meta_box_cb'  =>  array( $this, 'add_meta_boxes' )
                    'taxonomies'          => ['product_cat'],
                ]
            );
            flush_rewrite_rules();
        }

        /**
         * Manage plugin table columns
         * @return void
         * @since 1.0.0
         * @access public
         * @param array $columns  Columns array
         */
        public function alfaomega_ebook_access_cpt_columns($columns): array
        {
            return [
                'cb'                        => $columns['cb'],
                'alfaomega_access_cover'    => "Cover",
                'title'                     => esc_html__('Title', 'alfaomega-ebook'),
                //'alfaomega_access_isbn'     => esc_html__('ISBN', 'alfaomega-ebook'),
                //'alfaomega_access_type'     => esc_html__('Type', 'alfaomega-ebook'),
                'alfaomega_access_status'   => esc_html__('Status', 'alfaomega-ebook'),
                'alfaomega_access_read'     => esc_html__('Read', 'alfaomega-ebook'),
                'alfaomega_access_download' => esc_html__('Download', 'alfaomega-ebook'),
                'author'                    => esc_html__('Usuario', 'alfaomega-ebook'),
                'alfaomega_access_due_date' => esc_html__('Due date', 'alfaomega-ebook'),
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
        public function alfaomega_ebook_access_custom_columns( $column, $post_id ): void
        {
            switch( $column ){
                case 'alfaomega_ebook_cover':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_access_cover', true ) );
                    break;
                case 'alfaomega_ebook_isbn':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_access_isbn', true ) );
                break;
                case 'alfaomega_access_type':
                    echo esc_url( get_post_meta( $post_id, 'alfaomega_access_type', true ) );
                break;
                case 'alfaomega_access_status':
                    echo esc_url( get_post_meta( $post_id, 'alfaomega_access_status', true ) );
                    break;
                case 'alfaomega_access_read':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_access_read', true ) );
                    break;
                case 'alfaomega_access_download':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_access_download', true ) );
                    break;
                case 'alfaomega_access_due_date':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_access_due_date', true ) );
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
        public function alfaomega_ebook_access_sortable_columns( $columns ): array
        {
            //$columns['alfaomega_access_type'] = 'alfaomega_access_type';
            $columns['alfaomega_access_status'] = 'alfaomega_access_status';
            $columns['alfaomega_access_due_date'] = 'alfaomega_access_due_date';
            $columns['author'] = 'author';
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
                'alfaomega_ebook_access_meta_box',
                esc_html__('eBook Access Information', 'alfaomega-ebook'),
                [$this, 'add_inner_meta_boxes'],
                ALFAOMEGA_EBOOKS_ACCESS_POST_TYPE,
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
            $cover = get_post_meta( $post->ID, 'alfaomega_access_cover', true );
            $isbn = get_post_meta( $post->ID, 'alfaomega_access_isbn', true );
            $type = get_post_meta( $post->ID, 'alfaomega_access_type', true );
            $order_id = get_post_meta( $post->ID, 'alfaomega_access_order_id', true );
            $sample_id = get_post_meta( $post->ID, 'alfaomega_access_sample_id', true );
            $status = get_post_meta( $post->ID, 'alfaomega_access_status', true );
            $read = get_post_meta( $post->ID, 'alfaomega_access_read', true );
            $download = get_post_meta( $post->ID, 'alfaomega_access_download', true );
            $due_date = get_post_meta( $post->ID, 'alfaomega_access_due_date', true );
            require_once( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_access_metabox.php' );
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
                if( ! wp_verify_nonce( $_POST['alfaomega_ebook_access_nonce'], 'alfaomega_ebook_access_nonce' ) ){
                    return;
                }
            }

            // 2. Check if we're doing autosave
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }

            // 3. Check if user has permissions to save data
            if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'alfaomega-access' ){
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
                    'alfaomega_access_cover' => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_cover', true),
                        'new'     => $_POST['alfaomega_access_cover'],
                        'default' => '',
                    ],
                    'alfaomega_access_isbn' => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_isbn', true),
                        'new'     => $_POST['alfaomega_access_isbn'],
                        'default' => 'ISBN',
                    ],
                    'alfaomega_access_type'   => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_type', true),
                        'new'     => $_POST['alfaomega_access_type'],
                        'default' => 'purchase',
                    ],
                    'alfaomega_access_order_id'   => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_order_id', true),
                        'new'     => $_POST['alfaomega_access_order_id'],
                        'default' => '',
                    ],
                    'alfaomega_access_sample_id'   => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_sample_id', true),
                        'new'     => $_POST['alfaomega_access_sample_id'],
                        'default' => '',
                    ],
                    'alfaomega_access_status'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_status', true),
                        'new'     => $_POST['alfaomega_access_status'],
                        'default' => 'created',
                    ],
                    'alfaomega_access_read'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_read', true),
                        'new'     => $_POST['alfaomega_access_read'],
                        'default' => 1,
                    ],
                    'alfaomega_access_download'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_download', true),
                        'new'     => $_POST['alfaomega_access_download'],
                        'default' => 1,
                    ],
                    'alfaomega_access_due_date'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_due_date', true),
                        'new'     => $_POST['alfaomega_access_due_date'],
                        'default' => '',
                    ],
                    'alfaomega_access_download_at'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_download_at', true),
                        'new'     => $_POST['alfaomega_access_download_at'],
                        'default' => '',
                    ],
                    'alfaomega_access_read_at'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_access_read_at', true),
                        'new'     => $_POST['alfaomega_access_read_at'],
                        'default' => '',
                    ],
                ];

                // Loop through the array and save the data
                foreach ( $fields as $field => $data ) {
                    $new_value = sanitize_text_field( $data['new'] );
                    $old_value = $data['old'];
            
                    if ( $new_value === '' ) {
                        $new_value = $data['default'];
                    }
            
                    update_post_meta( $post_id, $field, $new_value, $old_value );
                }
            }
        }

    }
}
