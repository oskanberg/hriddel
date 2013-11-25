<?php

define('ROOTPATH', __DIR__);
define('TEMPLATE_PATH', ROOTPATH . '/view/template/');
include ROOTPATH . '/control/bootstrap.php';

$map = array(
    'home' => array('controller' => 'IndexController', 'view' => 'IndexView'),
    'login' => array('controller' => 'LoginController', 'view' => 'LoginView')
);

$model = new Model();
$view = null;
$controller = null;

if(empty($_GET))
{
    $controller = new IndexController($model);
    $view = new IndexView($controller);
} else {
    $keys = array_keys($_GET);
    $page = $keys[0];
    $controller = new $map[$page]['controller']($model);
    $view = new $map[$page]['view']($controller);
}

$view->display();

?>