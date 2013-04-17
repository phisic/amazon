<?php

$list = array();
foreach ($items as $i) {
    $newPrice = Yii::app()->amazon->getNewPrice($i);
    $usedPrice = Yii::app()->amazon->getUsedPrice($i);
    $p='';
    if (isset($i['ItemAttributes']['ListPrice']['Amount']))
        $p = '<s class="muted" style="font-size:18px;">' . Yii::app()->amazon->formatUSD($i['ItemAttributes']['ListPrice']['Amount']) . '</s>';
    if ($newPrice)
        $p .=' <a href="" class="text-error" style="font-size:32px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
    if ($newPrice && $usedPrice)
        $p .= ' <span style="font-size:20px;"> & </span> ';
    if ($usedPrice)
        $p .= ' <a href="" class="text-error" style="font-size:26px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
    $img = isset($i['LargeImage']['URL']) ? str_replace(".jpg", "._LA500_.jpg", $i['LargeImage']['URL']) : Yii::app()->createUrl('images') . '/none.jpg';
    $list[] = array(
        'text' => '
            <div class="row">
                <div class="span6"><img src="' . $img . '"></div>
                <div class="span6" style="height:500px;vertical-align: middle;display: table-cell;">
                 <h3><a href="">' . $i['ItemAttributes']['Title'] . '</a> <span class="text-warning"style="font-size:12px;">by ' . $i['ItemAttributes']['Brand'] . '</span></h3>  
                 <h4>
                    <ul>
                        <li>' . join('</li><li>', $i['ItemAttributes']['Feature']) . '</li>
                    </ul>
                 </h4>
                 <h3>' . $p . '</h3>
                </div>
           </div>');
}
$this->widget('bootstrap.widgets.TbCarousel', array(
    'items' => $list
));

echo $pricedrop;
?>