<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

define ("IN_LIB_ROOT", "/Users/drweb_000/OneDrive/MyData/phplib");

spl_autoload_register(function ($class) {
    $suggested = [
        __DIR__. "/inBotLib/classes/$class.php",
        IN_LIB_ROOT. "/inBotLib/classes/$class.php",
        IN_LIB_ROOT. "/lhTestingSuite/classes/$class.php",
    ];
    
    foreach ($suggested as $file) {
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
