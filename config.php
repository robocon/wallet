<?php
define('HOST', 'db');
define('PORT', '3306');
define('DB', 'wallet');
define('USER', 'surasak');
define('PASS', '12345678');

$dbi = new mysqli(HOST, USER, PASS, DB, PORT);
if ($dbi->connect_error) {
    die("Connection failed: " . $dbi->connect_error);
}

function dump($t){
    echo "<pre>";
    print_r($t);
    echo "</pre>";
}