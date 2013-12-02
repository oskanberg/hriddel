
<!-- begin submit_article.tpl -->
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
<form action="?submit&action=submit_article" method="post">        
    <div id="accordion" style="display:none;">
        <h3>title</h3>
        <div>
            <input type="text" name="title" style="width:99%" placeholder="Title" autofocus/>
        </div>
        <h3>article content</h3>
        <div>
            <textarea name="content" placeholder="Article content" style="height:250px; width:99%">
            </textarea>
        </div>
        <h3>article type</h3>
        <div id="selector">
            <select name="type" id="type_selector">
                <option value="">Select article type...</option>
                <option value="article">article</option>
                <option value="review">review</option>
                <option value="column article">column article</option>
            </select>
            <input type="text" id="column_name" name="column_name" placeholder="Column name" style="display:none"/>
            <select name="review_score" id="review_score" style="display:none">
                <option value="">Select review score...</option>
                <option value="0">0</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
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
            <input type="text" id="additional_authors" name="additional_authors" style="width:99%" placeholder="Additional authors"/>
        </div>
        <h3>cover image</h3>
        <div>
            <input type="text" name="cover_image" style="width:99%"placeholder="Link to cover image"/>
        </div>
        <h3>submit!</h3>
        <div>
            <input type="submit" class="form_button"/>
        </div>
    </div>
</form>
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
<!-- end submit_article.tpl -->
