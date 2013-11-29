
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
        <h3>Title</h3>
        <div>
            <input type="text" name="title" style="width:99%" placeholder="Title"/>
        </div>
        <h3>Article Content</h3>
        <div>
            <textarea name="content" placeholder="Article content" style="height:250px; width:99%">
            </textarea>
        </div>
        <h3>Article Type</h3>
        <div id="selector">
            <select name="type" id="type_selector">
                <option value="">Select article type...</option>
                <option value="article">article</option>
                <option value="review">review</option>
                <option value="column article">column article</option>
            </select>
            <input type="text" id="column_name" name="column_name" placeholder="Column name" style="visibility:hidden"/>
            <select name="review_score" id="review_score" style="visibility:hidden">
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
        <h3>Additional Authors</h3>
        <div>
            <input type="text" name="additional_authors" style="width:99%" placeholder="Additional authors"/>
        </div>
        <h3>Cover Image</h3>
        <div>
            <input type="text" name="cover_image" style="width:99%"placeholder="Link to cover image"/>
        </div>
        <h3>Submit!</h3>
        <div>
            <input type="submit" class="form_button"/>
        </div>  
    </div>
</form>
<script type="text/javascript">
    $(function () {
        $("#accordion").accordion({
            active: false,
            collapsible: false,
            heightStyle: "content"
        }).show();
    });

    $('#type_selector').change(function() {
        if ($(this).val() === 'column article') {
            $('#review_score').css('visibility', 'hidden');
            $('#column_name').css('visibility', 'visible');
        } else if ($(this).val() === 'review') {
            $('#column_name').css('visibility', 'hidden');
            $('#review_score').css('visibility', 'visible');
        } else if ($(this).val() === 'article') {
            $('#review_score').css('visibility', 'hidden');
            $('#column_name').css('visibility', 'hidden');
        }
    });
</script>
<?php } ?>
<!-- end submit_article.tpl -->
