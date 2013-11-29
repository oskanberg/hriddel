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
    // special case: blank view for jQuery.post() result
    'manage_users_submit' => array(
        'model' => 'UserManagementModel',
        'controller' => 'UserManagementController',
        'view' => 'BlankView'
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