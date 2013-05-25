<?php

class BenchmarkCommand extends CConsoleCommand {

    protected $url = array(
        'cpu' => 'http://www.cpubenchmark.net/cpu_list.php',
        'vga' => 'http://www.videocardbenchmark.net/gpu_list.php',
        'hdd' => 'http://www.harddrivebenchmark.net/hdd_list.php',
        'ram' => 'http://www.memorybenchmark.net/ram_list.php'
    );
    protected $urlLaptop = array(
        'cpu'=>'http://www.cpubenchmark.net/laptop.html',
    );
    
    protected $urlAdditional = array(
        'cpu' => 'cpu-additional.html',
    );
    
    public function run($args) {
        $type = $args[0];
        if ($type == 'match') {
            $this->matchLaptops();
            return;
        }

        if ($type == 'assignmatch') {
            $this->assignMatch();
            return;
        }

        if ($type == 'preparematch') {
            $this->preparematch();
            return;
        }

        if (empty($this->url[$type]))
            die('invalid param');
        
        $contentLaptop = '';
        $content = file_get_contents($this->url[$type]);
        if(isset($this->urlLaptop[$type]))
            $contentLaptop = file_get_contents($this->urlLaptop[$type]);
        if(isset($this->urlAdditional))
            $contentLaptop .= file_get_contents($this->urlAdditional[$type]);
        
        $pos1 = strpos($content, '<TABLE ID="cputable" class="cpulist">');
        $pos2 = strpos($content, '</TABLE>');
        $table = substr($content, $pos1, $pos2 - $pos1);
        $process = true;
        while ($process) {
            $pos1 = strpos($table, '<TR>');
            $pos2 = strpos($table, '</TR>');

            $row = substr($table, $pos1, $pos2 + 5);
            $table = substr($table, $pos2 + 5);
            if (!empty($row)) {
                $data = $this->fetch($row);
                if (!empty($data['Model']) && strpos($contentLaptop, $data['Model'])) {
                    $c = new CDbCriteria(array('select' => 'Id'));
                    $c->addColumnCondition(array('type' => $type, 'Model' => $data['Model']));
                    $r = Yii::app()->db->getCommandBuilder()->createFindCommand('part', $c)->queryRow();
                    if (empty($r)) {
                        $data['Type'] = $type;
                        Yii::app()->db->getCommandBuilder()->createInsertCommand('part', $data)->execute();
                    }
                }
            }

            $process = $pos2;
        }
    }

    protected function fetch($row) {
        $pos1 = strpos($row, '<TD>');
        $pos2 = strpos($row, '</TD>');
        $model = substr($row, $pos1, $pos2 + 5);

        $row = substr($row, $pos2 + 5);
        $pos1 = strpos($row, '<TD>');
        $pos2 = strpos($row, '</TD>');
        $score = substr($row, $pos1, $pos2 + 5);

        return array('Model' => strip_tags($model), 'Score' => strip_tags($score));
    }

    protected function preparematch() {
        $rows = true;
        $size = 100;
        $page = 0;
        $c = new CDbCriteria(array('select' => 'ASIN,Data',
                //'condition'=>'ASIN="B005JY68GW"'
        ));
        while ($rows) {
            $c->limit = $size;
            $c->offset = $size * $page;
            $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
            foreach ($rows as $row) {
                echo 'ASIN=' . $row['ASIN'] . "\n";
                $d = @unserialize($row['Data']);
                if (empty($d))
                    continue;
                $s = '';
                if (isset($d['ItemAttributes']['Feature'])) {
                    if (is_array($d['ItemAttributes']['Feature'])){
                        foreach ($d['ItemAttributes']['Feature'] as &$f)
                            $f = strip_tags ($f);
                        
                        $s .= join(' ', $d['ItemAttributes']['Feature']);
                    }
                    else
                        $s.=strip_tags ($d['ItemAttributes']['Feature']);
                }
                $s .= ' ' . strip_tags($d['ItemAttributes']['Title']);
                if (isset($d['EditorialReviews']['EditorialReview'])) {
                    if (is_array($d['EditorialReviews']['EditorialReview'])) {
                        if (isset($d['EditorialReviews']['EditorialReview']['Content']))
                            $s.= ' ' . strip_tags ($d['EditorialReviews']['EditorialReview']['Content']);
                        else
                            foreach ($d['EditorialReviews']['EditorialReview'] as $rev) {
                                $s.= ' ' . strip_tags($rev['Content']);
                            }
                    } else {
                        $s.= ' ' . strip_tags($d['EditorialReviews']['EditorialReview']);
                    }
                }
                Yii::app()->db->getCommandBuilder()->createInsertCommand('listingdata', array('ASIN' => $row['ASIN'], 'Data' => $s))->execute();
            }
            $page++;
        }
    }

    protected function matchLaptops($mode = 'exact') {
        $rows = true;
        $size = 100;
        $page = 0;
        Yii::app()->db->getCommandBuilder()->createSqlCommand('truncate partmatch;')->execute();
        $searchCriteria = new stdClass();
        $pages = new CPagination();
        $pages->pageSize = 10000;
        $searchCriteria->select = '*';
        $searchCriteria->paginator = $pages;
        $searchCriteria->from = 'listingdata_index';
        
        $c = new CDbCriteria(array('select' => 'Model,Id'));
        $c->compare('Type', 'cpu');
        while ($rows) {
            $c->limit = $size;
            $c->offset = $size * $page;
            $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('part', $c)->queryAll();
            foreach ($rows as $row) {
                $row['Model'] = strtr($row['Model'],array('/'=>'',' APU'=>'', 'AMD'=>'','Intel'=>''));
                $p = strpos($row['Model'], '@');
                if($p!==false)
                    $row['Model'] = substr($row['Model'], 0, $p);
                $row['Model'] = trim($row['Model']);
                if(empty($row['Model']))
                    continue;
                $rankMultiplier = 1;
                if($mode=='similar')
                    $row['Model'] = strtr($row['Model'], array(' '=>'|'));
                    
                 if($mode == 'exact')
                     $rankMultiplier = 10;
                echo $row['Model']."\n";
                $searchCriteria->query = $row['Model'];
                Yii::App()->search->setMatchMode(SPH_MATCH_BOOLEAN);
                //Yii::App()->search->setRankingMode(SPH_RANK_WORDCOUNT);
                $resArray = Yii::App()->search->searchRaw($searchCriteria);
                if(!empty($resArray['matches'])){
                    $c2 = new CDbCriteria(array('select'=>'Id,ASIN'));
                    $c2->addInCondition('Id', array_keys($resArray['matches']));
                    $asinlist = Yii::app()->db->getCommandBuilder()->createFindCommand('listingdata', $c2)->queryAll();
                    $sql = '';
                    $cnt = count($asinlist)-1;
                    foreach($asinlist as $a=>$asin){
                        $sql .= '("'.$asin['ASIN'].'"'.','.'"cpu"'.','.$row['Id'].','.$resArray['matches'][$asin['Id']]['weight']*$rankMultiplier.')'.($a!=$cnt ? ',' : '');
                    }
                    
                    $sql = 'insert into partmatch (ASIN,Type,PartId,Relevance) values '.$sql;
                    Yii::app()->db->getCommandBuilder()->createSqlCommand($sql)->execute();
                }
            }
            $page++;
        }
        if($mode == 'exact')
            $this->matchLaptops ('similar');
    }

    protected function assignMatch() {
        $c = new CDbCriteria(array('select' => 'ASIN'));
        $c->distinct = true;
        $c->compare('Type', 'cpu');
        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('partmatch', $c)->queryAll();
        foreach ($rows as $row) {
            $c2 = new CDbCriteria(array('select' => 'PartId'));
            $c2->addColumnCondition(array('ASIN' => $row['ASIN'], 'Type' => 'cpu'));
            $c2->order = 'Relevance desc';
            $c2->limit = 1;
            $match = Yii::app()->db->getCommandBuilder()->createFindCommand('partmatch', $c2)->queryRow();
            $c3 = new CDbCriteria();
            $c3->compare('ASIN', $row['ASIN']);
            Yii::app()->db->getCommandBuilder()->createUpdateCommand('listing', array('CPU' => $match['PartId']), $c3)->execute();
        }
    }

}