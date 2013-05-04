<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id' => 'registerModal')); ?>
<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h4>Register</h4>
	<ul class="services">
		<li><a href="<?=Yii::app()->createUrl('site/login/service/facebook')?>"><img src="<?=Yii::app()->theme->baseUrl ?>/images/services/fb.png"></a></li>
		<li><a href="<?=Yii::app()->createUrl('site/login/service/google')?>"><img src="<?=Yii::app()->theme->baseUrl ?>/images/services/gl.png"></a></li>
		<li><a href="<?=Yii::app()->createUrl('site/login/service/twitter')?>"><img src="<?=Yii::app()->theme->baseUrl ?>/images/services/t.png"></a></li>
	</ul>
</div>
<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'register-form',
	'type' => 'horizontal',
	'enableClientValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
	),
)); ?>
<div class="modal-body">
	<? $model = new RegistrationForm ?>
	<div class="form">
		<?php echo $form->textFieldRow($model,'email'); ?>
		<?php echo $form->textFieldRow($model,'firstname'); ?>
		<?php echo $form->textFieldRow($model,'lastname'); ?>
		<?php echo $form->passwordFieldRow($model,'password'); ?>
		<?php echo $form->passwordFieldRow($model,'verifyPassword'); ?>
	</div>
</div>
<div class="modal-footer">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
	'buttonType' => 'button',
	'url' => Yii::app()->createUrl('/site/login'),
	'type' => 'primary',
	'label' => 'Register',
	'htmlOptions' => array(
		'id' => 'registerWithAjax'
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