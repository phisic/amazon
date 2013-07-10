<?php 
if(empty($q))
    echo '<h1>Question not found</h1>';
else{
?>
<h1><?=$q['Title']?></h1>
<div class="">
    <?=$q['Text']?>
</div>
<h3>Answers</h3>
<?php 
foreach ($a as $n => $ans){
    $text = preg_replace('@<span[^>]*?>.*?</span>@si', '', $ans['Text']); 
    echo '<p class=""><b>'.($n+1).'.</b>'.$text.'</p>';
}
?>
<h3>Linked products</h3>
<?php 
foreach ($p as $n => $pr){
    echo '<div><a href="'.Yii::app()->createSeoUrl('search/detail/' . $pr['ASIN'], $pr['Title']).'">'.$pr['Title'].'</a></div>';
}
?>
<?}?>