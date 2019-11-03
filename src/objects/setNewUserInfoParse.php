<?php


    namespace blackpanda\ibs\objects;


    class setNewUserInfoParse extends HTMLParse
    {

        public function __construct($html)
        {
            parent::__construct($html);

        }

        private function validation()
        {
            return (!$this->getPart('already exist',20)) ? true : false ;
        }

        public function success()
        {
            if(!$this->validation()) return false;
            return (!$this->getPart('IBSng/admin/user/user_info.php?user_id_multi',44)) ? false : true;
        }

    }
