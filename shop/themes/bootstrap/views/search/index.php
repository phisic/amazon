<h3>Search Result</h3>
<?php foreach ($items as $item){ ?>
<div class="row">
  <div class="span2"><img src="<?=isset($item['MediumImage']['URL']) ?str_replace("._SL160_.","._AA160_.",$item['MediumImage']['URL']) :  Yii::app()->createUrl('images') . '/none.jpg';?>" alt="product 2"></div>
  <div class="span8">
      <h4><a href=''><?=$item['ItemAttributes']['Title']?></a> <span class='text-warning'style='font-size:12px;'>by <?=$item['ItemAttributes']['Brand']?></span></h4>
      <h6><ul>
      <?php foreach($item['ItemAttributes']['Feature'] as $attr){
          echo '<li>'.$attr.'</li>';
      }?>
              </ul>
      </h6>
  </div>
  <div class="span2"><a href="#" class="btn btn-success">Buy</a></div>
  </div>
<?php } ?>
<? $this->widget('ext.bootstrap.widgets.TbPager', array('htmlOptions'=>array('class'=>'pager'),'pages' => $pages)); ?>