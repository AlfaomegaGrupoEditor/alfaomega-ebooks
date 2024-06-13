<?php
namespace AlfaomegaEbooks\Http;

use AlfaomegaEbooks\Http\Controllers\EbooksController;
use AlfaomegaEbooks\Http\Controllers\EbooksMassController;
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
    public const string ROUTE_NAMESPACE = 'alfaomega-ebooks/v1';

    /**
     * The routes to be registered.
     *
     * Each route is an associative array with the keys 'methods', 'callback', and 'permission_callback'.
     *
     * @var array
     */
    protected array $routes = [
        'import-ebooks' => [
            'methods'             => 'GET',
            'callback'            => [EbooksController::class, 'importEbooks'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'refresh-ebooks' => [
            'methods'             => 'GET',
            'callback'            => [EbooksController::class, 'refreshEbooks'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'link-products' => [
            'methods'             => 'GET',
            'callback'            => [EbooksController::class, 'linkProducts'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'link-ebooks' => [
            'methods'             => 'GET',
            'callback'            => [EbooksController::class, 'linkEbooks'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],

        'mass-update-meta' => [
            'methods'             => 'POST',
            'callback'            => [EbooksMassController::class, 'massUpdateMeta'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'mass-link-product' => [
            'methods'             => 'POST',
            'callback'            => [EbooksMassController::class, 'massLinkProduct'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'mass-link-ebook' => [
            'methods'             => 'POST',
            'callback'            => [EbooksMassController::class, 'massLinkEbook'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],

        'queue-status' => [
            'methods'             => 'GET',
            'callback'            => [QueueController::class, 'queueStatus'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
        'queue-clear' => [
            'methods'             => 'GET',
            'callback'            => [QueueController::class, 'queueClear'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],
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
}
