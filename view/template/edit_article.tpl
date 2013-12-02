
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
    <div style="width:470px; display:inline-block; verticle-align:top">
        <form action="?submit&action=submit_article" method="post">        
            <div id="accordion" style="display:none;">
                <h3>title</h3>
                <div>
                    <input type="text" name="title" style="width:90%" placeholder="Title" autofocus/>
                </div>
                <h3>article content</h3>
                <div>
                    <textarea name="content" placeholder="Article content" style="height:250px; width:90%">
                    </textarea>
                </div>
                <h3>additional authors</h3>
                <div>
                    <select id="author_list">
                        <option value="placeholder">Add additional author ...</option>
                        <?php
                        foreach ($data['authors'] as $author)
                        {
                            if ($author->username != $this->_model->get_logged_in_username())
                            {
                                echo '<option value="' . $author->username . '">';
                                echo $author->username . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <input type="text" id="additional_authors" name="additional_authors" style="width:90%" placeholder="Additional authors"/>
                </div>
                <h3>cover image</h3>
                <div>
                    <input type="text" name="cover_image" style="width:90%"placeholder="Link to cover image"/>
                </div>
                <h3>update!</h3>
                <div>
                    <input type="submit" class="form_button"/>
                </div>
            </div>
        </form>
    </div>
    <div id="commenting_area">
        <ul id="past_comments">
            <?php
            $comments = array();
            foreach ($comments as $comment)
            {
                echo '<li class="comment">';
                echo $comment->content;
                echo '</li>';
            }
            ?>
        </ul>
        <textarea id="comment_box" style="width:450px;" placeholder="Add comment here ..."></textarea>
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
</script>
<?php } ?>
<!-- end edit_article.tpl -->
