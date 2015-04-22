<?php
/*
 * This file works as an class autoloader and needs to be included at the top of every file that accesses classes or namespaces
 */
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

/*
 * Register given function as __autoload() implementation
 */
spl_autoload_register(function ($className)
{
    $className = ltrim($className, '\\');
    $filename  = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $filename  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $filename .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $filename = stream_resolve_include_path($filename);

    if ($filename === false) {
        return false;
    }
    include_once $filename;
}, true, true);