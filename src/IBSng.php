<?php


    namespace blackpanda\ibs;


    class IBSng
    {
        protected $ip;
        protected $port;
        protected $adminUsername;
        protected $adminPassword;
        private $cookie;
        private $request;
        private $agent;
        protected $connected = false;
        protected $adminURLSuffix;

        public function __construct(string $ip, int $port = null, string $adminUsername = null, string $adminPassword = null, bool $autpLogin = true)
        {
            $this->ip = $ip;
            $this->port = (is_null($port)) ? config('ibsng.IBSng_port') : $port;
            $this->adminUsername = (is_null($adminUsername)) ? config('ibsng.default_admin_username') : $adminUsername;
            $this->adminPassword = (is_null($adminPassword)) ? config('ibsng.default_admin_password') : $adminPassword;
            $this->agent = (is_null($adminPassword)) ? config('ibsng.default_requests_agent') : $adminPassword;

            // set cooKie
            $this->cookie = tempnam(sys_get_temp_dir(), "CURLCOOKIE");

            // Set Admin URL Suffix
            $this->adminURLSuffix = '/IBSng/admin/';

            // Hidden lib xml errors
            libxml_use_internal_errors(true);

            if ($autpLogin) $this->login();


        }


        // get User By ID
        public function getUserbyID(int $userID)
        {
            $url = $this->ip . $this->adminURLSuffix . 'user/user_info.php';
            $GET = [
                'user_id_multi' => $userID
            ];
            $response = $this->sendRequest($url, [], $GET, true);
            var_dump($response);

        }


        private function login()
        {
            $url = $this->ip . $this->adminURLSuffix;
            $postData = [];
            $postData['username'] = $this->adminUsername;
            $postData['password'] = $this->adminPassword;
            $response = $this->sendRequest($url, $postData, [], true);
            if (strpos($response, 'admin_index') > 0) {
                $this->connected = true;
                return true;
            }
            throw new \Exception ("Can't login to IBSng. Wrong username or password");
        }

        // Setter Methods

        public function __set($name, $value)
        {
            $this->{$name} = $value;
        }

        // Setter For Facade

        public function setSetver(string $ip, int $port = null, string $adminUsername = null, string $adminPassword = null)
        {
            $this->ip = $ip;
            $this->port = (is_null($port)) ? config('ibsng.IBSng_port') : $port;
            $this->adminUsername = (is_null($adminUsername)) ? config('ibsng.default_admin_username') : $adminUsername;
            $this->adminPassword = (is_null($adminPassword)) ? config('ibsng.default_admin_password') : $adminPassword;
        }

        // Send IBSng Requests

        private function sendRequest($url, array $post = [], array $get = [], $header = false, $cookies = null)
        {
            if (empty($cookies)) {
                $cookies = $this->cookie;
            }

            // Set Handler
            $this->request = curl_init();

            // SetTimeOut
            curl_setopt($this->request, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($this->request, CURLOPT_TIMEOUT, 10);

            // Set URl and PORT
            curl_setopt($this->request, CURLOPT_URL, $url);
            curl_setopt($this->request, CURLOPT_PORT, $this->port);

            // Set Post PARAMS
            curl_setopt($this->request, CURLOPT_POST, true);
            curl_setopt($this->request, CURLOPT_POSTFIELDS, $post);

            // SET Headers
            if ($header) {
                curl_setopt($this->request, CURLOPT_HEADER, $header);
                curl_setopt($this->request, CURLOPT_HTTPHEADER, [
                    "Content-type" => "application/json",
                    "Accept" => "application/json",
                    "User-Agent" => "ibs-jsonrpc",
                    "Accept-Charset" => "utf-8",
                    "Cache-Control" => "no-cache",
                ]);
            }

            // Set User Agent
            curl_setopt($this->request, CURLOPT_USERAGENT, $this->agent);


            // Set Cookies
            curl_setopt($this->request, CURLOPT_COOKIEFILE, $this->cookie);
            curl_setopt($this->request, CURLOPT_COOKIEJAR, $this->cookie);

            // other Request Settings
            //curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($this->request, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($this->request, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($this->request);

            return $response;
        }


    }
