<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="language" content="en"/>

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css"/>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
	<script type="text/javascript" src="/js/main.js"></script>

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
					<? if(Yii::app()->user->getIsGuest()) : ?>
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
					<?= Yii::app()->user->name ?> <a href="/site/logout" class="btn btn-primary" type="submit">Logout</a>
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
				<div class="span8"><h4><a href="">Top price drops</a> <a href="">Bestsellers</a> <a href="">New
					releases</a></h4></div>
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
	</div>
	<!-- footer -->

</div>
<? if (Yii::app()->user->getIsGuest()) : ?>
<!-- page -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id' => 'loginModal')); ?>
<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h4>Login</h4>
</div>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'login-form',
	'type'=>'horizontal',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
<div class="modal-body">
<? $model = new LoginForm; ?>
<div class="form">
	<?php echo $form->textFieldRow($model,'username'); ?>
	<?php echo $form->passwordFieldRow($model,'password'); ?>
	<?php echo $form->checkBoxRow($model,'rememberMe'); ?>
</div>

<div class="modal-footer">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType'=>'button',
	'url' => Yii::app()->createUrl('/site/login'),
	'type'=>'primary',
	'label'=>'Login',
	'htmlOptions' => array(
		'id' => 'loginWithAjax'
	)
)); ?>
	<?php $this->widget('bootstrap.widgets.TbButton', array(
	'label' => 'Close',
	'url' => '#',
	'htmlOptions' => array('data-dismiss' => 'modal'),
)); ?>
</div>
<?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>
<? endif ?>
</body>
</html>
