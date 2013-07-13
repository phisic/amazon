<?php

class SitemapCommand extends CConsoleCommand {

    protected $urls = 0;

    public function run($args) {
        $d = Yii::app()->params['domain'];
        $urls = array(
            array('u' => $d, 'p' => 0.9, 'f' => 'daily'),
            array('u' => $d . '/search/bestsellers', 'p' => 0.8, 'f' => 'weekly'),
            array('u' => $d . '/search/toppricedrops', 'p' => 0.9, 'f' => 'daily'),
            array('u' => $d . '/search/newreleases', 'p' => 0.7, 'f' => 'weekly'),
            array('u' => $d . '/search/topreviewed', 'p' => 0.6, 'f' => 'monthly'),
                //array('u'=>'laptoptop7.com/all','p'=>0.8,'f'=>'daily'),
        );

        $size = 100;
        $page = 1;
        $c = new CDbCriteria(array(
            'order' => 'SalesRank',
            'distinct' => true,
            'select' => 'ASIN, Title'
        ));
        $sitemaps[] = 'sitemap-laptop.xml';

        $f = fopen(Yii::app()->basePath . '/../sitemap-laptop.xml', 'w+');
        fwrite($f, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($f, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
        foreach ($urls as $url) {
            $this->writeUrl($url, $f);
        }

        $fetch = true;
        while ($fetch) {
            $c->limit = $size;
            $c->offset = $size * ($page - 1);

            $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('listing', $c)->queryAll();
            $fetch = !empty($rows);
            if ($fetch) {
                foreach ($rows as $r) {
                    $url = $d . Yii::app()->createSeoUrl('search/detail/' . $r['ASIN'], $r['Title']);
                    $this->writeUrl(array('u' => $url, 'p' => '0.8', 'f' => 'weekly'), $f);
                }
            }
            $page++;
        }
        fwrite($f, '</urlset>');
        fclose($f);




        $page = 1;
        $index = 0;
        $c = new CDbCriteria(array(
            'select' => 'Id'
        ));
        $fetch = true;
        while ($fetch) {
            if ($page == 1) {
                $sitemaps[] = 'sitemap'.$index.'.xml';
                $f = fopen(Yii::app()->basePath . '/../sitemap'.$index.'.xml', 'w+');
                fwrite($f, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
                fwrite($f, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
            }

            $c->limit = $size;
            $c->offset = $size * ($page - 1)+($index*500*$size);

            $rows = Yii::app()->db->getCommandBuilder()->createFindCommand('question', $c)->queryAll();
            $fetch = !empty($rows);
            if ($fetch) {
                foreach ($rows as $r) {
                    $url = $d . 'search/question/' . $r['Id'];
                    $this->writeUrl(array('u' => $url, 'p' => '1', 'f' => 'weekly'), $f);
                }
            }
            
            if ($page == 500) {
                fwrite($f, '</urlset>');
                fclose($f);
                $page=0;
            }
            $page++;
        }
        if($page!=500){
            fwrite($f, '</urlset>');
            fclose($f);
        }


        $f = fopen(Yii::app()->basePath . '/../sitemap.xml', 'w+');
        fwrite($f, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        fwrite($f, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
        foreach ($sitemaps as $s) {
            fwrite($f, '<sitemap>');
            fwrite($f, '<loc>http://laptoptop7.com/' . $s . '</loc>');
            fwrite($f, '<lastmod>' . date('Y-m-d') . '</lastmod>');
            fwrite($f, '</sitemap>');
        }
        fwrite($f, '</sitemapindex>');
        fclose($f);



        echo 'Urls written:' . $this->urls . "\n";
    }

    protected function writeFile($num, CDbCriteria $c) {
        
    }

    protected function writeUrl($u, $f) {
        $this->urls++;
        $s = '<url>' . "\n";
        $s .= '<loc>http://' . $u['u'] . '/</loc>' . "\n";
        $s .= '<changefreq>' . $u['f'] . '</changefreq>' . "\n";
        $s .= '<priority>' . $u['p'] . '</priority>' . "\n";
        $s .= '</url>' . "\n";
        fwrite($f, $s);
    }

}