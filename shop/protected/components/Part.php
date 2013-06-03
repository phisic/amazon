<?php

class Part extends CApplicationComponent {
    protected $_parts = array();
    
    public function getByAsins($asins){
        $c = new CDbCriteria();
        $c->addInCondition('ASIN', $asins);
        $c->select = 'ASIN, CPU, VGA';
        $partsAsin = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
        $partList = array();
        foreach ($partsAsin as $partAsin) {
            if(!empty($partAsin['CPU']))
                $partList[$partAsin['CPU']][] = $partAsin['ASIN'];
            if(!empty($partAsin['VGA']))
                $partList[$partAsin['VGA']][] = $partAsin['ASIN'];
        }
        
        $c2 = new CDbCriteria();
        $c2->select = 'Id,Type,Model,Score,Image';
        $c2->addInCondition('Id', array_keys($partList));
        $parts = Yii::app()->db->getCommandBuilder()->createFindCommand('part', $c2)->queryAll();
        $asinList = array();
        foreach($parts as $part){
            foreach ($partList[$part['Id']] as $asin)
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
        $c2 = new CDbCriteria(array('select'=>'t.Id,p.Type,sum(p.Relevance) as Relevance, t.Model,t.Image'));
        $c2->join = 'Left JOIN partmatch p on p.partId=t.id';
        $c2->compare('ASIN', $asin);
        $c2->group = 't.Id';
        $c2->order = 'Relevance desc';
        if(empty($this->_parts))
            $this->_parts = Yii::app()->db->getCommandBuilder()->createFindCommand('part', new CDbCriteria())->queryAll();
        
        return CHtml::listData(array_merge(Yii::app()->db->getCommandBuilder()->createFindCommand('part', $c2)->queryAll(), $this->_parts), 'Id', 'Model', 'Type');
    }
    
}