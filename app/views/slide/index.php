<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Слайдове</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Слайдове в презентация</h1>

    <a href="/slide/create/<?php echo $presentationId; ?>">Създай нов слайд</a>

    <ul>
        <?php foreach ($slides as $slide): ?>
            <li>
                <a href="/slide/view/<?php echo $slide['id']; ?>">
                    <?php echo htmlspecialchars($slide['content']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>