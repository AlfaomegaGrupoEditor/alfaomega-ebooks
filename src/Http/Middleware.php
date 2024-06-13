<?php
namespace AlfaomegaEbooks\Http;

use WP_REST_Request;

class Middleware
{
    /**
     * Middleware for the REST API route.
     *
     * This method is responsible for checking if the user has the required permissions to access the REST API route.
     * It checks if the user has the 'edit_posts' capability and if the nonce is valid.
     * @param WP_REST_Request $request The REST API request object.
     * @return bool Returns true if the user has the required permissions, false otherwise.
     */
    public function auth(WP_REST_Request $request): bool
    {
        if (!current_user_can('edit_posts')){
            return false;
        }

        if( !$request->has_param('alfaomega_ebook_nonce')){
            return false;
        }

        if( ! wp_verify_nonce( $request->get_param('alfaomega_ebook_nonce'), 'alfaomega_ebook_nonce' ) ){
            return false;
        }

        return true;
    }
}
