<?php

class Part extends CApplicationComponent {
    
    public function getByIds(array $ids)
    {
        $c2 = new CDbCriteria();
        $c2->addInCondition('Id', $ids);
        $parts = Yii::app()->db->getCommandBuilder()->createFindCommand('part', $c2)->queryAll();
        $partList = array();
        foreach ($parts as $part) {
            $partList[$part['Id']] = $part;
        }
        return $partList;
    }
    
    public function getByAsin($asin){
        $c2 = new CDbCriteria(array('select'=>'p.Id,t.Type,p.Model'));
        $c2->join = 'JOIN part p on t.partId=p.id';
        $c2->compare('ASIN', $asin);
        return Yii::app()->db->getCommandBuilder()->createFindCommand('partmatch', $c2)->queryAll();
    }
    
}