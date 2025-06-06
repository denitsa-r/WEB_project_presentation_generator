<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'presentation_generator');
define('DB_USER', 'root');
define('DB_PASS', ''); // смени при нужда

// Автоматично определяне на базовия URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$base_url = $protocol . $host . $script_name;
define('BASE_URL', $base_url);
