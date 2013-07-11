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
        $c->addCondition('Answer=0 and SubItem=0');
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
                    $resCount = 0;
                    $wordsCount = 4;
                    $totalCount = 0;
                    
                    while ($totalCount < 50 && $wordsCount > 0) {
                        $resCount = 10;
                        
                        $page = 1;
                        $keyword = join('+', array_slice($keywords, 0, $wordsCount));
                        echo 'Key=' . $keyword ."\n";
                        while ($resCount == 10 && $page < 15) {
                            $res = $this->search($keyword, $page);
                            $resCount = count($res);
                            $totalCount+=$resCount;
                            echo 'Page=' . $page . ' count=' . $totalCount . "\n";
                            if ($resCount)
                                $result = array_merge($result, $res);
                            $page++;
                        }
                        $wordsCount--;
                        
                    }
                    

                    foreach ($result as $qid => $q) {
                        $exist = Yii::app()->db->getCommandBuilder()->createFindCommand('question', new CDbCriteria(array('select' => 'QId', 'condition' => 'qid=:qid', 'params' => array(':qid' => $qid))))->queryRow();
                        if (empty($exist)) {
                            Yii::app()->db->getCommandBuilder()->createInsertCommand('question', array('QId' => $qid, 'Title' => $q['Title'], 'Text' => array_shift($q['Answers']), 'Date' => date('Y-m-d H:i')))->execute();
                            foreach ($q['Answers'] as $answer) {
                                Yii::app()->db->getCommandBuilder()->createInsertCommand('answer', array('QId' => $qid, 'Text' => $answer))->execute();
                            }
                        }
                        Yii::app()->db->getCommandBuilder()->createSqlCommand('Replace into listing2question (QId,ASIN) Values(:qid,:asin)', array(':qid' => $qid, ':asin' => $r['ASIN']))->execute();
                    }
                    $c = new CDbCriteria();
                    $c->compare('ASIN', $r['ASIN']);
                    Yii::app()->db->getCommandBuilder()->createUpdateCommand('listing', array('Answer' => 1), $c)->execute();
                }
            }
            Yii::app()->db->getCommandBuilder()->createSqlCommand('Update listing l set Answer=(select count(*) from listing2question lq where l.ASIN=lq.ASIN limit 1)')->execute();
        }
    }

    protected function search($keyword, $page) {
        $result = $this->grabContent('http://answers.yahoo.com/search/search_result?type=2button&p=' . $keyword . '&page=' . $page);
        preg_match_all('@\?qid\=[0-9a-zA-Z]+@', $result, $matches);
        if (empty($matches[0]))
            return array();
        $qe = array();
        foreach ($matches[0] as $m) {
            $q = $this->grabContent('http://answers.yahoo.com/question/index' . $m);
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

    public function grabContent($url) {
        $curl = curl_init();
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com');
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);

        $html = curl_exec($curl);
        curl_close($curl);

        return $html;
    }

}