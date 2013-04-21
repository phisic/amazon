<?php

class UpdateCommand extends CConsoleCommand {
    
    public function getHighPriceAsin($maxPrice){
        $asin = array();
        for($page=1; $page <= 10; $page++){
            $r = Yii::app()->amazon
                ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                ->category('Electronics')
                ->responseGroup('ItemIds')
                ->optionalParameters(array('ItemPage' => $page, 'Sort' => '-price','MaximumPrice' => $maxPrice+1,'Availability' => 'Available'))
                ->search('', Yii::app()->params['node']);
            if(!isset($r['Items']['Item']))
                return $asin;
            
            foreach ($r['Items']['Item'] as $i){
                if(is_array($i))
                    $asin[$page][] = $i['ASIN'];
                else
                    $asin[$page][] = $i;
            }
        }
        return $asin;
    }
    
    public function getLastLog(){
        $c = new CDbCriteria();
        $c->order = 'DateStart desc';
        $c->limit = 1;
        return Yii::app()->db->getCommandBuilder()->createFindCommand('price_log', $c)->queryRow();
    }
    
    public function getItemsByAsin($asin){
        return Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Offers,ItemAttributes')->lookup(join(',',$asin));
    }
    
    public function run($args) {
        $lowPrice = 6000;
        $highPrice = 1000000;
        $lastLog = $this->getLastLog();
        if(empty($lastLog))
            $maxPrice = $highPrice;
        elseif($lastLog['Price'] > $lowPrice)
            $maxPrice = $lastLog['Price'];
        else
           $maxPrice = $highPrice;
        
        Yii::app()->db->getCommandBuilder()->createInsertCommand('price_log', array(
            'Price' => $maxPrice,
            'ItemsRead'=>0,
            'DateStart' => date('Y-m-d H:i:s')
        ))->execute();
        $logId = Yii::app()->db->getCommandBuilder()->getLastInsertID('price_log');
        
        $c = new CDbCriteria();
        $c->compare('Id', $logId);
        
        $page = 1;
        $itemsRead = 0;
        do{
            echo 'MaxPrice = '.$maxPrice."\n";
            $asinList = $this->getHighPriceAsin($maxPrice);
            if(empty($asinList))
                break;
            foreach ($asinList as $page => $asins){
                $items = $this->getItemsByAsin($asins);
                echo '  Page='.$page.' Asin:'.join(',',$asins)."\n" ;
                if(count($asins)==1)
                    $items['Items']['Item'] = array('0'=>$items['Items']['Item']);
                
                foreach($items['Items']['Item'] as $i){
                    $priceRow = $this->getLastPrice($i['ASIN']);
                    if(empty($priceRow) && !empty($i['ItemAttributes']['ListPrice']['Amount'])){
                        Yii::app()->db->getCommandBuilder()->createInsertCommand('price', array(
                            'ASIN' => $i['ASIN'],
                            'PriceNew' => $i['ItemAttributes']['ListPrice']['Amount'],
                            'PriceUsed' => 0,
                            'Date' => time()-(24*3600),// 1 day old
                        ))->execute();
                        $priceRow['PriceNew']=$i['ItemAttributes']['ListPrice']['Amount'];
                    }
                    $data = array();
                    if($newPrice = Yii::app()->amazon->getNewPrice($i)){
                        $oldNew = empty($priceRow['PriceNew']) ? $newPrice : $priceRow['PriceNew'];
                        $deltaNew = $oldNew - $newPrice; 
                    }  else {
                        $deltaNew = 0;
                    }
                    $usedPrice = Yii::app()->amazon->getUsedPrice($i);;                    
                    //if price changed or no any price row exist
                    if(!empty($deltaNew) || empty($priceRow)){
                        $data['ASIN'] = $i['ASIN'];
                        $data['PriceNew'] = $newPrice;
                        $data['PriceUsed'] = $usedPrice;
                        $data['Delta'] = $deltaNew;
                        Yii::app()->db->getCommandBuilder()->createInsertCommand('price', $data)->execute();
                    }
                    echo $i["ASIN"].'='.$newPrice." max=".$maxPrice."\n";
                    $maxPrice2 = $newPrice ? $newPrice : $usedPrice;
                    if(empty($maxPrice2))
                        $maxPrice++;
                    else
                        $maxPrice = $maxPrice2;
                    $itemsRead++;
                }
                Yii::app()->db->getCommandBuilder()->createUpdateCommand('price_log', array('ItemsRead'=>$itemsRead), $c)->execute();
            }
            
        } while(true);
        
        Yii::app()->db->getCommandBuilder()->createUpdateCommand('price_log', array('ItemsRead'=>$itemsRead,'DateEnd'=>date('Y-m-d H:i:s')), $c)->execute();
    }
    
    protected function getLastPrice($ASIN)
    {
        $c = new CDbCriteria(array('order' => '`Date` desc','limit' => 1));
        $c->addColumnCondition(array('ASIN' => $ASIN));
        
        return Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryRow();
    }
}