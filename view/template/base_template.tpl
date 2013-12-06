<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
        <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="<?php echo HTTP_ROOTPATH; ?>/css/base.css" />
        <title><?php echo $data['title']; ?></title>
    </head>
    <body>
        <section id="site">
            <header id="global_header">
                <div class="restrict_and_center pad">
                    <section id="user_menu">
                        <?php if ($this->_model->is_user_logged_in())
                        {
                            echo '<p>Logged in as ' . $this->_model->get_logged_in_username() . '</p>';
                            echo '<a href="<?php echo HTTP_ROOTPATH; ?>/?login&action=logout">Logout</a>';
                        } else {
                        ?>
                        <ul>
                            <li><a href="<?php echo HTTP_ROOTPATH; ?>/?login">Login</a></li>
                            <li><a href="<?php echo HTTP_ROOTPATH; ?>/?register">Register</a></li>
                        </ul>
                        <?php } ?>
                    </section>
                </div>
            </header>
            <nav id="main_menu" class="cf">
                <div class="restrict_and_center">
                    <ul id="horizontal_list" style="width:465px">
                        <li>
                            <a href="/">home</a>
                        </li>
                        <li>
                            <a href="<?php echo HTTP_ROOTPATH; ?>/?articles">articles</a>
                        </li>
                        <li>
                            <a href="<?php echo HTTP_ROOTPATH; ?>/?reviews">reviews</a>
                        </li>
                        <li>
                            <a href="<?php echo HTTP_ROOTPATH; ?>/?columns">columns</a>
                        </li>
                        <?php
                        if ($this->_model->can_current_user_submit_articles())
                        {
                        ?>
                        <li>
                            <a href="#"></a>
                        </li>
                        <li style="background: rgba(100,200,100,0.5);">
                            <a href="<?php echo HTTP_ROOTPATH; ?>/?submit">submit article</a>
                        </li>
                        <li style="background: rgba(100,200,100,0.5);">
                            <a href="<?php echo HTTP_ROOTPATH; ?>/?manage_articles">manage articles</a>
                        </li>
                        <?php
                        }
                        if ($this->_model->can_current_user_manage_users()) {
                        ?>
                        <li style="background: rgba(100,200,100,0.5);">
                            <a href="<?php echo HTTP_ROOTPATH; ?>/?manage_users">manage users</a>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </nav>
            <div id="non_nav" class="cf">
                <?php
                include_once(TEMPLATE_PATH . $data['view_specific_template']);
                ?>
            </div>
            <div class="spacer"></div>
            <footer id="global_footer">
            </footer>
        </section>
        <div class="spacer"></div>
    </body>
</html>