<?php

class SitemapCommand extends CConsoleCommand {
    protected $urls = 0;
    
    public function run($args) {
        $urls = array(
            array('u' => 'laptoptop7.com', 'p' => 0.9, 'f' => 'daily'),
            array('u' => 'laptoptop7.com/bestsellers', 'p' => 0.8, 'f' => 'weekly'),
            array('u' => 'laptoptop7.com/toppricedrops', 'p' => 0.9, 'f' => 'daily'),
            array('u' => 'laptoptop7.com/newreleases', 'p' => 0.7, 'f' => 'weekly'),
            array('u' => 'laptoptop7.com/topreviewed', 'p' => 0.6, 'f' => 'monthly'),
                //array('u'=>'laptoptop7.com/all','p'=>0.8,'f'=>'daily'),
        );

        $size = 100;
        $page = 1;
        $c = new CDbCriteria(array(
            'order' => 'SalesRank',
            'distinct' => true,
            'select' => 'ASIN'
        ));
        $f = fopen(Yii::app()->basePath . '/../sitemap.xml', 'w+');
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
                    $this->writeUrl(array('u' => 'laptoptop7.com/search/detail/' . $r['ASIN'], 'p' => '0.8', 'f' => 'weekly'), $f);
                }
            }
            $page++;
        }
        fwrite($f, '</urlset>');
        fclose($f);
        echo 'Urls written:'.$this->urls."\n";
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