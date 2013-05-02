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
                            <a class="btn btn-primary" type="submit">Login</a>
                            <a class="btn btn-primary" type="submit">Register</a>
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
                <div class="hide watch-form-body">
            <?php    
                $model = new WatchForm();
                $form = new CForm('application.views.site.watch', $model);
                echo $form->render();
            ?>
                    </div>
            <div class="clear"></div>

            <div id="footer">
                Copyright &copy; <?php echo date('Y'); ?> by My Company.<br/>
                All Rights Reserved.<br/>
                <?php echo Yii::powered(); ?>
            </div><!-- footer -->

        </div><!-- page -->
        <div class="hide watch-form-body2">
            <form class='form-horizontal' tag="">
                <div><span>First Name</span><input type="text" name="firstname"></div>
                <div><span>Email</span><input type="text" name="email"></div>
                <p>&nbsp;</p>
                <button tag="" type="button" class="btn btn-primary">Watch</button> 
                <button type="button" class="btn btn-warning" style="margin-left:20px;">Cancel</button>
            </form>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.watch-click').click(function() {
                    return false;
                });
                $('.form-horizontal .btn-primary').live('click', function() {
                    alert(1);
                })
                $('.form-horizontal .btn-warning').live('click', function() {
                    var elId = $(this).prev().attr('tag');
                    $('#'+elId).popover('hide');
                })
                $('.watch-click').each(function() {
                    var el = $(this);
                    var id = el.attr('id');
                    $('.watch-form-body .btn-primary').attr('tag', id);
                    el.popover({"html": true, "content": $('.watch-form-body').html(), "placement": "bottom"});
                });
            })

        </script>

    </body>
</html>
