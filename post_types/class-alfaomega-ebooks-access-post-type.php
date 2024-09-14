<?php

use AlfaomegaEbooks\Services\eBooks\Service;
use Carbon_Fields\Container;
use Carbon_Fields\Field\Field;

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
            add_action('carbon_fields_register_fields', [$this, 'add_meta_boxes_view']);
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
                        'name'          => esc_html__('Alfaomega eBooks access', 'alfaomega-ebook'),
                        'singular_name' => esc_html__('AO eBook Access', 'alfaomega-ebook'),
                    ],
                    'public'              => true,
                    //'supports'            => ['title', 'author', 'thumbnail'],
                    'supports'            => false,
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
                'alfaomega_access_cover'    => esc_html__('Cover', 'alfaomega-ebooks'),
                'title'                     => esc_html__('Title', 'alfaomega-ebooks'),
                'alfaomega_access_isbn'     => esc_html__('ISBN', 'alfaomega-ebooks'),
                'alfaomega_access_type'     => esc_html__('Type', 'alfaomega-ebooks'),
                'alfaomega_access_status'   => esc_html__('Status', 'alfaomega-ebooks'),
                'alfaomega_access_read'     => esc_html__('Read', 'alfaomega-ebooks'),
                'alfaomega_access_download' => esc_html__('Download', 'alfaomega-ebooks'),
                // 'categories'                => esc_html__('Categories', 'alfaomega-ebook'),
                'author'                    => esc_html__('Client', 'alfaomega-ebooks'),
                'alfaomega_access_due_date' => esc_html__('Due date', 'alfaomega-ebooks'),
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
                case 'alfaomega_access_cover':
                    // echo esc_html( get_post_meta( $post_id, 'alfaomega_access_cover', true ) );
                    echo '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id .'&action=edit">';
                    echo '  <img width="50" height="60" src="' . get_post_meta( $post_id, 'alfaomega_access_cover', true ) . '"';
                    echo '    class="attachment-thumbnail size-thumbnail" alt="" decoding="async"';
                    echo '</a>';
                    break;
                case 'alfaomega_access_isbn':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_access_isbn', true ) );
                break;
                case 'alfaomega_access_type':
                    echo esc_html__(get_post_meta( $post_id, 'alfaomega_access_type', true ), 'alfaomega-ebooks');
                break;
                case 'alfaomega_access_status':
                    echo esc_html__(get_post_meta( $post_id, 'alfaomega_access_status', true ), 'alfaomega-ebooks');
                    break;
                case 'alfaomega_access_read':
                    $read = get_post_meta( $post_id, 'alfaomega_access_read', true );
                    echo $read == 1
                        ? esc_html__('Yes', 'alfaomega-ebooks')
                        : esc_html__('No', 'alfaomega-ebooks');
                    break;
                case 'alfaomega_access_download':
                    $download = get_post_meta( $post_id, 'alfaomega_access_read', true );
                    echo $download == 1
                        ? esc_html__('Yes', 'alfaomega-ebooks')
                        : esc_html__('No', 'alfaomega-ebooks');
                    break;
                case 'alfaomega_access_due_date':
                    $dueDate = get_post_meta( $post_id, 'alfaomega_access_due_date', true );
                    echo $dueDate == ''
                        ? esc_html__('Unlimited', 'alfaomega-ebooks')
                        : Carbon\Carbon::parse($dueDate)->format('d/m/Y');
                    break;
                /*case 'categories':
                    $terms = get_the_terms($post_id, 'product_cat');
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $term_names = wp_list_pluck($terms, 'name');
                        echo esc_html(join(', ', $term_names));
                    } else {
                        echo esc_html__('No Categories', 'alfaomega-ebook');
                    }
                    break;*/
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
         *
         * @return void
         * @throws \Exception
         * @since  1.0.0
         * @access public
         */
        public function add_meta_boxes_view() : void
        {
            global $pagenow;

            if ($pagenow === 'post.php') {
                $accessPost = Service::make()
                    ->ebooks()
                    ->accessPost()
                    ->get($_GET['post']);

                if (empty($accessPost)) {
                    return;
                }

                Container::make('post_meta', __('View eBook Access', 'alfaomega-ebooks'))
                    ->where('post_type', '=', 'alfaomega-access')
                    ->add_fields([
                        Field::make('textarea', 'alfaomega_access_isbn', __('eBook', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_rows(2)
                            ->set_width(50)
                            ->set_default_value($accessPost['title'] . " ({$accessPost['isbn']})")
                            ->set_help_text(__('The eBook to access to', 'alfaomega-ebooks')),

                        Field::make( 'radio_image', 'alfaomega_access_cover', __( 'Cover', 'alfaomega-ebooks' ) )
                            ->set_options( [
                                'cover' => $accessPost['cover'],
                            ])
                            ->set_help_text(__('The cover of the eBook', 'alfaomega-ebooks'))
                            ->set_width(50),

                        Field::make('text', 'alfaomega_access_type', __('Type', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(33)
                            ->set_help_text(__('The access type', 'alfaomega-ebooks'))
                            ->set_default_value(__($accessPost['type'], 'alfaomega-ebooks')),

                        Field::make('text', 'alfaomega_access_status', __('Status', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(33)
                            ->set_help_text(__('The access status', 'alfaomega-ebooks'))
                            ->set_default_value(__($accessPost['status'], 'alfaomega-ebooks')),

                        Field::make('text', 'alfaomega_access_due_date', __('Due date', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(33)
                            ->set_help_text(__('Access validity', 'alfaomega-ebooks'))
                            ->set_default_value($accessPost['due_date'] == ''
                                ? esc_html__('Unlimited', 'alfaomega-ebooks')
                                : Carbon\Carbon::parse($accessPost['due_date'])->format('d/m/Y')),

                        Field::make('text', 'alfaomega_access_author', __('Client', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(33)
                            ->set_help_text(__('Client allowed to access', 'alfaomega-ebooks'))
                            ->set_default_value($accessPost['user_email']),

                        Field::make('text', 'alfaomega_access_read', __('Read online', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(33)
                            ->set_help_text(__('Access to read online the eBook', 'alfaomega-ebooks'))
                            ->set_default_value($accessPost['read']
                                ? esc_html__('Yes', 'alfaomega-ebooks')
                                : esc_html__('No', 'alfaomega-ebooks')),

                        Field::make('text', 'alfaomega_access_download', __('Download', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(33)
                            ->set_help_text(__('download the PDF with DRM', 'alfaomega-ebooks'))
                            ->set_default_value($accessPost['read']
                                ? esc_html__('Yes', 'alfaomega-ebooks')
                                : esc_html__('No', 'alfaomega-ebooks'))

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
            } else {
                return;
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
            }*/
        }

        /**
         * Get status list
         * @return array
         * @since 1.0.0
         * @access public
         */
        public function get_status_list(): array
        {
            return [
                'created' => esc_html__('Created', 'alfaomega-ebooks'),
                'pending' => esc_html__('Pending', 'alfaomega-ebooks'),
                'active' => esc_html__('Active', 'alfaomega-ebooks'),
                'expired' => esc_html__('Expired', 'alfaomega-ebooks'),
                'cancelled' => esc_html__('Cancelled', 'alfaomega-ebooks'),
            ];
        }

        /**
         * Get type list
         * @return array
         * @since 1.0.0
         * @access public
         */
        public function get_type_list(): array
        {
            return [
                'sample'   => esc_html__('sample', 'alfaomega-ebooks'),
                'purchase' => esc_html__('purchase', 'alfaomega-ebooks'),
                'import'   => esc_html__('import', 'alfaomega-ebooks'),
            ];
        }

    }
}
