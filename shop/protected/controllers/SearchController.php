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

    public function ajaxsearch() {
        header('Content-Type: application/json');
        $keyword = Yii::app()->request->getParam('search', '');
        $keywords = explode(' ', $keyword);
        $params = array(':k'=>$keyword);
        foreach ($keywords as $k => $word) {
            if(empty($word))
                continue;
            $where[] = 'MATCH (Title) AGAINST (:w' . $k . ' IN BOOLEAN MODE)';
            $params[':w' . $k] = $word . '*';
        }
        
        $where = $where ? ' WHERE ' . join(' AND ', $where) : '';
        $s1 = 'SELECT Title FROM listing WHERE MATCH (Title) AGAINST (:k) LIMIT 10';
        $s2 = 'SELECT Title FROM listing ' . $where . ' LIMIT 10';
        $sql = 'SELECT distinct * from (('.$s1.') UNION ('.$s2.')) s';
        $r = Yii::app()->db->getCommandBuilder()->createSqlCommand($sql, $params)->queryAll();

        $data = array();
        if (!empty($r)) {
            foreach ($r as $i) {
                $data[] = $i['Title'];
            }
            echo CJSON::encode($data);
        } else {
            echo '[]';
        }

        Yii::app()->end();
    }

    public function actionIndex() {
        if (Yii::app()->request->getIsAjaxRequest()) {
            $this->ajaxsearch();
        }

        $this->pageTitle = 'Search laptop ' . Yii::app()->request->getParam('search', '');

        $r = Yii::app()->amazon
                ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                ->category('Electronics')
                ->responseGroup('Medium')
                ->optionalParameters(array('ItemPage' => Yii::app()->request->getParam('page', 1)))
                ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
        if (isset($r['Items']['Item']['ASIN']))
            $r['Items']['Item'] = array(0 => $r['Items']['Item']);

        if (!empty($r['Items']['TotalResults'])) {
            if ($r['Items']['TotalPages'] > 10)
                $r['Items']['TotalPages'] = 10;
            $pages = new CPagination($r['Items']['TotalResults']);
            $pages->pageSize = ceil($r['Items']['TotalResults'] / $r['Items']['TotalPages']);
            $this->render('index', array('title' => 'Search result', 'items' => $r['Items']['Item'], 'pages' => $pages));
        } else {
            $this->render('empty_list', array('keyword' => Yii::app()->request->getParam('search', '')));
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
        $c->addColumnCondition(array('ASIN' => $asin));
        $c->addCondition('(PriceNew > 0 or PriceUsed > 0)');

        $history = Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryAll();
        $row = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', new CDbCriteria(array(
                    'select' => 'Data',
                    'condition' => 'ASIN=:a',
                    'params' => array(':a' => $asin)
                )))->queryRow();

        if (empty($row['Data'])) {
            if (!($r = Yii::app()->cache->get($asin))) {
                $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Large')->lookup($asin);
                Yii::app()->cache->add($asin, $r, 1800);
            }
        } else {
            $r['Items']['Item'] = unserialize($row['Data']);
        }

        $this->pageTitle = $r['Items']['Item']['ItemAttributes']['Title'];

        $description = array();
        if (isset($r['Items']['Item']['EditorialReviews']['EditorialReview']['Content'])) {
            $description[] = preg_replace('/<a name="([0-9a-zA-Z]+)">/', '<a name="$1"></a>', $r['Items']['Item']['EditorialReviews']['EditorialReview']['Content']);
        } elseif (isset($r['Items']['Item']['EditorialReviews']['EditorialReview'])) {
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
        $this->pageTitle = 'Bestseller laptops';
        $page = Yii::app()->request->getParam('page', 1);
        if (!$r = Yii::app()->cache->get('best-' . $page)) {
            $r = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('Medium')
                    ->optionalParameters(array('Sort' => 'salesrank', 'ItemPage' => $page))
                    ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
            Yii::app()->cache->set('best-' . $page, $r, 1800);
        }
        if (!empty($r['Items']['TotalResults'])) {
            if ($r['Items']['TotalPages'] > 10)
                $r['Items']['TotalPages'] = 10;
            $pages = new CPagination($r['Items']['TotalResults']);
            $pages->pageSize = ceil($r['Items']['TotalResults'] / $r['Items']['TotalPages']);
            $this->render('index', array('title' => 'Best Sellers', 'items' => $r['Items']['Item'], 'pages' => $pages));
        } else {
            $this->render('empty_list', array('keyword' => 'Bestsellers'));
        }
    }

    public function actionTopPriceDrops() {
        $this->pageTitle = 'Top price drop laptops';
        $page = abs(Yii::app()->request->getParam('page', 1));
        $size = 10;
        if (!$r = Yii::app()->cache->get('pdrop-' . $page)) {
            $c = new CDbCriteria(array(
                'select' => 'ASIN, sum(delta) as price_drop',
                'order' => 'price_drop desc',
                'group' => 'ASIN',
            ));
            $c->addCondition('`Date` > (now() - Interval 1 DAY) and delta > 0');
            $count = Yii::app()->db->getCommandBuilder()->createCountCommand('price', $c)->queryScalar();

            $c->limit = $size;
            $c->offset = $size * ($page - 1);

            $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('price', $c)->queryAll();
            $asins = array();
            foreach ($rows as $row) {
                $asins[$row['ASIN']] = $row['price_drop'];
            }

            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Medium')->lookup(join(',', array_keys($asins)));
            $r['asins'] = $asins;
            $r['count'] = $count;
            Yii::app()->cache->set('pdrop-' . $page, $r, 1800);
        }
        $pages = new CPagination($r['count']);
        $pages->pageSize = $size;

        $this->render('index', array('title' => 'Top Price Drops', 'items' => isset($r['Items']['Item']) ? $r['Items']['Item'] : array(), 'pages' => $pages, 'priceDrops' => $r['asins']));
    }

    public function actionTopReviewed() {
        $this->pageTitle = 'Top reviewed laptops';

        $page = abs(Yii::app()->request->getParam('page', 1));
        if (!$r = Yii::app()->cache->get('toprev-' . $page)) {
            $r = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('Medium')
                    ->optionalParameters(array('Sort' => 'reviewrank', 'ItemPage' => $page))
                    ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
            Yii::app()->cache->set('toprev-' . $page, $r, 1800);
        }
        if (!empty($r['Items']['TotalResults'])) {
            if ($r['Items']['TotalPages'] > 10)
                $r['Items']['TotalPages'] = 10;
            $pages = new CPagination($r['Items']['TotalResults']);
            $pages->pageSize = ceil($r['Items']['TotalResults'] / $r['Items']['TotalPages']);
            $this->render('index', array('title' => 'Top Reviewed', 'items' => $r['Items']['Item'], 'pages' => $pages));
        } else {
            $this->render('empty_list', array('keyword' => 'Top Reviewed'));
        }
    }

    public function actionNewReleases() {
        $this->pageTitle = 'New released laptops';
        $this->render('index', array('title' => 'New Releases', 'items' => Yii::app()->stat->getNewReleases()));
    }

    public function actionAll() {
        $this->pageTitle = 'All laptops';
        $page = abs(Yii::app()->request->getParam('page', 1));
        $size = 10;
        $c = new CDbCriteria(array(
            'order' => 'SalesRank',
            'distinct' => true,
        ));

        $count = Yii::app()->db->getCommandBuilder()->createCountCommand('listing', $c)->queryScalar();

        $c->limit = $size;
        $c->offset = $size * ($page - 1);

        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
        $list = array();
        foreach ($rows as $row) {
            $list[] = unserialize($row['Data']);
        }
        $pages = new CPagination($count);
        $pages->pageSize = $size;

        $this->render('index', array('title' => 'All laptops', 'items' => $list, 'pages' => $pages));
    }

}