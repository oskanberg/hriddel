
<!-- begin edit_article.tpl -->
<?php
if ($data['show_error'])
{
    echo $data['error_string'];
}
if ($data['show_result'])
{
    echo $data['submit_result_text'];
}
if ($data['show_form'])
{
?>
<div id="management">
    <div style="width:570px; display:inline-block; verticle-align:top">
        <form action="?submit&action=ammend_article&a_id=<?php echo $_GET['a_id']; ?>" method="post">
            <div id="accordion" style="display:none;">
                <h3>title</h3>
                <div>
                    <input type="text" name="title" style="width:90%" placeholder="Title" value="<?php echo $data['article']->title; ?>" autofocus/>
                </div>
                <h3>article content</h3>
                <div>
                    <textarea name="content" placeholder="Article content" style="height:250px; font-size: 12px; width:90%"><?php echo $data['article']->content; ?></textarea>
                </div>
                <h3>additional authors</h3>
                <div>
                    <select id="author_list">
                        <option value="placeholder">Add additional author ...</option>
                        <?php
                        foreach ($data['additional_author_possibilities'] as $author)
                        {
                            echo '<option value="' . $author->username . '">';
                            echo $author->username . '</option>';
                        }
                        ?>
                    </select>
                    <input type="text" id="additional_authors" name="additional_authors" style="width:90%" placeholder="Additional authors" value="<?php
                    foreach ($data['article']->authors as $author)
                    {
                        echo $author->username . ';';
                    }
                    ?>"/>
                </div>
                <?php
                if ($data['show_review_score'])
                {
                ?>
                <h3>review_score</h3>
                <div>
                    <select name="review_score" id="review_score">
                        <option value="">Select review score...</option>
                        <?php
                        for ($i = 0; $i <= 10; $i++)
                        {
                            echo '<option value="' . $i . '"';
                            if ($data['article']->review_score == $i)
                            {
                                echo ' selected>';
                            } else {
                                echo '>';
                            }
                            echo $i . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
                } else if ($data['show_column_name']) {
                ?>
                <input type="text" id="column_name" name="column_name" placeholder="Column name"
                value="<?php echo $data['article']->column_name ?>"/>
                <?php } ?>
                <h3>cover image</h3>
                <div>
                    <input type="text" name="cover_image" style="width:90%"placeholder="Link to cover image" value="<?php echo $data['article']->cover_image; ?>"/>
                </div>
                <h3>update!</h3>
                <div>
                    <input type="submit" class="form_button" value="update article"/>
                </div>
            </div>
        </form>
    </div>
    <div id="commenting_area">
        <ul id="comment_list">
            <?php
            foreach ($data['comments'] as $comment)
            {
                echo '<li class="comment">';
                echo '<span class="author">' . $comment->username . '</span><br />';
                echo '<span class="comment">' . $comment->content . '</span>';
                echo '</li>';
            }
            ?>
        </ul>
        <textarea id="comment_box" style="width:340px;" placeholder="Add comment here ..."></textarea>
        <p class='success'></p><p class='error'></p>
        <input type="submit" id="comment_button" onClick="add_comment()" class="form_button" value="add comment" style="background: rgba(25, 25, 60, .7)" />
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#accordion").accordion({
            activate: function(event, ui) {
                $(ui.newPanel).find('textarea, input, select').focus();
            },
            active: false,
            collapsible: false,
            heightStyle: 'content'
        }).show();
    });
    $(document).on('keydown', function(e)
    {
        var key = e.which;
        if (key == 9) { 
            e.preventDefault();
            if ($('*:focus').siblings(':visible').not('#type_selector').not('#author_list').length > 0)
            {
                console.log($('*:focus').siblings(':visible'));
                // there's an input after the focused one
                $('*:focus').siblings(':visible').focus();
            } else {
                var active = $('#accordion').accordion("option", "active");
                $('#accordion').accordion("option", "active", active + 1);
            }
        }
    });
    $('#type_selector').change(function()
    {
        if ($(this).val() === 'column article')
        {
            $('#review_score').hide();
            $('#column_name').show();
        } else if ($(this).val() === 'review') {
            $('#column_name').hide();
            $('#review_score').show();
        } else if ($(this).val() === 'article') {
            $('#review_score').hide()
            $('#column_name').hide();
        }
    });
    $('#author_list').change(function()
    {
        var content = $('#additional_authors').val();
        var selected = $('#author_list option:selected').val();
        if (selected != 'placeholder')
        {
            if (content === '')
            {
                content = selected;
            } else {
                content = content + ';' + selected;
            }
            $('#additional_authors').val(content);
            $('._statusDDL').val('0');
            $('#author_list option[value="' + selected +'"]').remove();
        }
    });
    function add_comment()
    {
        var article_id = <?php echo $_GET['a_id'] ?>;
        var comment = $('#comment_box').val();
        if (comment.length > 0)
        {
            var jqxhr = $.post(
            '?add_comment_submit&action=add_comment',
            {
                'a_id': article_id,
                'comment' : comment
            },
            function(data)
            {
                if (data === "")
                {
                    $('#commenting_area p.success').text('changes successfully saved.');
                    $('#comment_list').append('<li><span class="author"><?php echo $this->_model->get_logged_in_username() ?></span><br /><span class="comment">' + $('#comment_box').val() + '</span></li>');
                    $('#comment_box').val('');
                } else {
                    $('#commenting_area p.error').text(data);
                }
            }
            )
            .fail(function() {
                // for the sake of this assessment, this should never happen
                alert('Internal Server Error. Crap.');
            });
        } else {
            $('#commenting_area p.error').text('please enter a comment first');
        }
    }
</script>
<?php } ?>
<!-- end edit_article.tpl -->
