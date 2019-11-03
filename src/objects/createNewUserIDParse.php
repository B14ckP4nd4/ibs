<?php


    namespace blackpanda\ibs\objects;


    class createNewUserIDParse extends HTMLParse
    {
        public function __construct($html)
        {
            parent::__construct($html);
            $this->html = $this->getResponse();
            $this->validate();

        }

        private function validate(){
            if(strpos($this->html,'invalid') !== false)
                throw new \Exception('Something is wrong in request Parameters');

            return true;
        }

        public function getUserID(){

            $locationWrapper = $this->getPart('Location' , 100);

            $preg = preg_match("#Location(.*)edit_user=1&user_id=(?<userID>\d+)&.*#s", $locationWrapper, $match);

            return (isset($match['userID'])) ? $match['userID'] : false;

        }



    }
