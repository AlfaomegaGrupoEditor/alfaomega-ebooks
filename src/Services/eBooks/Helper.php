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

    /**
     * Cache a value using a callback if it does not already exist.
     *
     * @param string $key
     * @param int $expiration
     * @param callable $callback
     *
     * @return mixed
     */
    function cacheRemember(string $key, int $expiration, callable $callback): mixed
    {
        $value = get_transient($key);
        if ($value === false) {
            $value = $callback();
            set_transient($key, $value, $expiration);
        }

        return $value;
    }

    /**
     * Forget a cached value.
     *
     * @param string $key: A unique key for the cached value, wildcards available as prefix.
     *                   Example: 'user-books-search-1-*'
     *
     * @return bool
     */
    function cacheForget(string $key): bool
    {
        global $wpdb;

        if (!str_contains($key, '*')) {
            return delete_transient($key);
        }

        // Get all transients with the specified prefix
        $prefix = str_replace('*', '', $key);
        $transients = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
                '_transient_' . $wpdb->esc_like($prefix) . '%'
            )
        );

        // Loop through and delete each transient
        $result = true;
        foreach ($transients as $transient) {
            $key = str_replace('_transient_', '', $transient);
            if (!delete_transient($key)) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Sanitize a string to prevent serialization errors.
     *
     * @param string $string The string to sanitize.
     * @return string The sanitized string.
     */
    function sanitize(string $string): string
    {
        // Step 1: Trim whitespace
        $string = trim($string);

        // Step 2: Remove invalid characters
        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);

        // Step 3: Escape quotes
        $string = addslashes($string);

        // Step 4: Ensure valid UTF-8 encoding
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = utf8_encode($string);
        }

        // Step 5: Optionally filter harmful content
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    /**
     * Log a message to the error log.
     *
     * @param string $message The message to log.
     * @return void
     */
    function log(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log($message);
        }
    }
}
