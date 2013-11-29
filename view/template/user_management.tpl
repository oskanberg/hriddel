
<!-- begin submit_article.tpl -->
<ul id="user_list">
    <li class="title">
        <span class="username">Username</span><span class="full_name">Name</span><span class="type">Type</span>
    </li>
    <?php
    foreach ($data['users'] as $user)
    {
        echo '<li class="' . $user->type . '">';
        echo '<span class="username">' . $user->username . '</span>';
        echo '<span class="full_name">' . $user->name . '</span>';
        echo '<span class="type">' . $user->type . '</span>';
        echo '</li>';
    }
    ?>
</ul>
<div style="text-align:center">
    <p>Make selected:</p>
    <input type="submit" onClick="assign_role('writer')" class="form_button" value="writers" style="background: rgba(25, 25, 60, .7)" />
    <input type="submit" onClick="assign_role('publisher')" class="form_button" value="publishers" style="background: rgba(25, 60, 25, .7)"/>
    <input type="submit" onClick="assign_role('editor')" class="form_button" value="editors" style="background: rgba(60, 25, 25, .7)"/>
    <input type="submit" onClick="assign_role('subscriber')" class="form_button" value="subscribers" style="background: rgba(80, 0, 80, .7)"/>
</div>
<script type="text/javascript">
    $("#user_list li").click(function()
    {
        $(this).toggleClass('selected');
    });
    function assign_role(role)
    {
        var usernames = new Array();
        $("#user_list li.selected").each(function()
        {
            usernames.push($(this).find('.username').text());
        });
        var jqxhr = $.post(
            '?manage_users_submit&action=change_type_multiple',
            {'users': usernames, 'type': role},
            function(data)
            {
                alert(data);
                $('#user_list li.selected')
                    .removeClass('selected publisher writer subscriber editor')
                    .addClass(role);
            }
        )
        .fail(function() {
            alert("error");
        });
    }
</script>

<!-- end submit_article.tpl -->
