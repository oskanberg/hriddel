
<!-- begin register.tpl -->
<?php
if ($data['register_result'])
{
    echo $data['register_result_text'];
} else {
    echo $data['register_result_text']; ?>
<form action="?register" method="post">
  <input type="text" name="username" placeholder="Username">
  <input type="text" name="name" placeholder="Full name">
  <input type="submit">
</form>
<?php } ?>
<!-- end register.tpl -->
