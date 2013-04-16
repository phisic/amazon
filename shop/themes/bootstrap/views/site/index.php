<h3>Hot New Releases</h3>
<?php foreach ($items as $item) { ?>
    <div class="row" style="border-bottom: 1px dashed #ccc;margin-bottom: 10px;padding-bottom: 10px;">
        <div class="span2"><img src="<?= isset($item['MediumImage']['URL']) ? str_replace("._SL160_.", "._AA160_.", $item['MediumImage']['URL']) : Yii::app()->createUrl('images') . '/none.jpg'; ?>" alt="product 2"></div>
        <div class="span10">
            <h4><a href=''><?= $item['ItemAttributes']['Title'] ?></a> <span class='text-warning'style='font-size:12px;'>by <?= $item['ItemAttributes']['Brand'] ?></span></h4>
            <h5>
                        <?php
                        $newPrice = Yii::app()->amazon->getNewPrice($item);
                        $usedPrice = Yii::app()->amazon->getUsedPrice($item);
                        if (isset($item['ItemAttributes']['ListPrice']['Amount']))
                            echo '<s class="muted" style="font-size:12px;">' . Yii::app()->amazon->formatUSD($item['ItemAttributes']['ListPrice']['Amount']) . '</s>';
                        if($newPrice) 
                            echo ' <a href="" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
                        if($newPrice && $usedPrice)
                            echo ' <span style="font-size:20px;"> and </span> ';
                        if($usedPrice) 
                            echo ' <a href="" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
                        ?>
                        <span class="text-warning">Tools: <a href="">See price history</a> 
                        <?php
                        if($newPrice) echo ' / <a href="">Watch new price</a>';
                        if($usedPrice) echo ' / <a href="">Watch used price</a>';
                        ?>
                        </span>
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
            <h6><a href="#" class="btn btn-info btn-small">Buy at Amazon ></a></h6>
        </div>
    </div>
<?php } ?>
<?
if (isset($pages))
    $this->widget('ext.bootstrap.widgets.TbPager', array('htmlOptions' => array('class' => 'pager'), 'pages' => $pages)); ?>