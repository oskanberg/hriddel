
<!-- begin login.tpl -->
<?php
if ($data['login_result'])
{
    echo $data['login_result_text'];
} else {
    echo $data['login_result_text']; ?>
<form action="?login" method="post">
  <input type="text" name="u_id" placeholder="Username">
  <input type="submit">
</form>
<?php } ?>
<!-- end login.tpl -->
