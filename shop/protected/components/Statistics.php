<?php

class Statistics extends CApplicationComponent {

    public function getTopPriceDrops($limit = 10) {

        if (!($r = Yii::app()->cache->get('price-drops-daily'))) {
            $c = new CDbCriteria(array(
                'select' => 'ASIN, sum(delta) as price_drop',
                'order' => 'price_drop desc',
                'limit' => $limit,
                'group' => 'ASIN',
            ));
            $c->addCondition('`Date` > (now() - Interval 1 DAY)');
            $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryAll();
            $asins = array();
            foreach ($rows as $row) {
                $asins[$row['ASIN']] = $row['price_drop'];
            }

            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Medium')->lookup(join(',', array_keys($asins)));
            $r['asins'] = $asins;
            Yii::app()->cache->add('price-drops-daily', $r, 1800);
        }

        if (empty($r['Items']['Item']))
            return array('items' => array(), 'priceDrops' => array());
        return array('items' => $r['Items']['Item'], 'priceDrops' => $r['asins']);
    }

    public function getTopBestSellers($limit = 10) {
        if (!($bestSellers = Yii::app()->cache->get('best-sellers'))) {
            $bestSellers = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('Medium')
                    ->optionalParameters(array('Sort' => 'salesrank', 'ItemPage' => Yii::app()->request->getParam('page', 1)))
                    ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
            Yii::app()->cache->add('best-sellers', $bestSellers, 1800);
        }
        $list = array();
        for ($i = 0; $i < $limit; $i++) {
            $list[] = $bestSellers['Items']['Item'][$i];
        }
        return $list;
    }

    public function getTopReviewed($limit = 10) {
        if (!($bestSellers = Yii::app()->cache->get('top-reviews'))) {
            $bestSellers = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('Medium')
                    ->optionalParameters(array('Sort' => 'reviewrank', 'ItemPage' => Yii::app()->request->getParam('page', 1)))
                    ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
            Yii::app()->cache->add('top-reviews', $bestSellers, 1800);
        }
        $list = array();
        for ($i = 0; $i < $limit; $i++) {
            $list[] = $bestSellers['Items']['Item'][$i];
        }
        return $list;
    }

    public function getNewReleases() {
        if (!($r = Yii::app()->cache->get('new-releases'))) {
            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('NewReleases')->browseNodeLookup(Yii::app()->params['node']);
            Yii::app()->cache->add('new-releases', $r, 1800);
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
                    Yii::app()->cache->add('new-releases-body', $r, 1800);
                }
            }
        }

        return $r['Items']['Item'];
    }

    public function inWatch($asinList) {
        if (Yii::app()->user->getIsGuest())
            return array();

        $c = new CDbCriteria(array('select' => 'ASIN,NewUsed'));
        $c->addInCondition('ASIN', $asinList);
        $c->addColumnCondition(array('UserId' => Yii::app()->user->getId()));
        $r = Yii::app()->db->getCommandBuilder()->createFindCommand('watch', $c)->queryAll();
        $list = array();
        foreach ($r as $row) {
            $list[$row['ASIN']][$row['NewUsed']] = true;
        }

        return $list;
    }

    public function getHash($asin, $newUsed, $id) {
        return md5($asin . $newUsed . Yii::app()->params['secret'] . $id);
    }

    public function getLaptopCount() {
        $row = Yii::app()->db->getCommandBuilder()->createFindCommand('price_log', new CDbCriteria(array(
                    'order' => 'DateEnd desc',
                    'limit' => 1
                )))->queryRow();
        
        return number_format($row['ItemsRead'],0,'.',' ');
    }
    
    public function wrapText($str, $length){
        $code = '@@@';
        return array_shift(explode($code, wordwrap($str, $length, $code)));
    }

}