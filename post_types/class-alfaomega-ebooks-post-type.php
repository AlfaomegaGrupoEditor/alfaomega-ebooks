<?php

use AlfaomegaEbooks\Services\eBooks\Service;
use Carbon_Fields\Container;
use Carbon_Fields\Field\Field;

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
            add_action('carbon_fields_register_fields', [$this, 'add_meta_boxes_view']);
            add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
            add_filter( 'manage_alfaomega-ebook_posts_columns', [$this, 'alfaomega_ebook_cpt_columns'] );
            add_action( 'manage_alfaomega-ebook_posts_custom_column', [$this, 'alfaomega_ebook_custom_columns'], 10, 2 );
            add_filter( 'manage_edit-alfaomega-ebook_sortable_columns', [$this, 'alfaomega_ebook_sortable_columns'] );
            add_action('admin_head', [$this, 'custom_admin_css']);
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
                    'supports'            => false,
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
            flush_rewrite_rules();
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
                'cb'                          => $columns['cb'],
                'alfaomega_ebook_cover'       => esc_html__('Cover', 'alfaomega-ebooks'),
                'title'                       => esc_html__('Title', 'alfaomega-ebook'),
                'alfaomega_ebook_isbn'        => esc_html__('Digital ISBN', 'alfaomega-ebook'),
                'alfaomega_ebook_id'          => esc_html__('PDF', 'alfaomega-ebook'),
                'alfaomega_ebook_url'         => esc_html__('HTML', 'alfaomega-ebook'),
                'alfaomega_ebook_product_sku' => esc_html__('Product SKU', 'alfaomega-ebook'),
                'date'                        => esc_html__('Date', 'alfaomega-ebook'),
            ];
        }

        /**
         * Manage plugin table custom columns content
         *
         * @param string $column Column name
         * @param int $post_id   Post ID
         *
         * @return void
         * @throws \Exception
         * @since  1.0.0
         * @access public
         */
        public function alfaomega_ebook_custom_columns( $column, $post_id ): void
        {
            $ebookPost = Service::make()
                ->ebooks()
                ->ebookPost()
                ->get($post_id);

            switch( $column ){
                case 'alfaomega_ebook_cover':
                    echo '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id .'&action=edit">';
                    echo '  <img width="50" height="60" src="' . ALFAOMEGA_COVER_PATH . $ebookPost['cover'] . '"';
                    echo '    class="attachment-thumbnail size-thumbnail" alt="" decoding="async"';
                    echo '</a>';
                    break;
                case 'alfaomega_ebook_isbn':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_ebook_isbn', true ) );
                break;
                case 'alfaomega_ebook_id':
                    $pdf = get_post_meta( $post_id, 'alfaomega_ebook_id', true );
                    echo !empty($pdf)
                        ? esc_html__('Yes', 'alfaomega-ebooks')
                        : esc_html__('No', 'alfaomega-ebooks');
                    break;
                case 'alfaomega_ebook_url':
                    $html = get_post_meta( $post_id, 'alfaomega_ebook_url', true );
                    echo !empty($html)
                        ? esc_html__('Yes', 'alfaomega-ebooks')
                        : esc_html__('No', 'alfaomega-ebooks');
                    break;
                    break;
                case 'alfaomega_ebook_product_sku':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_ebook_product_sku', true ) );
                    break;
            }
        }

        /**
         * Custom admin CSS
         * @return void
         * @since 1.0.0
         * @access public
         */
        function custom_admin_css(): void
        {
            if (isset($_GET['post_type']) && $_GET['post_type'] === 'alfaomega-ebook') {
                echo '<style>
                .column-cb { width: 5% !important; }
                .column-alfaomega_ebook_cover { width: 10% !important; }
                .column-title { width: 30%!important; }
                .column-alfaomega_ebook_isbn { width: 10% !important; }
                .column-alfaomega_ebook_id { width: 10% !important; }
                .column-alfaomega_ebook_url { width: 10% !important; }
                .column-alfaomega_ebook_product_sku { width: 10% !important; }
                .column-date { width: 15%; }
            </style>';
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
            $columns['alfaomega_ebook_product_sku'] = 'alfaomega_ebook_product_sku';
            return $columns;
        }

        /**
         * Add meta boxes
         *
         * @return void
         * @throws \Exception
         * @since  1.0.0
         * @access public
         */
        public function add_meta_boxes_view() : void
        {
            global $pagenow;

            if ($pagenow === 'post.php' && !empty($_GET['post'])) {
                $ebookPost = Service::make()
                    ->ebooks()
                    ->ebookPost()
                    ->get($_GET['post']);

                if (empty($ebookPost)) {
                    return;
                }

                Container::make('post_meta', __('View eBooks', 'alfaomega-ebooks'))
                    ->where('post_type', '=', 'alfaomega-ebook')
                    ->add_fields([
                        Field::make('textarea', 'alfaomega_ebook_isbn', __('eBook', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_rows(2)
                            ->set_width(50)
                            ->set_default_value($ebookPost['title'] . " ({$ebookPost['isbn']})")
                            ->set_help_text(__('The eBook title and digital ISBN', 'alfaomega-ebooks')),

                        Field::make( 'radio_image', 'alfaomega_ebook_cover', __( 'Cover', 'alfaomega-ebooks' ) )
                            ->set_options( [
                                'cover' => $ebookPost['cover'],
                            ])
                            ->set_help_text(__('The cover of the eBook', 'alfaomega-ebooks'))
                            ->set_width(50),

                        Field::make('text', 'alfaomega_access_download', __('PDF file', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(25)
                            ->set_help_text(__('Download the PDF with DRM', 'alfaomega-ebooks'))
                            ->set_default_value(!empty($ebookPost['pdf_id'])
                                ? esc_html__('Yes', 'alfaomega-ebooks')
                                : esc_html__('No', 'alfaomega-ebooks')),

                        Field::make('text', 'alfaomega_access_read', __('HTML eBook', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(25)
                            ->set_help_text(__('HTML eBook to read online', 'alfaomega-ebooks'))
                            ->set_default_value(!empty($ebookPost['ebook_url'])
                                ? esc_html__('Yes', 'alfaomega-ebooks')
                                : esc_html__('No', 'alfaomega-ebooks')),

                        Field::make('text', 'alfaomega_access_product', __('Linked Product', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(25)
                            ->set_help_text(__('Store product linked to this eBook', 'alfaomega-ebooks'))
                            ->set_default_value($ebookPost['product_sku']),

                        Field::make('text', 'alfaomega_access_date', __('Updated At', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(25)
                            ->set_help_text(__('Date when the eBook was updated', 'alfaomega-ebooks'))
                            ->set_default_value(Carbon\Carbon::parse($ebookPost['date'])->format('d/m/Y h:i A')),
                    ]);
            }
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
            return;

            /*// A series of guard clauses to make sure we are saving the right data
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
            } else {
                return;
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
                    'alfaomega_ebook_product_sku'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_ebook_product_sku', true),
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
            }*/
        }

    }
}
