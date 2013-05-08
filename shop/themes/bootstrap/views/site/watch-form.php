<div class="hide watch-form-body">
    <form class="form-horizontal watch-form" action="<?= Yii::app()->createUrl('watch/index') ?>">
        <div class="text-error hide"></div>
        <div class="watch-body">
            <p>
            <div><span>First name<span class="text-error">*</span></span></div>
            <input type="text" name="FirstName" class="input-large">
            </p>
            <p>
            <div><span>Email<span class="text-error">*</span></span></div>
            <input type="text" name="Email" class="input-large">
            </p>    
            <p>
                <button tag="" type="button" class="btn btn-primary">Watch</button> 
                <button type="button" class="btn btn-warning pull-right">Cancel</button>
            </p>
        </div>
    </form>
</div>