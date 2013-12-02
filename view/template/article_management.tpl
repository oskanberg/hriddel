
<!-- begin submit_article.tpl -->
<div id="management">
    <div id="management_selectors">
        <ul>
            <li class="title">selectors</li>
            <li id="mine">show my authored</li>
            <?php
            $type = $this->_model->get_logged_in_type();
            if ($type == 'editor' || $type == 'publisher')
            {
            ?>
            <li id="my_edited">show my edited</li>
            <li id="submitted">show submitted</li>
            <li id="under_review">show under review</li>
            <li id="awaiting_changes">show awaiting changes</li>
            <li id="published">show published</li>
            <li id="highlighted">show highlighted</li>
            <?php
            }
            ?>
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
                echo '<li class="' . str_replace(' ', '_', $article->status);
                if ($is_mine) {
                    echo ' mine" ';
                } else {
                    echo '" ';
                }
                echo 'data-a_id="' . $article->get_id() . '"';
                echo '>';
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
            <input type="submit" id="view_button" onClick="view_article()" class="form_button" value="view" style="background: rgba(25, 25, 60, .7)" />
            <?php 
            $type = $this->_model->get_logged_in_type();
            if ($type == 'editor' || $type == 'publisher')
            {
            ?>
            <input type="submit" id="edit_button" onClick="edit_article()" class="form_button" value="edit / comment" style="background: rgba(25, 25, 60, .7)" />
            <input type="submit" id="ur_button" onClick="change_status('under review')" class="form_button" value="change to 'under review'" style="background: rgba(25, 25, 60, .7)" />
            <input type="submit" id="pb_button" onClick="change_status('published')" class="form_button" value="publish" style="background: rgba(25, 25, 60, .7)" />
            <input type="submit" id="reject_button" onClick="change_status('rejected')" class="form_button" value="reject" style="background: rgba(25, 25, 60, .7)" />
            <?php
            } else if ($type == 'writer') {
            ?>
            <input type="submit" id="edit_button" onClick="edit_article()" class="form_button" value="edit / view comments" style="background: rgba(25, 25, 60, .7)" />
            <?php
            }
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#item_list li').not('.guidance_message').not('.title').click(function()
    {
        if ($(this).hasClass('selected'))
        {
            // deselect
            $(this).toggleClass('selected');
        } else {
            // otherwise, make sure none others are selected first
            $('#item_list li').each(function()
            {
                if ($(this).hasClass('selected'))
                {
                    $(this).toggleClass('selected');
                }
            });
            $(this).toggleClass('selected');
        }
        showhide();
        
    });
    window.visible_elements = ['selected'];
    $('#management_selectors li').not('.title').click(function()
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
            $.each(class_list, function(i, v)
                {
                    // we have 'column_name' as a class, convert it
                    class_list[i].replace('_', ' ');
                });
            var hidden = true;
            for (var i = 0; i < class_list.length; i++)
            {
                console.log(class_list[i]);
                console.log(window.visible_elements);
                if ($.inArray(class_list[i], window.visible_elements) > -1)
                {
                    //console.log($(this) + ' : ' + converted);
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
        // showhide all buttons. The disallowed
        // buttons will just not exist for restricted users
        $('input.form_button').hide();
        $('#item_list li.selected').each(function ()
        {
            // calling 'each', but should only be one selected
            var class_list = $(this).attr('class').split(/\s+/);
            $.each(class_list, function(i, v)
                {
                    // we have 'column_name' as a class, convert it
                    class_list[i].replace('_', ' ');
                });
            $('#view_button').show();
            if ($.inArray('submitted', class_list) > -1)
            {
                $('#ur_button').show();
                $('#pb_button').show();
                $('#reject_button').show();
                $('#edit_button').show();
            } else if ($.inArray('awaiting_changes', class_list)) {
                $('#edit_button').show();
            }
        });
    }
    function view_article()
    {
        var a_id = $('#item_list li.selected').attr('data-a_id');
        parent.location = '?view_article&a_id=' + a_id;
    }
    function edit_article()
    {
        var a_id = $('#item_list li.selected').attr('data-a_id');
        parent.location = '?edit_article&a_id=' + a_id;
    }
    function change_status(type)
    {
        var article_id = $('#item_list li.selected').attr('data-a_id');
        if (article_id.length > 0)
        {
            var jqxhr = $.post(
            '?manage_articles_submit&action=change_article_status',
            {
                'a_id': article_id,
                'new_status' : type
            },
            function(data)
            {
                if (data === "")
                {
                    $('#item_list li.selected')
                        .removeClass('selected submitted under_review awaiting_changes published highlighted')
                        .addClass(type.replace(' ', '_'))
                        .find('.status').text(type);
                    $('p.success').text('changes successfully saved.');
                } else {
                    $('p.error').text(data);
                }
            }
            )
            .fail(function() {
                // for the sake of this assessment, this should never happen
                alert('Internal Server Error. Crap.');
            });
        } else {
            $('p.error').text('please choose one or more users to change (click on them)');
        }
    }
</script>

<!-- end submit_article.tpl -->
