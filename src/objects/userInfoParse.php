<?php


    namespace blackpanda\ibs\objects;


    class userInfoParse extends HTMLParse
    {
        public function __construct($html)
        {
            parent::__construct($html);

            $this->html = $this->getResponse();
        }

        public function userExist()
        {
            return (!strpos($this->html, 'does not exists'));
        }

        public function getUserID()
        {
            $preg = preg_match("/.*change_credit.php\?user_id\=(?<id>\d+)/i", $this->html, $match);
            return (isset($match['id'])) ? $match['id'] : false;
        }

        public function getUserGroup()
        {
            $preg = preg_match("/\"\/IBSng\/admin\/group\/group_info\.php\?group_name=(?<group>\w+)\"/i", $this->html, $match);
            return (isset($match['group'])) ? $match['group'] : false;
        }

        public function getUsername()
        {
            $usernameWrapper = $this->getPart('Internet Username',200);
            $preg = preg_match("#Internet Username(.*)class=\"Form_Content_Row_Right_userinfo_light\">(?<username>.*)<\/td>#s", $usernameWrapper, $match);
            if(isset($match['username']))
            {
                $preg = preg_match("/\w+/i",$match['username'],$username);
            }

            return (isset($username[0])) ? $username[0] : false;
        }

        public function getCreationDate()
        {
            $creationWrapper = $this->getPart('Creation Date' , 200);
            $preg = preg_match("#Creation Date(.*)class=\"Form_Content_Row_Right_light\">(?<creation>\d+-\d+-\d+\s\d+:\d+).*<\/td>#s", $creationWrapper, $match);
            return (isset($match['creation'])) ? strtotime($match['creation']) : false;
        }

    }
