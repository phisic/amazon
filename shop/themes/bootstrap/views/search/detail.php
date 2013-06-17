<?php
$asin = $i['ASIN'];
$model = (isset($i['ItemAttributes']['Brand']) ? $i['ItemAttributes']['Brand'] . ' ' : '') . (isset($i['ItemAttributes']['Model']) ? $i['ItemAttributes']['Model'] . ' ' : '');
?>
<div class="row">
    <div class="span4">
        <div style="padding-bottom: 5px;">
            <?php $src = isset($i['LargeImage']['URL']) ? str_replace('.jpg', '._AA500_.jpg', $i['LargeImage']['URL']) : Yii::app()->theme->baseUrl . '/images/noimage.jpeg'; ?>
            <img class="image-large" title="image <?= htmlspecialchars($i['ItemAttributes']['Title']) ?>" alt="image <?= htmlspecialchars($i['ItemAttributes']['Title']) ?>" src="<?= $src; ?>">
        </div>

        <?php if (isset($i['ImageSets']['ImageSet'])) foreach ($i['ImageSets']['ImageSet'] as $key => $value): ?>
                <?php if (isset($value['TinyImage']['URL'])): ?>
                    <?php $srcThumb = str_replace('._SL75_', '._SX38_SY50_CR,0,0,68,80_', $value['SmallImage']['URL']); ?>
                    <div class="image-border">
                        <img class="image-thumb" title="image <?= htmlspecialchars($i['ItemAttributes']['Title']) ?>" alt="image <?= htmlspecialchars($i['ItemAttributes']['Title']) ?>" src="<?= $srcThumb; ?>">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

    </div>
    <div class="span8">

        <div class="span8">
            <h1><?= $i['ItemAttributes']['Title'] ?> <span class="text-warning" style="font-size:12px;"><?= isset($i['ItemAttributes']['Brand']) ? 'by ' . $i['ItemAttributes']['Brand'] : ''; ?></span></h1>
            <h5>
                <?php
                $newPrice = Yii::app()->amazon->getNewPrice($i);
                $usedPrice = Yii::app()->amazon->getUsedPrice($i);
                if (isset($i['ItemAttributes']['ListPrice']['Amount']) && $i['ItemAttributes']['ListPrice']['Amount'] != $newPrice)
                    echo '<s class="muted" style="font-size:12px;">' . Yii::app()->amazon->formatUSD($i['ItemAttributes']['ListPrice']['Amount']) . '</s>';
                if ($newPrice)
                    echo ' <a title="' . $i['ItemLinks']['ItemLink'][6]['Description'] . '" target="_blank" href="' . $i['ItemLinks']['ItemLink'][6]['URL'] . '" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
                if ($newPrice && $usedPrice)
                    echo ' <span style="font-size:16px;"> & </span> ';
                if ($usedPrice)
                    echo ' <a title="' . $i['ItemLinks']['ItemLink'][6]['Description'] . '" target="_blank" href="' . $i['ItemLinks']['ItemLink'][6]['URL'] . '" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
                ?>
            </h5>
            <h5>
                <?php
                $inwatch = Yii::app()->stat->inWatch((array) $i['ASIN']);
                if ($newPrice)
                    echo (isset($inwatch[$i['ASIN']]['new']) ? '<a rel="nofollow" class="in-watch" href="#">New price in Watch</a>' : '<a id="' . $i['ASIN'] . '-new-' . $newPrice . '" class="watch-click" href="#" title="Watch amazon price drop" rel="nofollow">Watch new price</a>');
                if ($newPrice && $usedPrice)
                    echo ' / ';
                if ($usedPrice)
                    echo (isset($inwatch[$i['ASIN']]['used']) ? '<a rel="nofollow" class="in-watch" href="#">Used price in Watch</a>' : '<a id="' . $i['ASIN'] . '-used-' . $usedPrice . '" class="watch-click" href="#" title="Watch amazon price drop" rel="nofollow">Watch used price</a>');
                ?>
            </h5> 
            <h6><a target="_blank" href="<?= $i['DetailPageURL'] ?>" class="btn btn-info btn-small">Buy at Amazon ></a></h6>
            <div class="row">
                <?php
                if (isset($parts[$asin])) {
                    echo '<div class="span5">';
                    echo '<h4>Performance Benchmark</h4>';
                    if (isset($parts[$asin]['cpu'])) {
                        $mark = round($parts[$asin]['cpu']['Score'] / (Yii::app()->part->getMaxScore('cpu') / 10), 2);
                        $percent = ceil($mark * 10);
                        echo '<div>CPU: <span class="text-success">' . $parts[$asin]['cpu']['Model'] . '</span>  Mark: <span class="text-success">' . $mark . '</span> / 10</div>';
                        echo '<div class = "progress progress-success">
                                <div class = "bar" style = "width: ' . $percent . '%"></div>
                              </div>';
                    }

                    if (isset($parts[$asin]['vga'])) {
                        $mark = round($parts[$asin]['vga']['Score'] / (Yii::app()->part->getMaxScore('vga') / 10), 2);
                        $percent = ceil($mark * 10);
                        echo '<div>VGA: <span class="text-warning">' . $parts[$asin]['vga']['Model'] . '</span>  Mark: <span class="text-success">' . $mark . '</span> / 10</div>';
                        echo '<div class = "progress progress-warning">
                                <div class = "bar" style = "width: ' . $percent . '%"></div>
                              </div>';
                    }

                    echo '</div>';
                }
                ?>
                <div class="<?= isset($parts[$asin]) ? 'span3' : 'span8' ?>">
                    <ul>
                        <?php
                        if (isset($i['ItemAttributes']['Feature']) && is_array($i['ItemAttributes']['Feature'])) {
                            $i['ItemAttributes']['Feature'] = array_reverse($i['ItemAttributes']['Feature']);
                            foreach ($i['ItemAttributes']['Feature'] as $attr)
                                echo '<li>' . $attr . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<a rel="nofollow" name="history"></a><h3><?= $model ?>Price History From amazon.com</h3>
<?php
if (empty($history))
    echo '<p>Price history not available for this ' . Yii::app()->params['category'] . '</p>';
else
    echo '<div id="chart1"></div>';
?>


<div id="productDescription">
    <?php
    if (!empty($i['EditorialReviews']['EditorialReview'])) {
        echo '<h3><a class="description-click" rel="nofollow" href="' . Yii::app()->createUrl('search/description', array('asin' => $asin)) . '" title="' . $model . '">Read ' . $model . ' Description</a></h3>';
    }
    ?>
</div>
<?php
if (!empty($history)) {
    $lnew = $lused = array();
    $line = '';
    foreach ($history as $n => $h) {
        if (!empty($h['PriceNew'])) {
            if ($n == 0 || ($history[$n - 1]['PriceNew'] != $h['PriceNew']))
                $lnew[] = '["' . $h['Date'] . '",' . ($h['PriceNew'] / 100) . ']';
        }
        if (!empty($h['PriceUsed'])) {
            if ($n == 0 || ($history[$n - 1]['PriceUsed'] != $h['PriceUsed']))
                $lused[] = '["' . $h['Date'] . '",' . ($h['PriceUsed'] / 100) . ']';
        }
    }

    if ($newPrice && !empty($h['PriceNew']) && $h['PriceNew'] != $newPrice) {
        $lnew[] = '["' . date('Y-m-d H:i:s') . '",' . ($newPrice / 100) . ']';
    }

    if ($usedPrice && !empty($h['PriceUsed']) && $h['PriceUsed'] != $usedPrice) {
        $lused[] = '["' . date('Y-m-d H:i:s') . '",' . ($usedPrice / 100) . ']';
    }

    if ($lnew)
        $line .= '[' . join(',', $lnew) . ']';

    if ($lnew && $lused)
        $line .= ',';

    if ($lused)
        $line .= '[' . join(',', $lused) . ']';
    ?>
    <?php if (!empty($line)) { ?>
        <script type="text/javascript">
            $(document).ready(function() {
                var plot1 = $.jqplot('chart1', [<?php echo $line; ?>], {
                    title: '<strong><span class="text-info">New price</span>, <span class="text-warning">Used price</span></strong>, time zone in graph GMT+2',
                    axes: {
                        xaxis: {
                            renderer: $.jqplot.DateAxisRenderer,
                            tickOptions: {formatString: '%b %#d %H:%M'},
                        },
                        yaxis: {
                            tickOptions: {
                                formatString: '$%.2f'
                            },
                            label: '&nbsp;&nbsp;&nbsp;',
                        }
                    },
                    highlighter: {
                        show: true,
                        sizeAdjust: 7.5
                    },
                    cursor: {
                        show: false
                    }
                });
            });
        </script>
    <?php } ?>
<?php } ?>

<?php
if (isset($parts[$asin])) {
    if (isset($parts[$asin]['cpu'])) {
        echo '<h3>Laptops with identical ' . $parts[$asin]['cpu']['Model'] . ' processor ';
//        if ($this->getSimilarCPUCount($parts[$asin]['cpu']['Id']) > 20) {
//            echo '<a href="#">View All(' . $this->getSimilarCPUCount($parts[$asin]['cpu']['Id']) . ')</a>';
//        }
        echo '</h3>';
    }
}
if (isset($parts[$asin])) {
    if (isset($parts[$asin]['cpu']))
        foreach ($this->getSimilar($parts[$asin]['cpu']['Id']) as $n => $item) :
            ?>
            <div class="row">
                <div class="span2">
                    <img class="img-rounded" title="image <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>" src="<?= isset($item['SmallImage']['URL']) ? $item['SmallImage']['URL'] : Yii::app()->theme->baseUrl . '/images/noimage.jpeg' ?>" alt="image of <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>">
                </div>
                <div class="span10">
                    <h4><a title="View details of <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>" href="<?= Yii::app()->createSeoUrl('search/detail/' . $item['ASIN'], $item['ItemAttributes']['Title']) ?>"><?= strlen($item['ItemAttributes']['Title'])>=70 ? mb_substr($item['ItemAttributes']['Title'], 0, 70, "UTF-8").'...' : $item['ItemAttributes']['Title'] ?></a> <span class="text-warning" style="font-size:12px;"><?= isset($item['ItemAttributes']['Brand']) ? 'by ' . $item['ItemAttributes']['Brand'] : ''; ?></span></h4>
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
                        ?>
                    </h5>
                </div>
            </div>
            <?php
        endforeach;
}?>