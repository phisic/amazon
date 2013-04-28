<div class="hero-unit">
    <h1>Hello, currently 15 000 laptops is here</h1>
    <h1>Are you looking for best price laptop?</h1>
    <h1></h1>
    <p style="font-size:20px;">
        We can help you to find best price laptop, search laptops and watch prices. 
        When price dropped we will email to you, just add laptop to watch! 
        Also, we provide price history for every laptop, so you can easily decide is this best price.
    </p>
</div>
<?php
$list = array();
foreach ($items as $i) {
    $newPrice = Yii::app()->amazon->getNewPrice($i);
    $usedPrice = Yii::app()->amazon->getUsedPrice($i);
    $p = '';
    if (isset($i['ItemAttributes']['ListPrice']['Amount']) && $i['ItemAttributes']['ListPrice']['Amount'] != $newPrice)
        $p = '<s class="muted" style="font-size:18px;">' . Yii::app()->amazon->formatUSD($i['ItemAttributes']['ListPrice']['Amount']) . '</s>';
    if ($newPrice)
        $p .=' <a title="'.$i['ItemLinks']['ItemLink'][6]['Description'].' at amazon.com" target="_blank" href="'.$i['ItemLinks']['ItemLink'][6]['URL'].'" class="text-error" style="font-size:32px;"><strong>' . Yii::app()->amazon->formatUSD($newPrice) . '</strong></a> new';
    if ($newPrice && $usedPrice)
        $p .= ' <span style="font-size:20px;"> & </span> ';
    if ($usedPrice)
        $p .= ' <a title="'.$i['ItemLinks']['ItemLink'][6]['Description'].' at amazon.com" target="_blank" href="'.$i['ItemLinks']['ItemLink'][6]['URL'].'" class="text-error" style="font-size:26px;"><strong>' . Yii::app()->amazon->formatUSD($usedPrice) . '</strong></a> used';
    $img = isset($i['LargeImage']['URL']) ? str_replace(".jpg", "._AA500_.jpg", $i['LargeImage']['URL']) : Yii::app()->createUrl('images') . '/none.jpg';
    $list[] = array(
        'text' => '
            <div class="row">
                <div class="span6"><img src="' . $img . '"></div>
                <div class="span6">
                 <h3><a title="View details of '.$i['ItemAttributes']['Title'].'" href="'.Yii::app()->createUrl('search/detail/'.$i['ASIN']).'">' . $i['ItemAttributes']['Title'] . '</a> <span class="text-warning"style="font-size:12px;">by ' . $i['ItemAttributes']['Brand'] . '</span></h3>  
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