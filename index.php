<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

define('SBER_CLIENT_ID', '');
define('SBER_SECRET', '');
define('SBER_SCOPE', '');

require_once './secret.php';

require_once 'inbotlib_autoloader.php';

echo "Lib testing...\n";

$test = new inRecodeSpeech2Text();
//$test->_test();

$test = new inUniversalRecoder();
$test->_test();


$test = new inDayTitleGenerator();
$test->_test();



echo "\nLib testing done.\n";