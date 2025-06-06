<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Презентационен генератор</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <?php if (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/dashboard.css">
    <?php endif; ?>
    <?php if (strpos($_SERVER['REQUEST_URI'], '/presentation') !== false): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/presentation.css">
    <?php endif; ?>
    <?php if (strpos($_SERVER['REQUEST_URI'], '/workspace') !== false): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/workspace.css">
    <?php endif; ?>
    <?php if (strpos($_SERVER['REQUEST_URI'], '/slide') !== false): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/slides.css">
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <nav class="main-nav">
                <div class="nav-content">
                    <a href="<?php echo BASE_URL; ?>" class="logo">Презентационен генератор</a>
                    <ul class="nav-links">
                        <li><a href="<?php echo BASE_URL; ?>/dashboard">Табло</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/presentations">Презентации</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/logout">Изход</a></li>
                    </ul>
                </div>
            </nav>
        <?php endif; ?> 