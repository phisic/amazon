<?php $asin = $i['ASIN'];?>
<div class="row">
    <div class="span4"><img title="image <?=htmlspecialchars($i['ItemAttributes']['Title'])?>" alt="image <?=htmlspecialchars($i['ItemAttributes']['Title'])?>" src="<?= isset($i['LargeImage']['URL']) ? $i['LargeImage']['URL'] : Yii::app()->theme->baseUrl . '/images/noimage.jpeg' ?>"></div>
    <div class="span8">

        <div class="span8">
            <h1><?= $i['ItemAttributes']['Title'] ?> <span class='text-warning'style='font-size:12px;'><?= isset($i['ItemAttributes']['Brand']) ? 'by ' . $i['ItemAttributes']['Brand'] : ''; ?></span></h1>
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
                $inwatch = Yii::app()->stat->inWatch((array)$i['ASIN']);
                if ($newPrice)
                    echo (isset($inwatch[$i['ASIN']]['new']) ? '<a class="in-watch" href="#">New price in Watch</a>': '<a id="'.$i['ASIN'].'-new-'.$newPrice.'" class="watch-click" href="#" title="Watch amazon price drop">Watch new price</a>');
                if($newPrice && $usedPrice)
                    echo ' / ';
                if ($usedPrice)
                    echo (isset($inwatch[$i['ASIN']]['used']) ? '<a class="in-watch" href="#">Used price in Watch</a>': '<a id="'.$i['ASIN'].'-used-'.$usedPrice.'" class="watch-click" href="#" title="Watch amazon price drop">Watch used price</a>');
                ?>
            </h5> 
            <h6><a target="_blank" href="<?= $i['DetailPageURL'] ?>" class="btn btn-info btn-small">Buy at Amazon ></a></h6>
            <div class="row">
                    <?php
                    if (isset($parts[$asin]['cpu'])) {
                        $mark = round($parts[$asin]['cpu']['Score'] / (Yii::app()->part->getMaxScore('cpu')/10), 2);
                        $percent = ceil($mark * 10);
                        echo '<div class="span5">';
                        echo '<h4>Performance Benchmark</h4>';
                        echo '<div>CPU: <span class="text-success">' . $parts[$asin]['cpu']['Model'] . '</span>  Mark: <span class="text-success">' . $mark . '</span> out of 10</div>';
                        echo '<div class = "progress progress-success">
                        <div class = "bar" style = "width: ' . $percent . '%"></div>
                        </div>';

                        echo '</div>';
                    }
                    ?>
                    <div class="span3">
                        <ul>
                            <?php
                            if (isset($i['ItemAttributes']['Feature']) && is_array($i['ItemAttributes']['Feature']))
                                foreach ($i['ItemAttributes']['Feature'] as $attr) {
                                    echo '<li>' . $attr . '</li>';
                                }
                            ?>
                        </ul>
                    </div>
                </div>
        </div>
    </div>
</div>
<a name="history"></a><h3>Price history from amazon.com</h3>
<?php

if (empty($history))
    echo '<p>Price history not available for this laptop</p>';
else 
    echo '<div id="chart1"></div>';
?>


<div class="hide" itemscope itemtype="http://data-vocabulary.org/Product">
  <span itemprop="brand"><?=isset($i['ItemAttributes']['Brand']) ? $i['ItemAttributes']['Brand'] : '';?></span> 
  <span itemprop="name"><?=$i['ItemAttributes']['Title']?></span>
  <?php
    if(isset($i['SmallImage']['URL']))
        echo '<img itemprop="image" src="'.$i['SmallImage']['URL'].'" />';
  ?>
  <span itemprop="description">
      <?php
      if(isset($i['ItemAttributes']['Feature']) && is_array($i['ItemAttributes']['Feature'])){
          echo join('<br>',$i['ItemAttributes']['Feature']);
      }
      ?>
  </span>
  Category: <span itemprop="category" content="Laptops,Notebooks,Ultrabooks">Laptops,Notebooks,Ultrabooks</span>
  Product #: <span itemprop="identifier" content="asin:<?=$i['ASIN']?>"><?=$i['ASIN']?></span>
  <?php if(isset($mark)){ ?>
  <span itemprop="review" itemscope itemtype="http://data-vocabulary.org/Review-aggregate">
    <span itemprop="rating"><?=$mark?></span> CPU performance benchmark out of 
         <span itemprop="best">10</span> 
  </span>
  <?php } ?>
  <?php if($newPrice || $usedPrice){?>
  <span itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
    Regular price: $<?=Yii::app()->amazon->formatUSD($newPrice)?>
    <meta itemprop="currency" content="USD" />
    $<span itemprop="price"><?= $newPrice ? Yii::app()->amazon->formatUSD($newPrice) : Yii::app()->amazon->formatUSD($usedPrice) ?></span>
    <?php if(!$newPrice && $usedPrice){?>
    Condition: <span itemprop="condition" content="used">Previously owned, in excellent condition</span>
    <?php } ?>
    <span itemprop="availability" content="in_stock">In stock! Order now!</span>
  </span>
  <?php } ?>
</div>



<div id="productDescription">
    <?php
    if (!empty($i['EditorialReviews']['EditorialReview'])) {
        echo '<h3>Product description</h3>';
        foreach ($i['EditorialReviews']['EditorialReview'] as $d) {
            echo htmlspecialchars_decode($d);
        }
    }
    ?>
</div>
<?php
if (!empty($history)) {
    $lnew = $lused = array();
    $line = '';
    foreach ($history as $n => $h) {
        if (!empty($h['PriceNew'])){
            if($n == 0 || ($history[$n-1]['PriceNew'] != $h['PriceNew']))            
                $lnew[] = '["' . $h['Date'] . '",' . ($h['PriceNew'] / 100) . ']';
        }    
        if (!empty($h['PriceUsed'])){
            if($n == 0 || ($history[$n-1]['PriceUsed'] != $h['PriceUsed']))
                $lused[] = '["' . $h['Date'] . '",' . ($h['PriceUsed'] / 100) . ']';
        }
    }
    
    if($newPrice && !empty($h['PriceNew']) && $h['PriceNew'] != $newPrice){
        $lnew[] = '["' . date('Y-m-d H:i:s') . '",' . ($newPrice / 100) . ']';
    }
    
    if($usedPrice && !empty($h['PriceUsed']) && $h['PriceUsed'] != $usedPrice){
        $lused[] = '["' . date('Y-m-d H:i:s') . '",' . ($usedPrice / 100) . ']';
    }
    
    if ($lnew)
        $line .= '[' . join(',', $lnew) . ']';

    if ($lnew && $lused)
        $line .= ',';

    if ($lused)
        $line .= '[' . join(',', $lused) . ']';
    ?>
    <?php if(!empty($line)){?>
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
