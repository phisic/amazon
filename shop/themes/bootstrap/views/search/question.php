<?php
if (empty($q))
    echo '<h1>Question not found</h1>';
else {
    ?>
    <h1><?= $q['Title'] ?></h1>
    <div class="row">
        <div class="<?= $related ? "span8" : "span12"; ?>">
            <p class="">
                <?= $q['Text'] ?>
            </p>
            <h3>Answers</h3>
            <?php
            foreach ($a as $n => $ans) {
                $text = preg_replace('@<span[^>]*?>.*?</span>@si', '', $ans['Text']);
                echo '<p class=""><b>' . ($n + 1) . '.</b>' . $text . '</p>';
            }
            ?>
            <h3>Linked products</h3>
            <?php
            foreach ($p as $n => $pr) {
                echo '<div><a href="' . Yii::app()->createSeoUrl('search/detail/' . $pr['ASIN'], $pr['Title']) . '">' . $pr['Title'] . '</a></div>';
            }
            ?>
        <? } ?>
    </div>
    <?php if ($related) { ?>
        <div class="span4">
            <h3 style="margin: 0px;">Similar Questions</h3>
            <?php
            foreach ($related as $r) {
                if($r['Id']==$q['Id'])
                    echo '<h5 style="background-color:#fdf59a">'.$r['Title'].'</h5>';
                else
                    echo '<h5><a href="'.Yii::app()->createUrl('search/question/'.$r['Id']).'">'.$r['Title'].'</a></h5>';
            }
            ?>
        </div>
    <?php } ?>
</div>