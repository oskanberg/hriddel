
<!-- begin login.tpl -->
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
<form action="?login&action=authenticate" method="post">
  <input type="text" name="username" placeholder="Username">
  <input type="submit">
</form>
<?php
}
?>
<!-- end login.tpl -->
