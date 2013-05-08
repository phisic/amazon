<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />

        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
	    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/main.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/<?=Yii::app()->user->getIsGuest() ? 'watchGuest.js' : 'watchUser.js'?>"></script>
    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <div class="row-fluid">
                        <div class="span2"><a class="brand" href="<?= Yii::app()->homeUrl ?>"><?= Yii::app()->name ?></a></div>
                        <div class="span7">
                            <form action="<?= Yii::app()->createUrl('search/index') ?>" class="navbar-form form-search">
                                <div class="input-append input-block-level">
                                    <input name="search" type="text" placeholder="search..." class="input-block-level">
                                        <button class="btn" type="submit">GO</button>
                                </div>
                            </form>
                        </div>
                        <div class="span3 text-right">
	                        <? if (Yii::app()->user->getIsGuest()) : ?>
	                        <?php $this->widget('bootstrap.widgets.TbButton', array(
		                        'label' => 'Login',
		                        'type' => 'primary',
		                        'htmlOptions' => array(
			                        'data-toggle' => 'modal',
			                        'data-target' => '#loginModal',
		                        ),
	                        )); ?>
	                        <?php $this->widget('bootstrap.widgets.TbButton', array(
		                        'label' => 'Register',
		                        'type' => 'primary',
		                        'htmlOptions' => array(
			                        'data-toggle' => 'modal',
			                        'data-target' => '#registerModal',
		                        ),
	                        )); ?>
	                        <? else : ?>
	                        <?= Yii::app()->user->name ?> <a href="<?=Yii::app()->createUrl('site/logout')?>" class="btn btn-primary"
	                                                         type="submit">Logout</a>
	                        <? endif ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span2">
                            <?php
                            $this->widget('bootstrap.widgets.TbButtonGroup', array(
                                'htmlOptions' => array('class' => 'btn-block'),
                                'type' => 'warning', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                                'buttons' => array(
                                    array('label' => 'All categories', 'htmlOptions' => array('class' => ''), 'items' => array(
                                            array('label' => 'Laptops', 'url' => '#'),
                                            array('label' => 'Tablets', 'url' => '#'),
                                            array('label' => 'Ultrabooks', 'url' => '#'),
                                            '---',
                                            array('label' => 'All categories', 'url' => '#'),
                                        )),
                                ),
                            ));
                            ?></div>
                        <div class="span8"><h4><a href="<?= Yii::app()->createUrl('search/toppricedrops') ?>">Top Price Drops Today</a> / <a href="<?= Yii::app()->createUrl('search/bestsellers') ?>">Best Sellers</a> / <a href="<?= Yii::app()->createUrl('search/topreviewed') ?>">Top Reviewed</a> / <a href="<?= Yii::app()->createUrl('search/newreleases') ?>">New Releases</a></h4></div>
                    </div>   
                </div>
            </div>
        </div>
        <div class="container" id="page">

            <?php if (isset($this->breadcrumbs)): ?>
                <?php
                $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                    'links' => $this->breadcrumbs,
                ));
                ?><!-- breadcrumbs -->
            <?php endif ?>

            <?php echo $content; ?>

            <div class="clear"></div>

            <div id="footer">
                Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
                All Rights Reserved.<br/>
                <?php echo Yii::powered(); ?>
            </div><!-- footer -->

        </div><!-- page -->
        <?php if(Yii::app()->user->getIsGuest()){?>
            <? $this->renderPartial('//site/watch-form'); ?>
	        <? $this->renderPartial('//site/login-form'); ?>
	        <? $this->renderPartial('//site/register-form'); ?>
        <?php 
        }else{
            echo '<script type="text/javascript">var watchUrl = "'.Yii::app()->createUrl('watch/index').'"</script>';
        } 
        ?>
    </body>
</html>
