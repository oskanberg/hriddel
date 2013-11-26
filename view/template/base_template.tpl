<!-- begin base_template.tpl -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="/css/base.css" />
        <title><?php echo $data['title']; ?></title>
    </head>
    <body>
        <section id="site">
            <header id="global_header">
                <div class="restrict_and_center pad cf">
                    <img id="logo" src="https://www.google.co.uk/images/srpr/logo11w.png" height='70px' width='200px'/>
                    <section id="user_menu">
                        <ul>
                            <li><a href="?Home">Home</a></li>
                            <li><a href="?login">Login</a></li>
                            <li><a href="?register">Register</a></li>
                        </ul>
                    </section>
                </div>
            </header>
            <nav id="main_menu">
                <div class="restrict_and_center cf">
                    <ul id="horizontal_list">
                        <li class="horizontal_list">
                            <a href="/">HOME</a>
                        </li>
                        <li class="horizontal_list">
                            <a href="/articles">ARTICLES</a>
                        </li>
                        <li class="horizontal_list">
                            <a href="/reviews">REVIEWS</a>
                        </li>
                        <li class="horizontal_list">
                            <a href="/columns">COLUMNS</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div id="non_nav">
                <section class="content_box restrict_and_center pad">
                    <?php
                    include_once(TEMPLATE_PATH . $data['view_specific_template']);
                    ?>
                </section>
            </div>
            <footer id="global_footer">
            </footer>
        </section>
    </body>
</html>
<!-- begin base_template.tpl -->