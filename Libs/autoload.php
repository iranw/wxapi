<?php

spl_autoload_register(function($class){
    $classfile = str_replace("\\", '/', $class);
    $classfile = './'.substr($classfile, 3);
    include $classfile.'.php';
}, true, true);
