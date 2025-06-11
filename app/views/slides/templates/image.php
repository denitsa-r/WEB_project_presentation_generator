<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/slide-templates.css">
    <title>Изображение слайд</title>
</head>
<body>
    <div class="slide">
        <h2 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h2>
        <div class="slide-content layout-full">
            <div class="content-element">
                <img src="<?= htmlspecialchars($slide['content']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>" class="slide-image">
            </div>
        </div>
    </div>
</body>
</html> 