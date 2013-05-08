<?php

class WatchCommand extends CConsoleCommand {

    public function run($args) {
        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('watch', new CDbCriteria(
                        array('select' => 'ASIN,NewUsed,Email,UserId,FirstName,Price')
                ))->queryAll();
        
        if(empty($rows))
            return;
        
        $asins = array();
        
        foreach ($rows as $row){
            $asins[$row['ASIN']][$row['NewUsed']] = $row;
        }
        
        $priceDrops = array();
        $asinList = array_keys($asins);
        $asinList = array_chunk($asinList, 10);
        
        foreach ($asinList as $asin10){
            $prices = $this->getPriceByAsin($asin10);
            
            foreach ($prices as $p){
                foreach ($asins[$p['ASIN']] as $newUsed=>$row){
                    $oldPrice = $row['Price'];
                    if($newUsed=='new')
                        $newPrice = Yii::app()->amazon->getNewPrice($p);
                    else
                        $newPrice = Yii::app()->amazon->getUsedPrice($p);
                   
                    $priceDrop = $oldPrice - $newPrice;
                    $row['PriceDrop'] = $priceDrop;
                    if($priceDrop >= 100){
                        $priceDrops[] = $row;
                    }    
                }
            }
            
            print_r($priceDrops);
        }
    }
    
    public function getPriceByAsin($asin) {
        $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Offers,ItemIds')->lookup(join(',', $asin));
        if(isset($r['Items']['Item'])){
            if(!isset($r['Items']['Item'][0]))
                $r['Items']['Item'] = array(0 => $r['Items']['Item']);
            
            return $r['Items']['Item'];
        }
        else
            return array();
    }


}