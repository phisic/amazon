<?php

class WatchCommand extends CConsoleCommand {

    public function run($args) {
        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('watch', new CDbCriteria(
                        array('select' => 'Id,ASIN,NewUsed,Email,UserId,FirstName,Price')
                ))->queryAll();

        if (empty($rows))
            return;

        $asins = array();

        foreach ($rows as $row) {
            $asins[$row['ASIN']][$row['NewUsed']] = $row;
        }

        $priceDrops = array();
        $asinList = array_keys($asins);
        $asinList = array_chunk($asinList, 10);

        foreach ($asinList as $asin10) {
            $prices = $this->getPriceByAsin($asin10);

            foreach ($prices as $p) {
                foreach ($asins[$p['ASIN']] as $newUsed => $row) {
                    $oldPrice = $row['Price'];
                    if ($newUsed == 'new')
                        $newPrice = Yii::app()->amazon->getNewPrice($p);
                    else
                        $newPrice = Yii::app()->amazon->getUsedPrice($p);

                    $priceDrop = $oldPrice - $newPrice;
                    $row['PriceDrop'] = $priceDrop;
                    if ($priceDrop >= 100) {
                        $row['DetailPageURL'] = $p['DetailPageURL'];
                        $row['Title'] = $p['ItemAttributes']['Title'];
                        $priceDrops[] = $row;
                    }
                }
            }

            $this->sendMail($priceDrops);
        }
    }

    public function getPriceByAsin($asin) {
        $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Offers,ItemAttributes')->lookup(join(',', $asin));
        if (isset($r['Items']['Item'])) {
            if (!isset($r['Items']['Item'][0]))
                $r['Items']['Item'] = array(0 => $r['Items']['Item']);

            return $r['Items']['Item'];
        }
        else
            return array();
    }

    public function sendMail($priceDrops) {
        
        $template = file_get_contents(Yii::app()->basePath.'/commands/shell/pricedropNotification.html');

        foreach ($priceDrops as $d) {
            $c = new CDbCriteria();
            $c->addColumnCondition(array('id'=>$d['Id']));
            Yii::app()->db->getCommandBuilder()->createUpdateCommand('watch', array('Price'=>$d['Price']+$d['PriceDrop'],'PriceDate'=>date('Y-m-d H:i:s')), $c)->execute();
            if(empty($d['Email']))
                continue;
            
            $pricedDrop = Yii::app()->amazon->formatUSD($d['PriceDrop']);
            $oldPrice = Yii::app()->amazon->formatUSD($d['Price']);
            $newPrice = Yii::app()->amazon->formatUSD($d['Price'] - $d['PriceDrop']);
            $removeUrl = 'http://laptoptop7.com/watch/remove/' . $d['ASIN'] . '-' . $d['NewUsed'] . '-' . Yii::app()->stat->getHash($d['ASIN'], $d['NewUsed'], $d['Id']) . '-' . $d['Id'];

            $message = strtr($template, array(
                '{$priceDrop}' => $pricedDrop,
                '{$link}' => 'http://laptoptop7.com/search/detail/' . $d['ASIN'],
                '{$laptopTitle}' => $d['Title'],
                '{$oldPrice}' => $oldPrice,
                '{$newPrice}' => $newPrice,
                '{$amazonLink}' => $d['DetailPageURL'],
                '{$removeLink}' => $removeUrl,
            ));
            
            UserModule::sendMail($d['Email'], 'Price drop notification from laptoptop7.com', $message);
        }
    }

}