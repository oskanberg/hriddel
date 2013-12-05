<!-- begin view_article.tpl -->
<div id="article_id" style="display:none"><?php echo $_GET['a_id']?></div>
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
        }
        echo '<p>Date: ' . $data['article']->date . '</p>';
        if ($this->_model->can_user_like_dislike())
        {
        ?>
        <p class="error"></p>
        <p class="success"></p>
        <input type="submit" id="like_button" onClick="like()" class="form_button" value="like" <?php echo $data['like_suffix']; ?> />
        <input type="submit" id="dislike_button" onClick="dislike()" class="form_button" value="dislike" <?php echo $data['dislike_suffix'];?> />
        <?php
        }
        ?>
    </div>
</div>
<script type="text/javascript">
    function like()
    {
        var article_id = $('#article_id').text();
        if (article_id.length > 0)
        {
            var jqxhr = $.post(
            '?manage_articles_submit&action=like_article',
            {
                'a_id': article_id,
            },
            function(data)
            {
                if (data === "")
                {
                    $('p.success').text('liked');
                    $('#like_button')
                        .attr('disabled','disabled')
                        .css('background', 'rgba(25, 25, 60, .2)');
                    $('#dislike_button')
                        .removeAttr('disabled')
                        .css('background', 'rgba(25, 25, 60, .7)');
                } else {
                    $('p.error').text(data);
                }
                setTimeout(function()
                {
                    $('p.error').text('');
                    $('p.success').text('');
                }, 2000);
            })
            .fail(function() {
                // for the sake of this assessment, this should never happen
                alert('Internal Server Error. Crap.');
            })
        } else {
            $('p.error').text('please choose an article to change (click on one)');
        }
    }
    function dislike()
    {
        var article_id = $('#article_id').text();
        if (article_id.length > 0)
        {
            var jqxhr = $.post(
            '?manage_articles_submit&action=dislike_article',
            {
                'a_id': article_id,
            },
            function(data)
            {
                if (data === "")
                {
                    $('p.success').text('disliked');
                    $('#dislike_button')
                        .attr('disabled','disabled')
                        .css('background', 'rgba(25, 25, 60, .2)');
                    $('#like_button')
                        .removeAttr('disabled')
                        .css('background', 'rgba(25, 25, 60, .7)');
                } else {
                    $('p.error').text(data);
                }
                setTimeout(function()
                {
                    $('p.error').text('');
                    $('p.success').text('');
                }, 2000);
            })
            .fail(function() {
                // for the sake of this assessment, this should never happen
                alert('Internal Server Error. Crap.');
            })
        } else {
            $('p.error').text('please choose an article to change (click on one)');
        }
    }
</script>
<!-- end view_article.tpl -->