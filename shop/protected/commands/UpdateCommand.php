<?php

class UpdateCommand extends CConsoleCommand {
    
    public function getLowestPriceAsin($minPrice){
        $asin = array();
        for($page=1; $page <= 10; $page++){
            $r = Yii::app()->amazon
                ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                ->category('Electronics')
                ->responseGroup('ItemIds')
                ->optionalParameters(array('ItemPage' => $page, 'Sort' => 'price','MinimumPrice' => $minPrice+1,'Availability' => 'Available'))
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
    
    public function getItemsByAsin($asin){
        return Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Offers,ItemAttributes')->lookup(join(',',$asin));
    }
    
    public function run($args) {
        $t1= microtime(true);
        Yii::app()->db->createCommand('truncate `price`;')->execute();
        $page = 1;
        $minPrice = 5000;
        do{
            echo 'MinPrice = '.$minPrice."\n";
            $asinList = $this->getLowestPriceAsin($minPrice);
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
                    
                    $minPrice2 = empty($newPrice) ? $usedPrice : $newPrice;
                    if($minPrice < $minPrice2)
                        $minPrice = $minPrice2;
                }
            }
            
        } while(true);
        echo 'time ='.(microtime(true)-$t1);
    }
    
    protected function getLastPrice($ASIN)
    {
        $c = new CDbCriteria(array('order' => '`Date` desc','limit' => 1));
        $c->addColumnCondition(array('ASIN' => $ASIN));
        
        return Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryRow();
    }
}