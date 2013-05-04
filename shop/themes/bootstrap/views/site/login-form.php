<!-- page -->
<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id' => 'loginModal')); ?>
<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h4>Login using Facebook, Google or Twitter</h4>
	<ul class="services">
        <li><a href="<?=Yii::app()->createUrl('site/login/service/facebook')?>"><img src="<?=Yii::app()->theme->baseUrl ?>/images/services/fb.png"></a></li>
		<li><a href="<?=Yii::app()->createUrl('site/login/service/google')?>"><img src="<?=Yii::app()->theme->baseUrl ?>/images/services/gl.png"></a></li>
		<li><a href="<?=Yii::app()->createUrl('site/login/service/twitter')?>"><img src="<?=Yii::app()->theme->baseUrl ?>/images/services/t.png"></a></li>
	</ul>
</div>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'login-form',
	'type' => 'horizontal',
	'enableClientValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
	),
)); ?>
<div class="modal-body">
	<? $model = new LoginForm; ?>
	<div class="form">
		<?php echo $form->textFieldRow($model, 'username'); ?>
		<?php echo $form->passwordFieldRow($model, 'password'); ?>
		<?php echo $form->checkBoxRow($model, 'rememberMe'); ?>
	</div>
</div>
<div class="modal-footer">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType' => 'button',
	'url' => Yii::app()->createUrl('/site/login'),
	'type' => 'primary',
	'label' => 'Login',
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