
<!-- begin submit_article.tpl -->
    <style type="text/css">
    #user_list ul {
        width: 100%;
        padding: 0 0 0 0;
        margin: 0 auto;
    }
    #user_list span.username {
        display: inline-block;
        width: 30%;
    }
    #user_list span.full_name {
        display: inline-block;
        width: 40%;
    }
    #user_list span.type {
        display: inline-block;
        width: 30%;
    }
    #user_list li {
        color: #FFFFFF;
        list-style: none;
        border-radius: 2px;
        padding: 10px;
        margin: 10px 0 10px 0;
        border: 5px solid rgba(0,0,0,0);
        cursor: pointer;
    }
    li.subscriber {
        background: rgba(80, 0, 80, .6);
    }
    li.writer {
        background: rgba(25, 25, 60, .6);
    }
    li.publisher {
        background: rgba(25, 60, 25, .6);
    }
    li.editor {
        background: rgba(60, 25, 25, .6);
    }
    #user_list li.title {
        background: rgba(0,0,0,0);
        color: #444444;
        font-weight: bold;
    }
    #user_list li.selected {
        border: 5px dotted rgba(0,0,0,0.7);
    }
</style>
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
    <input type="submit" class="form_button" value="writers" style="background: rgba(25, 25, 60, .7)" />
    <input type="submit" class="form_button" value="publishers" style="background: rgba(25, 60, 25, .7)"/>
    <input type="submit" class="form_button" value="editors" style="background: rgba(60, 25, 25, .7)"/>
    <input type="submit" class="form_button" value="subscribers" style="background: rgba(80, 0, 80, .7)"/>
</div>
<script type="text/javascript">
    $("#user_list li").click(function() {
        $(this).toggleClass('selected');
    });
</script>
<!-- end submit_article.tpl -->
