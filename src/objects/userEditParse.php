<?php


    namespace blackpanda\ibs\objects;


    class userEditParse extends HTMLParse
    {
        private $html;

        public function __construct($html)
        {
            parent::__construct($html);
            $this->html = $this->getResponse();
        }

        public function getUserPassword()
        {
            $preg = preg_match('#<input type=text id=\"password\" name=\"password\" value=\"(?<password>.*)\" class=text>#i',$this->html,$match);
            return (isset($match['password'])) ? $match['password'] : false;
        }

        public function getUsername()
        {
            $preg = preg_match('#<input type=hidden name=current_normal_username value=\'(?<username>.*)\'>#i',$this->html,$match);
            return (isset($match['username'])) ? $match['username'] : false;
        }


    }
