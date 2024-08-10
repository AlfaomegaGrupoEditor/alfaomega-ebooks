<?php
namespace AlfaomegaEbooks\Http;

use AlfaomegaEbooks\Http\Controllers\EbooksController;
use AlfaomegaEbooks\Http\Controllers\EbooksMassActionsController;
use AlfaomegaEbooks\Http\Controllers\EbooksQuickActionsController;
use AlfaomegaEbooks\Http\Controllers\QueueController;

/**
 * Class RouteManger
 *
 * This class is responsible for registering the REST API routes for the plugin.
 * It contains a constant for the namespace of the routes and an array of the routes to be registered.
 * Each route in the array is an associative array with the keys 'methods', 'callback', and 'permission_callback'.
 * The 'methods' key specifies the HTTP method for the route.
 * The 'callback' key specifies the callback function for the route.
 * The 'permission_callback' key specifies the permission callback function for the route.
 *
 * @package AlfaomegaEbooks\Http
 */
class RouteManager
{
    /**
     * The namespace for the routes.
     *
     * @var string
     */
    public const ROUTE_NAMESPACE = 'alfaomega-ebooks/v1';

    /**
     * @var array $routes
     *
     * An associative array that maps route names to their corresponding configurations.
     * Each key in the array is a string that represents the route name.
     * Each value in the array is an associative array with the following keys:
     * - 'methods': The HTTP method for the route. This should be a string.
     * - 'callback': An indexed array with two elements:
     *   - The first element is the fully qualified class name of the controller that should handle the route.
     *   - The second element is the name of the method in the controller that should be called when the route is accessed.
     * - 'permission_callback': An indexed array with two elements:
     *   - The first element is the fully qualified class name of the middleware that should handle the permission check for the route.
     *   - The second element is the name of the method in the middleware that should be called to perform the permission check.
     *
     * Currently, the following routes are supported:
     * - 'import-ebooks': Calls the 'importEbooks' method on the EbooksController class with 'GET' method.
     * - 'refresh-ebooks': Calls the 'refreshEbooks' method on the EbooksController class with 'GET' method.
     * - 'link-products': Calls the 'linkProducts' method on the EbooksController class with 'GET' method.
     * - 'link-ebooks': Calls the 'linkEbooks' method on the EbooksController class with 'GET' method.
     * - 'queue-status': Calls the 'queueStatus' method on the QueueController class with 'GET' method.
     * - 'queue-clear': Calls the 'queueClear' method on the QueueController class with 'GET' method.
     */
    protected array $routes = [
        'import-ebooks'  => [
            'methods'             => 'GET',
            'callback'            => [EbooksController::class, 'importEbooks'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'refresh-ebooks' => [
            'methods'             => 'GET',
            'callback'            => [EbooksController::class, 'refreshEbooks'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'link-products'  => [
            'methods'             => 'GET',
            'callback'            => [EbooksController::class, 'linkProducts'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'link-ebooks'    => [
            'methods'             => 'GET',
            'callback'            => [EbooksController::class, 'linkEbooks'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],

        'queue-status' => [
            'methods'             => 'GET',
            'callback'            => [QueueController::class, 'queueStatus'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'queue-clear'  => [
            'methods'             => 'GET',
            'callback'            => [QueueController::class, 'queueClear'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
    ];

    /**
     * @var array $massActions
     *
     * An associative array that maps actions to their corresponding controllers and methods.
     * Each key in the array is a string that represents the action to be performed.
     * Each value in the array is an indexed array with two elements:
     * - The first element is the fully qualified class name of the controller that should handle the action.
     * - The second element is the name of the method in the controller that should be called to perform the action.
     *
     * Currently, the following actions are supported:
     * - 'update-meta': Calls the 'massUpdateMeta' method on the EbooksMassActionsController class.
     * - 'link-product': Calls the 'massLinkProducts' method on the EbooksMassActionsController class.
     * - 'link-ebook': Calls the 'massLinkEbooks' method on the EbooksMassActionsController class.
     */
    protected array $massActions = [
        'update-meta'  => [EbooksMassActionsController::class, 'massUpdateMeta'],
        'link-product' => [EbooksMassActionsController::class, 'massLinkProducts'],
        'link-ebook'   => [EbooksMassActionsController::class, 'massLinkEbooks'],
    ];

    /**
     * @var array $quickActions
     *
     * An associative array that maps actions to their corresponding controllers and methods.
     * Each key in the array is a string that represents the action to be performed.
     * Each value in the array is an indexed array with two elements:
     * - The first element is the fully qualified class name of the controller that should handle the action.
     * - The second element is the name of the method in the controller that should be called to perform the action.
     *
     * Currently, the following actions are supported:
     * - 'update-meta': Calls the 'quickUpdateMeta' method on the EbooksQuickActionsController class.
     * - 'link-product': Calls the 'quickLinkProduct' method on the EbooksQuickActionsController class.
     * - 'link-ebook': Calls the 'quickLinkEbook' method on the EbooksQuickActionsController class.
     */
    protected array $quickActions = [
        'update-meta'  => [EbooksQuickActionsController::class, 'quickUpdateMeta'],
        'link-product' => [EbooksQuickActionsController::class, 'quickLinkProduct'],
        'link-ebook'   => [EbooksQuickActionsController::class, 'quickLinkEbook'],
    ];

    /**
     * Registers the routes.
     *
     * This method loops through the $routes array and registers each route using the register_rest_route function.
     * The namespace for the routes is specified by the ROUTE_NAMESPACE constant.
     *
     * @return void
     */
    public function register(): void
    {
        $classes = [];
        foreach ($this->routes as $route => $args) {
            if (!in_array($args['callback'][0], $classes)) {
                $classes[$args['callback'][0]] = new $args['callback'][0];
            }
            if (!in_array($args['permission_callback'][0], $classes)) {
                $classes[$args['permission_callback'][0]] = new $args['permission_callback'][0];
            }
            $args['callback'][0] = $classes[$args['callback'][0]];
            $args['permission_callback'][0] = $classes[$args['permission_callback'][0]];
            register_rest_route(self::ROUTE_NAMESPACE, "/$route", $args);
        }
    }

    /**
     * Executes a mass action on a set of posts.
     *
     * This method takes a redirect URL, an action, and an array of post IDs. It checks if the action exists in the
     * $massActions array. If it does, it creates a new instance of the controller associated with the action and
     * calls the method associated with the action, passing the post IDs as an argument. The result of this method
     * call is then returned. If the action does not exist in the $massActions array, the method simply returns
     * the redirect URL.
     *
     * @param string $redirect_url The URL to redirect to if the action does not exist in the $massActions array.
     * @param string $action The action to perform. This should be a key in the $massActions array.
     * @param array $post_ids An array of post IDs on which to perform the action.
     * @return string The result of the action method call, or the redirect URL if the action does not exist.
     */
    public function massAction(string $redirect_url, string $action, array $post_ids): string
    {
        if (!array_key_exists($action, $this->massActions)) {
            return $redirect_url;
        }

        $controller = new $this->massActions[$action][0];
        return $controller->{$this->massActions[$action][1]}($post_ids);
    }

    /**
     * Executes a quick action on a single post.
     *
     * This method checks if the 'ebook_action' and 'post' query parameters are set in the GET request.
     * If they are not set, the method returns without doing anything.
     *
     * If the 'ebook_action' query parameter is set, the method checks if this action exists in the $quickActions array.
     * If it does, it creates a new instance of the controller associated with the action and calls the method associated
     * with the action, passing the 'post' query parameter as an argument.
     *
     * If the 'ebook_action' query parameter is not a key in the $quickActions array, the method returns without doing anything.
     *
     * @return void
     */
    public function quickAction():void
    {
        if (!isset($_GET['ebook_action'])) {
            return;
        }

        $action = $_GET['ebook_action'];
        if (!array_key_exists($action, $this->quickActions) || !isset($_GET['post'])){
            wp_redirect(remove_query_arg($action));
            return;
        }

        $controller = new $this->quickActions[$action][0];
        $controller->{$this->quickActions[$action][1]}($_GET['post']);
        wp_redirect(remove_query_arg($action));
    }
}
