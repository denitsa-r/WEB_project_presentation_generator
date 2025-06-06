<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/slide-templates.css">
    <title>Двуколонен слайд</title>
</head>
<body>
    <div class="slide">
        <h2 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h2>
        <div class="slide-content layout-columns-2">
            <?php
            $columns = explode('|||', $slide['content']);
            $leftContent = trim($columns[0] ?? '');
            $rightContent = trim($columns[1] ?? '');
            ?>
            <div class="content-element">
                <?= nl2br(htmlspecialchars($leftContent)) ?>
            </div>
            <div class="content-element">
                <?= nl2br(htmlspecialchars($rightContent)) ?>
            </div>
        </div>
    </div>
</body>
</html> 