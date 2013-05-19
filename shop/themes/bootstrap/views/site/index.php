<div class="hero-unit">
    <h3> <?=Yii::app()->stat->getLaptopCount()?> laptops in the database</h3>
</div>
<?php
$list = array();

$asins = array();
foreach($items as $item){
    $asins[] = $item['ASIN'];
}

$inwatch = Yii::app()->stat->inWatch($asins);

foreach ($items as $i) {
    $newPrice = Yii::app()->amazon->getNewPrice($i);
    $usedPrice = Yii::app()->amazon->getUsedPrice($i);
    $p = '';
    if (isset($i['ItemAttributes']['ListPrice']['Amount']) && $i['ItemAttributes']['ListPrice']['Amount'] != $newPrice)
        $p .= '<s class="muted" style="font-size:18px;">' . Yii::app()->amazon->formatUSD($i['ItemAttributes']['ListPrice']['Amount']) . '</s>';
    if ($newPrice)
        $p .=' <a title="'.$i['ItemLinks']['ItemLink'][6]['Description'].' at amazon.com" target="_blank" href="'.$i['ItemLinks']['ItemLink'][6]['URL'].'" class="text-error" style="font-size:32px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
    if ($newPrice && $usedPrice)
        $p .= ' <span style="font-size:20px;"> & </span> ';
    if ($usedPrice)
        $p .= ' <a title="'.$i['ItemLinks']['ItemLink'][6]['Description'].' at amazon.com" target="_blank" href="'.$i['ItemLinks']['ItemLink'][6]['URL'].'" class="text-error" style="font-size:26px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
    $img = isset($i['LargeImage']['URL']) ? str_replace(".jpg", "._AA500_.jpg", $i['LargeImage']['URL']) : Yii::app()->theme->baseUrl . '/images/noimage.jpeg';

    $ph = '<h5><a href="'. Yii::app()->createUrl('search/detail/' . $i['ASIN']) .'#history">See price history</a>'; 
    if ($newPrice)
       $ph .=' / '. (isset($inwatch[$i['ASIN']]['new']) ? '<a class="in-watch" href="#">New price in Watch</a>': '<a id="'.$i['ASIN'].'-new-'.$newPrice.'" class="watch-click" href="#" title="Watch amazon price drop">Watch new price</a>');
    if ($usedPrice)
       $ph .= ' / '. (isset($inwatch[$i['ASIN']]['used']) ? '<a class="in-watch" href="#">Used price in Watch</a>': '<a id="'.$i['ASIN'].'-used-'.$usedPrice.'" class="watch-click" href="#" title="Watch amazon price drop">Watch used price</a>');
    $ph .= '</h5>';
    foreach ($i['ItemAttributes']['Feature'] as &$feature){
        $feature = Yii::app()->stat->wrapText($feature, 100);
    }
    $list[] = array(
        'text' => '
            <div class="row">
                <div class="span6"><img alt="image '.htmlspecialchars($i['ItemAttributes']['Title']).'" src="' . $img . '"></div>
                <div class="span6">
                 <h3><a title="View details of '.htmlspecialchars($i['ItemAttributes']['Title']).'" href="'.Yii::app()->createUrl('search/detail/'.$i['ASIN']).'">' . $i['ItemAttributes']['Title'] . '</a> <span class="text-warning"style="font-size:12px;">by ' . $i['ItemAttributes']['Brand'] . '</span></h3>
                 '.$ph.'
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
echo '<br></br>';
echo $bestseller;
echo '<br></br>';
echo $review;

?>