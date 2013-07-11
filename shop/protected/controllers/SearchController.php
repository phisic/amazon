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
        $params = array(':k' => $keyword);
        foreach ($keywords as $k => $word) {
            if (empty($word))
                continue;
            $where[] = 'MATCH (Title) AGAINST (:w' . $k . ' IN BOOLEAN MODE)';
            $params[':w' . $k] = $word . '*';
        }

        $where = $where ? ' WHERE ' . join(' AND ', $where) : '';
        $s1 = 'SELECT Title FROM listing WHERE MATCH (Title) AGAINST (:k) LIMIT 10';
        $s2 = 'SELECT Title FROM listing ' . $where . ' LIMIT 10';
        $sql = 'SELECT distinct * from ((' . $s1 . ') UNION (' . $s2 . ')) s';
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

        $this->pageTitle = 'Search ' . Yii::app()->params['category'] . ' ' . Yii::app()->request->getParam('search', '');

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

    public function actionDetail($asin, $txt = false) {
        if (empty($txt)) {
            $row = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', new CDbCriteria(array(
                        'select' => 'Title',
                        'condition' => 'ASIN=:a',
                        'params' => array(':a' => $asin)
                    )))->queryRow();
            if (!empty($row)) {
                if (empty($row['Title'])) {
                    $row = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', new CDbCriteria(array(
                                'select' => 'Data',
                                'condition' => 'ASIN=:a',
                                'params' => array(':a' => $asin)
                            )))->queryRow();
                    $r = unserialize($row['Data']);
                    if(isset($r['Items']['Item']))
                        $row['Title'] = $r['Items']['Item']['ItemAttributes']['Title'];
                    else
                        $row['Title'] = $r['ItemAttributes']['Title'];
                }

                $this->redirect(Yii::app()->createSeoUrl('search/detail/' . $asin, $row['Title']));
            } else {
                throw new CHttpException(404);
            }
        }
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
        $cs->registerCssFile($tp . '/css/detail_image.css');
        $cs->registerScriptFile($tp . '/js/detail_image.js', CClientScript::POS_END);

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
            $c = new CDbCriteria();
            $c->order = 'DateStart desc';
            $c->limit = 1;
            $c->select = 'Id';
            $log = Yii::app()->db->getCommandBuilder()->createFindCommand('price_log', $c)->queryRow();
            $r = Yii::app()->amazon->returnType(AmazonECS::RETURN_TYPE_ARRAY)->responseGroup('Large')->lookup($asin);
            if (isset($r['Items']['Item'])) {
                $r = $r['Items']['Item'];
                $data = array(
                    'LogId' => $log['Id'],
                    'Data' => $this->serializeItem($r),
                    'SalesRank' => isset($r['SalesRank']) ? $r['SalesRank'] : 1E6,
                    'Title' => isset($r['ItemAttributes']['Title']) ? $r['ItemAttributes']['Title'] : '',
                    'Brand' => isset($r['ItemAttributes']['Brand']) ? $r['ItemAttributes']['Brand'] : '',
                    'Model' => isset($r['ItemAttributes']['Model']) ? $r['ItemAttributes']['Model'] : '',
                    'ASIN' => $r['ASIN'],
                    'SubItem' => 1,
                );

                Yii::app()->db->getCommandBuilder()->createInsertCommand('listing', $data)->execute();
                $r['Items']['Item'] = $r;
            }
        } else {
            $r = unserialize($row['Data']);
            if (!isset($r['Items']['Item']))
                $r['Items']['Item'] = $r;
        }
        $c = new CDbCriteria();
        $c->select = 'QId';
        $c->addColumnCondition(array('ASIN' => $asin));
        $c->limit = 100;
        $qid = Yii::app()->db->getCommandBuilder()->createFindCommand('listing2question', $c)->queryColumn();
        if (empty($qid))
            $questions = array();
        else {
            $c = new CDbCriteria();
            $c->select = 'Id,Title';
            $c->addInCondition('QId', $qid);
            $c->order = 'Id';
            $questions = Yii::app()->db->getCommandBuilder()->createFindCommand('question', $c)->queryAll();
        }
        $this->pageTitle = $r['Items']['Item']['ItemAttributes']['Title'];


        $r['Items']['Item']['EditorialReviews']['EditorialReview'] = $this->getDescription($r);
        $this->render('detail', array('i' => $r['Items']['Item'], 'history' => $history, 'parts' => Yii::app()->part->getByAsins(array($asin)), 'questions' => $questions));
    }

    protected function serializeItem($item) {
        if (isset($item['Items']['Item']['EditorialReviews']['EditorialReview']['Content'])) {
            $item['Items']['Item']['EditorialReviews']['EditorialReview']['Content'] = htmlspecialchars($item['Items']['Item']['EditorialReviews']['EditorialReview']['Content']);
        } elseif (isset($r['Items']['Item']['EditorialReviews']['EditorialReview'])) {
            foreach ($r['Items']['Item']['EditorialReviews']['EditorialReview'] as &$i) {
                $i['Content'] = htmlspecialchars($i['Content']);
            }
        }

        if (isset($item['ItemAttributes']['Feature']) && is_array($item['ItemAttributes']['Feature']))
            foreach ($item['ItemAttributes']['Feature'] as &$attr) {
                $attr = htmlspecialchars($attr);
            }

        return serialize($item);
    }

    protected function getDescription($r) {
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
        return $description;
    }

    public function actionDescription() {
        if (!Yii::app()->request->getIsAjaxRequest())
            return;

        $asin = Yii::app()->request->getParam('asin', '');
        if ($asin) {
            $row = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', new CDbCriteria(array(
                        'select' => 'Data',
                        'condition' => 'ASIN=:a',
                        'params' => array(':a' => $asin)
                    )))->queryRow();
            if ($row) {
                $i['Items']['Item'] = unserialize($row['Data']);

                $description = $this->getDescription($i);
                foreach ($description as $d) {
                    echo htmlspecialchars_decode($d);
                }
            }
        }

        Yii::app()->end();
    }

    public function actionBestsellers() {
        $this->pageTitle = 'Bestseller ' . Yii::app()->params['category'] . 's';
        $page = Yii::app()->request->getParam('page', 1);
        if (!$r = Yii::app()->cache->get('best-' . $page)) {
            $r = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('Medium')
                    ->optionalParameters(array('Sort' => 'salesrank', 'ItemPage' => $page))
                    ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
            Yii::app()->cache->set('best-' . $page, $r, 3600 * 4);
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
        $this->pageTitle = 'Top price drop ' . Yii::app()->params['category'] . 's';
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
            Yii::app()->cache->set('pdrop-' . $page, $r, 3600 * 4);
        }
        $pages = new CPagination($r['count']);
        $pages->pageSize = $size;

        $this->render('index', array('title' => 'Top Price Drops', 'items' => isset($r['Items']['Item']) ? $r['Items']['Item'] : array(), 'pages' => $pages, 'priceDrops' => $r['asins']));
    }

    public function actionTopPowerful() {
        $this->pageTitle = 'Top powerful laptops';
        $page = abs(Yii::app()->request->getParam('page', 1));
        $size = 10;
        $c = new CDbCriteria(array(
            'join' => 'JOIN part p ON p.Type="vga" and p.Id=t.VGA',
            'select' => 'ASIN'
        ));
        $c->compare('CPU', '> 0');
        $count = Yii::app()->db->getCommandBuilder()->createCountCommand('listing', $c)->queryScalar();
        $c->order = 'p.Score Desc';
        $c->limit = $size;
        $c->offset = $size * ($page - 1);
        $c->select = '*';

        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
        $list = array();
        foreach ($rows as $row) {
            $list[] = unserialize($row['Data']);
        }

        $pages = new CPagination($count);
        $pages->pageSize = $size;
        $this->render('index', array('title' => 'Top powerful gaming laptops', 'items' => $list, 'pages' => $pages));
    }

    public function actionTopReviewed() {
        $this->pageTitle = 'Top reviewed ' . Yii::app()->params['category'] . 's';

        $page = abs(Yii::app()->request->getParam('page', 1));
        if (!$r = Yii::app()->cache->get('toprev-' . $page)) {
            $r = Yii::app()->amazon
                    ->returnType(AmazonECS::RETURN_TYPE_ARRAY)
                    ->category('Electronics')
                    ->responseGroup('Medium')
                    ->optionalParameters(array('Sort' => 'reviewrank', 'ItemPage' => $page))
                    ->search(Yii::app()->request->getParam('search', ''), Yii::app()->params['node']);
            Yii::app()->cache->set('toprev-' . $page, $r, 3600 * 4);
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
        $this->pageTitle = 'New released ' . Yii::app()->params['category'] . 's';
        $this->render('index', array('title' => 'New Releases', 'items' => Yii::app()->stat->getNewReleases()));
    }

    public function actionAll() {
        $this->pageTitle = 'All ' . Yii::app()->params['category'] . 's';
        $page = abs(Yii::app()->request->getParam('page', 1));
        $size = 10;
        $c = new CDbCriteria(array(
            'order' => 'SalesRank',
            'select' => 'ASIN'
        ));
        $c->addColumnCondition(array('SubItem' => 0));
        $count = Yii::app()->db->getCommandBuilder()->createCountCommand('listing', $c)->queryScalar();

        $c->limit = $size;
        $c->offset = $size * ($page - 1);
        $c->select = '*';

        $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
        $list = array();
        foreach ($rows as $row) {
            $list[] = unserialize($row['Data']);
        }

        $pages = new CPagination($count);
        $pages->pageSize = $size;
        $this->render('index', array('title' => 'All ' . Yii::app()->params['category'] . 's', 'items' => $list, 'pages' => $pages));
    }

    public function actionQuestion($id) {
        $id = (int) $id;
        $c = new CDbCriteria();
        $c->addColumnCondition(array('Id' => $id));
        $q = Yii::app()->db->getCommandBuilder()->createFindCommand('question', $c)->queryRow();
        if (empty($q))
            $a = array();
        else {
            $c = new CDbCriteria();
            $c->addColumnCondition(array('QId' => $q['QId']));
            $a = Yii::app()->db->getCommandBuilder()->createFindCommand('answer', $c)->queryAll();
            $c->select = 'ASIN';
            $asin = Yii::app()->db->getCommandBuilder()->createFindCommand('listing2question', $c)->queryColumn();
            $c = new CDbCriteria();
            $c->select = 'ASIN,Title';
            $c->addInCondition('ASIN', $asin);
            $p = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();

            $this->pageTitle = $q['Title'] . ' - ' . join(' ', array_slice(explode(' ', $p[0]['Title']), 0, 4));

            $c = new CDbCriteria();
            $c->select = 't.Title,t.Id';
            $c->join = 'JOIN listing2question lq ON lq.QId = t.QId';
            $c->addInCondition('lq.ASIN', $asin);
            $related = Yii::app()->db->getCommandBuilder()->createFindCommand('question', $c)->queryAll();
        }

        $this->render('question', array('q' => $q, 'a' => $a, 'p' => $p, 'related' => $related));
    }

}

