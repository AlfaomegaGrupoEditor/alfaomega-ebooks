<?php

// Check if the class Alfaomega_Ebooks_Api exists
if( ! class_exists( 'Alfaomega_Ebooks_Api' )) {
    class Alfaomega_Ebooks_Api
    {
        // The authentication token
        protected $auth;

        // The filename where the token is stored
        protected $token_filename;

        // The settings array
        protected array $settings;

        /**
         * Constructor for the Alfaomega_Ebooks_Api class.
         *
         * @param array $settings The settings array.
         *
         * @since  1.0
         */
        public function __construct(array $settings)
        {
            // Assign the settings array to the settings property
            $this->settings = $settings;

            // Assign the token filename to the token_filename property
            $this->token_filename = ALFAOMEGA_SECURITY_PATH . '/token.txt';
        }

        /**
         * Retrieves the authentication token from a file.
         *
         * This method checks if the file where the token is stored exists. If it does, it reads the file and returns the content.
         * If the file does not exist, it returns null.
         *
         * @return string|null The authentication token, or null if the file does not exist.
         *
         * @since 1.0
         */
        private function getAuthFromFile(): ?string
        {
            return file_exists($this->token_filename)
                ? file_get_contents($this->token_filename)
                : null;
        }

        /**
         * Saves the authentication data to a file.
         *
         * This method takes the authentication data as input, decodes it from JSON format, and adds an expiration date to it.
         * The expiration date is set to 360 days from the current date. The data is then encoded back to JSON format and written to a file.
         *
         * @param mixed $data The authentication data.
         *
         * @since 1.0
         */
        public function saveAuthToFile($data): void
        {
            $data = json_decode($data);
            $nextYear = date('Y-m-d', strtotime('+360 days'));
            $data->expires_in = date_timestamp_get(date_create($nextYear));
            file_put_contents($this->token_filename, json_encode($data));
        }

        /**
         * Authenticates with the API and retrieves the authentication token.
         * This method sends a POST request to the API's token URL with the necessary authentication details.
         * The authentication details include the client ID, client secret, grant type, username, and password.
         * The grant type is set to "password", indicating that the client is requesting an access token by presenting the resource owner's password.
         * The scope is left empty.
         * The response from the API is then saved to a file using the saveAuthToFile method.
         *
         * @return mixed The response from the API, which includes the authentication token.
         * @throws \Exception
         * @since 1.0
         */
        public function authenticate(): mixed
        {
            // FIXME
            /*$response = wp_remote_post($this->getTokenUrl(), [
                'headers' => ['content-type' => 'application/json'],
                'body'    => [
                    "client_id"     => $this->getClientId(),
                    "client_secret" => $this->getSecret(),
                    "grant_type"    => "password",
                    "username"      => $this->getUsername(),
                    "password"      => $this->getPassword(),
                    "scope"         => "",
                ],
            ]);
            if ( is_wp_error( $response ) ||  $response['code'] !== 200) {
                $body = json_decode($response['body'], true);
                if (!empty($body['error'])) {
                    throw new \Exception(esc_html__('Authentication error', 'alfaomega-ebooks') . "- " . $body['error']);
                }

                throw new \Exception(esc_html__('Authentication error', 'alfaomega-ebooks'));
            }*/

            $response = $this->curlPost($this->getTokenUrl(), [
                "client_id"     => $this->getClientId(),
                "client_secret" => $this->getSecret(),
                "grant_type"    => "password",
                "username"      => $this->getUsername(),
                "password"      => $this->getPassword(),
                "scope"         => "",
            ]);

            $this->saveAuthToFile($response);

            return $response;
        }

        /**
         * Retrieves the authentication token.
         * This method first attempts to retrieve the authentication token from a file. If the token is not found or is expired,
         * it authenticates with the API to get a new token. The token's expiration is checked by comparing the expiration time
         * with the current time.
         *
         * @return mixed The authentication token, or the result of the authenticate method if the token is not found or is expired.
         * @throws \Exception
         * @since 1.0
         */
        public function getAuth(): mixed
        {
            $token = $this->getAuthFromFile();
            if (empty($token)) {
                return $this->authenticate();
            }
            $token = json_decode($token);
            return empty($token->expires_in) || $token->expires_in <= time()
                ? $this->authenticate()
                : $token;
        }

        /**
         * Retrieves the headers for the API request.
         * This method first retrieves the authentication token by calling the getAuth method. If the token is available,
         * it returns an array of headers for the API request. The headers include the Authorization header with the token,
         * and headers for Accept, Content-Type, and Cache-Control. If the token is not available, it returns null.
         *
         * @return array|null The headers for the API request, or null if the authentication token is not available.
         * @throws \Exception
         * @since 1.0
         */
        public function getHeaders(): ?array
        {
            $auth = $this->getAuth();
            if ($auth) {
                return [
                    'Authorization' => 'Bearer ' . $auth->access_token,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                    'Cache-Control' => 'no-cache',
                ];
            } else {
                return null;
            }
        }

        /**
         * Sends a request to the API.
         * This method prepares and sends a request to the API. It first retrieves the API URL and the headers for the request.
         * If the headers are available, it prepares the arguments for the request, including the headers and a timeout of 60 seconds.
         * Depending on the HTTP method specified, it sends a POST or GET request to the API.
         * If the method is not supported, it throws an exception.
         * If the request results in an error, it throws an exception with the error details.
         * If the headers are not available, it throws an exception indicating that the request failed.
         *
         * @param string $method The HTTP method.
         * @param string $uri    The URI.
         * @param mixed $data    The data to send with the request.
         * @param bool $retrying Whether the request is a retry.
         *
         * @return array|\WP_Error The response from the API.
         * @throws \Exception If the method is not supported, if the request results in an error, or if the headers are not available.
         * @since 1.0
         */
        public function request(string $method, string $uri, mixed $data = null, bool $retrying = false): array|WP_Error
        {
            try {
                $uri = $this->getApiUrl($uri);
                $headers = $this->getHeaders();
                if ($headers) {
                    $args = ['headers' => $headers, 'timeout' => 60];
                    switch ($method) {
                        case 'post':
                            $args['body'] = json_encode($data);
                            $args['method'] = 'POST';
                            $response = wp_remote_post($uri, $args);
                            break;
                        case 'get':
                            $response = wp_remote_get($uri, $args);
                            break;
                        default:
                            throw new Exception(esc_html__('method not supported yet', 'alfaomega-ebooks'));
                    }
                    if (is_wp_error($response) || ! empty($response->errors)) {
                        throw new Exception(json_encode($response->errors));
                    }

                    return $response;
                } else {
                    throw new Exception(esc_html__('Request failed', 'alfaomega-ebooks'));
                }
            } catch (\Exception $exception) {
                throw new Exception(esc_html__('Request failed', 'alfaomega-ebooks') . ":" . $exception->getMessage());
            }
        }

        /**
         * Sends a GET request to the specified URL.
         * This method is a wrapper for the request method, with the HTTP method set to 'get'.
         * It takes the URL and a retrying flag as input, and passes them to the request method.
         *
         * @param string $url    The URL to send the request to.
         * @param bool $retrying Whether the request is a retry.
         *
         * @return mixed The response from the API.
         * @throws \Exception
         * @since 1.0
         */
        public function get(string $url, bool $retrying = false): array|WP_Error
        {
            return $this->request('get', $url, $retrying);
        }

        /**
         * Sends a POST request to the specified URL with the specified data.
         * This method is a wrapper for the request method, with the HTTP method set to 'post'.
         * It takes the URL, the data to send with the request, and a retrying flag as input, and passes them to the request method.
         *
         * @param string $url    The URL to send the request to.
         * @param mixed $data    The data to send with the request.
         * @param bool $retrying Whether the request is a retry.
         *
         * @return mixed The response from the API.
         * @throws \Exception
         * @since 1.0
         */
        public function post(string $url, mixed $data, bool $retrying = false): array|WP_Error
        {
            return $this->request('post', $url, $data, $retrying);
        }

        /**
         * Retrieves the token URL from the settings.
         * This method returns the value of the 'alfaomega_ebooks_token' key from the settings array.
         *
         * @return string The token URL.
         * @since 1.0
         */
        public function getTokenUrl()
        {
            return $this->settings['alfaomega_ebooks_token'];
        }

        /**
         * Retrieves the API URL from the settings and appends the specified URL to it.
         * This method first retrieves the value of the 'alfaomega_ebooks_api' key from the settings array.
         * It then appends the specified URL to it, ensuring that there is no trailing slash on the API URL.
         *
         * @param string $url The URL to append to the API URL.
         *
         * @return string The full API URL.
         * @since 1.0
         */
        public function getApiUrl($url)
        {
            return rtrim($this->settings['alfaomega_ebooks_api'], '/') . $url;
        }

        /**
         * Retrieves the server URL from the settings.
         * This method returns the value of the 'alfaomega_ebooks_panel' key from the settings array, ensuring that there is no trailing slash.
         *
         * @return string The server URL.
         * @since 1.0
         */
        public function getServerUrl()
        {
            return rtrim($this->settings['alfaomega_ebooks_panel'], '/');
        }

        /**
         * Retrieves the client ID from the settings.
         * This method returns the value of the 'alfaomega_ebooks_client_id' key from the settings array.
         *
         * @return string The client ID.
         * @since 1.0
         */
        public function getClientId()
        {
            return $this->settings['alfaomega_ebooks_client_id'];
        }

        /**
         * Retrieves the client from the settings.
         * This method returns the value of the 'alfaomega_ebooks_client' key from the settings array.
         *
         * @return string The client.
         * @since 1.0
         */
        public function getClient()
        {
            return $this->settings['alfaomega_ebooks_client'];
        }

        /**
         * Retrieves the client secret from the settings.
         * This method returns the value of the 'alfaomega_ebooks_client_secret' key from the settings array.
         *
         * @return string The client secret.
         * @since 1.0
         */
        public function getSecret()
        {
            return $this->settings['alfaomega_ebooks_client_secret'];
        }

        /**
         * Retrieves the username from the settings.
         * This method returns the value of the 'alfaomega_ebooks_username' key from the settings array.
         *
         * @return string The username.
         * @since 1.0
         */
        public function getUsername()
        {
            return $this->settings['alfaomega_ebooks_username'];
        }

        /**
         * Retrieves the password from the settings.
         * This method returns the value of the 'alfaomega_ebooks_password' key from the settings array.
         *
         * @return string The password.
         * @since 1.0
         */
        public function getPassword()
        {
            return $this->settings['alfaomega_ebooks_password'];
        }

        /**
         * Retrieves the reader URL from the settings.
         * This method returns the value of the 'alfaomega_ebooks_reader' key from the settings array.
         *
         * @return string The reader URL.
         * @since 1.0
         */
        public function getReaderUrl()
        {
            return $this->settings['alfaomega_ebooks_reader'];
        }

        /**
         * Retrieves the user token string.
         * This method is currently empty and needs to be implemented.
         *
         * @param mixed $data The data used to retrieve the user token string.
         *
         * @return void
         * @since 1.0
         */
        public function getUserTokenString(mixed $data): void
        {
        }

        /**
         * Sends a POST request to the specified URL with the specified payload.
         * This method initializes a new cURL session, sets various options for the session, and then executes the session.
         * The options set include the URL, the method (POST), the return transfer, the SSL peer verification, and the payload.
         * If the session execution results in an error, it throws an exception with the error details.
         *
         * @param string $url    The URL to send the request to.
         * @param mixed $payload The payload to send with the request.
         *
         * @return string The result of the cURL session execution.
         * @throws \Exception If the session execution results in an error.
         * @since 1.0
         */
        protected function curlPost(string $url, array $payload): string
        {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

                return curl_exec($ch);
            } catch (\Exception $exception) {
                throw new \Exception(esc_html__('Request error', 'alfaomega-ebooks') . "- " . $exception->getMessage());
            }
        }
    }
}
