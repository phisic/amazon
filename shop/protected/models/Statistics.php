<?php
class Statistics {
    public function getTopPriceDrops($limit = 10){
        $c = new CDbCriteria(array(
            'select'=>'ASIN, sum(delta) as price_drop',
            'order' => 'price_drop desc', 
            'limit' => $limit,
            'group' => 'ASIN',
        ));
        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryColumn();
        $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Medium')->lookup(join(',',$rows));
        //print_r($r);exit;
        return $r['Items']['Item'];
    }
}