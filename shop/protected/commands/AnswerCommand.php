<?php

class AnswerCommand extends CConsoleCommand {

    public function run($args) {
        $size = 100;
        $page = 1;
        $c = new CDbCriteria(array(
            'order' => 'SalesRank',
            'distinct' => true,
            'select' => 'ASIN, Title'
        ));
        $c->addCondition('Answer=0');
        $fetch = true;
        while ($fetch) {
            $c->limit = $size;

            $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
            $fetch = !empty($rows);
            if ($fetch) {
                foreach ($rows as $r) {
                    $r['Title'] = strtr($r['Title'], array(',' => '', '(' => '', ')' => ''));
                    $keywords = explode(' ', $r['Title']);
                    $result = array();
                    $resCount = 0; $wordsCount = 4; $page=1;
                    while($resCount==0 && $wordsCount > 0)
                    {
                        $resCount = 10;
                        $keyword = join('+', array_slice($keywords, 0, $wordsCount));
                        while($resCount == 10){
                            echo 'Page='.$page."\n";
                            $res = $this->search($keyword,$page);
                            $resCount = count($res);
                            if($resCount)
                                $result = array_merge($result, $res);
                            $page++;
                        }
                        $wordsCount--;
                    }
                    echo 'Key='.$keyword.' count='.$resCount."\n";
                    
                    foreach ($result as $qid => $q) {
                        echo $qid."\n";
                        $exist = Yii::app()->db->getCommandBuilder()->createFindCommand('question', new CDbCriteria(array('select' => 'QId', 'condition' => 'qid=:qid', 'params' => array(':qid' => $qid))))->queryRow();
                        if (empty($exist)) {
                            Yii::app()->db->getCommandBuilder()->createInsertCommand('question', array('QId' => $qid, 'Title' => $q['Title'], 'Text' => array_shift($q['Answers']), 'Date' => date('Y-m-d H:i')))->execute();
                            foreach ($q['Answers'] as $answer) {
                                Yii::app()->db->getCommandBuilder()->createInsertCommand('answer', array('QId' => $qid, 'Text' => $answer))->execute();
                            }
                        }
                        Yii::app()->db->getCommandBuilder()->createInsertCommand('listing2question', array('QId' => $qid, 'ASIN' => $r['ASIN']))->execute();
                    }
                    $c = new CDbCriteria();
                    $c->compare('ASIN', $r['ASIN']);
                    Yii::app()->db->getCommandBuilder()->createUpdateCommand('listing', array('Answer' => 1), $c)->execute();
                }
            }
            Yii::app()->db->getCommandBuilder()->createSqlCommand('Update listing l set Answer=(select count(*) from listing2question lq where l.ASIN=lq.ASIN limit 1)')->execute();
        }
    }

    protected function search($keyword,$page) {
        $result = file_get_contents('http://answers.yahoo.com/search/search_result?p=' . $keyword.'&page='.$page);
        preg_match_all('@\?qid\=[0-9a-zA-Z]+@', $result, $matches);
        if (empty($matches[0]))
            return array();
        $qe = array();
        foreach ($matches[0] as $m) {
            $q = file_get_contents('http://answers.yahoo.com/question/index' . $m);
            $qid = substr($m, 5);
            $t1 = strpos($q, '<h1 class="subject">');
            if ($t1 == false)
                continue;
            $t2 = strpos($q, '</h1>', $t1);
            $qe[$qid]['Title'] = substr($q, $t1 + 20, $t2 - $t1 - 20);
            $d1 = true;
            while ($d1) {
                $d1 = strpos($q, '<div class="content">');
                if ($d1 == false)
                    continue;

                $d2 = strpos($q, '</div>', $d1);
                $qe[$qid]['Answers'][] = substr($q, $d1 + 21, $d2 - $d1 - 21);
                $q = substr($q, $d2);
            }
        }

        return $qe;
    }

}