<h3>Search Result</h3>
<div class="items">
<ul class="thumbnails">
<?php foreach ($items as $item){ ?>
<li class="span4">  
<div class="thumbnail">
  <img src="<?=isset($item['MediumImage']['URL']) ?str_replace("._SL160_.","._AA160_.",$item['MediumImage']['URL']) :  Yii::app()->createUrl('images') . '/none.jpg';?>" alt="product 2">
  <div class="caption">  
    <h5>Product detail</h5>  
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>  
    <p><a href="#" class="btn btn-success">Buy</a> <a href="#" class="btn btn-warning">Try</a></p>  
  </div>  
</div>  
</li>
<?php } ?>
</ul></div>  
<? $this->widget('ext.bootstrap.widgets.TbPager', array('htmlOptions'=>array('class'=>'pager'),'pages' => $pages)); ?>