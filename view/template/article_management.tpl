
<!-- begin submit_article.tpl -->
<div id="management">
    <div id="management_selectors">
        <ul>
            <li class="title">selectors</li>
            <li id="mine">show my authored</li>
            <li id="my_edited">show my edited</li>
            <li id="submitted">show submitted</li>
            <li id="under_review">show under review</li>
            <li id="awaiting_changes">show awaiting changes</li>
            <li id="published">show published</li>
            <li id="highlighted">show highlighted</li>
        </ul>
    </div>
    <div id="management_menu">
        <ul id="item_list">
            <li class="title">
                <span class="title">title</span><span class="authors">authors</span><span class="article-type">type</span><span class="status">status</span>
            </li>
            <li class="guidance_message">
                <p style="text-align:center">please apply a selector on the left</p>
            </li>
            <?php
            foreach ($data['articles'] as $article)
            {
                $author_string = '';
                $num_authors = count($article->authors);
                $is_mine = false;
                for ($i = 0; $i < $num_authors; $i++)
                {
                    $author_string .= $article->authors[$i]->name;
                    // comma separate unless the last
                    if ($i != $num_authors - 1)
                    {
                        $author_string .= ', ';
                    }
                    if ($article->authors[$i]->username == $this->_model->get_logged_in_username())
                    {
                        $is_mine = true;
                    }
                }
                echo '<li class="' . $article->status;
                if ($is_mine) {
                    echo ' mine';
                }
                echo '">';
                echo '<span class="title">' . $article->title . '</span>';
                echo '<span class="authors">' . $author_string . '</span>';
                echo '<span class="article-type">' . $article->type . '</span>';
                echo '<span class="status">' . $article->status . '</span>';
                echo '</li>';
            }
            ?>
        </ul>
        <div class="buttons">
            <p class="error"></p><p class="success"></p>
        </div>
    </div>
</div>
<script type="text/javascript">
    window.visible_elements = [];
    $("#management_selectors li").not('.title').click(function()
    {
        $('#item_list li.guidance_message').hide();
        $('#item_list li.title').show();
        $('#management_menu .buttons').show();
        if ($(this).hasClass('selected'))
        {
            remove_element_from_list($(this).attr('id'), window.visible_elements);
        } else {
            window.visible_elements.push($(this).attr('id'));
        }
        $(this).toggleClass('selected');
        showhide();
    });
    function remove_element_from_list(element, list)
    {
        list.splice($.inArray(element, list), 1);
    }
    function showhide()
    {
        $('#item_list li')
            .not('.title')
            .not('.guidance_message')
            .each(function ()
            {
                var class_list = $(this).attr('class').split(/\s+/);
                var hidden = true;
                for (var i = 0; i < class_list.length; i++)
                {
                    var converted = class_list[i].replace(/abc/g, '');
                    if ($.inArray(converted, window.visible_elements) > -1)
                    {
                        console.log($(this) + ' : ' + converted);
                        hidden = false;
                    }
                }
                if (hidden)
                {
                    $(this).hide('slow');
                } else {
                    $(this).show('slow');
                }
            });
    }
</script>

<!-- end submit_article.tpl -->
