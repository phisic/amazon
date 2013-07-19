<?php
if (isset($models)) {    
        foreach ($models as $n => $item) :
            ?>
            <div class="row">
                <div class="span2">
                    <img class="img-rounded" title="image <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>" src="<?= isset($item['SmallImage']['URL']) ? $item['SmallImage']['URL'] : Yii::app()->theme->baseUrl . '/images/noimage.jpeg' ?>" alt="image of <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>">

                </div>
                <div class="span10">
                    <h4><a title="View details of <?= htmlspecialchars($item['ItemAttributes']['Title']) ?>" href="<?= Yii::app()->createSeoUrl('search/detail/' . $item['ASIN'], $item['ItemAttributes']['Title']) ?>"><?= strlen($item['ItemAttributes']['Title']) >= 70 ? mb_substr($item['ItemAttributes']['Title'], 0, 70, "UTF-8") . '...' : $item['ItemAttributes']['Title'] ?></a> <span class="text-warning" style="font-size:12px;"><?= isset($item['ItemAttributes']['Brand']) ? 'by ' . $item['ItemAttributes']['Brand'] : ''; ?></span></h4>
                    <h5>
                        <?php
                        $newPrice = Yii::app()->amazon->getNewPrice($item);
                        $usedPrice = Yii::app()->amazon->getUsedPrice($item);
                        if (isset($item['ItemAttributes']['ListPrice']['Amount']) && $item['ItemAttributes']['ListPrice']['Amount'] != $newPrice)
                            echo '<s class="muted" style="font-size:12px;">' . Yii::app()->amazon->formatUSD($item['ItemAttributes']['ListPrice']['Amount']) . '</s>';
                        if ($newPrice)
                            echo ' <a rel="nofollow" title="' . $item['ItemLinks']['ItemLink'][6]['Description'] . ' at amazon.com" target="_blank" href="' . $item['ItemLinks']['ItemLink'][6]['URL'] . '" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
                        if ($newPrice && $usedPrice)
                            echo ' <span style="font-size:16px;"> & </span> ';
                        if ($usedPrice)
                            echo ' <a rel="nofollow" title="' . $item['ItemLinks']['ItemLink'][6]['Description'] . ' at amazon.com" target="_blank" href="' . $item['ItemLinks']['ItemLink'][6]['URL'] . '" class="text-error" style="font-size:20px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
                        if (isset($priceDrops[$asin]))
                            echo ' <span>&nbsp;/&nbsp;Price drop today: <span class="text-success" style="font-size:26px;">' . Yii::app()->amazon->formatUSD($priceDrops[$asin]) . '</span></span>';
                        ?>
                    </h5>
                </div>
            </div>
            <?php
        endforeach;
}
?>
