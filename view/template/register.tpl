
<!-- begin register.tpl -->
<?php
if ($data['show_error'])
{
    echo $data['error_string'];
}
if ($data['show_result'])
{
    echo $data['register_result_text'];
}
if ($data['show_form'])
{
?>
<form action="?register&action=register_user" method="post" class="pad" style="width:220px; margin: 0 auto;">
  <input type="text" name="username" placeholder="Username"><br />
  <input type="text" name="name" placeholder="Full name"><br />
  <input type="submit">
</form>
<?php } ?>
<!-- end register.tpl -->
