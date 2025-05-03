<?php
session_start();
define('HOST', 'HOST_NAME');
define('PORT', 'PORT');
define('DB', 'DATABASE_NAME');
define('USER', 'DATABASE_USER');
define('PASS', 'DATABASE_PASSWORD');

try{
    $dbi = new mysqli(HOST, USER, PASS, DB, PORT);
} catch (Exception $e) {
    echo "Connection failed: ".$e->getMessage()." ไม่พบฐานข้อมูล ".DB;
    exit;
}
$dbi->query("SET NAMES utf8mb4");

function dump($t){
    echo "<pre>";
    print_r($t);
    echo "</pre>";
}

include_once 'functions.php';