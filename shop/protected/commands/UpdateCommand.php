<?php

class UpdateCommand extends CConsoleCommand {

    public function run($args) {
        Yii::app()->db->createCommand('truncate `listing2`;')->execute();
        $page = 1;
        do{
            $r = Yii::app()->amazon
                ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                ->category('Electronics')
                ->responseGroup('Medium,OfferFull')
                ->optionalParameters(array('ItemPage' => $page))
                ->search('', Yii::app()->params['node']);
            
            $page++;
            
            if(empty($r['Items']['Item']))
                break;
            
            foreach ($r['Items']['Item'] as $i){
                $priceRow = $this->getLastPrice($i['ASIN']);
                $pNew = empty($priceRow['PriceNew']) ? $this->getNewPrice($i) : $priceRow['PriceNew'];
                $deltaNew = $pNew - $this->getNewPrice($i);
                
                $data = array(
                    'ASIN' => $i['ASIN'],
                    'Attr' => serialize(array(
                        'Image' => $i['MediumImage']['URL'],
                        'Title' => $i['ItemAttributes']['Title'],
                        'Brand' => $i['ItemAttributes']['Brand'],
                    )),
                    'PriceNew' => $this->getNewPrice($i),
                    'PriceUsed' => $this->getUsedPrice($i),
                    'Delta' => $deltaNew,
                );
                
                Yii::app()->db->getCommandBuilder()->createInsertCommand('listing2', $data)->execute();
                //if price changed or no any price row exist
                if(!empty($deltaNew) || empty($priceRow)){
                   unset($data['Attr']); 
                   Yii::app()->db->getCommandBuilder()->createInsertCommand('price', $data)->execute();
                }   
            }
            
            if($page > 3) break;
            
        } while($r['Items']['TotalPages'] > $page);
    }
    
    protected function getLastPrice($ASIN)
    {
        $c = new CDbCriteria(array('order' => '`Date` desc','limit' => 1));
        $c->addColumnCondition(array('ASIN' => $ASIN));
        
        return Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryRow();
    }
    
    protected function getNewPrice($i){
        if(empty($i['OfferSummary']['LowestNewPrice']['Amount'])){
            if(!empty($i['Offers']['Offer']['OfferAttributes']['Condition']) && $i['Offers']['Offer']['OfferAttributes']['Condition'] == 'New')
                return $i['Offers']['Offer']['OfferListing']['Price']['Amount'];
        }else    
            return $i['OfferSummary']['LowestNewPrice']['Amount']; 
        
        return 0;
    }
    
    protected function getUsedPrice($i){
        if(empty($i['OfferSummary']['LowestUsedPrice']['Amount'])){
            if(!empty($i['Offers']['Offer']['OfferAttributes']['Condition']) && $i['Offers']['Offer']['OfferAttributes']['Condition'] == 'Used')
                return $i['Offers']['Offer']['OfferListing']['Price']['Amount'];
        }else    
            return $i['OfferSummary']['LowestUsedPrice']['Amount']; 
        
        return 0;
    }
    

}