<?php

spl_autoload_register('class_autoloader');

function class_autoloader($class)
{
    $dirs = array('/model/', '/view/', '/control/');
    foreach ($dirs as $dir)
    {
        $path = ROOTPATH . $dir . $class . '.class.php';
        if (file_exists($path))
        {
            include $path;
            break;
        }
    }
}