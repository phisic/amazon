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
class SearchController extends Controller
{

	public function actionIndex()
	{
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
                ->search(Yii::app()->request->getParam('search',''), Yii::app()->params['node']);
       // print_r($r);exit;
        if(!empty($r['Items']['TotalResults']))
        {
            $pages=new CPagination($r['Items']['TotalResults']);
            $pages->pageSize = floor($r['Items']['TotalResults'] / $r['Items']['TotalPages']);
            $this->render('index', array('items' => $r['Items']['Item'],'pages' => $pages));
        }  else {
            $this->render('empty_list');
        }
        
		
	}
}