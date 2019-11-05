<?php


    namespace blackpanda\ibs\objects;


    use Illuminate\Support\Collection;

    class searchUsersParse extends HTMLParse
    {
        public function __construct($html)
        {
            parent::__construct($html);
        }

        private function validation(){
            return (!$this->getPart('List of Users',10)) ? false : true;
        }

        public function getResults()
        {
            if(!$this->validation()) return false;
            $startTable = strpos($this->html,'List of Users');
            $startTable = substr($this->html,$startTable,999999);
            $endTable = strpos($startTable,'Attributes to Edit');
            $resultTable = substr($startTable,0,$endTable);
            $replace = preg_replace("/\s+/A","",$resultTable);
            $rows = explode('</tr>',$replace);
            $users = [];
            foreach ($rows as $row)
            {
                $preg = preg_match_all("#(<tr class=\"list_row_\w+Color\".*user_id=\d+'\".*input type=checkbox.*value=\"\d+\".*</script>.*</td>.*<td class=\"list_col\".*a class=\"link_in_body\".*href=\"/IBSng/admin/user/user_info\.php\?user_id=(?<id>\d+)\">\d+</a>\s+?</td>\s+?<td\s+class=\"\w+\"\s+?valign=\"\w+\"\s+?>\s+(?<user>[\w\W]+)\s+?</td>\s+?<td\s+class=\"\w+\"\s+?valign=\"\w+\"\s+?>\s+?(?<credit>[\d,]+)\s+</td>\s+?<td\s+class=\"\w+\"\s+?valign=\"\w+\"\s+?>\s+?(?<group>\w+)\s+</td>\s+?<td\s+?class=\"\w+\"\s+?valign=\"\w+\"\s+?>\s+?(?<owner>\w+)\s+?</td>\s+?<td\s+?class=\"\w+\"\s+?valign=\"\w+\"\s+?>\s+?(?<creation>[-:\s\d]+)\s+?</td>\s+?<td\s+?class=\"\w+\"\s+?valign=\"\w+\"\s+?>[\w\s\W]+\s+?</td>\s+?<td\s+?class=\"\w+\"\s+?valign=\"\w+\"\s+?>[\s\w\W]+\s+?</td>\s+?<td\s+?class=\"\w+\"\s+?valign=\"\w+\"\s+?>[\s\W\w]+\s+?</td>\s+?<td\s+?class=\"\w+\"\s+?valign=\"\w+\"\s+?>\s+?[\w\W\s]+\s+?</td>\s+?)#mis",$row,$match);
                if($preg > 0) {
                    $results = array_filter($match,function ($key){return (in_array($key,['id','user','credit','owner','group','creation']));},ARRAY_FILTER_USE_KEY);
                    unset($results[0]);
                    $item = [];
                    foreach ($results as $key => $val){
                        $item[$key] = $val[0];
                    }
                    $users[] = $item;
                }
            }
            return $users;
        }

    }
