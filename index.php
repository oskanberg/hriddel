<?php
session_save_path('/n/www/student/Y6386774/iapt/001/tmp/');
session_start();
define('ROOTPATH', __DIR__);
define('TEMPLATE_PATH', ROOTPATH . '/view/template/');
include ROOTPATH . '/control/bootstrap.php';

$map = array(
    'home' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
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
    'view_article' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'ViewArticleView'
    ),
    'articles' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'ArticlesView'
    ),
    'reviews' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'ReviewsView'
    ),
    'columns' => array(
        'model' => 'ArticleManagementModel',
        'controller' => 'ArticleManagementController',
        'view' => 'ColumnsView'
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
    $model = new ArticleManagementModel();
    $controller = new ArticleManagementController($model);
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