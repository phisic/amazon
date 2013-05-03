<?php

class Statistics {

    public function getTopPriceDrops($limit = 10) {
        $c = new CDbCriteria(array(
            'select' => 'ASIN, sum(delta) as price_drop',
            'order' => 'price_drop desc',
            'limit' => $limit,
            'group' => 'ASIN',
        ));
        $c->addCondition('`Date` > (now() - Interval 1 DAY)');
        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryAll();
        $asins = array();
        foreach ($rows as $row){
            $asins[$row['ASIN']] = $row['price_drop'];
        }
        if (!($r = Yii::app()->cache->get('price-drops-daily'))) {
            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Medium')->lookup(join(',', array_keys($asins)));
            Yii::app()->cache->add('price-drops-daily', $r, 3600);
        }
        
        if(empty($r['Items']['Item']))
            return array('items'=>array(),'priceDrops'=>array());
        return array('items'=>$r['Items']['Item'],'priceDrops'=>$asins);
    }
    
    public function getTopBestSellers($limit = 10){
        if (!($bestSellers = Yii::app()->cache->get('best-sellers'))) {
            $bestSellers = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('Medium')
                    ->optionalParameters(array('Sort'=>'salesrank','ItemPage' => Yii::app()->request->getParam('page', 1)))
                    ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
            Yii::app()->cache->add('best-sellers', $bestSellers, 3600);
        }
        $list = array();
        for($i=0; $i< $limit; $i++){
            $list[] = $bestSellers['Items']['Item'][$i];
        }
        return $list;
    }
    
    public function getTopReviewed($limit = 10){
        if (!($bestSellers = Yii::app()->cache->get('top-reviews'))) {
            $bestSellers = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('Medium')
                    ->optionalParameters(array('Sort'=>'reviewrank','ItemPage' => Yii::app()->request->getParam('page', 1)))
                    ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
            Yii::app()->cache->add('top-reviews', $bestSellers, 3600);
        }
        $list = array();
        for($i=0; $i< $limit; $i++){
            $list[] = $bestSellers['Items']['Item'][$i];
        }
        return $list;
    }
    
    public function getNewReleases(){
        if (!($r = Yii::app()->cache->get('new-releases'))) {
            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('NewReleases')->browseNodeLookup(Yii::app()->params['node']);
            Yii::app()->cache->add('new-releases', $r, 3600);
        }

        if (!empty($r['BrowseNodes']['BrowseNode']['NewReleases']['NewRelease'])) {
            $asin = array();
            foreach ($r['BrowseNodes']['BrowseNode']['NewReleases']['NewRelease'] as $i) {
                $asin[] = $i['ASIN'];
            }
            if (!empty($asin)) {

                if (!($r = Yii::app()->cache->get('new-releases-body'))) {
                    $asin = join(',', $asin);
                    $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Medium')->lookup($asin);
                    Yii::app()->cache->add('new-releases-body', $r, 3600);
                }
            }
        }
        
        return $r['Items']['Item'];
    }

}