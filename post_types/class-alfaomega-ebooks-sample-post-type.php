<?php

use AlfaomegaEbooks\Http\RouteManager;
use AlfaomegaEbooks\Services\eBooks\Service;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

if( !class_exists('Alfaomega_Ebooks_Sample_Post_Type') ){
    class Alfaomega_Ebooks_Sample_Post_Type{

        /**
         * Constructor
         * @return void
         * @since 1.0.0
         * @access public
         */
        public function __construct(){
            add_action('init', [$this, 'create_post_type']);
            //add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
            add_action('carbon_fields_register_fields', [$this, 'add_meta_boxes']);
            add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
            add_filter( 'manage_alfaomega-sample_posts_columns', [$this, 'alfaomega_ebook_sample_cpt_columns'] );
            add_action( 'manage_alfaomega-sample_posts_custom_column', [$this, 'alfaomega_ebook_sample_custom_columns'], 10, 2 );
            add_filter( 'manage_edit-alfaomega-sample_sortable_columns', [$this, 'alfaomega_ebook_sample_sortable_columns'] );
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
                ALFAOMEGA_EBOOKS_SAMPLE_POST_TYPE,
                [
                    'label'               => esc_html__('AO eBook Sample', 'alfaomega-ebook'),
                    'description'         => esc_html__('Alfaomega eBook Sample', 'alfaomega-ebook'),
                    'labels'              => [
                        'name'               => esc_html__('Códigos de Muestras a Alfaomega eBooks', 'alfaomega-ebook'),
                        'singular_name'      => esc_html__('AO eBook Sample', 'alfaomega-ebook'),
                        'add_new'            => esc_html__('Generate', 'alfaomega-ebooks'),
                        'add_new_item'       => esc_html__('Generate eBook Access Samples', 'alfaomega-ebooks'),
                        'new_item'           => esc_html__('Generate', 'alfaomega-ebooks'),
                        'edit_item'          => esc_html__('Edit Sample Code', 'alfaomega-ebooks'),
                        'view_item'          => esc_html__('View Sample Code', 'alfaomega-ebooks'),
                        'all_items'          => esc_html__('All Sample Code', 'alfaomega-ebooks'),
                        'search_items'       => esc_html__('Search Sample Codes', 'alfaomega-ebooks'),
                        'parent_item_colon'  => esc_html__('Parent Sample Code:', 'alfaomega-ebooks'),
                        'not_found'          => esc_html__('No sample code found.', 'alfaomega-ebooks'),
                        'not_found_in_trash' => esc_html__('No sample code found in Trash.', 'alfaomega-ebooks'),
                    ],
                    'public'              => true,
                    'supports'            => false, // ['title', 'author'],
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
                    'menu_icon'           => 'dashicons-editor-code',
                    'capability_type'     => 'post',
                    /*'capabilities'        => [
                        'create_posts' => true, // Removes the ability to add new
                    ],*/
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
        public function alfaomega_ebook_sample_cpt_columns($columns): array
        {
            return [
                'cb'                            => $columns['cb'],
                'title'                         => esc_html__('Code', 'alfaomega-ebooks'),
                'author'                        => esc_html__('Author', 'alfaomega-ebooks'),
                'alfaomega_sample_description'  => esc_html__('Description', 'alfaomega-ebooks'),
                'alfaomega_sample_destination'  => esc_html__('Email', 'alfaomega-ebooks'),
                //'alfaomega_sample_promoter'    => esc_html__('Promoter', 'alfaomega-ebooks'),
                'alfaomega_sample_status'       => esc_html__('Status', 'alfaomega-ebooks'),
                //'alfaomega_access_payload'     => esc_html__('Payload', 'alfaomega-ebooks'),
                'alfaomega_sample_activated_at' => esc_html__('Activated at', 'alfaomega-ebooks'),
                'alfaomega_sample_due_date'     => esc_html__('Due date', 'alfaomega-ebooks'),
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
        public function alfaomega_ebook_sample_custom_columns( $column, $post_id ): void
        {
            switch( $column ){
                case 'alfaomega_sample_description':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_sample_description', true ) );
                    break;
                case 'alfaomega_sample_destination':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_sample_destination', true ) );
                break;
                case 'alfaomega_sample_status':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_sample_status', true ) );
                    break;
                case 'alfaomega_sample_activated_at':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_sample_activated_at', true ) );
                    break;
                case 'alfaomega_sample_due_date':
                    echo esc_html( get_post_meta( $post_id, 'alfaomega_sample_due_date', true ) );
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
        public function alfaomega_ebook_sample_sortable_columns( $columns ): array
        {
            $columns['alfaomega_sample_description'] = 'alfaomega_sample_description';
            $columns['alfaomega_sample_status'] = 'alfaomega_sample_status';
            $columns['alfaomega_sample_activated_at'] = 'alfaomega_sample_activated_at';
            $columns['alfaomega_sample_due_date'] = 'alfaomega_sample_due_date';
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
        public function add_meta_boxes() : void
        {
            global $pagenow;

            switch ($pagenow) {
                case 'post-new.php':
                    $this->add_meta_boxes_new();
                    break;
                case 'post.php':
                    $this->add_meta_boxes_edit();
                    break;
            }
        }

        /**
         * Add meta boxes to generate new sample codes
         * @return void
         */
        public function add_meta_boxes_new() : void
        {
            // only to add new posts
            $apiUrl =  esc_url(rest_url(RouteManager::ROUTE_NAMESPACE)) . "/search-ebooks";
            $searchSetup = [ 'options' => [ 'value' => 'isbn', 'label' => 'title', ], ];

            Container::make('post_meta', __('Complete the following form to generate eBook Access Samples', 'alfaomega-ebooks'))
                ->where('post_type', '=', 'alfaomega-sample')
                ->add_fields([
                    Field::make( 'hidden', 'alfaomega_ebook_sample_nonce', '')
                        ->set_default_value(wp_create_nonce('alfaomega_ebook_sample_nonce')),

                    Field::make( 'hidden', 'alfaomega_ebook_sample_action', '')
                        ->set_default_value('generate'),

                    Field::make('textarea', 'alfaomega_sample_description', __('Description', 'alfaomega-ebooks'))
                        ->set_required()
                        ->set_rows(4)
                        ->set_attribute('placeholder', __('In a few words describe the intended use of this access code', 'alfaomega-ebooks'))
                        ->set_help_text( __('Add a note to identify the generated code later', 'alfaomega-ebooks') ),

                    Field::make('text', 'alfaomega_sample_destination', __('Destination', 'alfaomega-ebooks'))
                        ->set_required(false)
                        ->set_attribute('type', 'email')
                        ->set_attribute('placeholder', __('customer@gmail.com', 'alfaomega-ebooks'))
                        ->set_width(50)
                        ->set_help_text( __('Upon generation the sample code can be send to the client email mean to use it', 'alfaomega-ebooks') ),

                    Field::make('text', 'alfaomega_sample_promoter', __('Promoter', 'alfaomega-ebooks'))
                        ->set_required(false)
                        ->set_attribute('type', 'email')
                        ->set_attribute('placeholder', __('promoter@alfaomega.com.mx', 'alfaomega-ebooks'))
                        ->set_width(50)
                        ->set_help_text( __('A copy of the code sent to the client can be send to a promoter too', 'alfaomega-ebooks') ),

                    Field::make('text', 'alfaomega_sample_count', __('Count', 'alfaomega-ebooks'))
                        ->set_required()
                        ->set_attribute('type', 'number')
                        ->set_attribute('min',1)
                        ->set_attribute('max', 100)
                        ->set_default_value(1)
                        ->set_width(50)
                        ->set_help_text( __('How many codes to generate with this setup', 'alfaomega-ebooks') ),

                    Field::make('date', 'alfaomega_sample_due_date', __('Due date', 'alfaomega-ebooks'))
                        ->set_required(false)
                        ->set_width(50)
                        ->set_help_text( __('The code should be used before this date, left empty to never due', 'alfaomega-ebooks') ),

                    Field::make('complex', 'alfaomega_sample_payload', __('Setup the eBook access', 'alfaomega-ebooks'))
                        ->add_fields([
                            Field::make('choices', 'alfaomega_sample_payload_isbn', __('eBook', 'alfaomega-ebooks'))
                                ->set_required()
                                ->set_width(50)
                                ->set_attribute('placeholder', __('Start typing to search eBook...', 'alfaomega-ebooks'))
                                ->set_attribute('shouldSort', true)
                                ->set_attribute('searchEnabled', true)
                                ->set_render_choice_limit(10)
                                ->set_attribute('loadingText', __('Searching eBooks...', 'alfaomega-ebooks'))
                                ->set_attribute('noResultsText', __('No results found.', 'alfaomega-ebooks'))
                                ->set_attribute('searchPlaceholderValue', __('Type to start searching...', 'alfaomega-ebooks'))
                                ->set_fetch_url($apiUrl, $searchSetup)
                                ->add_options(['' => __('Select the eBook', 'alfaomega-ebooks')])
                                ->set_help_text(__('The eBook ISBN to generate the access code.', 'alfaomega-ebooks')),

                            Field::make('choices', 'alfaomega_sample_payload_access_time', __('Access time', 'alfaomega-ebooks'))
                                ->add_options([
                                    ''    => __('Select the access duration', 'alfaomega-ebooks'),
                                    '3'   => sprintf(__('%s days', 'alfaomega-ebooks'), 3),
                                    '7'   => sprintf(__('%s week', 'alfaomega-ebooks'), 1),
                                    '15'  => sprintf(__('%s weeks', 'alfaomega-ebooks'), 2),
                                    '30'  => sprintf(__('%s month', 'alfaomega-ebooks'), 1),
                                    '60'  => sprintf(__('%s months', 'alfaomega-ebooks'), 2),
                                    '180' => sprintf(__('%s months', 'alfaomega-ebooks'), 6),
                                    '365' => sprintf(__('%s year', 'alfaomega-ebooks'), 1),
                                    '0'   => __('Unlimited', 'alfaomega-ebooks'),
                                ])
                                ->set_required()
                                ->set_default_value(3)
                                ->set_attribute('placeholder', __('Select the access duration', 'alfaomega-ebooks'))
                                ->set_attribute('shouldSort', false)
                                ->set_width(50)
                                ->set_help_text(__('Defines the time while the eBook access will be available after redeeming this code', 'alfaomega-ebooks')),

                            Field::make( 'checkbox', 'alfaomega_sample_payload_read', __('Read online', 'alfaomega-ebooks') )
                                ->set_option_value( 'yes' )
                                ->set_default_value( true )
                                ->set_width(50)
                                ->set_help_text(__('The customer will be able to read the eBook online', 'alfaomega-ebooks')),

                            Field::make( 'checkbox', 'alfaomega_sample_payload_download', __('Download PDF', 'alfaomega-ebooks') )
                                ->set_option_value( 'yes' )
                                ->set_default_value( false )
                                ->set_width(50)
                                ->set_help_text(__('The customer will be able to download the eBook PDF. Be careful this option generate costs by download', 'alfaomega-ebooks')),
                        ])
                        ->set_required()
                        ->set_default_value([
                            [
                                'alfaomega_sample_payload_isbn'        => '',
                                'alfaomega_sample_payload_access_time' => 3,
                                'alfaomega_sample_payload_read'        => true,
                                'alfaomega_sample_payload_download'    => false,
                            ],
                        ]),
                ]);
        }

        /**
         * Add meta boxes to edit sample codes
         *
         * @return void
         * @throws \Exception
         */
        public function add_meta_boxes_edit() : void
        {
            $samplePost = Service::make()
                ->ebooks()
                ->samplePost()
                ->get($_GET['post']);

            Container::make('post_meta', __('Edit eBook Access Sample', 'alfaomega-ebooks'))
                ->where('post_type', '=', 'alfaomega-sample')
                ->add_fields([
                    Field::make( 'hidden', 'alfaomega_ebook_sample_nonce' )
                        ->set_default_value(wp_create_nonce('alfaomega_ebook_sample_nonce')),

                    Field::make('textarea', 'alfaomega_sample_description', __('Description', 'alfaomega-ebooks'))
                        ->set_required()
                        ->set_rows(4)
                        ->set_attribute('placeholder', __('In a few words describe the intended use of this access code', 'alfaomega-ebooks'))
                        ->set_help_text( __('Add a note to identify the generated code later', 'alfaomega-ebooks') )
                        ->set_default_value($samplePost['description']),

                    Field::make('text', 'alfaomega_sample_destination', __('Destination', 'alfaomega-ebooks'))
                        ->set_required(false)
                        ->set_attribute('readOnly', true)
                        ->set_attribute('type', 'email')
                        ->set_attribute('placeholder', __('customer@gmail.com', 'alfaomega-ebooks'))
                        ->set_width(50)
                        ->set_help_text( __('Upon generation the sample code can be send to the client email mean to use it', 'alfaomega-ebooks') )
                        ->set_default_value($samplePost['destination']),

                    Field::make('text', 'alfaomega_sample_promoter', __('Promoter', 'alfaomega-ebooks'))
                        ->set_required(false)
                        ->set_attribute('readOnly', true)
                        ->set_attribute('type', 'email')
                        ->set_attribute('placeholder', __('promoter@alfaomega.com.mx', 'alfaomega-ebooks'))
                        ->set_width(50)
                        ->set_help_text( __('A copy of the code sent to the client can be send to a promoter too', 'alfaomega-ebooks') )
                        ->set_default_value($samplePost['promoter']),

                    $samplePost['status'] !== 'created'
                        ? Field::make('text', 'alfaomega_sample_status', __('Status', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(33)
                            ->set_help_text(__('Update the code status', 'alfaomega-ebooks'))
                            ->set_default_value($samplePost['status'])
                        : Field::make('select', 'alfaomega_sample_status', __('Status', 'alfaomega-ebooks'))
                            ->set_required(true)
                            ->add_options([
                                'created'  => __('created', 'alfaomega-ebooks'),
                                'redeemed' => __('redeemed', 'alfaomega-ebooks'),
                                'canceled' => __('canceled', 'alfaomega-ebooks'),
                                'expired'  => __('expired', 'alfaomega-ebooks'),
                            ])
                            ->set_default_value($samplePost['status'])
                            ->set_width(33)
                            ->set_help_text(__('Update the code status', 'alfaomega-ebooks')),

                    $samplePost['status'] !== 'created'
                        ? Field::make('text', 'alfaomega_sample_due_date', __('Due date', 'alfaomega-ebooks'))
                            ->set_attribute('readOnly', true)
                            ->set_attribute('type', 'text')
                            ->set_width(33)
                            ->set_default_value(empty($samplePost['due_date']) ? '' : $samplePost['due_date']->format('d/m/Y'))
                            ->set_help_text(__('The code should be used before this date, left empty to never due', 'alfaomega-ebooks'))
                        : Field::make('date', 'alfaomega_sample_due_date', __('Due date', 'alfaomega-ebooks'))
                            ->set_required(false)
                            ->set_width(33)
                            ->set_default_value(empty($samplePost['due_date']) ? '' : $samplePost['due_date']->toDateString())
                            ->set_help_text( __('The code should be used before this date, left empty to never due', 'alfaomega-ebooks') ),

                    Field::make('text', 'alfaomega_sample_activated_at', __('Activated at', 'alfaomega-ebooks'))
                        ->set_attribute('readOnly', true)
                        ->set_attribute('type', 'text')
                        ->set_width(33)
                        ->set_default_value(empty($samplePost['activated_at']) ? '' : $samplePost['activated_at']->format('d/m/Y h:i A'))
                        ->set_help_text(__('The client redeemed the code this date', 'alfaomega-ebooks')),

                    Field::make('complex', 'alfaomega_sample_payload', __('Setup the eBook access', 'alfaomega-ebooks'))
                        ->set_classes( 'cf-readonly-view' )
                        ->add_fields([
                            Field::make('textarea', 'alfaomega_sample_payload_isbn', __('eBook', 'alfaomega-ebooks'))
                                ->set_attribute('readOnly', true)
                                //->set_attribute('type', 'text')
                                ->set_rows(2)
                                ->set_width(50)
                                ->set_help_text(__('The eBook ISBN to generate the access code. Doubke click on search input to reset the search', 'alfaomega-ebooks')),

                            Field::make('text', 'alfaomega_sample_payload_duration', __('Access time', 'alfaomega-ebooks'))
                                ->set_attribute('readOnly', true)
                                ->set_attribute('type', 'text')
                                ->set_width(50)
                                ->set_help_text(__('Defines the time while the eBook access will be available after redeeming this code', 'alfaomega-ebooks')),

                            Field::make( 'checkbox', 'alfaomega_sample_payload_read', __('Read online', 'alfaomega-ebooks') )
                                ->set_option_value( 'yes')
                                ->set_width(50)
                                ->set_help_text(__('The customer will be able to read the eBook online', 'alfaomega-ebooks')),

                            Field::make( 'checkbox', 'alfaomega_sample_payload_download', __('Download PDF', 'alfaomega-ebooks') )
                                ->set_option_value( 'yes')
                                ->set_width(50)
                                ->set_help_text(__('The customer will be able to download the eBook PDF. Be careful this option generate costs by download', 'alfaomega-ebooks'))
                        ])->set_duplicate_groups_allowed(false)
                        ->set_min(count($samplePost['payload']))
                        ->set_default_value(array_map(function ($access) {
                            return [
                                'alfaomega_sample_payload_isbn'     => $access['title'] . " ({$access['isbn']})",
                                'alfaomega_sample_payload_duration' => $access['access_time_desc'],
                                'alfaomega_sample_payload_read'     => $access['read'],
                                'alfaomega_sample_payload_download' => $access['download'],
                            ];
                        }, $samplePost['payload'])),
                ]);
        }

        /**
         * Retrieves all 'alfaomega-ebook' posts and returns an array with ISBNs as keys and titles as values.
         *
         * @return array An associative array where the keys are the ISBNs and the values are the titles of the ebooks.
         * @fixme: Should be implemente a search by title or ISBN
         */
        public function get_ebooks_isbn(): array
        {
            $query = new WP_Query([
                'post_type' => 'alfaomega-ebook',
                'posts_per_page' => 10,
                'post_status' => 'publish',
            ]);

            $ebooks = [];

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $isbn = get_post_meta(get_the_ID(), 'alfaomega_ebook_isbn', true);
                    $title = get_the_title();
                    if (!empty($isbn) && !empty($title)) {
                        $ebooks[$isbn] = str_replace('&#8211;', '-', $title);
                    }
                }
                wp_reset_postdata();
            }

            return array_merge(['' => __('Select the eBook', 'alfaomega-ebooks')], $ebooks);
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
            /*$description = get_post_meta( $post->ID, 'alfaomega_sample_description', true );
            $destination = get_post_meta( $post->ID, 'alfaomega_sample_destination', true );
            $promoter = get_post_meta( $post->ID, 'alfaomega_sample_promoter', true );
            $status = get_post_meta( $post->ID, 'alfaomega_sample_status', true );
            $payload = get_post_meta( $post->ID, 'alfaomega_access_payload', true );
            $activated_at = get_post_meta( $post->ID, 'alfaomega_sample_activated_at', true );
            $due_date = get_post_meta( $post->ID, 'alfaomega_sample_due_date', true );
            require_once( ALFAOMEGA_EBOOKS_PATH . 'views/alfaomega_ebook_sample_metabox.php' );*/

        }

        /**
         * Save post
         *
         * @param int $post_id Post ID to be saved
         *
         * @return void
         * @throws \Exception
         * @since  1.0.0
         * @access public
         */
        public function save_post( $post_id ): void
        {
            // A series of guard clauses to make sure we are saving the right data
            // 1. Check if nonce is set
            if( isset( $_POST['carbon_fields_compact_input']['_alfaomega_ebook_sample_nonce'] ) ){
                if( ! wp_verify_nonce( $_POST['carbon_fields_compact_input']['_alfaomega_ebook_sample_nonce'], 'alfaomega_ebook_sample_nonce' ) ){
                    return;
                }
            }

            // 2. Check if we're doing autosave
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
                return;
            }

            // 3. Check if user has permissions to save data
            if( isset( $_POST['post_type'] ) && $_POST['post_type'] === 'alfaomega-sample' ){
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
            if ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost') {

                // Generate the sample codes
                if (isset($_POST['carbon_fields_compact_input']['_alfaomega_ebook_sample_action']) &&
                    $_POST['carbon_fields_compact_input']['_alfaomega_ebook_sample_action'] === 'generate') {

                    $_POST['carbon_fields_compact_input']['_alfaomega_ebook_sample_action'] = 'processing';
                    $fields = $_POST['carbon_fields_compact_input'];
                    Service::make()
                        ->ebooks()
                        ->samplePost()
                        ->updateOrCreate(null, [
                            // Email address to send the samples
                            'destination' => $fields['_alfaomega_sample_destination'] ?? '',
                            // Email address to sean a copy of the samples
                            'promoter'    => $fields['_alfaomega_sample_promoter'] ?? '',
                            // Sample description
                            'description' => $fields['_alfaomega_sample_description'] ?? '',
                            // Access information
                            'payload'     => array_map(function ($access) {
                                return [
                                    // eBook ISBN
                                    'isbn'        => $access['_alfaomega_sample_payload_isbn'] ?? '',
                                    // Days available to access the eBook after activation
                                    'access_time' => $access['_alfaomega_sample_payload_access_time'] ?? 3,
                                    // Allow to read the eBook
                                    'read'        => isset($access['_alfaomega_sample_payload_read'])
                                                     && $access['_alfaomega_sample_payload_read'] === 'yes',
                                    // Allow to download the eBook
                                    'download'    => isset($access['_alfaomega_sample_payload_download'])
                                                     && $access['_alfaomega_sample_payload_download'] === 'yes',
                                ];
                            }, $fields['_alfaomega_sample_payload'] ?? []),
                            // Valid until this date
                            'due_date'    => $fields['_alfaomega_sample_due_date'] ?? '',
                            // Number of samples to generate
                            'count'       => intval($fields['_alfaomega_sample_count'] ?? 1),
                        ]);

                    $this->delete_auto_draft();
                    return;
                }

                // Populate an array with the fields we want to save
                $fields = [
                    'alfaomega_sample_description' => [
                        'old'     => get_post_meta($post_id, 'alfaomega_sample_description', true),
                        'new'     => $_POST['alfaomega_sample_description'],
                        'default' => __('Sample code', 'alfaomega-ebooks'),
                    ],
                    'alfaomega_sample_destination' => [
                        'old'     => get_post_meta($post_id, 'alfaomega_sample_destination', true),
                        'new'     => $_POST['alfaomega_sample_destination'],
                        'default' => '',
                    ],
                    'alfaomega_sample_promoter' => [
                        'old'     => get_post_meta($post_id, 'alfaomega_sample_promoter', true),
                        'new'     => $_POST['alfaomega_sample_promoter'],
                        'default' => '',
                    ],
                    'alfaomega_sample_status'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_sample_status', true),
                        'new'     => $_POST['alfaomega_sample_status'],
                        'default' => 'created',
                    ],
                    'alfaomega_sample_payload'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_sample_payload', true),
                        'new'     => $_POST['alfaomega_sample_payload'],
                        'default' => '',
                    ],
                    'alfaomega_sample_due_date'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_sample_due_date', true),
                        'new'     => $_POST['alfaomega_sample_due_date'],
                        'default' => '',
                    ],
                    'alfaomega_sample_activated_at'  => [
                        'old'     => get_post_meta($post_id, 'alfaomega_sample_activated_at', true),
                        'new'     => $_POST['alfaomega_sample_activated_at'],
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

        /**
         * Delete auto draft posts
         * @return void
         * @since 1.0.0
         * @access public
         */
        function delete_auto_draft(): void
        {
            $args = [
                'post_type'      => ALFAOMEGA_EBOOKS_SAMPLE_POST_TYPE,
                's'              => __("Auto Draft"),
                'posts_per_page' => -1,
            ];

            $query = new WP_Query($args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    wp_delete_post(get_the_ID(), true);
                }
            }
        }
    }
}
