<?php
namespace AlfaomegaEbooks\Http;

use AlfaomegaEbooks\Http\Controllers\EbooksController;

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
            'methods'             => 'POST',
            'callback'            => [EbooksController::class, 'importEbooks'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],

        'refresh_ebooks' => [
            'methods'             => 'POST',
            'callback'            => [EbooksController::class, 'refreshEbooks'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],

        'link_products' => [
            'methods'             => 'POST',
            'callback'            => [EbooksController::class, 'linkProducts'],
            'permission_callback' => [Middleware::class, 'auth'],
        ],

        'link_ebooks' => [
            'methods'             => 'POST',
            'callback'            => [EbooksController::class, 'linkEbooks'],
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
        foreach ($this->routes as $route => $args) {
            /*$args['callback'][0] = new $args['callback'][0]();
            $args['permission_callback'][0] = new $args['permission_callback'][0]();*/
            register_rest_route(self::ROUTE_NAMESPACE, "/$route", $args);
        }
    }
}
