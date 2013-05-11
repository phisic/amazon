<?php

/*
 * 'Request','ItemIds','Small','Medium','Large','Offers','OfferFull','OfferSummary','OfferListings','PromotionSummary','PromotionDetails','VariationMinimum','VariationSummary','VariationMatrix','VariationOffers','Variations','TagsSummary','Tags','ItemAttributes','MerchantItemAttributes','Tracks','Accessories','EditorialReview','SalesRank','BrowseNodes','Images','Similarities','Subjects','Reviews','ListmaniaLists','SearchInside','PromotionalTag','SearchBins','AlternateVersions','Collections','RelatedItems','ShippingCharges','ShippingOptions'
 * 'Tags', 'Help', 'ListMinimum', 'VariationSummary', 'VariationMatrix',
  'TransactionDetails', 'VariationMinimum', 'VariationImages',
  'PartBrandBinsSummary', 'CustomerFull', 'CartNewReleases',
  'ItemIds', 'SalesRank', 'TagsSummary', 'Fitments',
  'Subjects', 'Medium', 'ListmaniaLists',
  'PartBrowseNodeBinsSummary', 'TopSellers', 'Request',
  'HasPartCompatibility', 'PromotionDetails', 'ListFull',
  'Small', 'Seller', 'OfferFull', 'Accessories',
  'VehicleMakes', 'MerchantItemAttributes', 'TaggedItems',
  'VehicleParts', 'BrowseNodeInfo', 'ItemAttributes',
  'PromotionalTag', 'VehicleOptions', 'ListItems', 'Offers',
  'TaggedGuides', 'NewReleases', 'VehiclePartFit',
  'OfferSummary', 'VariationOffers', 'CartSimilarities',
  'Reviews', 'ShippingCharges', 'ShippingOptions', 'EditorialReview',
  'CustomerInfo', 'PromotionSummary', 'BrowseNodes',
  'PartnerTransactionDetails', 'VehicleYears', 'SearchBins',
  'VehicleTrims', 'Similarities', 'AlternateVersions',
  'SearchInside', 'CustomerReviews', 'SellerListing',
  'OfferListings', 'Cart', 'TaggedListmaniaLists',
  'VehicleModels', 'ListInfo', 'Large', 'CustomerLists',
  'Tracks', 'CartTopSellers', 'Images', 'Variations',
  'RelatedItems','Collections'
 */

class SearchController extends Controller {
    
    public function actionIndex() {
        $r = Yii::app()->amazon
                ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                ->category('Electronics')
                ->responseGroup('Medium')
                ->optionalParameters(array('ItemPage' => Yii::app()->request->getParam('page', 1)))
                ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);

        if (!empty($r['Items']['TotalResults'])) {
            if ($r['Items']['TotalPages'] > 10)
                $r['Items']['TotalPages'] = 10;
            $pages = new CPagination($r['Items']['TotalResults']);
            $pages->pageSize = floor($r['Items']['TotalResults'] / $r['Items']['TotalPages']);
            $this->render('index', array('title' => 'Search result', 'items' => $r['Items']['Item'], 'pages' => $pages));
        } else {
            $this->render('empty_list', array('keyword'=>Yii::app()->request->getParam('search', '')));
        }
    }

    public function actionDetail($asin) {
        $cs = Yii::app()->clientScript;
        $tp = Yii::app()->getTheme()->getBaseUrl();
        $cs->registerScript('excanvas', '<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->', CClientScript::POS_END);
        $cs->registerScriptFile($tp . '/js/plot/jquery.jqplot.min.js', CClientScript::POS_END);
        $cs->registerScriptFile($tp . '/js/plot/plugins/jqplot.highlighter.min.js', CClientScript::POS_END);
        $cs->registerScriptFile($tp . '/js/plot/plugins/jqplot.canvasTextRenderer.min.js', CClientScript::POS_END);
        $cs->registerScriptFile($tp . '/js/plot/plugins/jqplot.canvasAxisLabelRenderer.min.js', CClientScript::POS_END);
        $cs->registerScriptFile($tp . '/js/plot/plugins/jqplot.dateAxisRenderer.min.js', CClientScript::POS_END);

        $cs->registerCssFile($tp . '/js/plot/jquery.jqplot.min.css');
        $cs->registerCssFile($tp . '/css/details.css');

        $c = new CDbCriteria();
        $c->compare('ASIN', $asin);
        $history = Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryAll();

        if (!($r = Yii::app()->cache->get($asin))) {
            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Large')->lookup($asin);
            Yii::app()->cache->add($asin, $r);
        }
        $description = array();
        if (isset($r['Items']['Item']['EditorialReviews']['EditorialReview']['Content'])) {
            $description[] = preg_replace('/<a name="([0-9a-zA-Z]+)">/', '<a name="$1"></a>', $r['Items']['Item']['EditorialReviews']['EditorialReview']['Content']);
        } else {
            foreach ($r['Items']['Item']['EditorialReviews']['EditorialReview'] as $i) {
                $description[] = preg_replace(array(
                    '@<a name="([0-9a-zA-Z]+)">@',
                    '@<style[^>]*?>.*?</style>@siU',
                    '@<head[^>]*?>.*?</head>@siU',
                    '<html>', '</html>', '<body>', '</body>'), array('<a name="$1"></a>', '', '', '', '', '', ''), $i['Content']);
            }
        }
        $r['Items']['Item']['EditorialReviews']['EditorialReview'] = $description;
        $this->render('detail', array('i' => $r['Items']['Item'], 'history' => $history));
    }

    public function actionBestsellers() {
        $r = Yii::app()->amazon
                ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                ->category('Electronics')
                ->responseGroup('Medium')
                ->optionalParameters(array('Sort' => 'salesrank', 'ItemPage' => Yii::app()->request->getParam('page', 1)))
                ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);

        if (!empty($r['Items']['TotalResults'])) {
            if ($r['Items']['TotalPages'] > 10)
                $r['Items']['TotalPages'] = 10;
            $pages = new CPagination($r['Items']['TotalResults']);
            $pages->pageSize = ceil($r['Items']['TotalResults'] / $r['Items']['TotalPages']);
            $this->render('index', array('title' => 'Best Sellers', 'items' => $r['Items']['Item'], 'pages' => $pages));
        } else {
            $this->render('empty_list', array('keyword'=>'Bestsellers'));
        }
    }

    public function actionTopPriceDrops() {
        $page = abs(Yii::app()->request->getParam('page', 1));
        $c = new CDbCriteria(array(
            'select' => 'ASIN, sum(delta) as price_drop',
            'order' => 'price_drop desc',
            'group' => 'ASIN',
        ));
        $c->addCondition('`Date` > (now() - Interval 1 DAY)');
        $count = Yii::app()->db->getCommandBuilder()->createCountCommand('price', $c)->queryScalar();
        $size = 10;
        $c->limit = $size;
        $c->offset = $size*($page-1);
        
        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryAll();
        $asins = array();
        foreach ($rows as $row){
            $asins[$row['ASIN']] = $row['price_drop'];
        }
        
        $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Medium')->lookup(join(',', array_keys($asins)));

        $pages = new CPagination($count);
        $pages->pageSize = $size;
        
        $this->render('index', array('title' => 'Top Price Drops', 'items' => $r['Items']['Item'], 'pages' => $pages, 'priceDrops'=>$asins));
    }
    
    public function actionTopReviewed() {
        $r = Yii::app()->amazon
                ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                ->category('Electronics')
                ->responseGroup('Medium')
                ->optionalParameters(array('Sort' => 'reviewrank', 'ItemPage' => Yii::app()->request->getParam('page', 1)))
                ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
        if (!empty($r['Items']['TotalResults'])) {
            if ($r['Items']['TotalPages'] > 10)
                $r['Items']['TotalPages'] = 10;
            $pages = new CPagination($r['Items']['TotalResults']);
            $pages->pageSize = ceil($r['Items']['TotalResults'] / $r['Items']['TotalPages']);
            $this->render('index', array('title' => 'Top Reviewed', 'items' => $r['Items']['Item'], 'pages' => $pages));
        } else {
            $this->render('empty_list', array('keyword'=>'Bestsellers'));
        }
    }
    
    public function actionNewReleases(){
        $this->render('index', array('title' => 'New Releases', 'items' => Yii::app()->stat->getNewReleases()));
    }
}