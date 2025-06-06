<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/slide-templates.css">
    <title>Списък слайд</title>
</head>
<body>
    <div class="slide">
        <h2 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h2>
        <div class="slide-content layout-full">
            <div class="content-element">
                <ul class="slide-list">
                    <?php
                    $items = explode("\n", $slide['content']);
                    foreach ($items as $item):
                        if (trim($item) !== ''):
                    ?>
                        <li><?= htmlspecialchars(trim($item)) ?></li>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html> 