<!-- begin base_template.tpl -->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="/css/base.css" />
        <title><?php echo $data['title']; ?></title>
    </head>
    <body class="site">
        <header>
                <div class="account_info" /> 
        </header>
        <nav>
            <a href="/">Home</a>
        </nav>
        <div class="page_content">
            <?php include_once(TEMPLATE_PATH . $data['view_specific_template']); ?>
        </div>
    </body>
</html>
<!-- begin base_template.tpl -->