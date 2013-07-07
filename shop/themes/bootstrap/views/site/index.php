<?php

/*<div class="hero-unit">
    <h1>We have incredible offer for you!</h1>
<p>
Participate in our special offer and <span class='text-warning' style="font-size: 28px;">every 7th buyer</span> will receive <span class='text-warning' style="font-size: 28px;">$70 amazon gift card</span>.<br>
Also, you can just choose to receive $10 amazon gift card for every laptop is ordered.<br>
If you buy more than one laptop and you are 7th buyer you will receive $70 gift card + $10 for every laptop is ordered.<br>

To receive gift card please <a href="<?=Yii::app()->createUrl('site/gift');?>">read details</a>.
</p>

</div>
 */ ?>
<?php
$list = array();

$asins = array();
foreach($items as $item){
    $asins[] = $item['ASIN'];
}

$inwatch = Yii::app()->stat->inWatch($asins);

foreach ($items as $i) {
    if(!isset($i['ItemAttributes']['Feature']) || !isset($i['LargeImage']['URL']))
        continue;
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

    $ph = '<h5><a href="'. Yii::app()->createSeoUrl('search/detail/' . $i['ASIN'],$i['ItemAttributes']['Title']) .'#history">See price history</a>'; 
    if ($newPrice)
       $ph .=' / '. (isset($inwatch[$i['ASIN']]['new']) ? '<a class="in-watch" href="#">New price in Watch</a>': '<a id="'.$i['ASIN'].'-new-'.$newPrice.'" class="watch-click" href="#" title="Watch amazon price drop">Watch new price</a>');
    if ($usedPrice)
       $ph .= ' / '. (isset($inwatch[$i['ASIN']]['used']) ? '<a class="in-watch" href="#">Used price in Watch</a>': '<a id="'.$i['ASIN'].'-used-'.$usedPrice.'" class="watch-click" href="#" title="Watch amazon price drop">Watch used price</a>');
    $ph .= '</h5>';
    if(is_array($i['ItemAttributes']['Feature'])){
        foreach ($i['ItemAttributes']['Feature'] as &$feature)
            $feature = Yii::app()->stat->wrapText($feature, 100);
    }    
    else 
        $i['ItemAttributes']['Feature'] = array(Yii::app()->stat->wrapText($i['ItemAttributes']['Feature'], 100));
    
    $list[] = array(
        'text' => '
            <div class="row">
                <div class="span6"><img alt="image '.htmlspecialchars($i['ItemAttributes']['Title']).'" src="' . $img . '"></div>
                <div class="span6">
                 <h3><a title="View details of '.htmlspecialchars($i['ItemAttributes']['Title']).'" href="'.Yii::app()->createSeoUrl('search/detail/'.$i['ASIN'],$i['ItemAttributes']['Title']).'">' . $i['ItemAttributes']['Title'] . '</a> <span class="text-warning" style="font-size:12px;">by ' . $i['ItemAttributes']['Brand'] . '</span></h3>
                 '.$ph.'
                    <ul class="carousel-h4">
                        <li>' . join('</li><li>', $i['ItemAttributes']['Feature']) . '</li>
                    </ul>
                 <h3>' . $p . '</h3>
                </div>
           </div>');
}
$this->widget('bootstrap.widgets.TbCarousel', array(
    'items' => $list
));
echo '<p class="text-center" style="font-size:24.5px;font-weight:bold;"><a href="'.Yii::app()->createUrl('search/all').'">' .Yii::app()->stat->getLaptopCount() .' '.Yii::app()->params['category'].'s in the database</a></p>';
echo $pricedrop;
echo '<br></br>';
echo $bestseller;
echo '<br></br>';
echo $review;

?>