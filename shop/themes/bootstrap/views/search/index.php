<h3 style="border-bottom: 1px solid;"><?= $title; ?></h3>
<?php
$count = count($items) - 1;
$asins = array();
foreach($items as $item){
    $asins[] = $item['ASIN'];
}
$inwatch = Yii::app()->stat->inWatch($asins);

foreach ($items as $n => $item) {
    ?>
    <div class="row" <?php if ($count > $n) echo 'style="border-bottom: 1px dashed #ccc;margin-bottom: 10px;padding-bottom: 10px;"'; ?>>
        <div class="span2">
            <img class="img-rounded" src="<?= isset($item['MediumImage']['URL']) ? str_replace("._SL160_.", "._AA160_.", $item['MediumImage']['URL']) : Yii::app()->createUrl('images') . '/none.jpg'; ?>" alt="product 2">
            <?php if (isset($item['SalesRank'])) echo '<h5>Sales Rank #' . $item['SalesRank'] . '</h5>'; ?>
        </div>
        <div class="span10">
            <h4><a title="View details of <?= $item['ItemAttributes']['Title'] ?>" href="<?= Yii::app()->createUrl('search/detail/' . $item['ASIN']) ?>"><?= $item['ItemAttributes']['Title'] ?></a> <span class='text-warning'style='font-size:12px;'><?= isset($item['ItemAttributes']['Brand']) ? 'by ' . $item['ItemAttributes']['Brand'] : ''; ?></span></h4>
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
                if (isset($priceDrops[$item['ASIN']]))
                    echo ' <span>&nbsp;/&nbsp;Price drop today: <span class="text-success" style="font-size:26px;">' . Yii::app()->amazon->formatUSD($priceDrops[$item['ASIN']]) . '</span></span>';
                ?>
            </h5>
            <h5>
                <a href="<?= Yii::app()->createUrl('search/detail/' . $item['ASIN']) ?>#history">See price history</a> 
                <?php
                if ($newPrice)
                    echo ' / '. (isset($inwatch[$item['ASIN']]['new']) ? '<a class="in-watch" href="#">New price in Watch</a>': '<a id="'.$item['ASIN'].'-new" class="watch-click" href="#" title="Watch amazon price drop">Watch new price</a>');
                if ($usedPrice)
                    echo ' / '. (isset($inwatch[$item['ASIN']]['used']) ? '<a class="in-watch" href="#">Used price in Watch</a>': '<a id="'.$item['ASIN'].'-used" class="watch-click" href="#" title="Watch amazon price drop">Watch used price</a>');
                ?>
            </h5> 
            <h6><ul>
                    <?php
                    if (isset($item['ItemAttributes']['Feature']) && is_array($item['ItemAttributes']['Feature']))
                        foreach ($item['ItemAttributes']['Feature'] as $attr) {
                            echo '<li>' . $attr . '</li>';
                        }
                    ?>
                </ul>
            </h6>
        </div>
    </div>
<?php } ?>
<?
if (isset($pages))
    $this->widget('ext.bootstrap.widgets.TbPager', array('htmlOptions' => array('class' => 'pager'), 'pages' => $pages));
?>