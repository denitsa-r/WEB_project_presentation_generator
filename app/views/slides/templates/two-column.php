<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/main.css">
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/slide-templates.css">
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