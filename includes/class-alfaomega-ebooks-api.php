<?php

if( ! class_exists( 'Alfaomega_Ebooks_Api' )) {
    class Alfaomega_Ebooks_Api
    {
        protected $auth;
        protected $token_filename;
        protected array $settings;

        public function __construct(array $settings)
        {
            $this->settings = $settings;
            $this->token_filename = plugin_dir_path( __FILE__ ) . '/token.txt';
        }

        private function getAuthFromFile()
        {
            return file_exists($this->token_filename)
                ? file_get_contents($this->token_filename)
                : null;
        }

        public function saveAuthToFile($data)
        {
            file_put_contents($this->token_filename, $data);
        }

        public function authenticate()
        {
            $data = [
                "client_id"     => $this->getClientId(),
                "client_secret" => $this->getSecret(),
                "grant_type"    => "password",
                "username"      => $this->getUsername(),
                "password"      => $this->getPassword(),
                "scope"         => ""
            ];

            $args = [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body'    => $data,
            ];

            $response = wp_remote_post( $this->getTokenUrl(), $args );
            if ( is_wp_error( $response ) ) {
                throw new \Exception(esc_html__('Authentication error', 'alfaomega-ebooks'));
            }

            $body = json_decode($response['body'], true);
            if (!empty($body['error'])) {
                throw new \Exception(esc_html__('Authentication error', 'alfaomega-ebooks') . "- " . $body['error']);
            }
            $this->saveAuthToFile(json_encode($response['body']));
            return $body['token'];
        }

        public function getAuth()
        {
            $token = $this->getAuthFromFile();
            if (is_null($token))
                $token = $this->authenticate();

            return json_decode($token);
        }

        public function getHeaders()
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

        public function request($method, $uri, $data = null, $retrying = false)
        {
            try {
                $uri = $this->getServerUrl() . $uri;
                $headers = $this->getHeaders();
                if ($headers) {
                    $args = [ 'headers' => $headers ];
                    switch ($method) {
                        case 'post':
                            $args['body'] = $data;
                            $response = wp_remote_post( $uri, $args );
                            break;
                        case 'get':
                            $response = wp_remote_get( $uri, $args );
                            break;
                        default:
                            throw new Exception(esc_html__('method not supported yet', 'alfaomega-ebooks'));
                    }
                    if ( is_wp_error( $response ) ) {
                        throw new Exception(esc_html__('Request failed', 'alfaomega-ebooks'));
                    }

                    return $response;
                } else {
                    throw new Exception(esc_html__('Request failed', 'alfaomega-ebooks'));
                }
            } catch (\Exception $exception) {
                throw new Exception(esc_html__('Request failed', 'alfaomega-ebooks') . ":" . $exception->getMessage());
            }
        }

        public function get($url, $retrying = false)
        {
            return $this->request('get', $url, $retrying);
        }

        public function post($url, $data, $retrying = false)
        {
            return $this->request('post', $url, $data, $retrying);
        }

        public function getTokenUrl()
        {
            return $this->settings['alfaomega_ebooks_token'];
        }

        public function getServerUrl()
        {
            return rtrim($this->settings['alfaomega_ebooks_panel'], '/');
        }

        public function getClientId()
        {
            return $this->settings['alfaomega_ebooks_client_id'];
        }

        public function getClient()
        {
            return $this->settings['alfaomega_ebooks_client'];
        }

        public function getSecret()
        {
            return $this->settings['alfaomega_ebooks_client_secret'];
        }

        public function getUsername()
        {
            return $this->settings['alfaomega_ebooks_username'];
        }

        public function getPassword()
        {
            return $this->settings['alfaomega_ebooks_password'];
        }

        public function getReaderUrl()
        {
            return $this->settings['alfaomega_ebooks_reader'];
        }

        public function getUserTokenString($data)
        {
        }
    }
}
