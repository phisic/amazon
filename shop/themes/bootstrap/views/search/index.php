<h3 style="border-bottom: 1px solid;"><?= $title; ?></h3>
<?php
$count = count($items) - 1;
$asins = $parts = array();
foreach ($items as $item) {
    $asins[] = $item['ASIN'];
}
$inwatch = Yii::app()->stat->inWatch($asins);
$parts = Yii::app()->part->getByAsins($asins); 

foreach ($items as $n => $item) {
    $asin = $item['ASIN'];
    ?>
    <div class="row" <?php if ($count > $n) echo 'style="border-bottom: 1px dashed #ccc;margin-bottom: 10px;padding-bottom: 10px;"'; ?>>
        <div class="span2">
            <img class="img-rounded" title="image <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>" src="<?= isset($item['MediumImage']['URL']) ? str_replace("._SL160_.", "._AA160_.", $item['MediumImage']['URL']) : Yii::app()->theme->baseUrl . '/images/noimage.jpeg' ?>" alt="image of <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>">

            <?php if (isset($item['SalesRank'])) echo '<h5>Sales Rank #' . $item['SalesRank'] . '</h5>'; ?>
            <div class="parts-inside">
                <?php
                if (isset($parts[$asin]['cpu'])) {
                    $cpu = $parts[$asin]['cpu'];
                    if (!empty($cpu['Image']))
                        echo '<img style="width:75px;height:70px;" alt="' . $cpu['Model'] . '" title="Processor: ' . $cpu['Model'] . '" src="' . Yii::app()->theme->getBaseUrl() . '/images/cpu/' . $cpu['Image'] . '">';
                }
                if (isset($parts[$asin]['vga'])) {
                    $vga = $parts[$asin]['vga'];
                    if (!empty($vga['Image'])){
                        echo '<img style="padding-left:5px;width:75px;height:70px;" alt="' . $vga['Model'] . '" title="Video Adapter: ' . $vga['Model'] . '" src="' . Yii::app()->theme->getBaseUrl() . '/images/vga/' . $vga['Image'] . '">';
                    }
                }
                ?>
            </div>
        </div>
        <div class="span10">
            <h4><a title="View details of <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>" href="<?= Yii::app()->createSeoUrl('search/detail/' . $asin, $item['ItemAttributes']['Title']) ?>"><?= $item['ItemAttributes']['Title'] ?></a> <span class="text-warning" style="font-size:12px;"><?= isset($item['ItemAttributes']['Brand']) ? 'by ' . $item['ItemAttributes']['Brand'] : ''; ?></span></h4>
            <h5>
                <?php
                $newPrice = Yii::app()->amazon->getNewPrice($item);
                $usedPrice = Yii::app()->amazon->getUsedPrice($item);
                if (isset($item['ItemAttributes']['ListPrice']['Amount']) && $item['ItemAttributes']['ListPrice']['Amount'] != $newPrice)
                    echo '<s class="muted" style="font-size:12px;">' . Yii::app()->amazon->formatUSD($item['ItemAttributes']['ListPrice']['Amount']) . '</s>';
                if ($newPrice)
                    echo ' <a title="' . $item['ItemLinks']['ItemLink'][6]['Description'] . ' at amazon.com" target="_blank" href="' . $item['ItemLinks']['ItemLink'][6]['URL'] . '" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
                if ($newPrice && $usedPrice)
                    echo ' <span style="font-size:16px;"> & </span> ';
                if ($usedPrice)
                    echo ' <a title="' . $item['ItemLinks']['ItemLink'][6]['Description'] . ' at amazon.com" target="_blank" href="' . $item['ItemLinks']['ItemLink'][6]['URL'] . '" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
                if (isset($priceDrops[$asin]))
                    echo ' <span>&nbsp;/&nbsp;Price drop today: <span class="text-success" style="font-size:26px;">' . Yii::app()->amazon->formatUSD($priceDrops[$asin]) . '</span></span>';
                ?>
            </h5>
            <h5>
                <a href="<?= Yii::app()->createSeoUrl('search/detail/' . $asin, $item['ItemAttributes']['Title']) ?>#history">See price history</a> 
                <?php
                if ($newPrice)
                    echo ' / ' . (isset($inwatch[$asin]['new']) ? '<a class="in-watch" href="#">New price in Watch</a>' : '<a id="' . $asin . '-new-' . $newPrice . '" class="watch-click" href="#" title="Watch amazon price drop">Watch new price</a>');
                if ($usedPrice)
                    echo ' / ' . (isset($inwatch[$asin]['used']) ? '<a class="in-watch" href="#">Used price in Watch</a>' : '<a id="' . $asin . '-used-' . $usedPrice . '" class="watch-click" href="#" title="Watch amazon price drop">Watch used price</a>');
                ?>
            </h5> 
            <div class="row">
                <?php
                if (isset($parts[$asin])) {
                    echo '<div class="span5">';
                    echo '<h4>Performance Benchmark</h4>';
                    if (isset($parts[$asin]['cpu'])) {
                        $mark = round($parts[$asin]['cpu']['Score'] / (Yii::app()->part->getMaxScore('cpu') / 10), 2);
                        $percent = ceil($mark * 10);
                        echo '<div>Processor: <span class="text-success">' . $parts[$asin]['cpu']['Model'] . '</span>  Mark: <span class="text-success">' . $mark . '</span> / 10</div>';
                        echo '<div class = "progress progress-success">
                                <div class = "bar" style = "width: ' . $percent . '%"></div>
                              </div>';
                    }
                    
                    if (isset($parts[$asin]['vga'])) {
                        $mark = round($parts[$asin]['vga']['Score'] / (Yii::app()->part->getMaxScore('vga') / 10), 2);
                        $percent = ceil($mark * 10);
                        echo '<div>Graphics: <span class="text-warning">' . $parts[$asin]['vga']['Model'] . '</span>  Mark: <span class="text-success">' . $mark . '</span> / 10</div>';
                        echo '<div class = "progress progress-warning">
                                <div class = "bar" style = "width: ' . $percent . '%"></div>
                              </div>';
                    }
                    echo '</div>';
                }
                ?>
                <div class="span5">
                    <ul>
                        <?php
                        if (isset($item['ItemAttributes']['Feature']) && is_array($item['ItemAttributes']['Feature']))
                            foreach ($item['ItemAttributes']['Feature'] as $attr) {
                                echo '<li>' . $attr . '</li>';
                            }
                        ?>
                    </ul>
                </div>
            </div>    
            <?php
            if (!Yii::app()->user->getIsGuest() && Yii::app()->user->isAdmin()) {
                Yii::app()->clientScript->registerScriptFile(Yii::app()->getTheme()->getBaseUrl() . '/js/jquery.search.min.js', CClientScript::POS_END);
                $partList = Yii::app()->part->getByAsin($asin);
                if (isset($partList['cpu'])) {
                    echo CHtml::dropDownList('cpu-' . $asin, isset($parts[$asin]['cpu']['Id']) ? $parts[$asin]['cpu']['Id'] : 0, array('------') + $partList['cpu'], array('class' => 'match-cpu'));
                }

                if (isset($partList['vga'])) {
                    echo CHtml::dropDownList('vga-' . $asin, isset($parts[$asin]['vga']['Id']) ? $parts[$asin]['vga']['Id'] : 0, array('------') + $partList['vga'], array('class' => 'match-cpu'));
                }
                
            }
            
            ?>
        </div>
    </div>
<?php } ?>
<?
if (!Yii::app()->user->getIsGuest() && Yii::app()->user->isAdmin()) {
    echo '<script type="text/javascript">var matchUrl = "' . Yii::app()->createUrl('watch/match') . '"</script>';
}
if (isset($pages))
    $this->widget('ext.bootstrap.widgets.TbPager', array('htmlOptions' => array('class' => 'pager'), 'pages' => $pages));
?>