<h3>Hot New Releases</h3>
<?php foreach ($items as $item){ ?>
<div class="row">
  <div class="span2"><img src="<?=isset($item['MediumImage']['URL']) ?str_replace("._SL160_.","._AA160_.",$item['MediumImage']['URL']) :  Yii::app()->createUrl('images') . '/none.jpg';?>" alt="product 2"></div>
  <div class="span8">
      <div class="span6">
      <h4><a href=''><?=$item['ItemAttributes']['Title']?></a> <span class='text-warning'style='font-size:12px;'>by <?=$item['ItemAttributes']['Brand']?></span></h4>
      <h6><ul>
      <?php if(isset($item['ItemAttributes']['Feature']) && is_array($item['ItemAttributes']['Feature']))foreach($item['ItemAttributes']['Feature'] as $attr){
          echo '<li>'.$attr.'</li>';
      }?>
              </ul>
      </h6>
      </div><div class="span6">
          <h6>
          <?php
            if(isset($item['ItemAttributes']['ListPrice']['Amount']))
                echo '<s class="">'.Yii::app()->amazon->formatUSD($item['ItemAttributes']['ListPrice']['Amount']).'</s>' . ' / ' . Yii::app()->amazon->formatUSD(Yii::app()->amazon->getNewPrice($item))
          ?>
          </h6></div>
  </div>
  <div class="span2"><a href="#" class="btn btn-success">Buy</a></div>
  </div>
<?php } ?>
<? if(isset($pages)) $this->widget('ext.bootstrap.widgets.TbPager', array('htmlOptions'=>array('class'=>'pager'),'pages' => $pages)); ?>