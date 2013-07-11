<?php
if (empty($q))
    echo '<h1>Question not found</h1>';
else {
    ?>
    <h1><?= $q['Title'] ?></h1>
    
    <div class="row">
        <div class="<?= $related ? "span8" : "span12"; ?>">
            <h4>Linked products</h4>
            <?php
            foreach ($p as $n => $pr) {
                echo '<h4><a href="' . Yii::app()->createSeoUrl('search/detail/' . $pr['ASIN'], $pr['Title']) . '">' . $pr['Title'] . '</a></h4>';
            }
            ?>
            <p class="">
                <?= $q['Text'] ?>
            </p>
            <h3>Answers</h3>
            <?php
            foreach ($a as $n => $ans) {
                $text = strip_tags($ans['Text'],'<br>,<i>,<b>,<p>,<ul>,<li>');
                $text = str_replace('<p>Report Abuse</p>', '',$text);
                echo '<p class=""><b>' . ($n + 1) . '.</b>' . $text . '</p>';
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
            <h5>Source: answers.yahoo.com</h5>
        </div>
    <?php } ?>
</div>