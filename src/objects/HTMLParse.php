<?php


    namespace blackpanda\ibs\objects;


    abstract class HTMLParse
    {
        private $html;

        public function __construct($html)
        {
            $this->html = $html;
            //$this->responseValidation();
            $this->clean();
        }

        private function clean(){
            $preg = preg_replace('/([.*+\w\W]+)(<body.*>)/i', '$2', $this->html);
            $preg = preg_replace('/\t/i', '', $preg);
            $this->html = $preg;
        }

        private function responseValidation(){
            if (!strpos($this->html, '200 OK')) {
                throw new \Exception('Response not valid');
            }
        }

        protected function getResponse(){
            return $this->html;
        }


    }
