<?php
define('HOST', 'HOST_NAME');
define('PORT', 'PORT');
define('DB', 'DATABASE_NAME');
define('USER', 'DATABASE_USER');
define('PASS', 'DATABASE_PASSWORD');

$dbi = new mysqli(HOST, USER, PASS, DB, PORT);
if ($dbi->connect_error) {
    die("Connection failed: " . $dbi->connect_error);
}

function dump($t){
    echo "<pre>";
    print_r($t);
    echo "</pre>";
}