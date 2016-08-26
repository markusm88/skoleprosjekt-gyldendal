<?php

//STARTS DB CONNECTION
if($_SERVER['HTTP_HOST'] == 'home.nith.no')
    $dsn = 'mysql:host=mysql.nith.no;dbname=ingmag13';
else 
    $dsn = 'mysql:host=mysql.nith.no:3306;dbname=ingmag13';
$username = 'ingmag13';
$password = 'ingmag13PJ';
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
); 

//ENABLES GLOBAL DB ACCESS TO ALL FILES INCLUDING
$db = new PDO($dsn, $username, $password, $options);
?>