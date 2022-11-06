<?php
ini_set('error_reporting', E_ALL);
date_default_timezone_set("Asia/Singapore");

$db_name = 'puduyugamftp_laravel';
$db_user = 'puduyugamftp_laravel';
$db_pass = 'j5XXZXQOY';
$db_host = '127.0.0.1';
$conn = new mysqli( $db_host, $db_user, $db_pass, $db_name ); 

mysqli_query($conn, 'ALTER TABLE `products` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `ProdCode`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`');




?>