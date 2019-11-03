<?php


    namespace blackpanda\ibs;


    use blackpanda\ibs\objects\createNewUserIDParse;
    use blackpanda\ibs\objects\setNewUserInfoParse;
    use blackpanda\ibs\objects\userEditParse;
    use blackpanda\ibs\objects\userInfoParse;

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
        protected $group;
        protected $credit;

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

            // Login to IBSng Automatically
            if ($autpLogin) $this->login();

            // set Default Values for Create New Users
            $this->group = config('ibsng.default_user_group');
            $this->credit = config('ibsng.default_user_credit');


        }


        // get User By ID from user info Page and edit Page
        public function getUserByID(int $userID)
        {
            $url = $this->adminUrl('user/user_info.php');
            $GET = [
                'user_id_multi' => $userID
            ];
            $response = $this->sendRequest($url, [], $GET, true);
            $parse = new userInfoParse($response);
            if($parse->userExist())
            {
                $result = new \stdClass();
                $result->id = $userID;
                $result->username = $parse->getUsername();
                $result->password = $this->getUserPassword($userID);
                $result->creation = $parse->getCreationDate();

                return $result;
            }

            return false;

        }

        // Get Password From edit page
        public function getUserPassword(int $userID)
        {
            return $this->getUserEditPageInfo($userID)->getUserPassword();
        }

        // Get Username from edit Page
        public function getUsername(int $userID)
        {
            return $this->getUserEditPageInfo($userID)->getUserPassword();
        }

        // Create New User
        public function createNewUser(string $username,string $password,string $group = null,int $credit = null)
        {
            if(is_null($group)) $group = $this->group;
            if(is_null($credit)) $credit = $this->credit;

            $newUserID = $this->createNewUserID($group , $credit);

            if(!$newUserID) return false;

            $url = $this->adminUrl('plugins/edit.php');

            $get = [
                'edit_user' => 1,
                'user_id' => $newUserID,
                'submit_form' => 1,
                'add' => 1,
                'count' => 1,
                'credit' => 1,
                'owner_name' => $this->adminUsername,
                'group_name' => $this->group,
                'x' => 35,
                'y' => 1,
                'edit__normal_username' => 'normal_username'
            ];

            $post = [
                'target_id' => $newUserID,
                'normal_username' => $username,
                'password' => $password,
                'credit' => $credit,
                'target' => 'user',
                'normal_save_user_add' => 1,
                'edit_tpl_cs' => 'normal_username',
                'attr_update_method_0' => 'normalAttrs',
                'has_normal_username' => 't',
                'current_normal_username' => '',
                'update' => 1,
            ];

            $request = $this->sendRequest($url,$post,$get,true);

            $parse = new setNewUserInfoParse($request);

            if($parse->success()){
                $result = new \stdClass();
                $result->id = $newUserID;
                $result->username = $username;
                $result->password = $password;
                $result->creation = strtotime('now');

                return $result;
            }

            return false;

        }

        // Create new User ID
        private function createNewUserID( string $group , int $credit){
            $params = [
                'submit_form' => 1,
                'add' => 1,
                'count' => 1,
                'credit' => $credit,
                'owner_name' => $this->adminUsername,
                'group_name' => $group,
                'edit__normal_username' => 1,
            ];

            $url = $this->adminUrl('user/add_new_users.php');

            $request = $this->sendRequest($url , $params , [] , true);

            $parse = new createNewUserIDParse($request);

            return $parse->getUserID();
        }

        // Parse schema://uri/IBSng/admin/plugins/edit.php page Information
        private function getUserEditPageInfo(int $userID)
        {
            $url = $this->adminUrl('plugins/edit.php');
            $post = [
                'user_id' => $userID,
                'edit_user' => 1,
                'attr_edit_checkbox_2' => 'normal_username'
            ];
            $response = $this->sendRequest($url,$post);
            return new userEditParse($response);
        }

        // login To IBSng
        private function login()
        {
            $url = $this->adminUrl();
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

            // set gets
            if($get)
            {
                $url = $url .'?'. http_build_query($get);
            }

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

        // Generate Admin URL for Send Requests
        private function adminUrl($endpoint = null)
        {
            if(is_null($endpoint)) return $this->ip . $this->adminURLSuffix;
            return $this->ip . $this->adminURLSuffix . ltrim($endpoint , '/');
        }



    }
