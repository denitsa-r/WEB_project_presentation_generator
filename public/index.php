<?php
session_start();
require_once '../app/core/App.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';
require_once '../app/core/Database.php';
require_once '../app/core/AuthMiddleware.php';

require_once '../config/config.php';

$app = new App();
