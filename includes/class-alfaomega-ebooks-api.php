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
            $this->token_filename = ALFAOMEGA_SECURITY_PATH . '/token.txt';
        }

        private function getAuthFromFile(): ?string
        {
            return file_exists($this->token_filename)
                ? file_get_contents($this->token_filename)
                : null;
        }

        public function saveAuthToFile($data): void
        {
            $data = json_decode($data);
            $nextYear = date('Y-m-d', strtotime('+360 days'));
            $data->expires_in = date_timestamp_get(date_create($nextYear));
            file_put_contents($this->token_filename, json_encode($data));
        }

        public function authenticate()
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

        public function getAuth()
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
                $uri = $this->getApiUrl($uri);
                $headers = $this->getHeaders();
                if ($headers) {
                    $args = [ 'headers' => $headers, 'timeout' => 60 ];
                    switch ($method) {
                        case 'post':
                            $args['body'] = json_encode($data);
                            $args['method'] = 'POST';
                            $response = wp_remote_post( $uri, $args );
                            break;
                        case 'get':
                            $response = wp_remote_get( $uri, $args );
                            break;
                        default:
                            throw new Exception(esc_html__('method not supported yet', 'alfaomega-ebooks'));
                    }
                    if ( is_wp_error( $response ) || !empty($response->errors)) {
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

        public function getApiUrl($url)
        {
            return rtrim($this->settings['alfaomega_ebooks_api'], '/') . $url;
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

        protected function curlPost($url, $payload): string
        {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

                return curl_exec($ch);
            } catch (\Exception $exception) {
                throw new \Exception(esc_html__('Request error', 'alfaomega-ebooks') . "- " . $exception->getMessage());
            }
        }
    }
}
