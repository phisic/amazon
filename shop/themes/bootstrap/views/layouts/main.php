<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />

        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />

        <title><?php echo CHtml::encode($this->pageTitle); ?></title>

    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <div class="row">
                        <div class="span2"><a class="brand" href="<?= Yii::app()->homeUrl ?>"><?= Yii::app()->name ?></a></div>
                        <div class="span8">
                            <form action="<?= Yii::app()->createUrl('search/index') ?>" class="navbar-form form-search">
                                <div class="input-append input-block-level">
                                    <input type="text" placeholder="search..." class="input-block-level">
                                        <button class="btn" type="submit">GO</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="span2">
                            <?php
                            $this->widget('bootstrap.widgets.TbButtonGroup', array(
                                'htmlOptions'=>array('class'=>'btn-block'),
                                'type' => 'warning', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                                'buttons' => array(
                                    array('label' => 'All categories', 'htmlOptions'=>array('class'=>''),'items' => array(
                                            array('label' => 'Action', 'url' => '#'),
                                            array('label' => 'Another action', 'url' => '#'),
                                            array('label' => 'Something else', 'url' => '#'),
                                            '---',
                                            array('label' => 'Separate link', 'url' => '#'),
                                        )),
                                ),
                            ));
                            ?></div>
                        <div class="span8"><h4><a href="">Laptops</a> <a href="">Tablets</a> <a href="">Ultrabooks</a></h4></div>
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

    </body>
</html>
