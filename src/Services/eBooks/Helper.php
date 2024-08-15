<?php

namespace AlfaomegaEbooks\Services\eBooks;

class Helper
{
    /**
     * Get a parameter value from a URL query string.
     *
     * @param string $url The URL containing the query string.
     * @param string $param The parameter name to retrieve.
     * @return string|null The parameter value or null if not found.
     */
    function getQueryParam(string $url, string $param): ?string
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            return null;
        }

        parse_str($query, $params);
        return $params[$param] ?? null;
    }
}
