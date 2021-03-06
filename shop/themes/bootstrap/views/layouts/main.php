<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="language" content="en" />
        <meta name="google-site-verification" content="GjaBXg6aq5ugFamlMsoWTIIczknXIx_VplwgVQ2pJIU" />
        <meta name="msvalidate.01" content="09C5B7A3686439EE646E6F1976900AB2" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
            <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />
            <title><?php echo CHtml::encode($this->pageTitle); ?></title>
            <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/main.js"></script>
            <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/<?= Yii::app()->user->getIsGuest() ? 'watchGuest.js' : 'watchUser.js' ?>"></script>
    </head>
    <body>
        <?php if (isset(Yii::app()->params['GACode'])) echo Yii::app()->params['GACode']; ?>        
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="row-fluid">
                                <div class="span2"><a class="brand" href="<?= Yii::app()->homeUrl ?>"><?= Yii::app()->name ?></a></div>
                                <div class="span8">
                                    <form id="searchbox-form" action="<?= Yii::app()->createUrl('search/index') ?>" class="navbar-form form-search">
                                        <div class="input-append input-block-level">
                                            <input id="searchbox" name="search" type="text" autocomplete="off" placeholder="<?= Yii::app()->params['searchPlace']; ?>" class="input-block-level" value="<?= htmlspecialchars(Yii::app()->request->getParam('search', '')) ?>">
                                                <button class="btn" type="submit">GO</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- in development div class="span3 text-right">
                                <? if (Yii::app()->user->getIsGuest()) : ?>
                                    <?php
                                    $this->widget('bootstrap.widgets.TbButton', array(
                                        'label' => 'Login',
                                        'type' => 'primary',
                                        'htmlOptions' => array(
                                            'data-toggle' => 'modal',
                                            'data-target' => '#loginModal',
                                        ),
                                    ));
                                    ?>
                                    <?php
                                    $this->widget('bootstrap.widgets.TbButton', array(
                                        'label' => 'Register',
                                        'type' => 'primary',
                                        'htmlOptions' => array(
                                            'data-toggle' => 'modal',
                                            'data-target' => '#registerModal',
                                        ),
                                    ));
                                    ?>
                                    <? else : ?>
                                        <?= Yii::app()->user->name ?> <a href="<?= Yii::app()->createUrl('site/logout') ?>" class="btn btn-primary" type="submit">Logout</a>
                                    <? endif ?>
                                </div-->
                                <div class="span2">
                                    
                                </div>
                            </div>
                            <div class="row-fluid">
                                <div class="span12">
                                    <p class="text-center" style="font-weight: bold;">
                                        <?php
                                        foreach (Yii::app()->params['menu'] as $route => $title) {
                                            if (!empty($title))
                                                $menu[] = '<a href="' . Yii::app()->createUrl($route) . '">' . $title . '</a>';
                                        }
                                        echo join(' / ', $menu);
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
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
                <br/>
                Copyright &copy; <?php echo date('Y'); ?> by Laptop Top7<br/>
                All Rights Reserved.<br/>
            </div><!-- footer -->

        </div><!-- page -->
        <?php if (Yii::app()->user->getIsGuest()) { ?>
            <? $this->renderPartial('//site/watch-form'); ?>
            <? $this->renderPartial('//site/login-form'); ?>
            <? $this->renderPartial('//site/register-form'); ?>
            <?php
        } else {
            echo '<script type="text/javascript">var watchUrl = "' . Yii::app()->createUrl('watch/index') . '"</script>';
        }
        ?>
    </body>
</html>
