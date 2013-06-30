<?php
/* @var $this SiteController */
/* @var $model GiftForm */
/* @var $form TbActiveForm */

$this->pageTitle = Yii::app()->name . ' - We have incredible offer for you!';
$this->breadcrumbs = array(
    'Incredible offer',
);
?>
<h1>We have incredible offer for you!</h1>
<p>
    Participate in our special offer and every 7th buyer will receive $70 amazon gift card.<br>
    Also, you can just choose to receive $10 amazon gift card for every laptop is ordered.<br>
    If you buy more than one laptop and you are 7th buyer you will receive $70 gift card + $10 for every laptop is ordered.<br>

    To receive gift card please do following requirements:<br>
    1. Be sure to go to amazon.com through <a target="_blank" href="http://www.amazon.com/b?_encoding=UTF8&camp=1789&creative=9325&linkCode=ur2&node=565108&site-redirect=&tag=laptoptop7com-20">this link</a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=laptoptop7com-20&l=ur2&o=1" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" /> or through laptop details "Buy at amazon" button.<br> 
    2. Order laptop for no more than 2 hours.<br>
    3. Go back and fill <a href="#form">Request Amazon Gift Card</a> form.<br>

    Gift cards will be mailed to you after laptops is shipped.<br>
<h5>No additional fees you will be charged and you lose nothing.</h5><br>
</p>
<?php
$this->widget('ext.WSocialButton', array('style' => 'box'));
?>
<h2>Request Amazon Gift Card</h2>
<a name="form"></a>
<?php if (Yii::app()->user->hasFlash('contact')): ?>

    <?php
    $this->widget('bootstrap.widgets.TbAlert', array(
        'alerts' => array('contact'),
    ));
    ?>

<?php else: ?>

    <div class="form">

        <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'contact-form',
            'type' => 'horizontal',
            'enableClientValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
            ),
        ));
        ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>
        <?php echo $form->textFieldRow($model, 'name'); ?>

        <?php echo $form->textFieldRow($model, 'email'); ?>

        <?php
        echo $form->radioButtonListRow($model, 'gift', array(
            '1' => 'I want to participate in the special offer and win $70 amazon gift card',
            '2' => 'No thanks, just send me $10 amazon gift card'
        ));
        ?>

        <?php echo $form->textAreaRow($model, 'body', array('rows' => 6, 'class' => 'span8')); ?>

        <?php if (CCaptcha::checkRequirements()): ?>
            <?php
            echo $form->captchaRow($model, 'verifyCode', array(
                'hint' => 'Please enter the letters as they are shown in the image above.<br/>Letters are not case-sensitive.',
            ));
            ?>
            <?php endif; ?>

        <div class="form-actions">
    <?php
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type' => 'primary',
        'label' => 'Submit',
    ));
    ?>
        </div>

    <?php $this->endWidget(); ?>

    </div><!-- form -->

<?php endif; ?>