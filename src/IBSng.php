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

        public function __construct( string $ip ,int $port = null ,string $adminUsername = null,string $adminPassword = null)
        {
            $this->ip = $ip;
            $this->port = ( is_null($port) ) ? config('ibsng.IBSng_port') : $port;
            $this->adminUsername = ( is_null($adminUsername) ) ? config('ibsng.default_admin_username') : $adminUsername;
            $this->adminPassword = ( is_null($adminPassword) ) ? config('ibsng.default_admin_password') : $adminPassword;
            $this->agent = ( is_null($adminPassword) ) ? config('ibsng.default_requests_agent') : $adminPassword;

            // set cooKie
            $this->cookie = tempnam(sys_get_temp_dir(),"CURLCOOKIE");

            // Hidden lib xml errors
            libxml_use_internal_errors(true);

        }

        private function login(){
            $url = $this->ip . '/IBSng/admin/';
            $postData['username'] = $this->username;
            $postData['password'] = $this->password;
            $output = $this->request($url, $postData, true);
            if (strpos($output, 'admin_index') > 0) {
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

        public function setSetver(string $ip ,int $port = null ,string $adminUsername = null,string $adminPassword = null)
        {
            $this->ip = $ip;
            $this->port = ( is_null($port) ) ? config('ibsng.IBSng_port') : $port;
            $this->adminUsername = ( is_null($adminUsername) ) ? config('ibsng.default_admin_username') : $adminUsername;
            $this->adminPassword = ( is_null($adminPassword) ) ? config('ibsng.default_admin_password') : $adminPassword;
        }

        // Send IBSng Requests

        private function sendRequest($url, array $post = [], array $get = [], $cookies = null ){
            if(empty($cookies)) { $cookies = $this->cookie; }

            $this->request = curl_init($url);
            curl_setopt($this->request, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($this->request, CURLOPT_TIMEOUT, 10);
            curl_setopt($this->request, CURLOPT_URL, $url);
            curl_setopt($this->request, CURLOPT_PORT, $this->port);
            curl_setopt($this->request, CURLOPT_POST, true);
            curl_setopt($this->request, CURLOPT_POSTFIELDS, $post);
            curl_setopt($this->request, CURLOPT_HEADER, $get);
            curl_setopt($this->request, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($this->request, CURLOPT_USERAGENT, $this->agent);
            curl_setopt($this->request, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->request, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->request, CURLOPT_COOKIEFILE, $this->cookie);
            curl_setopt($this->request, CURLOPT_COOKIEJAR, $this->cookie);

            $response = curl_exec($this->handler);
        }


    }
