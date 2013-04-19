<div class="row">
    <div class="span4"><img src="<?=$i['LargeImage']['URL']?>"></div>
</div>

    <?php
    foreach($i['EditorialReviews']['EditorialReview'] as $r){
        echo '<iframe content="'.$r['Content'].'">';
    }
    ?>