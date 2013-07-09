<?php

class AnswerCommand extends CConsoleCommand {

    public function run($args) {
        $size = 100;
        $page = 1;
        $c = new CDbCriteria(array(
            'order' => 'SalesRank',
            'distinct' => true,
            'select' => 'ASIN, Title, SubItem'
        ));
        $c->addCondition('Answer=0');
        $fetch = true;
        while ($fetch) {
            $c->limit = $size;
            $c->offset = $size * ($page - 1);
            
            $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
            $fetch = !empty($rows);
            if ($fetch) {
                foreach ($rows as $r) {
                    $keywords = explode(' ',$r['Title']);
                    if(count($keywords)>2)
                        $keyword = $keywords[0].'+'.$keywords[1] .'+'.$keywords[2];
                    else 
                        $keyword = $r['Title'];
                    $keyword = strtr($keyword, array(','=>'','('=>'',')'=>''));
                    $this->search($keyword);
                }
            }
            $page++;
        }
    }
    
    protected function search($keyword){
        $result = file_get_contents('http://answers.yahoo.com/search/search_result?p='.$keyword);
        preg_match_all('@\?qid\=[0-9a-zA-Z]+@', $result, $matches);
        if(empty($matches[0]))
            continue;
        
        foreach ($matches[0] as $m){
            $q = file_get_contents('http://answers.yahoo.com/question/index'.$m);
             $t1 = strpos($q, '<h1 class="subject">');
             if($t1==false)
                 continue;
             $t2 = strpos($q, '</h1>', $t1);
             $title = substr($q, $t1+20,$t2-$t1-20);
             $d1 = true;
             while($d1){
                $d1 = strpos($q, '<div class="content">');
                if($d1==false)
                    continue;
                
                $d2 = strpos($q, '</div>', $d1);
                $answers[] = substr($q, $d1+21, $d2-$d1-21);
                $q = substr($q, $d2);
             }
             
        }
    }

}