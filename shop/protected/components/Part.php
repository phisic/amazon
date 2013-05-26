<?php

class Part extends CApplicationComponent {
    
    public function getByAsins($asins){
        $c = new CDbCriteria();
        $c->addInCondition('ASIN', $asins);
        $c->select = 'ASIN, CPU, VGA';
        $partsAsin = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
        $partList = array();
        foreach ($partsAsin as $partAsin) {
            if(!empty($partAsin['CPU']))
                $partList[$partAsin['CPU']]= $partAsin['ASIN'];
            if(!empty($partAsin['VGA']))
                $partList[$partAsin['VGA']]= $partAsin['ASIN'];
        }
        
        $c2 = new CDbCriteria();
        $c2->select = 'Id,Type,Model,Score,Image';
        $c2->addInCondition('Id', array_keys($partList));
        $parts = Yii::app()->db->getCommandBuilder()->createFindCommand('part', $c2)->queryAll();
        $asinList = array();
        foreach($parts as $part){
            $asin = $partList[$part['Id']];
            $asinList[$asin][$part['Type']] = $part;
        }
        
        return $asinList;
    }
    
    public function getMaxScore($type){
        if($score = Yii::app()->cache->get('max-score-'.$type))
            return $score;
        $c = new CDbCriteria(array('select'=>'max(Score)','join'=>'JOIN listing l on l.'.$type.'=t.Id'));
        $score = Yii::app()->db->getCommandBuilder()->createFindCommand('part', $c)->queryScalar();
        Yii::app()->cache->set('max-score-'.$type, $score, 3600*24*7);
        return $score;
    }
    public function getByIds(array $ids)
    {
        $c2 = new CDbCriteria();
        $c2->addInCondition('Id', $ids);
        $parts = Yii::app()->db->getCommandBuilder()->createFindCommand('part', $c2)->queryAll();
        $partList = array();
        foreach ($parts as $part) {
            $partList[$part['Type']][$part['Id']] = $part;
        }
        return $partList;
    }
    
    public function getByAsin($asin){
        $c2 = new CDbCriteria(array('select'=>'p.Id,t.Type,p.Model,p.Image'));
        $c2->join = 'JOIN part p on t.partId=p.id';
        $c2->compare('ASIN', $asin);
        
        return CHtml::listData(Yii::app()->db->getCommandBuilder()->createFindCommand('partmatch', $c2)->queryAll(), 'Id', 'Model', 'Type');
    }
    
}