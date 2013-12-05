
<!-- begin user_management.tpl -->
<section class="content_box restrict_and_center pad">
    <div id="management">
        <div id="management_selectors">
            <ul>
                <li class="title">selectors</li>
                <li id="subscriber">show subscribers</li>
                <li id="writer">show writers</li>
                <li id="editor">show editors</li>
                <li id="publisher">show publishers</li>
            </ul>
        </div>
        <div id="management_menu">
            <ul id="item_list">
                <li class="title">
                    <span class="username">username</span><span class="full_name">name</span><span class="type">type</span>
                </li>
                <li class="guidance_message">
                    <p style="text-align:center">please apply a selector on the left</p>
                </li>
                <li class="none_found_message">
                    <p style="text-align:center">no matching users</p>
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
            <div class="buttons">
                <p class="error"></p><p class="success"></p>
                <input type="submit" onClick="assign_role('writer')" class="form_button" value="make writer" style="background: rgba(25, 25, 60, .7)" />
                <input type="submit" onClick="assign_role('publisher')" class="form_button" value="make publisher" style="background: rgba(25, 60, 25, .7)"/>
                <input type="submit" onClick="assign_role('editor')" class="form_button" value="make editor" style="background: rgba(60, 25, 25, .7)"/>
                <input type="submit" onClick="assign_role('subscriber')" class="form_button" value="make subscriber" style="background: rgba(80, 0, 80, .7)"/>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('#item_list li').not('.guidance_message, .none_found_message').click(function()
        {
            // if already selected, it might be an element
            // that should otherwise be hidden
            if ($(this).hasClass('selected'))
            {
                $(this).toggleClass('selected');
                var type = $(this).attr('class');
                if (!$('#management_selectors').find('#' + type).hasClass('selected'))
                {
                    $(this).hide('slow');
                }
            } else {
                $(this).toggleClass('selected');
            }
            $('p.error').text('');
            $('p.success').text('');
        });
        $('#management_selectors li').not('.title').click(function()
        {
            $(this).toggleClass('selected');
            if ($('#management_selectors li.selected').length > 0)
            {
                $('#item_list li.guidance_message').hide();
                $('#item_list li.title').show();
                $('#management_menu .buttons').show();
            } else {
                $('#item_list li.none_found_message').hide();
                $('#item_list li.guidance_message').show();
                $('#item_list li.title').hide();
                $('#management_menu .buttons').hide();
            }

            var type = $(this).attr('id');
            if ($(this).hasClass('selected'))
            {
                $('.' + type).show('slow');
            } else {
                // don't hide things we have selected
                $('.' + type).not('.selected').hide('slow');
            }
        });
        function assign_role(role)
        {
            var usernames = new Array();
            $('#item_list li.selected').each(function()
            {
                usernames.push($(this).find('.username').text());
            });
            if (usernames.length > 0)
            {
                var jqxhr = $.post(
                '?manage_users_submit&action=change_type_multiple',
                {
                    'users': usernames,
                    'type': role
                },
                function(data)
                {
                    $('#item_list li.selected')
                        .removeClass('selected publisher writer subscriber editor')
                        .addClass(role)
                        .find('.type').text(role);
                    $('p.success').text('changes successfully saved.');
                }
                )
                .fail(function() {
                    // for the sake of this assessment, this should never happen
                    alert("Internal Server Error. Crap.");
                });
            } else {
                $('p.error').text('please choose one or more users to change (click on them)');
            }
        }
    </script>
</section>
<!-- end user_management.tpl -->
