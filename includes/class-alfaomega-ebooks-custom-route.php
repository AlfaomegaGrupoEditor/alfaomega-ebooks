<?php

/**
 * Checks if the Alfaomega_Ebooks_Custom_Route class exists.
 * If it does not exist, it defines the class.
 */
if( ! class_exists( 'Alfaomega_Ebooks_Custom_Route' )){
    /**
     * The Alfaomega_Ebooks_Custom_Route class is used to create custom routes in WordPress.
     * It takes four arguments in the constructor:
     *  1. The URL file path.
     *  2. An array of query variables that come from the URL path. The number of regex in the URL file path must match the number of query params.
     *  3. The file path to the template.
     *  4. A boolean value. If true, it will rebuild the permalink structure. It is recommended not to use this for production.
     * Example usage:
     * new Custom_Route('my-unique-route/(.+?)/(.+?)/?$',array('param_1','param_2'),'public/path_to_template_file.php',true);
     * In the template file, you can get the value of a query variable using get_query_var('param_1');
     *
     * @since      1.0.0
     * @package    Alfaomega_Ebooks
     * @subpackage Alfaomega_Ebooks/includes
     * @author     Livan Rodriguez <livan2r@gmail.com>
     */
    Class Alfaomega_Ebooks_Custom_Route {
        /**
         * @var string $route_name
         * The name of the custom route. This is used as the regex pattern for the rewrite rule.
         */
        public string $route_name;
        /**
         * @var string $query_name
         * The name of the query variable. This is used as the query variable for the rewrite rule.
         */
        public string $query_name;
        /**
         * @var string $route_path
         * The path to the template file that should be used when the custom route is matched.
         */
        public string $route_path;
        /**
         * @var array $params
         * An array of parameters that come from the URL path. The number of regex in the URL file path must match the number of query params.
         */
        public array $params;
        /**
         * @var bool $forch_flush
         * A boolean value. If true, it will rebuild the permalink structure. It is recommended not to use this for production.
         */
        public bool $forch_flush;
        /**
         * @var mixed $query_name_array
         * An array of query variables. This is used to add the query variables to the query_vars filter.
         */
        private mixed $query_name_array;

        /**
         * Alfaomega_Ebooks_Custom_Route constructor.
         *
         * @param string $route_name The name of the custom route.
         * @param string $query_name The name of the query variable.
         * @param string $route_path The path to the template file.
         * @param bool $forch_flush  If true, it will rebuild the permalink structure.
         */
        public function __construct(string $route_name, string $query_name, string $route_path, bool $forch_flush)
        {
            $this->route_name = $route_name;
            $this->query_name = $query_name;
            $this->route_path = $route_path;
            $this->forch_flush = $forch_flush;

            $this->query_name_array = $query_name;

            add_action('init', [$this, 'add_custom_rewrite']);
            add_filter('query_vars', [$this, 'add_custom_query_vars']);
            add_action('template_include', [$this, 'add_custom_template_include']);
            add_action('init', [$this, 'change_permalinks_option']);

            add_action('after-switch-theme', [$this, 'change_permalinks_option']);
        }

        /**
         * Adds a custom rewrite rule to WordPress.
         * This method constructs a query string from the query_name_array property and adds a rewrite rule to WordPress.
         * The rewrite rule is added with the route_name property as the regex pattern and the constructed query string as the query.
         * The rule is added at the top of the list of rewrite rules.
         *
         * @since 1.0.0
         */
        public function add_custom_rewrite(): void
        {
            $str = '';
            $keys = 1;
            foreach ($this->query_name_array as $value) {
                $str .= $value . '=$matches[' . $keys . ']&';
                $keys++;
            }
            add_rewrite_rule($this->route_name, 'index.php?' . $str, 'top');
        }

        /**
         * Adds custom query variables to WordPress.
         * This method adds the query_name_array property to the query_vars filter.
         *
         * @param array $query_vars An array of query variables.
         *
         * @return array
         */
        public function add_custom_query_vars($query_vars): array
        {
            foreach ($this->query_name_array as $value) {
                $query_vars[] = $value;
            }

            return $query_vars;
        }

        /**
         * Adds a custom template include to WordPress.
         * This method checks if the query variable is set and returns the route_path property if it is.
         *
         * @param string $template The path to the template file.
         *
         * @return string
         */
        public function add_custom_template_include(string $template): string
        {
            foreach ($this->query_name_array as $value) {
                if (! get_query_var($value) || get_query_var( $value ) == '' ) {
                    return $template;
                }else{
                    return  $this->route_path;
                }
            }

            return $template; // Added this line to ensure a string is always returned
        }

        /**
         * Changes the permalinks option in WordPress.
         * This method changes the permalink structure to '/%postname%/' and flushes the rewrite rules.
         *
         * @since 1.0.0
         */
        public function change_permalinks_option(): void
        {
            if($this->forch_flush){
                global $wp_rewrite;
                $wp_rewrite->set_permalink_structure('/%postname%/');
                $wp_rewrite->flush_rules();
            }
        }
    }
}
