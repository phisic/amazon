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
        //ASIN, PriceNew, PriceUsed, Date, Delta - price table
        //ASIN, Title, PriceNew, PriceUsed,Image, Attributes, Delta - details table 
        //$r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->optionalParameters(array('ItemPage' => 2))->responseGroup('NewReleases')->browseNodeLookup(565108);
        //$r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Offers,ItemAttributes,SalesRank')->lookup('B0074703CM,B005CWJB5G');
        //print_r($r);exit;
        $r = Yii::app()->amazon
                ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                ->category('Electronics')
                ->responseGroup('Medium')
                ->optionalParameters(array('ItemPage' => Yii::app()->request->getParam('page', 1)))
                ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
         print_r($r);exit;
        if (!empty($r['Items']['TotalResults'])) {
            $pages = new CPagination($r['Items']['TotalResults']);
            $pages->pageSize = floor($r['Items']['TotalResults'] / $r['Items']['TotalPages']);
            $this->render('index', array('items' => $r['Items']['Item'], 'pages' => $pages));
        } else {
            $this->render('empty_list');
        }
    }

    public function actionDetail($id) {
        $c = Yii::app()->clientScript;
        $tp = Yii::app()->getTheme()->getBaseUrl();
        $c->registerScript('excanvas', '<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->', CClientScript::POS_HEAD);
        $c->registerScriptFile($tp . '/js/plot/jquery.jqplot.min.js', CClientScript::POS_HEAD);
        $c->registerScriptFile($tp . '/js/plot/plugins/jqplot.highlighter.min.js', CClientScript::POS_HEAD);
        $c->registerScriptFile($tp . '/js/plot/plugins/jqplot.canvasTextRenderer.min.js', CClientScript::POS_HEAD);
        $c->registerScriptFile($tp . '/js/plot/plugins/jqplot.canvasAxisLabelRenderer.min.js', CClientScript::POS_HEAD);
        $c->registerScriptFile($tp . '/js/plot/plugins/jqplot.dateAxisRenderer.min.js', CClientScript::POS_HEAD);
        
        $c->registerCssFile($tp . '/js/plot/jquery.jqplot.min.css');
        
        if (!($r = Yii::app()->cache->get($id))) {
            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Large')->lookup($id);
            Yii::app()->cache->add($id, $r);
        }

        $description = array();
        if (isset($r['Items']['Item']['EditorialReviews']['EditorialReview']['Content'])) {
            $description[] = preg_replace('/<a name="([0-9a-zA-Z]+)">/', '<a name="$1"></a>', $r['Items']['Item']['EditorialReviews']['EditorialReview']['Content']);
        } else {
            foreach ($r['Items']['Item']['EditorialReviews']['EditorialReview'] as $i) {
                $description[] = preg_replace('/<a name="([0-9a-zA-Z]+)">/', '<a name="$1"></a>', $i['Content']);
            }
        }
        $r['Items']['Item']['EditorialReviews']['EditorialReview'] = $description;
        $this->render('detail', array('i' => $r['Items']['Item']));
    }

}