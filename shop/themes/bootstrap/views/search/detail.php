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
<h3>Price history from amazon.com</h3>

<div id="chart1" style=""></div> 

<div id="productDescription">
    <?php
    if(!empty($i['EditorialReviews']['EditorialReview']))
        echo '<h3>Product description</h3>';
    foreach ($i['EditorialReviews']['EditorialReview'] as $d){
        echo $d;
    }   
    ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
  var line1=[['23-May-2008', 578.55], ['20-Jun-2008', 566.5], ['25-Jul-2008', 480.88], ['22-Aug-2008', 509.84],
      ['26-Sep-2008', 454.13], ['24-Oct-2008', 379.75], ['21-Nov-2008', 303], ['26-Dec-2008', 308.56],
      ['23-Jan-2009', 299.14], ['20-Feb-2009', 346.51], ['20-Mar-2009', 325.99], ['24-Apr-2009', 386.15]];
  var line0=[['23-May-2008', 478.55], ['20-Jun-2008', 466.5], ['25-Jul-2008', 480.88], ['22-Aug-2008', 409.84],
      ['26-Sep-2008', 454.13], ['24-Oct-2008', 379.75], ['21-Nov-2008', 303], ['26-Dec-2008', 408.56],
      ['23-Jan-2009', 499.14], ['20-Feb-2009', 446.51], ['20-Mar-2009', 425.99], ['24-Apr-2009', 486.15]];
  var plot1 = $.jqplot('chart1', [line1,line0], {
      title:'Data Point Highlighting',
      axes:{
        xaxis:{
          renderer:$.jqplot.DateAxisRenderer,
          tickOptions:{formatString:'%b %#d, %y'},
          min:'May 30, 2008',
          tickInterval:'2 month'
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