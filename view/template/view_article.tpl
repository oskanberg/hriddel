<!-- begin view_article.tpl -->
<div id="article-area">
    <div id="cover-image" style="background-image:url(<?php echo $data['article']->cover_image; ?>);">
        <h2><?php
        echo $data['article']->title;
        if ($data['article'] instanceof Review)
        {
            echo '<br />';
            echo $data['article']->review_score . '/5';
        }
        ?></h2>
    </div>
    <div id="article">
        <p><?php echo nl2br($data['article']->content);?></p>
    </div>
    <div id="article-meta">
        <?php
        foreach ($data['article']->authors as $author)
        {
            echo '<p>Author: ' . $author->name . '</p>';
            echo '<p>Date: ' . $author->date . '</p>';
        }
        ?>
    </div>
</div>
<!-- end view_article.tpl -->