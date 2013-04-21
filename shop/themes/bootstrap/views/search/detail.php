<style>

</style>
<div class="row">
    <div class="span4"><img src="<?=$i['LargeImage']['URL']?>"></div>
    <div class="span8">
        
        <div class="span8">
            <h1><?= $i['ItemAttributes']['Title'] ?> <span class='text-warning'style='font-size:12px;'>by&nbsp;<?= $i['ItemAttributes']['Brand'] ?></span></h1>
            <h5>
                        <?php
                        $newPrice = Yii::app()->amazon->getNewPrice($i);
                        $usedPrice = Yii::app()->amazon->getUsedPrice($i);
                        if (isset($i['ItemAttributes']['ListPrice']['Amount']))
                            echo '<s class="muted" style="font-size:12px;">' . Yii::app()->amazon->formatUSD($i['ItemAttributes']['ListPrice']['Amount']) . '</s>';
                        if($newPrice) 
                            echo ' <a href="" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
                        if($newPrice && $usedPrice)
                            echo ' <span style="font-size:16px;"> & </span> ';
                        if($usedPrice) 
                            echo ' <a href="" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
                        ?>
               </h5>
               <h5>
                        <?php
                        if($newPrice) echo '<a href="">Watch new price</a>';
                        if($newPrice && $usedPrice) echo ' / ';
                        if($usedPrice) echo '<a href="">Watch used price</a>';
                        ?>
               </h5> 
                    <h6><ul>
                            <?php
                            if (isset($i['ItemAttributes']['Feature']) && is_array($i['ItemAttributes']['Feature']))
                                foreach ($i['ItemAttributes']['Feature'] as $attr) {
                                    echo '<li>' . $attr . '</li>';
                                }
                            ?>
                        </ul>
                    </h6>
            <h6><a href="#" class="btn btn-info btn-small">Buy at Amazon ></a></h6>
        </div>
    </div>
</div>
<?php 
if(!empty($history))
    echo '<h3>Price history from amazon.com</h3><div id="chart1" style=""></div>';
?>

<div id="productDescription">
    <?php
    if(!empty($i['EditorialReviews']['EditorialReview']))
        echo '<h3>Product description</h3>';
    foreach ($i['EditorialReviews']['EditorialReview'] as $d){
        echo $d;
    }   
    ?>
</div>
<?php 
if(!empty($history)) {
    $lnew = $lused = array();
    $line = '';
    foreach ($history as $h){
        if(!empty($h['PriceNew']))
            $lnew[] = '["' . $h['Date'].'",'.($h['PriceNew']/100).']';
        if(!empty($h['PriceUsed']))
            $lused[] = '["' . $h['Date'].'",'.($h['PriceUsed']/100).']';
    }
    if($lnew)
        $line .= '['.join(',',$lnew).']';
    
    if($lnew&&$lused)
        $line .= ',';
    
    if($lused)
        $line .= '['.join(',',$lused).']';
?>
<script type="text/javascript">
$(document).ready(function(){
   var plot1 = $.jqplot('chart1', [<?php echo $line;?>], {
      title:'Data Point Highlighting',
      axes:{
        xaxis:{
          renderer:$.jqplot.DateAxisRenderer,
          tickOptions:{formatString:'%b %#d %H:%M'},
        },
        yaxis:{
          tickOptions:{
            formatString:'$%.2f'
            },
            label:'&nbsp;&nbsp;&nbsp;',
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