
<!-- begin login.tpl -->
<section class="content_box restrict_and_center pad">
    <?php
    if ($data['show_error'])
    {
        echo $data['error_string'];
    }
    if ($data['show_result_text'])
    {
        echo $data['login_result_text'];
    }
    if ($data['show_login'])
    {
    ?>
    <form action="?login&action=authenticate" method="post" class="pad" style="width:220px; margin: 0 auto;">
        <input type="text" name="username" placeholder="Username" autofocus><input type="submit">
    </form>
    <?php
    }
    ?>
</section>
<!-- end login.tpl -->
