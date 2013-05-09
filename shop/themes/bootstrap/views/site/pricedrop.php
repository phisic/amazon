<h3>Hot New Releases</h3>
<?php 
$count = count($items)-1;
foreach ($items as $n=>$i) { 
?>
    <div class="row" <?=($n==$count) ? '' : 'style="border-bottom: 1px dashed #ccc;margin-bottom: 10px;padding-bottom: 10px;"';?>>
        <div class="span2"><img class="img-rounded" src="<?= isset($i['MediumImage']['URL']) ? str_replace("._SL160_.", "._AA160_.", $i['MediumImage']['URL']) : Yii::app()->createUrl('images') . '/none.jpg'; ?>" alt="product 2"></div>
        <div class="span10">
            <h4><a href="<?=Yii::app()->createUrl('search/detail/'.$i['ASIN']);?>"><?= $i['ItemAttributes']['Title'] ?></a> <span class='text-warning'style='font-size:12px;'>by <?= $i['ItemAttributes']['Brand'] ?></span></h4>
            <h5>
                        <?php
                        $newPrice = Yii::app()->amazon->getNewPrice($i);
                        $usedPrice = Yii::app()->amazon->getUsedPrice($i);
                        if (isset($i['ItemAttributes']['ListPrice']['Amount']))
                            echo '<s class="muted" style="font-size:12px;">' . Yii::app()->amazon->formatUSD($i['ItemAttributes']['ListPrice']['Amount']) . '</s>';
                        if($newPrice) 
                            echo ' <a href="'.Yii::app()->createUrl('search/detail/'.$i['ASIN']).'" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
                        if($newPrice && $usedPrice)
                            echo ' <span style="font-size:16px;"> & </span> ';
                        if($usedPrice) 
                            echo ' <a href="'.Yii::app()->createUrl('search/detail/'.$i['ASIN']).'" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
                        ?>
               </h5>
               <h5>
                   <a href="">See price history</a> 
                        <?php
                        if($newPrice) echo ' / <a href="">Watch new price</a>';
                        if($usedPrice) echo ' / <a href="">Watch used price</a>';
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
        </div>
    </div>
<?php } ?>
<?
if (isset($pages))
    $this->widget('ext.bootstrap.widgets.TbPager', array('htmlOptions' => array('class' => 'pager'), 'pages' => $pages)); ?>