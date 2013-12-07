<?php

/**
 * register an autoload function
 */
spl_autoload_register('class_autoloader');


/**
 * autoload classes
 * 
 * Load classes from /model/ /view/ and /control/
 * @param string $class name of the class required
 */
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