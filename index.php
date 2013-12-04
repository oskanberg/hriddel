<?php

define('ROOTPATH', __DIR__);
define('TEMPLATE_PATH', ROOTPATH . '/view/template/');
include ROOTPATH . '/control/bootstrap.php';

$map = array(
    'home' => array(
        'model' => 'HomeModel',
        'controller' => 'IndexController',
        'view' => 'IndexView'
    ),
    'login' => array(
        'model' => 'UserManagementModel',
        'controller' => 'UserManagementController',
        'view' => 'LoginView'
    ),
    'register' => array(
        'model' => 'UserManagementModel',
        'controller' => 'UserManagementController',
        'view' => 'RegisterView'
    ),
    'submit' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'SubmitArticleView'
    ),
    'manage_users' => array(
        'model' => 'UserManagementModel',
        'controller' => 'UserManagementController',
        'view' => 'UserManagementView'
    ),
    'manage_articles' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'ArticleManagementView'
    ),
    'edit_article' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'EditArticleView'
    ),
    // special cases: blank view for jQuery.post() result
    'manage_users_submit' => array(
        'model' => 'UserManagementModel',
        'controller' => 'UserManagementController',
        'view' => 'ErrorView'
    ),
    'manage_articles_submit' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'ErrorView'
    ),
    'add_comment_submit' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'ErrorView'
    ),
);

$model = null;
$view = null;
$controller = null;

if (empty($_GET))
{
    $model = new IndexModel();
    $controller = new IndexController($model);
    $view = new IndexView($controller, $model);
} else {
    $keys = array_keys($_GET);
    $page = $keys[0];
    $model = new $map[$page]['model']();
    $controller = new $map[$page]['controller']($model);
    $view = new $map[$page]['view']($controller, $model);
}

if (isset($_GET['action']))
{
    $controller->{$_GET['action']}();
}

$view->display();

?>