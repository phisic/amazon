<?php

class UpdateCommand extends CConsoleCommand {

    public function getHighPriceAsin($maxPrice, $minPrice) {
        $asin = array();
                
        for ($page = 1; $page <= 10; $page++) {
            $r = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('ItemIds')
                    ->optionalParameters(array('ItemPage' => $page, 'MaximumPrice' => $maxPrice, 'MinimumPrice' => $minPrice, 'Availability' => 'Available'))
                    ->search('', Yii::app()->params['node']);
            if (!isset($r['Items']['Item']))
                return $asin;

            foreach ($r['Items']['Item'] as $i) {
                if (is_array($i))
                    $asin[$page][] = $i['ASIN'];
                else
                    $asin[$page][] = $i;
            }
            if(count($r['Items']['Item'])<10)
                return $asin;
        }
        return $asin;
    }

    public function getLastLog() {
        $c = new CDbCriteria();
        $c->order = 'DateStart desc';
        $c->limit = 1;
        return Yii::app()->db->getCommandBuilder()->createFindCommand('price_log', $c)->queryRow();
    }

    public function getItemsByAsin($asin) {
        return Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Offers,ItemAttributes')->lookup(join(',', $asin));
    }

    public function run($args) {
        $lowPrice = 6000;
        $highPrice = 1000000;
        $lastLog = $this->getLastLog();
        if (empty($lastLog))
            $maxPrice = $highPrice;
        elseif (empty($lastLog['DateEnd']))
            $maxPrice = $lastLog['Price'];
        else
            $maxPrice = $highPrice;
        Yii::app()->db->getCommandBuilder()->createInsertCommand('price_log', array(
            'Price' => $maxPrice,
            'ItemsRead' => 0,
            'DateStart' => date('Y-m-d H:i:s')
        ))->execute();
        $logId = Yii::app()->db->getCommandBuilder()->getLastInsertID('price_log');

        $c = new CDbCriteria();
        $c->compare('Id', $logId);

        $page = 1;
        $itemsRead = 0;
        
        do {
            $startPrice = $maxPrice;
            
            $minPrice = Yii::app()->db->createCommand('select min(pricenew) as minprice from (select pricenew from price where pricenew < '.$maxPrice.' group by ASIN order by pricenew desc limit 90) s;')->queryScalar();
            if(empty($minPrice))
                $minPrice = $maxPrice - 1000;
            echo 'MaxPrice = ' . $maxPrice . " MinPrice = ".$minPrice. " delta = ".($maxPrice-$minPrice)."\n";
            $delta = $maxPrice - $minPrice;

            $asinList = $this->getHighPriceAsin($maxPrice, $minPrice);
            //jump empty price ranges
            if (empty($asinList)) {
                $maxPrice -= ceil($maxPrice / 10);
                continue;
            }

            if (($maxPrice <= $lowPrice))
                break;
            foreach ($asinList as $page => $asins) {
                $items = $this->getItemsByAsin($asins);
                echo '  Page=' . $page . ' Asin:' . join(',', $asins) . "\n";
                if (count($asins) == 1)
                    $items['Items']['Item'] = array('0' => $items['Items']['Item']);

                foreach ($items['Items']['Item'] as $i) {
                    $priceRow = $this->getLastPrice($i['ASIN']);
                    if (empty($priceRow) && !empty($i['ItemAttributes']['ListPrice']['Amount'])) {
                        Yii::app()->db->getCommandBuilder()->createInsertCommand('price', array(
                            'ASIN' => $i['ASIN'],
                            'PriceNew' => $i['ItemAttributes']['ListPrice']['Amount'],
                            'PriceUsed' => 0,
                            'Date' => date('Y-m-d H:i:s', time() - (24 * 3600)), // 1 day old
                        ))->execute();
                        $priceRow['PriceNew'] = $i['ItemAttributes']['ListPrice']['Amount'];
                    }
                    $data = array();
                    if ($newPrice = Yii::app()->amazon->getNewPrice($i)) {
                        $oldNew = empty($priceRow['PriceNew']) ? $newPrice : $priceRow['PriceNew'];
                        $deltaNew = $oldNew - $newPrice;
                    } else {
                        $deltaNew = 0;
                    }
                    $usedPrice = Yii::app()->amazon->getUsedPrice($i);
                    ;
                    //if price changed or no any price row exist
                    if (!empty($deltaNew) || empty($priceRow)) {
                        $data['ASIN'] = $i['ASIN'];
                        $data['PriceNew'] = $newPrice;
                        $data['PriceUsed'] = $usedPrice;
                        $data['Delta'] = $deltaNew;
                        Yii::app()->db->getCommandBuilder()->createInsertCommand('price', $data)->execute();
                    }

                    $maxPrice2 = $newPrice ? $newPrice : $usedPrice;
                    if (!empty($maxPrice2) && ($maxPrice2 < $maxPrice) && (($startPrice - $maxPrice2) <= $delta))
                        $maxPrice = $maxPrice2;

                    echo $i["ASIN"] . '=' . $newPrice . " max=" . $maxPrice . "\n";
                    $itemsRead++;
                }
                Yii::app()->db->getCommandBuilder()->createUpdateCommand('price_log', array('ItemsRead' => $itemsRead, 'Price' => $maxPrice), $c)->execute();
                //prevent infinite loop
                if ($maxPrice == $startPrice)
                    $maxPrice -= 1;
            }
        } while (true);

        Yii::app()->db->getCommandBuilder()->createUpdateCommand('price_log', array('DateEnd' => date('Y-m-d H:i:s')), $c)->execute();
    }

    protected function getLastPrice($ASIN) {
        $c = new CDbCriteria(array('order' => '`Date` desc', 'limit' => 1));
        $c->addColumnCondition(array('ASIN' => $ASIN));

        return Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryRow();
    }

}